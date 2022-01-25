<?php

namespace onebone\tokenapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\tokenapi\TokenAPI;

class TakeTokenCommand extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("taketoken");
		parent::__construct("taketoken", $desc["description"], $desc["usage"]);

		$this->setPermission("tokenapi.command.taketoken");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params): bool{
		if(!$this->plugin->isEnabled()) return false;
		if(!$this->testPermission($sender)){
			return false;
		}

		$player = array_shift($params);
		$amount = array_shift($params);

		if(!is_numeric($amount)){
			$sender->sendMessage(TextFormat::RED . "Dùng: " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		if($amount < 0){
			$sender->sendMessage($this->plugin->getMessage("taketoken-invalid-number", [$amount], $sender->getName()));
			return true;
		}

		$result = $this->plugin->reduceToken($player, $amount);
		switch($result){
			case TokenAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("taketoken-player-lack-of-token", [$player, $amount, $this->plugin->myToken($player)], $sender->getName()));
			break;
			case TokenAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("taketoken-took-token", [$player, $amount], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("taketoken-token-taken", [$amount], $sender->getName()));
			}
			break;
			case TokenAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("taketoken-failed", [], $sender->getName()));
			break;
			case TokenAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
		}

		return true;
	}
}
