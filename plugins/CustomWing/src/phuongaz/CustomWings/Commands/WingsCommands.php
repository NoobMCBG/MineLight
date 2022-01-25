<?php

namespace phuongaz\CustomWings\Commands;

use pocketmine\command\{
	Command,
	CommandSender
};

use phuongaz\CustomWings\Loader;
use pocketmine\Player;

Class  WingsCommands extends Command {

	public function __construct(){
		parent::__construct("wings", "Show wings form");
	}

	public function execute(CommandSender $sender, string $label, array $args) :bool {
		if($sender instanceof Player) {
			Loader::sendForm($sender);
			//Loader::getInstance()->dropWing("Wing2", $sender->asPosition());
		}
		return true;
	}
}