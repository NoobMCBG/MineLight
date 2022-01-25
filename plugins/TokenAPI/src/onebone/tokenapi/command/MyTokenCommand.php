<?php

namespace onebone\tokenapi\command;

use pocketmine\event\TranslationContainer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\tokenapi\TokenAPI;

class MyTokenCommand extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("mytoken");
		parent::__construct("mytoken", $desc["description"], $desc["usage"]);

		$this->setPermission("mytoken.command.mytoken");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		if($sender instanceof Player){
			$token = $this->plugin->myToken($sender);
			$sender->sendMessage($this->plugin->getMessage("mytoken-mytoken", [$token]));
			return true;
		}
		$sender->sendMessage(TextFormat::RED."Please run this command in-game.");
		return true;
	}
}
