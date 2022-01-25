<?php

namespace NoobMCBG\BuyFly;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, COnsoleCommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info(" :) ");
		$this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->saveDefaultConfig();
	}

	public function EconomyAPI(){
		return $this->money;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "muafly":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("SỬ DỤNG TRG GAME");
			    	return true;
			    }else{
			    	$money = $this->EconomyAPI()->myMoney($sender);
			    	$cost = $this->getConfig()->get("cost-fly");
			    	if($money >= $cost){
			    		$this->EconomyAPI()->reduceMoney($sender, $cost);
			    		$sender->sendMessage("§l§c•§e Bạn Đã Mua §bFly §aThành Công !");
			    		$sender->setAllowFlight(true);
                        $sender->setFlying(true);
			    	}else{
			    		$sender->sendMessage("§l§c•§e Bạn Không Đủ §aXu§e Để Mua §bFly");
			    	}
			    }
			break;
		}
		return true;
	}
}