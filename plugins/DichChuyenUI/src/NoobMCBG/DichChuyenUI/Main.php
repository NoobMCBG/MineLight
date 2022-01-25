<?php

namespace NoobMCBG\DichChuyenUI;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, COnsoleCommandSendẻ};
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("MỜ LÈM MỜ LÈM :>\n\n DỊCH CHUYỂN UI by NOOBMCBG");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "dichchuyen":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
			    	return true;
			    }else{
			    	$this->MenuDichChuyen($sender);
			    }
			break;
		}
		return true;
	}

	public function MenuDichChuyen($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				break;
				case 1:
				    $this->getServer()->getCommandMap()->dispatch($player, "warp trade");
				break;
				case 2:
				    $this->getServer()->getCommandMap()->dispatch($player, "warp pvp");
				break;
				case 3:
				    $this->getServer()->getCommandMap()->dispatch($player, "warp crate");
				break;
				case 4:
                    $this->getServer()->getCommandMap()->dispatch($player, "warp server");
			}
		});
		$form->setTitle("§l§c•§9 Menu Dịch Chuyển §c•");
		$form->addButton("§l§c•§9 Khu Trade §c•", 0, "textures/other/trade");
		$form->addButton("§l§c•§9 Khu PvP §c•", 0, "textures/other/pvp");
		$form->addButton("§l§c•§9 Khu Quay Rương §c•", 0, "textures/other/crate");
		$form->addButton("§l§c•§9 Khu Server §c•", 0, "textures/other/server");
		$form->sendToPlayer($player);
	}
}