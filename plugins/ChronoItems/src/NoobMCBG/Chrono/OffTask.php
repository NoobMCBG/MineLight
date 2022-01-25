<?php

namespace NoobMCBG\Chrono;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use NoobMCBG\Chrono\Main as ShieldItem;

class OffTask extends Task{
    
    public function __construct(ShieldItem $plugin){
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
                    $name = $this->getPlugin()->getServer()->getPlayer($p)->getName();
                    unset($all[$p]);
                    $this->getPlugin()->time->setAll($all);
                    $this->getPlugin()->mode->set($p, 'off');
                    if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                        $this->getPlugin()->getServer()->getPlayer($p)->sendMessage("§l§eKhiên Đã hết thời gian sử dụng");
                        $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), "setuperm ".$p." buildertools.command.undo");
		                $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), "rca ".$name." /undo");
                        $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), "unsetuperm ".$p." buildertools.command.hsphere");
                        $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), "unsetuperm ".$p." buildertools.command.undo");
                    }
                    continue;
                }
                if($this->getPlugin()->getServer()->getPlayer($p) !== null){
                    $this->getPlugin()->getServer()->getPlayer($p)->sendPopup("§l§eKhiên của bạn còn§a $time giây.");
                }
                $this->getPlugin()->time->set($p, $time -1);
            }
            $this->getPlugin()->time->save();
            $this->getPlugin()->mode->save();
        }
    }
}