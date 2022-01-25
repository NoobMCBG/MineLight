<?php

namespace instantlyta\fcaclan;

use _64FF00\PureChat\PureChat;
use instantlyta\fcaclan\event\ClanChangeEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\RemoteConsoleCommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
//use LDX\iProtector\Main as Iprotect;

class FCAClan extends PluginBase implements Listener
{
    const OWNER_RANK = 3;

    const ACTION_INVITE = 0;
    const ACTION_KICK = 1;
    const ACTION_REQUEST_CONTROL = 2;
    const ACTION_SET_MOTD = 3;
    const ACTION_SET_RANK = 4;
    const ACTION_LEVEL_UP = 5;

    const SETTING_MAX_PLAYERS = 0;
    const SETTING_REQUIRED_POINT = 1;
    const SETTING_REQUIRED_MONEY = 2;
    const SETTING_CLAN_TAG = 3;

    private $settings = [];

    private $clans = [];
    private $invitePending = [];
    private $inviteSendConfirm = [];
    private $kickPending = [];
    private $quitPending = [];
    private $welcomePending = [];
    private $clanPromotePending = [];
    private $clanDeletePending = [];

    public $topClan = [];

    private static $instance = null;

    public function onLoad()
    {
        self::$instance = $this;
    }

    /**
     * @return FCAClan
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->reloadConfig();
        @mkdir($this->getDataFolder() . "profiles/");
        $this->clans = (new Config($this->getDataFolder() . "clans.yml", Config::YAML))->getAll();
        $this->settings = $this->getConfig()->get("settings");
		$this->iprotect = $this->getServer()->getPluginManager()->getPlugin("iProtector");
        $this->updateTopClan();
        $this->getScheduler()->scheduleDelayedRepeatingTask(new UpdateTopClanTask(), 36000, 36000);
    }

    public function onDisable()
    {
        $this->save();
    }

    // Events

    /*public function onJoin(PlayerLoginEvent $event) {
        $player = $event->getPlayer();
        if ($this->haveClan($player)) {
            $player->setDisplayName("[§l§3• §a" . $this->getClanName($player) . "§f •]" . $player->getName());
        }
    }*/

    public function updateNametag(ClanChangeEvent $event)
    { // PureChat API
        /** @var PureChat $pureChat */
        $pureChat = $this->getServer()->getPluginManager()->getPlugin("PureChat");
        if ($pureChat === null) return;
        $p = $event->getPlayer();

        $isMultiWorldSupportEnabled = $pureChat->getConfig()->get("enable-multiworld-support");

        $levelName = $isMultiWorldSupportEnabled ? $p->getLevel()->getName() : null;

        $p->setNameTag($pureChat->getNameTag($p, $levelName));
    }


