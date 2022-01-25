<?php

namespace NoobMCBG\UGWE;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;

class Main extends PluginBase {

	public function onEnable(): void {
		$this->getLogger()->info(":>>");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "/up":
			    if(!$sender instanceof Player){
			    	if(!is_numeric($args[0]) == null){
					    $sender->sendMessage("§l§c•§e Sử Dụng:§b //up <y>");
					    return true;
				    }
			    	if(!is_numeric($args[0])){
					    $sender->sendMessage("§l§c•§e Sử Dụng:§b //up <y>");
					    return true;
				    }
				    $x = $sender->getX();
				    $y = $sender->getY();
				    $z = $sender->getZ();
				    $level = $sender->getLevel();
				    $level->setblock(new Vector3($x, $y+$args[0], $z), Block::get(20, 0));
			    	return true;
			    }else{
			    	
			    }$sender->sendMessage("SỬ DỤNG TRONG GAME");
			break;
		}
		return true;
	}
}