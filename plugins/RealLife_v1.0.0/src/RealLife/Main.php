<?php

namespace RealLife;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener as L;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\command\{Command, CommandSender};
use RealLife\HoiPhucTask;

Class Main extends PB implements L{
	
	public function onEnable():void{
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
	    $this->getLogger()->info("§l§a> §bPlugin RealLife By NoobMCBG");
        @mkdir($this->getDataFolder(), 0744, true);  
        $this->getScheduler()->scheduleRepeatingTask(new HoiPhucTask($this), 20);     
    }


    public function onBreak(BlockBreakEvent $ev){
        if(!$ev->isCancelled()){
            if(mt_rand(0,1) == 1){
                $player = $ev->getPlayer();
                $player->setHealth(999999);
                $money = 10;
                $this->eco->addMoney($player, $money);
                $player->sendPopup("§l§d>§e +§a 10 VNĐ §d<");
            }
        }
    }
}