<?php

namespace NoobMCBG\JailPrison;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent, PlayerChatEvent, PlayerInteractEvent};
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\level\{Level, Position};
use jojoe77777\FormAPI\{SimpleForm, CustomForm};

class Main extends PB implements L {

	public $time;

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("\n\n§l§b-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n§l§a        JailPrison v1.0.0\n§l§e       by NoobMCBG\n§l§b-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n\n");
        $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
        $this->vaynanglai = $this->getServer()->getPluginManager()->getPlugin("VayNangLai");
        $this->jail = new Config($this->getDataFolder() . "jail.yml", Config::YAML);
        $this->jailtime = new Config($this->getDataFolder() . "jailtime.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new JailTask($this), 20 * 60);
        $this->saveDefaultConfig();
	}

	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		$name = $player->getName();
		if(!$this->jail->exists(strtolower($name))){
			$this->jail->set(strtolower($name), false);
			$this->jail->save();
			$this->getLogger()->notice("Đã Tạo Tài Khoản $name");
		}
		if(!$this->jailtime->exists(strtolower($name))){
			$this->jailtime->set(strtolower($name), 0);
			$this->jailtime->save();
		}
		//Check Vay :>
		if($this->vaynanglai->vaytime->get(strtolower($player->getName())) == 1){
            $this->jail->set(strtolower($name), true);
            $this->jail->save();
		}
		//Check Vay :>
		if($this->vaynanglai->vaytime->get(strtolower($player->getName())) == 0){
            $this->jail->set(strtolower($name), false);
            $this->jail->save();
		}
		if($this->jail->get(strtolower($name)) == true){
			$x = $this->getServer()->getLevelByName("Jail")->getSafeSpawn()->getFloorX();
            $y = $this->getServer()->getLevelByName("Jail")->getSafeSpawn()->getFloorY();
            $z = $this->getServer()->getLevelByName("Jail")->getSafeSpawn()->getFloorZ();
            $player->teleport(new Position($x, $y, $z, Server::getInstance()->getLevelByName("Jail")));           
		}
	}

	public function onQuit(PlayerQuitEvent $ev){
		$this->jail->save();
		$this->jailtime->save();
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "jail":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
			    	return true;
			    }
			    if(!$sender->hasPermission("jail.command")){
			    	$sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
			    }else{
			        if(count($args) == 0){
			        	$sender->sendMessage("§l§c•§e Sử Dụng:§b /jail help");
			        }
			        if(count($args) == 1){
			        	$sender->sendMessage("§l§c•§e Sử Dụng:§b /jail help");
			        	if(isset($args[0]) == "help"){
			        		$sender->sendMessage("§l§c•§e Cách Sử Dụng Jail §c•\n§l§b- /jail help§e - Để Xem Các Lệnh Jail\n§l§b- /jail add§e - Để Thêm Người Chơi Thành Tù Nhân\n§l§b- /jail remove§e - Để Xóa Tù Nhân\n§l§b- /jail time§e - Để Thêm Tù Nhân Trong Thời Gian");
			        	}
			        	if(isset($args[0]) == "add"){
			        		$this->MenuJailPlayer($sender);
			        	}
			        	if(isset($args[0]) == "remove"){
			        		$this->MenuUnJailPlayer($sender);
			        	}
			        	if(isset($args[0]) == "time"){
			        		$this->MenuJailTimePlayer($sender);
			        	}
			        }
			    }
			break;
		}
		return true;
	}

	public function MenuJailPlayer($player){
        $list = [];
		foreach ($this->getServer()->getOnlinePlayers() as $p) {
			$list[] = $p->getName();
		}
		$this->playerList[$player->getName()] = $list;
		$form = new CustomForm(function(Player $player, $data) {
			if ($data == null) {
				return true;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			if ($playerName instanceof Player) {
			}
			$this->jail->set(strtolower($playerName), true);
			return false;
		});
        $form->setTitle("§l§c•§9 Menu Tù Nhân §c•");
        $form->addDropdown("§l§c•§e Người Chơi Muốn Giam Giữ:§a ", $this->playerList[$player->getName()]);
        $form->sendToPlayer($player);
	}

	public function MenuUnJailPlayer($player){
		$list = [];
		foreach ($this->getServer()->getOnlinePlayers() as $p) {
			$list[] = $p->getName();
		}
		$this->playerList[$player->getName()] = $list;
		$form = new CustomForm(function(Player $player, $data) {
			if ($data == null) {
				return true;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			if ($playerName instanceof Player) {
			}
			$this->jail->set(strtolower($playerName), false);
			$this->jailtime->set(strtolower($playerName), 0);
			return false;
		});
        $form->setTitle("§l§c•§9 Menu Tù Nhân §c•");
        $form->addDropdown("§l§c•§e Người Chơi Bỏ Giam Giữ:§a ", $this->playerList[$player->getName()]);
        $form->sendToPlayer($player);
	}

	public function MenuJailTimePlayer($player){
		$list = [];
		foreach ($this->getServer()->getOnlinePlayers() as $p) {
			$list[] = $p->getName();
		}
		$this->playerList[$player->getName()] = $list;
		$form = new CustomForm(function(Player $player, $data) {
			if ($data == null) {
				return true;
			}
			$index = $data[1];
			$playerName = $this->playerList[$player->getName()][$index];
			if ($playerName instanceof Player) {
			}
			$this->jailtime->set(strtolower($playerName), $data[2]);
			return false;
		});
        $form->setTitle("§l§c•§9 Menu Tù Nhân §c•");
        $form->addLabel("§l§c•§e Thời Gian Tính Theo Phút !");
        $form->addDropdown("§l§c•§e Người Chơi Bỏ Giam Giữ:§a ", $this->playerList[$player->getName()]);
        $form->addSlider("§l§c•§e Số Thời Gian Giam Giữ:§a ", 1, 60);
        $form->sendToPlayer($player);
	}
}