<?php
namespace MyPlot\subcommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\Server;

class PlaySubCommand extends SubCommand
{


    public function canUse(CommandSender $sender) {
        return ($sender instanceof Player) and $sender->hasPermission("myplot.command.play");
    }
    
    public function pl(Player $player)
    {
        return $player;
    }

    public function execute(CommandSender $sender, array $args) {
        if (!empty($args)) {
            return false;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        $levelName = $player->getLevel()->getName();
        $name = $player->getName();
        $worldAsObject = $this->getPlugin()->getServer()->getLevelByName("sb");
        $this->pl($sender)->teleport(new Position(110, 110, 110, $worldAsObject));
		$this->getPlugin()->getServer()->dispatchCommand($player, "sb auto");
    return true;
    }
}