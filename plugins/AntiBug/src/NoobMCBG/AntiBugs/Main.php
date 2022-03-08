<?php

namespace NoobMCBG\AntiBugs;

use pocketmine\Sercer;
use pocketmine\Player;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;

class Main extends PB implements L {

	public function onEnable(){
                $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("ANTIBUG ORESPAWNERS");
		$this->saveDefaultConfig();
	}

    public function onBreak(BlockBreakEvent $ev){
    	$player = $ev->getPlayer();
    	$block = $ev->getBlock();
    	if($player->getGamemode() == 1){
            if($ev->getBlock()->getId() == 232){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
            if($ev->getBlock()->getId() == 228){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
            if($ev->getBlock()->getId() == 224){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
            if($ev->getBlock()->getId() == 223){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
            if($ev->getBlock()->getId() == 225){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
            if($ev->getBlock()->getId() == 231){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
            if($ev->getBlock()->getId() == 234){
                $ev->setCancelled(true);
                $player->sendMessage($this->getConfig()->get("msg-anti"));
            }
    	}
    }
}
