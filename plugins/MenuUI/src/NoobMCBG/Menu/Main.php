<?php

namespace NoobMCBG\Menu;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("E Na Bồ :))) !");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "menu":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi");
			    	return true;
			    }else{
			    	$this->MenuForm($sender);
			    }
			break;
		}
		return true;
	}

	public function MenuForm($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				break;
				case 1:
				    $this->getServer()->getCommandMap()->dispatch($player, "sb");
				break;
				case 2:
                    $this->getServer()->getCommandMap()->dispatch($player, "dichchuyen");
				break;
				case 3:
                    $this->getServer()->getCommandMap()->dispatch($player, "choden");
				break;
				case 4:
				    $this->getServer()->getCommandMap()->dispatch($player, "napthe");
				break;
				case 5:
				    $this->getServer()->getCommandMap()->dispatch($player, "muaec");
				break;
				case 6:
				    $this->getServer()->getCommandMap()->dispatch($player, "shop");
				break;
				case 7:
				    $this->getServer()->getCommandMap()->dispatch($player, "cuonghoa");
				break;
				case 8:
				    $this->getServer()->getCommandMap()->dispatch($player, "muavip");
				break;
				case 9:
				    $this->getServer()->getCommandMap()->dispatch($player, "ngaydisan");
				break;
                case 10:
                    $this->getServer()->getCommandMap()->dispatch($player, "quest");
                break;
                case 11:
                    $this->getServer()->getCommandMap()->dispatch($player, "server");
                break;
                case 12:
                    $this->getServer()->getCommandMap()->dispatch($player, "seasonpass");
                break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Server §c•");
		$form->addButton("§l§c•§9 Thoát Menu §c•", 0, "textures/other/exit");
		$form->addButton("§l§c•§9 SkyBlock §c•", 0, "textures/blocks/grass_path_side");
		$form->addButton("§l§c•§9 Khu Vực §c•", 0, "textures/other/castle");
		$form->addButton("§l§c•§9 Chợ Đen §c•", 0, "textures/other/choden");
		$form->addButton("§l§c•§9 Nạp Thẻ §c•", 0, "textures/other/donate");
		$form->addButton("§l§c•§9 Enchant §c•", 0, "textures/other/enchant");
		$form->addButton("§l§c•§9 Cường Hóa §c•", 0, "textures/other/cuonghoa");
		$form->addButton("§l§c•§9 Mua VIP §c•", 0, "textures/ui/op");
		$form->addButton("§l§c•§9 Đi Săn §c•", 0, "textures/other/pvp");
		$form->addButton("§l§c•§9 Nhiệm Vụ §c•", 0, "textures/other/quest");
		$form->addButton("§l§c•§9 Các Server Khác §c•", 0, "textures/other/server");
		$form->addButton("§l§c•§9 SeasonPass §c•", 0, "textures/other/eletepass");
		$form->sendToPlayer($player);
	}
}