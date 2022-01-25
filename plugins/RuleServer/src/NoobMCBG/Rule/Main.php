<?php

namespace NoobMCBG\Rule;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info(":)");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "rule":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("SỬ DỤNG TRG GAME !");
			    	return true;
			    }else{
			    	$this->MenuRules($sender);
			    }
			break;
		}
		return true;
	}

	public function MenuRules($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Luật §c•");
		$form->setContent("§l§c•§e Không Hack,Cheat,...\n§l§c•§e Không Gửi Các Link 18+\n§l§c•§e Không Chửi Thề Nhiều (CÓ ĐƯỢC NHƯNG ÍT THÔI)\n§l§c•§e Không Bug Đồ ! (Có Lỗi báo Admin Bạn sẽ có Quà)\n§l§c•§e Chúc Bạn Chơi Vui Vẻ !");
		$form->addButton("§l§c•§9 Thoát Menu §c•s");
		$form->sendToPlayer($player);
	}
}