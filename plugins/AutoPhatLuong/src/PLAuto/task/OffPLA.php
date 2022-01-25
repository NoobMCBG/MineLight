<?php

namespace PLAuto\task;

use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\Player;
use pocketmine\Server;
use onebone\economyapi\EconomyAPI;
use onebone\pointapi\PointAPI;
use PLAuto\Main as PLA;
class OffPLA extends Task{

    public function __construct(PLA $plugin)
    {
        $this->plugin = $plugin;
    }
    public function getPlugin(){
    return $this->plugin;
    }
    public function onRun($currenttick){
        if(count($this->getPlugin()->time->getAll()) >= 1){
            foreach($this->getPlugin()->time->getAll() as $p => $time){
                if($time == 0){
                    $all = $this->getPlugin()->time->getAll();
                    $money = $this->getPlugin()->getServer()->getPluginManager()->getPlugin("EconomyAPI");
                    unset($all[$p]);
                    $this->getPlugin()->time->setAll($all);
                    $this->getPlugin()->config->set($p, 'off');
                    if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                        $this->getPlugin()->getServer()->getPlayer($p)->sendMessage("§l§6【§l§cKing§1Night§6】§e Bạn Đã Nhận Được Lương Sau 20 Ngày Online\n§l§e Lương của bạn là §a10000000 VNĐ");
                        $money = 10000000;
                        $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), "givemoney ". $p ." 10000000");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                    $this->getPlugin()->getServer()->getPlayer($p)->sendMessage("§l§6【§cKing§1Night§6】§e Bạn Sẽ Nhận Được Lương Sau§b $time Phút Nữa.");
                }
                $this->getPlugin()->time->set($p, $time -1);
            }
            $this->getPlugin()->time->save();
            $this->getPlugin()->config->save();
        }
    }
}