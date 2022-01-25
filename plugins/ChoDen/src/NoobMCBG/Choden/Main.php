<?php

namespace NoobMCBG\Choden;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("Enable Pờ Lúc Gin");
		$this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
		$this->saveDefaultConfig();
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "choden":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("Hãy Sử Dụng Lệnh Trong Trò Chơi !");
			    	return true;
			    }else{
			    	$this->MenuChoden($sender);
			    }
			break;
		}
		return true;
	}

    public function MenuChoden($player){
    	$form = new SimpleForm(function(Player $player, $data){
    		if($data == null){
    			return true;
    		}
    		switch($data){
    			case 0:
    			break;
    			case 1:
    			    $token = $this->token->myToken($player);
    			    $cost = 600;
    			    if($token >= $cost){
    			    	$this->token->reduceToken($player, $cost);
    			    	$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "setuperm ".$player->getName()." kingofblock.command");
    			        $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Tính Năng§a KingOfBlock.");
    			        $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§a ".$player->getName()." §eVừa Mua Thành Công Tính Năng §aKingOfBlock.");
    			    }else{
    			    	$player->sendMessage("§l§c•§e Bạn Không Đủ §2Tokens §eĐể Mua Tính Năng Này !");
    			    }
    			break;
    			case 2:
                    $token = $this->token->myToken($player);
    			    $cost = 600;
    			    if($token >= $cost){
    			    	$this->token->reduceToken($player, $cost);
    			    	$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "setuperm ".$player->getName()." eternity.command");
    			        $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Tính Năng§a Eternity.");
    			        $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§a ".$player->getName()." §eVừa Mua Thành Công Tính Năng §aEternity.");
    			    }else{
    			    	$player->sendMessage("§l§c•§e Bạn Không Đủ §2Tokens §eĐể Mua Tính Năng Này !");
    			    }
    			break;
    		}
    	});
    	$form->setTitle("§l§c•§9 Menu Chợ Đen §c•");
    	$form->addButton("§l§c•§9 Thoát Chợ Đen §c•", 0, "textures/other/exit");
    	$form->addButton("§l§c•§9 KingOfBlock §2(600 Tokens) §c•", 0, "textures/other/kingofblock");
    	$form->addButton("§l§c•§9 Eternity §2(600 Tokens) §c•", 0, "textures/other/eternity");
    	$form->sendToPlayer($player);
    }
}