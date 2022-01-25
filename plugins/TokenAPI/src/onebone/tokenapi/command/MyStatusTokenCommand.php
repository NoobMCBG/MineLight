<?php

namespace onebone\tokenapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\tokenapi\TokenAPI;

class MyStatusTokenCommand extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("mystatustoken");
		parent::__construct("mystatustoken", $desc["description"], $desc["usage"]);

		$this->setPermission("tokenapi.command.mystatustoken");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
			return true;
		}

		$token = $this->plugin->getAllToken();

		$allToken = 0;
		foreach($token as $m){
			$allToken += $m;
		}
		$topToken = 0;
		if($allToken > 0){
			$topToken = round((($token[strtolower($sender->getName())] / $allToken) * 100), 2);
		}

		$sender->sendMessage($this->plugin->getMessage("mystatuspp-show", [$topToken], $sender->getName()));
		return true;
	}
}
