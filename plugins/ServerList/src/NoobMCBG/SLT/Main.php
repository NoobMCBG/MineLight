<?php

namespace NoobMCBG\SLT;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("ServerList Đã Hoạt Động");
		$this->saveDefaultConfig();
	}

	public function onCommand(CommandSender $s, Command $c, string $l, array $a) : bool {
		switch($c->getName()){
			case "server":
			    if(!$s instanceof Player){
			    	$s->sendMessage("Sử Dụng Trg Game !");
			    	return true;
			    }else{
			    	$this->MenuServerList($s);
			    }
			break;
		}
		return true;
	}

	public function MenuServerList($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				break;
				case 1:
				    $this->ServerKingNight($player);
				break;
				case 2:
				    $this->ServerAEVN($player);
				break;
				case 3:
				    $this->ServerTVT($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Server §c•");
		$form->addButton("§l§c•§9 Thoát Menu §c•");
		$form->addButton("§l§c•§9 Server KingNightVN §c•\n§l§0Trạng Thái:§4 Đóng Cửa");
		$form->addButton("§l§c•§9 Server Race Against Time §c•\n§l§0Trạng Thái:§4 Đóng Cửa");
		$form->addButton("§l§c•§9 Server TheVerTie §c•\n§l§0Trạng Thái: ".$this->getConfig()->get("thevertie-trangthai"));
		$form->sendToPlayer($player);
	}

	public function ServerKingNight($player){
		$player->transfer($this->getConfig()->get("kingnight-ip"), $this->getConfig()->get("kingnight-port"));
	}

	public function ServerAEVN($player){
		$player->transfer($this->getConfig()->get("aevn-ip"), $this->getConfig()->get("aevn-port"));
	}

	public function ServerTVT($player){
		$player->transfer($this->getConfig()->get("theverite-ip"), $this->getConfig()->get("thevertie-port"));
	}
}