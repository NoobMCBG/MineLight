<?php

namespace NoobMCBG\BungNo;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use NoobMCBG\BungNo\Main as BungNo;

class CooldownTask extends Task{

    public function __construct(BungNo $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currenttick){
        if(count($this->getPlugin()->cooldown->getAll()) >= 1){
            foreach($this->getPlugin()->cooldown->getAll() as $player => $cooldown){
                if($cooldown == 0){
                    $this->cooldown->set(strtolower($player->getName()), 0);
                    if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                        $this->getPlugin()->getServer()->getPlayer($player)->sendMessage("§l§c• Đã Hồi Chiêu §c•");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($player) !== null){
                    $this->getPlugin()->getServer()->getPlayer($player)->sendPopup("§l§c• Hồi Chiêu Xong Sau§a $cooldown §c•");
                }
                $this->getPlugin()->cooldown->set($player, $cooldown -1);
            }
            $this->getPlugin()->cooldown->save();
            $this->getPlugin()->config->save();
        }
    }
}