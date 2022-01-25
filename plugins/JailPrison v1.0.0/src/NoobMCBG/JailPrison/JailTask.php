<?php

namespace NoobMCBG\JailPrison;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use NoobMCBG\JailPrison\Main as JailPrison;

class JailTask extends Task {

    public function __construct(JailPrison $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currenttick){
        if(count($this->getPlugin()->jailtime->getAll()) >= 1){
            foreach($this->getPlugin()->jailtime->getAll() as $player => $jailtime){
                if($jailtime == 0){
                    $all = $this->getPlugin()->jailtime->getAll();
                    unset($all[$player]);
                    $this->getPlugin()->jailtime->setAll($all);
                    if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                        $this->getPlugin()->getServer()->getPlayer($player)->sendMessage("§l§c•§e Ngày Giam Của Bạn Đã Hết, Bạn Đã Được §aRa Tù !");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                }
                $this->getPlugin()->jailtime->set($player, $jailtime -1);
            }
            $this->getPlugin()->jailtime->save();
        }
    }
}