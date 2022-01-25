<?php

namespace KOB\task;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use KOB\Main as KOB;
class OffKOB extends Task{

    public function __construct(KOB $plugin)
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
                    unset($all[$p]);
                    $this->getPlugin()->time->setAll($all);
                    $this->getPlugin()->config->set($p, 'off');
                    if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                        $this->getPlugin()->getServer()->getPlayer($p)->sendMessage("§l§c•§e Kĩ Năng§a §aKingOfBlock§e Của Bạn Đã Hết Thời Gian Sử Dụng");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                    $this->getPlugin()->getServer()->getPlayer($p)->sendMessage("§l§c•§e Kĩ Năng§a §aKingOfBlock§e§e Của Bạn Còn§b $time Phút");
                }
                $this->getPlugin()->time->set($p, $time -1);
            }
            $this->getPlugin()->time->save();
            $this->getPlugin()->config->save();
        }
    }
}