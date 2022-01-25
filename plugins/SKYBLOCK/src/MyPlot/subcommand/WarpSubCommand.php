<?php
namespace MyPlot\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\Server;

class WarpSubCommand extends SubCommand
{
    public function canUse(CommandSender $sender) {
        return ($sender instanceof Player) and $sender->hasPermission("myplot.command.warp");
    }

    public function execute(CommandSender $sender, array $args) {
        if (count($args) != 1)
            return false;
		
	
	$player = $sender->getServer()->getPlayer($sender->getName());
        $levelName = $player->getLevel()->getName();
        $name = $player->getName();
        $worldAsObject = $this->getPlugin()->getServer()->getLevelByName("sb");
        $player->teleport(new Position(110, 110, 110, $worldAsObject));

        $player = $sender->getServer()->getPlayer($sender->getName());
        $levelName = $player->getLevel()->getName();
        if (!$this->getPlugin()->isLevelLoaded($levelName)) {
            $sender->sendMessage(TextFormat::RED . $this->translateString("warp.notinplotworld"));
            return true;
        }

        $plotIdArray = explode(";", $args[0]);
        if (count($plotIdArray) != 2 or !is_numeric($plotIdArray[0]) or !is_numeric($plotIdArray[1])) {
            $sender->sendMessage(TextFormat::RED . $this->translateString("warp.wrongid"));
            return true;
        }

        $plot = $this->getPlugin()->getProvider()->getPlot($levelName, $plotIdArray[0], $plotIdArray[1]);
        if ($plot->owner == "" and !$sender->hasPermission("myplot.admin.warp")) {
            $sender->sendMessage(TextFormat::RED . $this->translateString("warp.unclaimed"));
            return true;
        }

        $this->getPlugin()->teleportPlayerToPlot($player, $plot);

        $plot = TextFormat::GREEN . $plot . TextFormat::WHITE;
        $sender->sendMessage($this->translateString("warp.success", [$plot]));
        return true;
    }
}
