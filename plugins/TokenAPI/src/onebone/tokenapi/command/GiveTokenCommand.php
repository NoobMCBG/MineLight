<?php

namespace onebone\tokenapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use onebone\tokenapi\TokenAPI;

class GiveTokenCommand extends Command{
	private $plugin;

	public function __construct(TokenAPI $plugin){
		$desc = $plugin->getCommandMessage("givetoken");
		parent::__construct("givetoken", $desc["description"], $desc["usage"]);

		$this->setPermission("tokenapi.command.givetoken");

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

		$result = $this->plugin->addToken($player, $amount);
		switch($result){
			case TokenAPI::RET_INVALID:
			$sender->sendMessage($this->plugin->getMessage("givetoken-invalid-number", [$amount], $sender->getName()));
			break;
			case TokenAPI::RET_SUCCESS:
			$sender->sendMessage($this->plugin->getMessage("givetoken-gave-token", [$amount, $player], $sender->getName()));

			if($p instanceof Player){
				$p->sendMessage($this->plugin->getMessage("givetoken-token-given", [$amount], $sender->getName()));
			}
			break;
			case TokenAPI::RET_CANCELLED:
			$sender->sendMessage($this->plugin->getMessage("request-cancelled", [], $sender->getName()));
			break;
			case TokenAPI::RET_NO_ACCOUNT:
			$sender->sendMessage($this->plugin->getMessage("player-never-connected", [$player], $sender->getName()));
			break;
		}
        return true;
	}
}
