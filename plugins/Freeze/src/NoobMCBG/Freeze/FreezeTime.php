<?php

namespace NoobMCBG\Freeze;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use NoobMCBG\Freeze\Main as Freeze;

class FreezeTime extends Task{

    public function __construct(Freeze $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currenttick){
        if(count($this->getPlugin()->freeze->getAll()) >= 1){
            foreach($this->getPlugin()->freeze->getAll() as $player => $freeze){
                if($freeze == 0){
                    $all = $this->getPlugin()->freeze->getAll();
                    unset($all[$player]);
                    $this->getPlugin()->freeze->setAll($all);
                    if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                        $this->getPlugin()->getServer()->getPlayer($player)->sendMessage("§l§c•§e Bạn Đã Được Bỏ§a Freeze !");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                }
                $this->getPlugin()->freeze->set($player, $freeze -1);
            }
            $this->getPlugin()->freeze->save();
        }
    }
}