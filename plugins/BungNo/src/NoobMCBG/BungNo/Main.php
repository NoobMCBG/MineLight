<?php

namespace NoobMCBG\BungNo;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\utils\Config;

class Main extends PB implements L {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("LOL :>");
        $this->saveDefaultConfig();
        $this->cooldown = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new CooldownTask($this), 20);
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "bungno":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("Út in gêm :)))");
			    	return true;
			    }else{
			    	$inv = $sender->getInventory();
                    $item = Item::get(743, 0, 1);
                    $item->setCustomName("§l§c•§e Kiếm Bùng Nổ §c•");
                    $item->setLore(array("§l§c•§e Giữ Hoặc Nhấn Để Tạo Lên Vụ Nổ Xung Quanh Bạn !"));
                    $inv->addItem($item);
                    $sender->sendMessage("§l§c•§e Đã Lấy Vật Phẩm Bùng Nổ");
			    }
			break;
		}
		return true;
	}

	public function onUse(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		$inv = $player->getInventory();
		if($inv->getItemInHand()->getCustomName() == "§l§c•§e Kiếm Bùng Nổ §c•"){
			if($this->cooldown->get(strtolower($player->getName())) <= 0){
				$this->cooldown->set(strtolower($player->getName()), $this->getConfig()->get("cooldown"));
				$this->cooldown->save();
		        $x = $player->getX();
		        $y = $player->getY();
		        $z = $player->getZ();
		        $world = $player->getLevel();
		        $width = $this->getConfig()->get("width");
		        $height = $this->getConfig()->get("height");
		        $pos = new Position($x+$width, $y+$height, $z, $world);
		        $explosive = new Explosion($pos, $this->getConfig()->get("explosive-level"), null);
			    $explosive->explodeB();
                $pos = new Position($x, $y+$height, $z+$width, $world);
		        $explosive = new Explosion($pos, $this->getConfig()->get("explosive-level"), null);
			    $explosive->explodeB();
			    $pos = new Position($x+$width, $y+$height, $z+$width, $world);
		        $explosive = new Explosion($pos, $this->getConfig()->get("explosive-level"), null);
			    $explosive->explodeB();
			    $pos = new Position($x-$width, $y+$height, $z, $world);
		        $explosive = new Explosion($pos, $this->getConfig()->get("explosive-level"), null);
			    $explosive->explodeB();
			    $pos = new Position($x, $y+$height, $z-$width, $world);
		        $explosive = new Explosion($pos, $this->getConfig()->get("explosive-level"), null);
			    $explosive->explodeB();
			    $pos = new Position($x-$width, $y+$height, $z-$width, $world);
		        $explosive = new Explosion($pos, $this->getConfig()->get("explosive-level"), null);
			    $explosive->explodeB();
			}else{
				$player->sendMessage("§l§c•§e Đang Hồi Chiêu, Hãy Chờ Một Lúc");
			}
		}
	}
}