<?php

namespace NoobMCBG\Freeze;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use pocketmine\event\player\{PlayerQuitEvent, PlayerMoveEvent};
use pocketmine\utils\Config;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Main extends PB implements L {

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("FREEZE BY NOOBMCBG");
		$this->freeze = new Config($this->getDataFolder() . "freeze.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new FreezeTime($this), 20 * 60);
	}

	public function getFreeze(){
		return $this->freeze;
	}

	public function onQuit(PlayerQuitEvent $ev){
		$this->getFreeze()->save();
	}

	public function onDisable(){
		$this->getFreeze()->save();
	}

    public function onMove(PlayerMoveEvent $ev){
    	$player = $ev->getPlayer();
    	if($this->getFreeze()->get(strtolower($player->getName())) == true){
    		$ev->setCancelled(true);
    		$player->sendPopup("§l§6♦§c Bạn Đang Bị Freeze §6♦");
    	}
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
    	switch($cmd->getName()){
    		case "freeze":
            if(!$sender->hasPermission("freeze.command")){
                $sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
            }else{
                if(isset($args[0])){
                    if(isset($args[1])){
                        $player = $this->getServer()->getPlayer($args[0]);
                        if(!is_numeric($args[1])){
                            $sender->sendMessage("§l§c•§e Số Thời Gian Bắt Buộc Phải Là Số !");
                            return true;
                        }
                        if(!$player instanceof Player){
                            $sender->sendMessage("§l§c•§e Người Chơi§b " . $args[0] . "§e Không Online !");
                            return true;
                        }
                        $this->getFreeze()->set(strtolower($player->getName()), $args[1]);
                        $this->getFreeze()->save();
                        $player->sendMessage("§l§c•§e Bạn Đã Bị Freeze Trong§a {$args[1]} Phút !");
                    }else{
                        $sender->sendMessage("§l§c•§e Sử Dụng:§b /freeze <player> <time>");
                    }
                }else{
                    $sender->sendMessage("§l§c•§e Sử Dụng:§b /freeze <player> <time>");
                }
            }
            break;
            case "unfreeze":
            if(!$sender->hasPermission("freeze.command")){
                $sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
            }else{
                if(!isset($args[0])){
                    $sender->sendMessage("§l§c•§e Sử Dụng:§b /unfreeze <player>");
                    return true;
                }else{
                    $player = $this->getServer()->getPlayer($args[0]);
                    if(!$player == null){
                        if($player->isOnline()){
                           $this->getFreeze()->set(strtolower($player->getName()), 0);
                           $this->getFreeze()->save();
                           $player->sendMessage("§l§c•§e Bạn Đã Được Bỏ§a Freeze !");
                       }
                    }
                }
            }
            break;
    	}
    	return true;
    }
}