<?php
namespace MyPlot\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class HelpSubCommand extends SubCommand
{
    public function canUse(CommandSender $sender) {
        return $sender->hasPermission("myplot.command.help");
    }

    /**
     * @return \MyPlot\Commands
     */
    private function getCommandHandler()
    {
        return $this->getPlugin()->getCommand($this->translateString("command.name"));
    }

    public function execute(CommandSender $sender, array $args) {
        if (count($args) === 0) {
            $pageNumber = 1;
        } elseif (is_numeric($args[0])) {
            $pageNumber = (int) array_shift($args);
            if ($pageNumber <= 0) {
                $pageNumber = 1;
            }
        } else {
            return false;
        }

        if ($sender instanceof ConsoleCommandSender) {
            $pageHeight = PHP_INT_MAX;
        } else {
            $pageHeight = 5;
        }

        $commands = [];
        foreach ($this->getCommandHandler()->getCommands() as $command) {
            if ($command->canUse($sender)) {
                $commands[$command->getName()] = $command;
            }
        }
        ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
        $commands = array_chunk($commands, $pageHeight);
        /** @var SubCommand[][] $commands */

							//////
            $sender->sendMessage("§7[⚒] ========== §6ＬỆNH ＳKYBLOCK §7========== [⚒]");
            $sender->sendMessage("§a/sb play§f - §eBắt đầu vào một hòn đảo mới");
			$sender->sendMessage("§a/sb claim§f - §eNhận ngay hòn đảo bạn đang đứng");
			$sender->sendMessage("§a/sb addhelper <player>§f - §eThêm người vào đảo của bạn");
			$sender->sendMessage("§a/sb removehelper <player>§f - §eXóa người chơi trong đảo của bạn");
			$sender->sendMessage("§a/sb home <Số> §f - §eDịch chuyển về đảo của bạn");
			$sender->sendMessage("§a/sb info§f - §eXem thông tin hòn đảo");
			$sender->sendMessage("§a/sb give <Tên người chơi> §f - §eCho người khác đảo của bạn");
			$sender->sendMessage("§a/sb warp <X;Y> §f - §eDi chuyển đến hòn đảo nào đó");
			$sender->sendMessage("§7[⚒] ======================================= [⚒]");
        return true;
    }
}
