<?php

namespace PLAuto;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener;
use pocketmine\command\{Command, CommandSender};
use pocketmine\{Player, Server};

use onebone\economyapi\EconomyAPI;
use onebone\pointapi\PointAPI;

use PLAuto\task\OffPLA;

class Main extends PB implements Listener
{

    /** @Var Config */
    private $cfg;
    /** @var Main */
    public static $api;
    public $time;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
	$this->getLogger()->info("\n\n\n\nPlugin Auto Nhận Lương by \n  NoobOfBlind\n  VanhLXYTB\n   NoobMCBG\n\n\n\n");
	$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
	$this->pointapi = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->time = new Config($this->getDataFolder() . "time.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new OffPLA($this), 20 * 60);
        self::$api = $this;
    }

    public static function getAPI(): Main
    {
        return self::$api;
    }
      public function onCommand(CommandSender $s, Command $cmd, string $label, array $args):bool
    {   
        if($cmd->getName() == 'nhanluong'){
            if (!$s->hasPermission("nhanluong.command")) { //Permission cho ọi người
$s->sendMessage("§l§c Bạn không có quyền để nhận lương, vì bạn là Member");
return true;
}else{
            $this->formopen($s);
        }
        return true;
    }
}
    public function formopen(Player $player)
    {
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                case 0:
                    break;
                case 1:
                    $this->config->set(strtolower($player->getName()), "on");
                    $this->config->save();
                    $this->time->set(strtolower($player->getName()), 28800);
                    $this->time->save();
                    $this->Message($player, false, "§e§l●§c Auto Nhận Lương§e Đã §aBật,§e 20 Ngày Sau Hãy Bật Nó Lại");
                    break;
                case 2:
                    $this->config->set(strtolower($player->getName()), "off");
                    $this->config->save();
                    $all = $this->time->getAll();
                    unset($all[strtolower($player->getName())]);
                    $this->time->setAll($all);
                    $this->time->save();
                    $this->Message($player, false, "§e§l●§c Auto Nhận Lương§e đã §cTắt, Bạn Không Thể Nhận Lương Nữa !, Hãy Bật Lại Nhé");
                    break;
            }
        });
        $form->setTitle("§l§2 Nhận Lương");
       $form->addButton("§l§d Thoát\n§7 Nhấn Để Thoát", 0, "textures/blocks/barrier");
        $form->addButton("§l§2Bật Auto Nhận Lương\n§l§7 BẤM ĐỂ BẬT NHẬN LƯƠNG", 0, "textures/other/onoff");
        $form->addButton("§l§cTắt Auto Nhận Lương\n§l§7 BẤM ĐỂ TẮT NHẬN LƯƠNG", 0, "textures/other/onoff");
        $form->sendToPlayer($player);
        return $form;
    }
    private function Message(Player $player, $nextform = false, $msg)
    {
        if ($nextform !== false) {
            $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createSimpleForm(function (Player $player, $data) {
                if ($data == null) return false;
                    switch ($data){
                        case 0:
                            $nextform;
                            break;
                        case 1:
                            break;
                }
            });
            $form->setTitle("§l§2Auto Nhận Lương");
            $form->setContent($msg);
            $form->addButton("Tiếp", 0, "textures/items/arrow");
            $form->addButton("Quay Lại", 0, "textures/other/exit");
            $form->sendToPlayer($player);
            return true;
        }
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createSimpleForm(function (Player $player, $data){
            if($data == null) return false;
            switch ($data){
                case 0:
                    break;
                case 1:
                    break;
            }
        });
       $form->setTitle("§l§2Auto Nhận Lương");
        $form->setContent($msg);
        $form->addButton("Thoát", 0, "textures/other/exit");       
        $form->sendToPlayer($player);   
    }
}