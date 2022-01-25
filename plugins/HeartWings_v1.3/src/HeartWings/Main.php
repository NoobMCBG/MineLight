<?php

namespace HeartWings;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	
	public $players = [];
	private $config = [];
	
	public function onEnable()
	{
		$this->getLogger()->info("HeartWings Việt Hóa By Nờ Cờ Dờ (NCD)");
		$df = $this->getDataFolder();
		@mkdir($df);
		if(!is_file($df . "config.yml")){
			$cfg = new Config($df . "config.yml", Config::YAML);
			$cfg->setAll([
				"wings-off" => "§aBạn đã tắt Cánh Trái Tim thành công",
				"wings-on" => "§aBạn đã bật Cánh Trái Tim thành công",
				"update-period" => 20
			]);
			$cfg->save();
		}
		$this->config = (new Config($df . "config.yml", Config::YAML))->getAll();
		$this->getScheduler()->scheduleRepeatingTask(new SendWingsTask($this), $this->config["update-period"]);
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $params) : bool
	{
		if(!$sender instanceof Player){
			$sender->sendMessage("§cBạn không có quyền để sử dụng lệnh này!");
			return false;
		}
		$username = strtolower($sender->getName());
		if($command->getName() == "canhtraitim"){
			if(isset($this->players[$username])){
				unset($this->players[$username]);
				$sender->sendMessage($this->config["wings-off"]);
				return true;
			}else{
				$this->players[$username] = true;
				$sender->sendMessage($this->config["wings-on"]);
				return true;
			}
		}else{
			return false;
		}
	}
	
}
