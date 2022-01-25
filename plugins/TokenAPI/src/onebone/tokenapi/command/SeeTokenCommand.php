<?php

namespace onebone\tokenapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\tokenapi\TokenAPI;

class SeeTokenCommand extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("seetoken");
		parent::__construct("seetoken", $desc["description"], $desc["usage"]);

		$this->setPermission("tokenapi.command.seetoken");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		$player = array_shift($params);
		if(trim($player) === ""){
			$sender->sendMessage(TextFormat::RED . "Dùng: " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		$token = $this->plugin->myToken($player);
		if($token !== false){
			$sender->sendMessage($this->plugin->getMessage("seetoken-seetoken", [$player, $token], $sender->getName()));
		}else{
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
		}
		return true;
	}
}
