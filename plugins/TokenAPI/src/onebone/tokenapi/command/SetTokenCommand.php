<?php

namespace onebone\tokenapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;

use onebone\tokenapi\TokenAPI;

class SetTokenCommand extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("settoken");
		parent::__construct("settoken", $desc["description"], $desc["usage"]);

		$this->setPermission("tokenapi.command.settoken");

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

		$result = $this->plugin->setToken($player, $amount);
		switch($result){
			case TokenAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("settoken-invalid-number", [$amount], $sender->getName()));
			break;
			case TokenAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
			case TokenAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("settoken-failed", [], $sender->getName()));
			break;
			case TokenAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("settoken-settoken", [$player, $amount], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("settoken-set", [$amount], $p->getName()));
			}
			break;
			default:
			$sender->sendMessage("...");
		}
		return true;
	}
}
