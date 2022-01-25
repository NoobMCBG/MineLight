<?php

namespace onebone\tokenapi\command;

use pocketmine\command\Command;
use pocketmine\Command\CommandSender;
use pocketmine\utils\TextFormat;

use onebone\tokenapi\TokenAPI;

class SetLangToken extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("setlangtoken");
		parent::__construct("setlangtoken", $desc["description"], $desc["usage"]);

		$this->setPermission("tokenapi.command.setlangtoken");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		$lang = array_shift($params);
		if(trim($lang) === ""){
			$sender->sendMessage(TextFormat::RED . "Dùng: " . $this->getUsage());
			return true;
		}

		if($this->plugin->setPlayerLanguage($sender->getName(), $lang)){
			$sender->sendMessage($this->plugin->getMessage("language-set", [$lang], $sender->getName()));
		}else{
			$sender->sendMessage(TextFormat::RED . "There is no language such as $lang");
		}
		return true;
	}
}
