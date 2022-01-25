<?php

namespace M82B;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use M82B\Main as M82B;
class CooldownTask extends Task{

    public function __construct(M82B $plugin)
    {
        $this->plugin = $plugin;
    }
    public function getPlugin(){
    return $this->plugin;
    }
    public function onRun($currenttick){
        if(count($this->getPlugin()->cooldown->getAll()) >= 1){
            foreach($this->getPlugin()->cooldown->getAll() as $p => $cooldown){
                if($cooldown == 2){
                    $all = $this->getPlugin()->cooldown->getAll();
                    unset($all[$p]);
                    $this->getPlugin()->cooldown->setAll($all);
                    if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                        $this->getPlugin()->getServer()->getPlayer($p)->addTitle("§l§eĐang Nhắm Mục Tiêu", "§7Đang Nhắm");
                    }
                }
                if($cooldown == 1){
                    $all = $this->getPlugin()->cooldown->getAll();
                    unset($all[$p]);
                    $this->getPlugin()->cooldown->setAll($all);
                    if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                        $this->getPlugin()->getServer()->getPlayer($p)->addTitle("§l§eChuẩn Bị", "§7Bóp Cò");
                    }
                }
                if($cooldown == 0){
                    $all = $this->getPlugin()->cooldown->getAll();
                    unset($all[$p]);
                    $this->getPlugin()->cooldown->setAll($all);
                    if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                        $this->getPlugin()->getServer()->getPlayer($p)->addTitle("§l§eBùm Chíu", "§7Chết Nè");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                }
                $this->getPlugin()->cooldown->set($p, $cooldown -1);
            }
            $this->getPlugin()->cooldown->save();
        }
    }
}