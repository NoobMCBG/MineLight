<?php

namespace NoobMCBG\AutoLight;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use NoobMCBG\AutoLight\Main as AutoLight;

class RankTime extends Task {

    public function __construct(AutoLight $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currenttick){
        if(count($this->getPlugin()->ranktime->getAll()) >= 1){
            foreach($this->getPlugin()->ranktime->getAll() as $player => $ranktime){
                if($ranktime == 0){
                    $all = $this->getPlugin()->ranktime->getAll();
                    unset($all[$player]);
                    $this->getPlugin()->ranktime->setAll($all);
                    if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                        $this->getPlugin()->pp->setGroup($player, $this->getPlugin()->pp->getDefaultGroup());
                        $this->getPlugin()->getServer()->getPlayer($player)->sendMessage("§l§c•§e Thời Gian Ranks Của Bạn Đã Hết !");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                }
                $this->getPlugin()->ranktime->set($player, $ranktime -1);
            }
            $this->getPlugin()->ranktime->save();
        }
    }
}