<?php

namespace NoobMCBG\FastChat;

use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use NoobMCBG\FastChat\Main as FastChat;

class DelayTask extends Task{

    public function __construct(FastChat $plugin){
        $this->plugin = $plugin;
        $this->i = 0;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currenttick){
        $messages = $this->getPlugin()->chat->get(strtolower(["message"]));
        back:
        if($this->i < count($messages)){
            $this->plugin->getServer()->broadcastMessage("§l§c•§e Nhanh Tay Chat Từ§a ".$messages[$this->i]."§e Để Nhận Quà Khủng Nào !");
            $this->i++;
        }else{
            $this->i = 0;
            goto back;
        }
    }
}