    public function onDamagee(EntityDamageEvent $event)
    {
        if($event->isCancelled() or !$this->iprotect->canGetHurt($event->getEntity())){
	    return false;
		}else{
            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                $entity = $event->getEntity();
                if (!($damager instanceof Player) || !($entity instanceof Player) || !$this->iprotect->canGetHurt($damager)) return;
                if ($this->haveClan($damager) && $this->haveClan($entity) && $this->getProfile($damager)->get("clan") === $this->getProfile($entity)->get("clan")) {
                    $event->setCancelled();
                    $damager->sendMessage("⨶§l§c Tù Nhân §e" . $entity->getName() . "§c Cùng Clan Với Bạn!");
                    return;
                } elseif ($entity->getHealth() - $event->getFinalDamage() <= 0) {
                    if ($this->haveClan($entity)) {
                        $this->clanAnnounce("⨶§l§e " . $entity->getName() . " §cBị Tù Nhân§e " . $damager->getName() . ($this->haveClan($damager) ? " §f[§cClan§e " . $this->getClanName($damager) . "§f]§c " : "") .
                            " §cĐánh Chết. Hãy Trả Thù Nào! §e", $this->getClanName($entity));
                    }
                    if ($this->haveClan($damager)) {
                        $point = $this->haveClan($entity) ? 2 : 1;
                        $this->clanAnnounce("⨶§l§e " . $damager->getName() . "§c Đã Giết Chết §e" . $entity->getName() . "§c, Giành §e$point Điểm§c Về Cho Clan!", $this->getClanName($damager));
                        $this->addPoint($point, $this->getClanName($damager));
                    }
                    return;
				}
			}
		}
	}

    protected function paginateText(CommandSender $sender, $pageNumber, array $txt)
    {
        $hdr = array_shift($txt);
        if ($sender instanceof ConsoleCommandSender) {
            $sender->sendMessage(TextFormat::GREEN . $hdr . TextFormat::RESET);
            foreach ($txt as $ln) $sender->sendMessage($ln);
            return true;
        }
        $pageHeight = 5;
        $lineCount = count($txt);
        $pageCount = intval($lineCount / $pageHeight) + ($lineCount % $pageHeight ? 1 : 0);
        $hdr = TextFormat::GREEN . $hdr . TextFormat::RESET;
        if ($pageNumber > $pageCount) {
            $sender->sendMessage($hdr);
            $sender->sendMessage("⨶§l§c Không Có Trang Hướng Dẫn Này!");
            return true;
        }
        $hdr .= TextFormat::RED . " §l§f[§e{$pageNumber}§f/§e{$pageCount}§f]";
        $sender->sendMessage($hdr);
        for ($ln = ($pageNumber - 1) * $pageHeight; $ln < $lineCount && $pageHeight--; ++$ln) {
            $sender->sendMessage($txt[$ln]);
        }
        return true;
    }

    protected function getPageNumber(array &$args)
    {
        $pageNumber = 1;
        if (count($args) && is_numeric($args[count($args) - 1])) {
            $pageNumber = (int)array_pop($args);
            if ($pageNumber <= 0) $pageNumber = 1;
        }
        return $pageNumber;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "clan":
                if (!isset($args[0])) {
                    $sender->sendMessage("⨶§l§c Sử Dụng: /clan help");
                    return true;
                }
                /** @var string[] $altArgs */
                $altArgs = array_slice($args, 1);
                switch ($args[0]) {
                    case "help":
                        $help = [
                            "§r⨶§l§b Hướng Dẫn Sử Dụng Lệnh Clan §r⨶",
                            "§l§c•§b /c §f:§e Trò Chuyện Kênh Clan",
                            "§l§c•§b /clan top §f:§e Xem Xếp Hạng Clan",
                            "§l§c•§b /clan create §f:§e Tạo Một Clan Mới",
                            "§l§c•§b /clan delete §f:§e Xoá Clan Của Bạn",
                            "§l§c•§b /clan join §f:§e Gửi Yêu Cầu Xin Gia Nhập Clan",
                            "§l§c•§b /clan quit §f:§e Thoát Clan Của Bạn",
                            "§l§c•§b /clan accept §f:§e Chấp Nhận Lời Mời Vào Clan",
                            "§l§c•§b /clan decline §f:§e Từ Chối Lời Mời Vào Clan",
                            "§l§c•§b /clan donate §f:§e Cống Hiến Cho Clan",
                            "§l§c•§b /clan lookup §f:§e Xem Tù Nhân Thuộc Clan Nào",
                            "§l§c•§b /clan requestlist §f:§e Xem Danh Sách Các Thành Viên Xin Vào Clan",
                            "§l§c•§b /clan status §f:§e Xem Thông Tin Clan",
                            "§l§c•§b /clan invite §f:§e Mời Thêm Tù Nhân Vào Clan",
                            "§l§c•§b /clan kick §f:§e Đuổi Thành Viên Khỏi Clan",
                            "§l§c•§b /clan motd §f:§e Sửa Khẩu Hiệu Clan",
                            "§l§c•§b /clan setrank §f:§e Chỉnh Chức Vụ cho Thành Viên Trong Clan",
                            "§l§c•§b /clan levelup §f:§e Nâng Cấp Clan",
                        ];
                        $this->paginateText($sender, $this->getPageNumber($args), $help);
                        break;
                    case "create":
                        if ($sender instanceof ConsoleCommandSender || $sender instanceof RemoteConsoleCommandSender) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Lệnh Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") !== false) {
                            $sender->sendMessage("⨶§l§c Bạn Đã Có Clan!");
                            return true;
                        }
                        if (count($altArgs) <> 1) {
                            $cost = $this->getConfig()->get("create-cost");
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan create <Tên Clan>\n⨶§l§c Lưu Ý: Tạo Clan Tốn§e $cost Xu!");
                            return true;
                        }

                        if (strpos($altArgs[0], "§")) {
                            $sender->sendMessage("⨶§l§c Không Được Ghi Nàu Trong Tên Clan!");
                            return true;
                        }

                        if (strlen($altArgs[0]) > 20) {
                            $sender->sendMessage("⨶§l§c Tên Clan Phải Phải Ngắn Hơn 20 Kí Tự!");
                            return true;
                        }

                        if (isset($this->clans[$altArgs[0]])) {
                            $sender->sendMessage("⨶§l§c Clan Đã Tồn Tại!");
                            return true;
                        }

                        $eco = EconomyAPI::getInstance();
                        $money = $eco->myMoney($sender);
                        $cost = $this->getConfig()->get("create-cost");
                        if ($money < $cost) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Đủ Tiền Tạo Clan\n§r⨶§l§f [§cPhí Tạo Clan:§e $cost Xu§c, Bạn Có:§e $money Xu§f]");
                            return true;
                        }

                        $eco->reduceMoney($sender, $cost);

                        $this->clans[$altArgs[0]] = [
                            "motd" => $altArgs[0],
                            "level" => 1,
                            "point" => 0,
                            "members" => [
                                strtolower($sender->getName()) => self::OWNER_RANK // see config.yml
                            ],
                            "request" => []
                        ];

                        $profile->set("clan", $altArgs[0]);
                        $profile->set("rank", self::OWNER_RANK);
                        $profile->save();

                        $this->save();
                        $sender->sendMessage("⨶§l§c Tạo Clan Thành Công!");
                        break;
                    case "invite":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "accept":
                                if (isset($this->invitePending[$sender->getName()]) && $this->invitePending[$sender->getName()][0] > time()) {
                                    $inviter = $this->getServer()->getPlayerExact($this->invitePending[$sender->getName()][2]);
                                    if ($inviter !== null) {
                                        $inviter->sendMessage("⨶§l§e " . $sender->getName() . "§c Đã Chấp Nhận Lời Mời Vào Clan Của Bạn!");
                                    }

                                    $this->addPlayerToClan($sender, $this->invitePending[$sender->getName()][1]);

                                    unset($this->invitePending[$sender->getName()]);
                                    return true;
                                } else {
                                    $sender->sendMessage("⨶§l§c Lời Mời Không Tồn Tại!");
                                    return true;
                                }
                                break;
                            case "decline":
                                if (!isset($this->invitePending[$sender->getName()])) {
                                    $sender->sendMessage("⨶§l§c Bạn Không Được Bất Cứ Ai Mời!");
                                    return true;
                                } else {
                                    $inviter = $this->getServer()->getPlayerExact($this->invitePending[$sender->getName()][2]);
                                    if ($inviter !== null) {
                                        $inviter->sendMessage("⨶§l§e " . $sender->getName() . "§c Đã Từ Chối Lời Mời Vào Clan Của Bạn!");
                                    }
                                    unset($this->invitePending[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Đã Từ Chối Lời Mời!");
                                    return true;
                                }
                                break;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || !$this->canDo($sender, self::ACTION_INVITE)) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Mời Tù Nhân Khác Vào Clan!");
                            return true;
                        }
                        if ($this->isClanFull($profile->get("clan"))) {
                            $sender->sendMessage("⨶§l§c Thành Viên Clan Đã Đầy!");
                            return true;
                        }
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "yes":
                                if (isset($this->inviteSendConfirm[$sender->getName()]) && $this->inviteSendConfirm[$sender->getName()][0] > time()) {
                                    $player = $this->getServer()->getPlayerExact($this->inviteSendConfirm[$sender->getName()][1]);
                                    $this->invitePending[$player->getName()] = [time() + 50, $profile->get("clan"), $sender->getName()];
                                    $sender->sendMessage("⨶§l§c Lời Mời Đã Được Gửi!");
                                    $player->sendMessage("⨶§l§e " . $sender->getName() . "§c Đã Gửi Cho Bạn Một Lời Mời Vào Clan§e '" . $profile->get("clan") . "'!");
                                    $player->sendMessage("⨶§l§c Bấm §e/clan invite accept§c Để Đồng Ý Hoặc §edecline §cĐể Từ Chối.");
                                    $player->sendMessage("⨶§l§c Lời Mời Sẽ Tự Động Hủy Sau 50 Giây!");
                                    return true;
                                } else {
                                    $sender->sendMessage("⨶§l§c Lời Mời Không Tồn Tại!");
                                    return true;
                                }
                                break;
                            case "no":
                                if (!isset($this->inviteSendConfirm[$sender->getName()])) {
                                    $sender->sendMessage("⨶§l§c Bạn Không Mời Bất Kì Ai!");
                                    return true;
                                } else {
                                    unset($this->inviteSendConfirm[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Đã Hủy Lời Mời!");
                                    return true;
                                }
                                break;
                        }
                        if (count($altArgs) <> 1) {
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan invite <Tên Tù Nhân>");
                            return true;
                        }
                        $player = $this->getServer()->getPlayer($altArgs[0]);
                        if ($player === null || $player->getName() === $sender->getName()) { // TODO this. is. bad.
                            $sender->sendMessage("⨶§l§c Không Tìm Thấy Tù Nhân Nào Với Từ Khóa§e '" . $altArgs[0] . "'");
                            return true;
                        } else if ($player->getName() === $altArgs[0] && $this->getProfile($player)->get("clan") !== false) {
                            $sender->sendMessage("⨶§l§c Tù Nhân§e " . $altArgs[0] . "§c Đã Có Clan Rồi!");
                            return true;
                        }
                        if (strtolower($altArgs[0]) <> strtolower($player->getName())) {
                            $this->inviteSendConfirm[$sender->getName()] = [time() + 50, $player->getName()];
                            $sender->sendMessage("⨶§l§c Hệ Thống Không Tìm Thấy Tù Nhân §e'" . $altArgs[0] . "'.\n⨶§l§c Có Phải Bạn Muốn Mời§e '" . $player->getName() . "'§c? Gõ§e /clan invite <yes/no>§c Để Đồng Ý Hoặc Từ Chối.");
                            $sender->sendMessage("⨶§l§c Lời Mời Sẽ Tự Động Hủy Sau 50 Giây!");
                            return true;
                        }
                        $this->invitePending[$player->getName()] = [time() + 50, $profile->get("clan"), $sender->getName()];
                        $sender->sendMessage("⨶§l§c Lời Mời Đã Được Gửi!");
                        $player->sendMessage("⨶§l§e " . $sender->getName() . " §cĐã Gửi Cho Bạn Một Lời Lời Vào Clan§e '" . $profile->get("clan") . "'.");
                        $player->sendMessage("⨶§l§c Gõ §e/clan invite accept§c Để Đồng Ý Hoặc§e decline §cĐể Từ Chuối.");
                        $player->sendMessage("⨶§l§c Lời Mời Sẽ Tự Động Hủy Sau 50 Giây!");
                        break;
                    case "my":
                    case "info":
                    case "status":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false) {
                            $sender->sendMessage("⨶§l§c Bạn Không Ở Trong Clan Nào!");
                            return true;
                        }
                        $clan = $this->clans[$profile->get("clan")];
                        $mes = "⨞§l§c §eClan§e " . $clan["motd"] . " §f[§e" . $profile->get("clan") . "§f]§c:\n";
                        $mes .= "§e⨞§l§c Cấp Clan: §e" . $clan["level"] . "\n";
                        $mes .= "⨞§l§c Điểm Clan: §e" . $clan["point"] . "\n";
                        $mes .= "⨞§l§c Chức Vụ Của Bạn: §e" . $this->getRankName($profile->get("rank")) . "\n";
                        $mes .= "⨞§l§c Thành Viên:§e\n";
                        foreach ($clan["members"] as $name => $rank) {
                            $mes .= "  $name: " . $this->getRankName($rank) . "§c, ";
                        }
                        $sender->sendMessage($mes);
                        break;
                    case "kick":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || !$this->canDo($sender, self::ACTION_KICK)) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Đuổi Thành Viên Khỏi Clan!");
                            return true;
                        }
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "yes":
                                if (isset($this->kickPending[$sender->getName()]) && $this->kickPending[$sender->getName()][0] > time()) {
                                    $pName = $this->kickPending[$sender->getName()][2];
                                    $targetProfile = $this->getProfile($pName);
                                    if ($targetProfile->get("clan") !== $profile->get("clan")) {
                                        $sender->sendMessage("⨶§l§c Người Này Không Oử Trong Clan Của Bạn!");
                                        unset($this->kickPending[$sender->getName()]);
                                        $sender->sendMessage("⨶§l§c Đã Hủy Yêu Cầu Đuổi Thành Viên!");
                                        return true;
                                    }
                                    $result = $this->removePlayerFromClan($pName);
                                    unset($this->kickPending[$sender->getName()]);
                                    $sender->sendMessage($result ? "⨶§l§c Đã Đuổi Tù Nhân. " : "Có Lỗi Xảy Ra Trong Quá Trình Đuổi.");
                                    return true;
                                } else {
                                    $sender->sendMessage("⨶§l§c Yêu Cầu Không Tồn Tại!");
                                    return true;
                                }
                                break;
                            case "no":
                                if (!isset($this->kickPending[$sender->getName()])) {
                                    $sender->sendMessage("⨶§l§c Bạn Không Đuổi Bất Cứ Ai!");
                                    return true;
                                } else {
                                    unset($this->kickPending[$sender->getName()]);
                                    $sender->sendMessage("⨞§l§c Đã Hủy Yêu Cầu!");
                                    return true;
                                }
                                break;
                        }
                        if (count($altArgs) <> 1) {
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan kick <Tên Tù Nhân>");
                            return true;
                        }
                        //$clan = $this->getClan($profile->get("clan"));
                        $needle = $altArgs[0];
                        $found = $this->getPlayerInClan($needle, $this->getClanName($sender), $sender->getName());
                        if ($found === null) {
                            $sender->sendMessage("⨶§l§c Không Tìm Thấy Tù Nhân Nào Với Từ Khóa§e '$needle' §cTrong Clan Của Bạn!.");
                            return true;
                        }
                        $this->kickPending[$sender->getName()] = [time() + 50, $profile->get("clan"), $found];
                        $sender->sendMessage("⨶§l§c Bạn Có Chắc Chắn Muốn Đuổi§e '$found' §cKhỏi Clan?");
                        $sender->sendMessage("⨶§l§c Gõ:§e /clan kick yes §cĐể Xác Nhận!");
                        $sender->sendMessage("⨶§l§c Yêu Cầu Sẽ Tự Động Hủy Sau 50 Giây!");
                        break;
                    case "requestlist":
                    case "rl":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || !$this->canDo($sender, self::ACTION_REQUEST_CONTROL)) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Chấp Nhận/Từ Chối Yêu Cầu Vào Clan.");
                            return true;
                        }
                        $clan =& $this->clans[$profile->get("clan")];
                        if (isset($altArgs[0])) {
                            //in_array($altArgs[0], $clan["request"])
                            if (($found = $this->getPlayerInRequestList($altArgs[0], $profile->get("clan"))) !== null) {
                                if ($this->isClanFull($profile->get("clan"))) {
                                    $sender->sendMessage("⨶§l§c Thành Viên Clan Đã Đầy!");
                                    return true;
                                }
                                $this->addPlayerToClan($found, $profile->get("clan"));
                                unset($clan["request"][array_search($found, $clan["request"])]);
                                $this->save();
                                $sender->sendMessage("⨶§l§c Đã Chấp Nhận Yêu Cầu Này!");
                            } else {
                                $sender->sendMessage("⨶§l§c Không Tìm Thấy Tù Nhân Này!");
                            }
                            return true;
                        }
                        $mes = "⨶§l§c Danh Sách Yêu Cầu Vào Clan:\n";
                        foreach ($clan["request"] as $name) {
                            $mes .= "§e{$name}§c, ";
                        }
                        $mes .= "\n⨞§l§a Gõ§e /clan requestlist <Tên Tù Nhân>§a Để Chấp Nhận Tù Nhân Vào Clan.";
                        $sender->sendMessage($mes);
                        break;
                    case "delete":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || $profile->get("rank") !== self::OWNER_RANK) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Xóa Clan!");
                            return true;
                        }
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "yes":
                                if (isset($this->clanDeletePending[$sender->getName()]) && $this->clanDeletePending[$sender->getName()][0] > time()) {
                                    $this->clanAnnounce("⨶§l§c Clan Đã Bị Giải Tán Bởi Chủ Clan!", $profile->get("clan"));
                                    foreach ($this->clans[$profile->get("clan")]["members"] as $name => $rank) {
                                        $this->removePlayerFromClan($name);
                                    }
                                    unset($this->clans[$profile->get("clan")]);
                                    $this->save();
                                    unset($this->clanDeletePending[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Xóa Clan Thành Công!");
                                    return true;
                                } else {
                                    $sender->sendMessage("⨶§l§c Yêu Cầu Không Tồn Tại!");
                                    return true;
                                }
                                break;
                            case "no":
                                if (!isset($this->clanDeletePending[$sender->getName()])) {
                                    $sender->sendMessage("⨶§l§c Yêu Cầu Không Tồn Tại!");
                                    return true;
                                } else {
                                    unset($this->clanDeletePending[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Đã Hủy Yêu Cầu!");
                                    return true;
                                }
                                break;
                        }
                        $this->clanDeletePending[$sender->getName()] = [time() + 50];
                        $sender->sendMessage("⨶§l§c Bạn Đang Xóa Clan§e " . $profile->get("clan") . ".");
                        $sender->sendMessage("⨶§l§c Gõ§e /clan delete yes §cĐể Xác Nhận!");

                        break;
                    case "quit":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Gia Nhập Clan Nào");
                            return true;
                        }
                        if ($profile->get("rank") === self::OWNER_RANK) {
                            $sender->sendMessage("⨶§l§c Bạn Không Thể Thoát Clan Khi Là Chủ Clan Hãy Chuyển Nhượng Chức Vụ Trước Hoặc Giải Tán Clan!");
                            return true;
                        }
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "yes":
                                if (isset($this->quitPending[$sender->getName()]) && $this->quitPending[$sender->getName()][0] > time()) {
                                    $result = $this->removePlayerFromClan($sender);
                                    unset($this->quitPending[$sender->getName()]);
                                    $sender->sendMessage($result ? "⨶§l§c Thoát Clan Thành Công." : "Có Lỗi Xảy Ra Trong Quá Trình Thoát Clan.");
                                    return true;
                                } else {
                                    $sender->sendMessage("⨶§l§c Yêu Cầu Không Tồn Tại!");
                                    return true;
                                }
                                break;
                            case "no":
                                if (!isset($this->quitPending[$sender->getName()])) {
                                    return true;
                                } else {
                                    unset($this->quitPending[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Đã Hủy Yêu Cầu!");
                                    return true;
                                }
                                break;
                        }
                        $this->quitPending[$sender->getName()] = [time() + 50];
                        $sender->sendMessage("⨶§l§c Bạn Có Chắc Chắn Muốn Rời Clan§e " . $profile->get("clan") . "§c?");
                        $sender->sendMessage("⨶§l§c Gõ§e /clan quit yes §cĐể Xác Nhận!");
                        break;
                    case "admin":
                        // TODO
                        break;
                    case "motd":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || !$this->canDo($sender, self::ACTION_SET_MOTD)) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Chỉnh Sửa Khẩu Hiệu Clan!");
                            return true;
                        }
                        if (!isset($altArgs[0])) {
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan motd <Khẩu Hiệu>");
                            return true;
                        }
                        $this->clans[$profile->get("clan")]["motd"] = $altArgs[0];
                        $this->save();
                        $sender->sendMessage("⨶§l§c Đã Chỉnh Sửa Khẩu Hiệu Clan!");
                        break;
                    case "welcome":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Ở Trong Clan Nào!");
                            return true;
                        }
                        if (isset($this->welcomePending[$profile->get("clan")])) {
                            foreach ($this->welcomePending[$profile->get("clan")] as $key => $data) {
                                if (!in_array($sender->getName(), $data[1])) {
                                    $this->clanChat($sender, "⨶§l§c Chào Mừng§e " . $data[0] . "§c Đã Tham Gia Clan!");
                                    $this->welcomePending[$profile->get("clan")][$key][1][] = $sender->getName();
                                }
                            }
                        }
                        break;
                    case "setrank":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || !$this->canDo($sender, self::ACTION_SET_RANK)) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Chỉnh Sửa Cấp Bậc Thành Viên Clan!");
                            return true;
                        }
                        if (!isset($altArgs[0])) {
                            $sender->sendMessage("⨶§l§c /clan setrank <Tên Thành Viên> <Cấp Bậc>"); // TODO Rank Name
                            $mes = "⨶§l§c Danh Sách Cấp Bậc:§e \n";
                            foreach ($this->getConfig()->get("rank") as $rank => $info) {
                                $mes .= "$rank §f-§e " . $info["name"] . "\n";
                            }
                            $sender->sendMessage($mes);
                            return true;
                        }
                        //$clan =& $this->clans[$profile->get("clan")];
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "yes":
                                if (isset($this->clanPromotePending[$sender->getName()]) && $this->clanPromotePending[$sender->getName()][0] > time()) {
                                    $newOwner = $this->clanPromotePending[$sender->getName()][1];
                                    $this->setRank($newOwner, self::OWNER_RANK);
                                    $this->setRank($sender, 1);
                                    unset($this->clanPromotePending[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Bạn Đã Chuyển Nhượng Clan Cho:§e $newOwner");
                                    $this->clanAnnounce("⨶§l§c Clan Đã Được Chuyển Nhượng Toàn Bộ Quyền Sở Hữu Cho:§e {$newOwner}§c, Hãy Chúc Mừng!", $profile->get("clan"));
                                    return true;
                                } else {
                                    $sender->sendMessage("⨶§l§c Yêu Cầu Không Tồn Tại!");
                                    return true;
                                }
                                break;
                            case "no":
                                if (!isset($this->clanPromotePending[$sender->getName()])) {
                                    return true;
                                } else {
                                    unset($this->clanPromotePending[$sender->getName()]);
                                    $sender->sendMessage("⨶§l§c Đã Hủy Yêu Cầu!");
                                    return true;
                                }
                                break;
                        }
                        if (isset($altArgs[0])) {
                            if (isset($altArgs[1])) {
                                $altArgs[1] = (int)$altArgs[1];
                                if (($found = $this->getPlayerInClan($altArgs[0], $profile->get("clan"), $sender->getName())) === null) {
                                    $sender->sendMessage("⨶§l§c Tù Nhân Không Có Trong Clan");
                                    return true;
                                }
                                $targetProfile = $this->getProfile($found);
                                if ($targetProfile->get("rank") >= $profile->get("rank")) {
                                    $sender->sendMessage("⨶§l§c Người Này Có Chức Vụ Cao Hơn Hoặc Ngang Bằng Bạn!");
                                    return true;
                                }
                                if ((string)((int)$altArgs[1]) !== (string)$altArgs[1]) {
                                    $sender->sendMessage("⨶§l§c Cấp Bậc Phải Là 1 Số");
                                    return true;
                                }
                                $altArgs[1] = (int)$altArgs[1];
                                if ($altArgs[1] >= $profile->get("rank") && $profile->get("rank") !== self::OWNER_RANK) {
                                    $sender->sendMessage("⨶§l§c Bạn Không Thể Đặt Chức Vụ Người Khác Cao Hơn Hoặc Ngang Bằng Bạn!");
                                    return true;
                                } else if ($altArgs[1] === $targetProfile->get("rank")) {
                                    $sender->sendMessage("⨶§l§c Người Này Đang Ở Chức Rồi!");
                                    return true;
                                }
                                if ($altArgs[1] === self::OWNER_RANK) {
                                    if ($profile->get("rank") !== self::OWNER_RANK) {
                                        $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Chuyển Nhượng Quyền Sở Hữu Clan!");
                                        return true;
                                    }
                                    $this->clanPromotePending[$sender->getName()] = [time() + 50, $found];
                                    $sender->sendMessage("⨶§l§c Bạn có§e CHẮC CHẮN§c Muốn Chuyển Quyền Sở Hữu Clan Cho:§e $found?");
                                    $sender->sendMessage("⨶§l§c Bạn Sẽ Không Còn Quyền Sở Hữu Clan Này Và Trở Về Cấp Bậc:§e " . $this->getRankName(1) . ".");
                                    $sender->sendMessage("⨶§l§c Gõ§e /clan setrank yes §cĐể Xác Nhận. Yêu Cầu Chuyển Nhượng Sẽ Bị Hủy Sau 50 Giây!");
                                    return true;
                                }
                                if (!isset($this->getConfig()->get("rank")[$altArgs[1]])) {
                                    $sender->sendMessage("⨶§l§c Không Có Cấp Bậc Này!");
                                    return true;
                                }
                                $promote = $altArgs[1] > $targetProfile->get("rank");
                                $this->setRank($found, $altArgs[1]);
                                $sender->sendMessage("⨶§l§c Đặt Chức Vụ Thành Công!");
                                $this->clanAnnounce("⨶§l§e " . $found . "§c Đã Được " . ($promote ? "Nâng" : "Hạ") . " Chức§e " . $this->getRankName($altArgs[1]) . " §cBởi§e " . $sender->getName(), $profile->get("clan"));
                            }
                        }
                        break;
                    case "lookup":
                        if (count($altArgs) <> 1) {
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan lookup <Tên Tù Nhân>");
                            return true;
                        }
                        $sender->sendMessage("⨶§l§c Tù Nhân " . $altArgs[0] . " " . ($this->haveClan($altArgs[0]) ? "Thuộc Clan " . $this->getClanName($altArgs[0]) : "Không Thuộc Clan Nào."));
                        break;
                    case "reload":
                        if (!($sender->isOp())) return true;
                        $this->saveDefaultConfig();
                        $this->reloadConfig();
                        $sender->sendMessage("⨶§l§c Đã Tải Lại§e config.yml");
                        break;
                    case "top":
                        $mes = "§e❖§l§c Xếp Hạng §e10 Clan§c Xuất Sắc Nhất Nhà Tù §eMine§aSuon §e❖\n";
                        for ($i = 0; $i < 10; $i++) {
                            $mes .= "§r⨶§l§b Xếp Hạng§e " . ($i + 1) . " §f:§a " . (isset($this->topClan[$i]) ? $this->topClan[$i] : "§cChưa cập nhật!") . "\n";
                        }
                        if ($sender instanceof Player && $this->haveClan($sender)) {
                            $mes .= "§r⨞§l§a Clan Bạn Đang Xếp Thứ: §e" . (isset(array_flip($this->topClan)[$this->getClanName($sender)]) ? array_flip($this->topClan)[$this->getClanName($sender)] + 1 : "Chưa Cập Nhật!") . "\n";
                        }
                        $mes .= "§r⨞§l§a Xếp Hạng Clan Sẽ Được Cập Nhật Mỗi:§e 30 Phút";
                        $sender->sendMessage($mes);
                        break;
                    case "levelup":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false || !$this->canDo($sender, self::ACTION_LEVEL_UP)) {
                            $sender->sendMessage("⨶§l§c Bạn Không Có Quyền Nâng Cấp Clan!");
                            return true;
                        }
                        $clan = $this->getClan($profile->get("clan"));
                        $nextLevel = $clan["level"] + 1;
                        if ($nextLevel > $this->getConfig()->get("max-clan-level")) {
                            $sender->sendMessage("⨶§l§c Clan Của Bạn Đã Đạt Cấp Tối Đa!");
                            return true;
                        }
                        $requiredPoint = $this->getSetting(self::SETTING_REQUIRED_POINT, $nextLevel);
                        $requiredMoney = $this->getSetting(self::SETTING_REQUIRED_MONEY, $nextLevel);
                        $eco = EconomyAPI::getInstance();
                        if (isset($altArgs[0]) && $altArgs[0] === "up") {
                            if ($clan["point"] < $requiredPoint) {
                                $sender->sendMessage("⨶§l§c Clan Của Bạn Không Có Đủ Điểm Để Nâng Cấp §f[§cCần:§e " . $requiredPoint . "§f]");
                                return true;
                            }
                            if ($eco->myMoney($sender) < $requiredMoney) {
                                $sender->sendMessage("⨶§l§c Bạn Không Đủ Tiền Để Nâng Cấp Clan §f[§cCần:§e " . $eco->getMonetaryUnit() . $requiredMoney . "§f]");
                                return true;
                            }
                            $this->clans[$profile->get("clan")]["point"] -= $requiredPoint;
                            $eco->reduceMoney($sender, $requiredMoney);
                            $this->clans[$profile->get("clan")]["level"] = $nextLevel;
                            $this->save();
                            //$eco->save();
                            $sender->sendMessage("⨶§l§c Nâng Cấp Clan Thành Công!");
                            $this->clanAnnounce("⨶§l§c Clan Đã Được Nâng Cấp Lên Cấp§e $nextLevel.§c Xin Chúc Mừng!", $profile->get("clan"));
                            return true;
                        }
                        $sender->sendMessage("⨶§l§c Để Nâng Cấp Clan Lên Cấp§e $nextLevel §cCần Tiêu Hao§e $requiredPoint Điểm §cClan Và§e " . $eco->getMonetaryUnit() . "$requiredMoney.");
                        $sender->sendMessage("⨶§l§c Điểm Clan Có Thể Kiếm Được Bằng Cách Giết Thành Viên Clan Khác(+2) Hoặc Tù Nhân Bình Thường(+1)");
                        $sender->sendMessage("⨶§l§c Gõ §e/clan§c levelup up Để Xác Nhận Nâng Cấp Clan!");
                        break;
                    case "join":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") !== false) {
                            $sender->sendMessage("⨶§l§c Bạn Đã Có Clan!");
                            return true;
                        }
                        if (isset($altArgs[0])) switch ($altArgs[0]) {
                            case "cancel":
                                if (!isset($altArgs[1])) {
                                    $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan join cancel <Tên Clan>");
                                    return true;
                                }
                                if (!isset($this->clans[$altArgs[1]])) {
                                    $sender->sendMessage("⨶§l§c Clan Không Tồn Tại!");
                                    return true;
                                }
                                $clan =& $this->clans[$altArgs[1]];
                                if (!in_array($sender->getName(), $clan["request"])) {
                                    $sender->sendMessage("⨶§l§c Bạn Chưa Yêu Cầu Vào Clan Này!");
                                    return true;
                                }
                                unset($clan["request"][array_search($sender->getName(), $clan["request"])]);
                                $this->save();
                                $sender->sendMessage("⨶§l§c Hùy Yêu Câu fThành Công!");
                                break;
                        }
                        if (!isset($altArgs[0])) {
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan join <Tên Clan>");
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan join cancel <Tên Clan>");
                            return true;
                        }
                        if (!isset($this->clans[$altArgs[0]])) {
                            $sender->sendMessage("⨶§l§c Clan §e" . $altArgs[0] . "§c Không Tồn Tại.");
                            return true;
                        }
                        if (in_array(strtolower($sender->getName()), $this->clans[$altArgs[0]]["request"])) {
                            $sender->sendMessage("⨶§l§c Bạn Đã Xin Vào Clan Này Rồi!");
                            return true;
                        }
                        $this->clans[$altArgs[0]]["request"][] = strtolower($sender->getName());
                        $this->save();
                        $sender->sendMessage("⨶§l§c Xin Vào Clan§e " . $altArgs[0] . "§c Thành Công. Bạn Có Thể Hùy Yêu Cầu Xin Vào Clan Bằng Lệnh:§e /clan join cancel");
                        break;
                    case "donate":
                        if (!($sender instanceof Player)) {
                            $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                            return true;
                        }
                        $profile = $this->getProfile($sender);
                        if ($profile->get("clan") === false) {
                            $sender->sendMessage("⨶§l§c Bạn Không Ở Trong Clan Nào!");
                            return true;
                        }
                        $perPointCost = $this->getConfig()->get("point-cost");
                        $eco = EconomyAPI::getInstance();
                        if (!isset($altArgs[0]) || (string)(int)$altArgs[0] !== $altArgs[0]) {
                            $sender->sendMessage("⨶§l§c Sử Dụng:§e /clan donate <Điểm>\n⨶§l§c1 Điểm = §e" . $eco->getMonetaryUnit() . $perPointCost);
                            return true;
                        }
                        $point = (int)$altArgs[0];
                        if ($point <= 0) {
                            $sender->sendMessage("⨶§l§c Điểm Cống Hiến Không Phù Hợp!");
                            return true;
                        }
                        if ($eco->myMoney($sender) - $point * $perPointCost < 0) {
                            $sender->sendMessage("⨶§l§c Bạn Không Đủ Tiền §f[§cCần:§e " . ($point * $perPointCost) . "§c, Bạn Có:§e " . $eco->myMoney($sender) . "§f]");
                            return true;
                        }
                        $this->addPoint($point, $this->getClanName($sender));
                        $eco->reduceMoney($sender, $point * $perPointCost);
                        $this->save();
                        $sender->sendMessage("⨶§l§c Đã Đóng Góp§e $point Điểm!");
                        $this->clanAnnounce("⨶§l§e " . $sender->getName() . "§c Đã Đóng Góp§e $point Điểm.", $this->getClanName($sender));
                        break;
                    default:
                        $sender->sendMessage("⨶§l§c Không Tìm Thấy Lệnh. Gõ §e/clan help §cĐể Xem Chi Tiết.");
                        return true;
                }

                return true;
            case "clanchat":
                if (!($sender instanceof Player)) {
                    $sender->sendMessage("⨶§l§c Vui Lòng Sử Dụng Câu Lệnh Này Trong Trò Chơi!");
                    return true;
                }
                $profile = $this->getProfile($sender);
                if ($profile->get("clan") === false) {
                    $sender->sendMessage("⨶§l§c Bạn Không Ở Trong Clan Nào!");
                    return true;
                }
                if (!isset($args[0])) {
                    $sender->sendMessage("⨶§l§c Sử Dụng:§e /cchat <Tin Nhắn>");
                    return true;
                }
                $this->clanChat($sender, implode(" ", $args));
                return true;
            default:
                return false;
        }
    }

    /**
     * @param Player|string $player
     * @return Config
     */
    public function getProfile($player)
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);
        $dir = $this->getDataFolder() . "/profiles/" . substr($player, 0, 1) . "/";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $cfg = new Config($dir . "$player.yml", Config::YAML, ["clan" => false, "rank" => 0]);
        return $cfg;
    }

    /**
     * @param int $rank
     * @return string
     */
    public function getRankName(int $rank)
    {
        return $this->getConfig()->get("rank")[$rank]["name"];
    }

    public function save()
    {
        $data = new Config($this->getDataFolder() . "clans.yml", Config::YAML);
        $data->setAll($this->clans);
        $data->save();
    }

    /**
     * @param Player|string $player
     * @param int $action
     * @return bool|null
     */
    public function canDo($player, int $action)
    {
        $profile = $this->getProfile($player);
        if ($profile === null) return null;
        if (($actionString = $this->actionToString($action)) === null) return null;
        return $this->getConfig()->get("rank")[$profile->get("rank")]["control"][$actionString];
    }

    public function actionToString(int $action)
    {
        switch ($action) {
            case self::ACTION_INVITE:
                return "invite";
            case self::ACTION_KICK:
                return "kick";
            case self::ACTION_REQUEST_CONTROL:
                return "requestcontrol";
            case self::ACTION_SET_MOTD:
                return "setmotd";
            case self::ACTION_SET_RANK:
                return "setrank";
            case self::ACTION_LEVEL_UP:
                return "levelup";
        }
        return null;
    }

    /**
     * @param Player|string $player
     * @param string $clanName
     * @return bool
     */
    public function addPlayerToClan($player, string $clanName)
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $profile = $this->getProfile($player);
        if (!isset($this->clans[$clanName])) return false;
        $clan =& $this->clans[$clanName];
        if (count($clan["members"]) >= $this->getSetting(self::SETTING_MAX_PLAYERS, $clan["level"])) return false;
        $clan["members"][strtolower($player)] = 1;
        $this->save();
        $profile->set("clan", $clanName);
        $profile->set("rank", 1);
        $profile->save();
        $this->welcomePending[$clanName][] = [$player, []];
        if (($tmp = $this->getServer()->getPlayerExact($player)) !== null) {
            $this->getServer()->getPluginManager()->callEvent(new ClanChangeEvent($tmp, $clanName));
        }
        $this->clanAnnounce("⨶§l§e ". $player . "§c Đã Vào Clan! Gõ §e/clan welcome§c Để Chào Mừng!", $clanName);
        return true;
    }

    /**
     * @param Player|string $player
     * @return bool
     */
    public function removePlayerFromClan($player)
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $profile = $this->getProfile($player);
        $clanName = $profile->get("clan");
        try {
            unset($this->clans[$clanName]["members"][strtolower($player)]);
        } catch (\Exception $exception) {
            $this->getLogger()->error("⨶§l§c Tên Clan Không Hợp Lệ!");
            return false;
        }
        $this->save();
        $profile->set("clan", false);
        $profile->set("rank", 0);
        $profile->save();
        if (($tmp = $this->getServer()->getPlayerExact($player)) !== null) {
            $this->getServer()->getPluginManager()->callEvent(new ClanChangeEvent($tmp, $clanName));
            $tmp->sendMessage("⨶§l§c Bạn Đã Bị Đuổi Khỏi Clan!");
        }
        $this->clanAnnounce("⨶§l§e " . $player . "§c Đã Rời Clan.", $clanName);
        return true;
    }

    public function clanAnnounce(string $message, string $clanName)
    {
        $clan = $this->clans[$clanName];
        foreach ($clan["members"] as $name => $rank) {
            $member = $this->getServer()->getPlayerExact($name);
            if (!($member instanceof Player)) continue;
            //$member->sendMessage("⨶§l§c " . $message);
            $member->sendMessage($message);
        }
    }

    public function clanChat(Player $player, string $message)
    {
        $profile = $this->getProfile($player);
        $clan = $this->clans[$profile->get("clan")];

        /** @var PureChat $pureChat */
        $pureChat = $this->getServer()->getPluginManager()->getPlugin("PureChat");
        if ($pureChat === null) return;
        $format = $this->getConfig()->get("clan-chat-format");
        $format .= $message;
        $mes = $pureChat->applyPCTags($format,$player,"",null);
        foreach ($clan["members"] as $name => $rank) {
            $member = $this->getServer()->getPlayerExact($name);
            if (!($member instanceof Player)) continue;
            $member->sendMessage($mes);
        }
    }

    public function setRank($player, int $rank)
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $profile = $this->getProfile($player);
        $clan =& $this->clans[$profile->get("clan")];
        $clan["members"][strtolower($player)] = $rank;
        $this->save();
        $profile->set("rank", $rank);
        $profile->save();
    }

    public function haveClan($player)
    {
        if ($player instanceof Player) $player = $player->getName();
        $profile = $this->getProfile($player);
        return $profile->get("clan") !== false;
    }

    public function getClanName($player)
    {
        $profile = $this->getProfile($player);
        return $profile->get("clan");
    }

    public function getClanTag($player)
    {
        /** @var string $style */
        $style = $this->getSetting(self::SETTING_CLAN_TAG, $this->getClan($this->getClanName($player))["level"]);
        $style = str_replace("%s", $this->getClanName($player), $style);
        return $style;
    }

    public function getClan(string $clanName)
    {
        return $this->clans[$clanName];
    }

    public function isClanFull(string $clanName)
    {
        $clan = $this->getClan($clanName);
        return count($clan["members"]) >= $this->getSetting(self::SETTING_MAX_PLAYERS, $clan["level"]);
    }

    public function getSetting(int $settingId, int $clanLevel)
    {
        switch ($settingId) {
            case self::SETTING_MAX_PLAYERS:
                $key = "max_players";
                break;
            case self::SETTING_REQUIRED_POINT:
                $key = "required_points";
                break;
            case self::SETTING_REQUIRED_MONEY:
                $key = "required_money";
                break;
            case self::SETTING_CLAN_TAG:
                $key = "clan_tag";
                break;
            default:
                return null;
        }
        return $this->settings[$key][$clanLevel - 1];
    }

    public function updateTopClan()
    {
        $topClan = array_keys($this->clans);
        for ($i = 0; $i <= count($topClan) - 2; $i++) {
            for ($j = $i + 1; $j <= count($topClan) - 1; $j++) {
                if ($this->clans[$topClan[$j]]["level"] > $this->clans[$topClan[$i]]["level"] ||
                    ($this->clans[$topClan[$j]]["level"] === $this->clans[$topClan[$i]]["level"] && $this->clans[$topClan[$j]]["point"] > $this->clans[$topClan[$i]]["point"])
                ) {
                    $t = $topClan[$j];
                    $topClan[$j] = $topClan[$i];
                    $topClan[$i] = $t;
                }
            }
        }
        $this->topClan = $topClan;
        $this->getServer()->broadcastMessage("⨶§l§c Xếp Hạng Clan Đã Được Cập Nhật!");
    }

    public function addPoint(int $point, string $clanName)
    {
        $clan =& $this->clans[$clanName];
        $clan["point"] += $point;
    }

    /**
     * @param string $needle
     * @param string $clanName
     * @param string $except
     * @return null|string
     */
    public function getPlayerInClan(string $needle, string $clanName, string $except = null)
    {
        $clan = $this->getClan($clanName);
        $found = null;
        $needle = strtolower($needle);
        $delta = PHP_INT_MAX;
        foreach ($clan["members"] as $name => $rank) {
            if ($except !== null) {
                if ($name === $except) continue;
            }
            if (stripos($name, $needle) === 0) {
                $curDelta = strlen($name) - strlen($needle);
                if ($curDelta < $delta) {
                    $found = $name;
                    $delta = $curDelta;
                }
                if ($curDelta === 0) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @param string $needle
     * @param string $clanName
     * @param string $except
     * @return null|string
     */
    public function getPlayerInRequestList(string $needle, string $clanName, string $except = null)
    {
        $clan = $this->getClan($clanName);
        $found = null;
        $needle = strtolower($needle);
        $delta = PHP_INT_MAX;
        foreach ($clan["request"] as $name) {
            if ($except !== null) {
                if ($name === $except) continue;
            }
            if (stripos($name, $needle) === 0) {
                $curDelta = strlen($name) - strlen($needle);
                if ($curDelta < $delta) {
                    $found = $name;
                    $delta = $curDelta;
                }
                if ($curDelta === 0) {
                    break;
                }
            }
        }

        return $found;
    }

}
