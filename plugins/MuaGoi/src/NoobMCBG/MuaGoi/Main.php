<?php

namespace NoobMCBG\MuaGoi;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};

class Main extends PB implements L {

    public function onEnable(){
    	$this->getLogger()->info("HOẠT ĐỘNG PLUGINS RÒI :>");
    	$this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    	$this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
    	switch($cmd->getName()){
    		case "muagoi":
    		    if(!$sender instanceof Player){
    		    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
    		    	return true;
    		    }else{
    		    	$this->MenuMuaGoi($sender);
    		    }
    		break;
    	}
    	return true;
    }

    public function MenuMuaGoi($player){
    	$form = new SimpleForm(function(Player $player, $data){
    		if($data == null){
    			return true;
    		}
    		switch($data){
    			Case 0:
    			break;
    			case 1:
    			    $this->MenuMuaToken($player);
    		    break;
    		}
    	});
    	$form->setTitle("§l§c•§9 Menu Mua Gói §c•");
    	$form->addButton("§l§c•§9 Thoát Menu §c•");
    	$form->addButton("§l§c•§9 Mua §2Token§6 (600K Xu/1 Token)", 0, "textures/other/token");
    	$form->sendToPlayer($player);
    }

    public function MenuMuaToken($player){
    	$form = new CustomForm(function(Player $player, $data){
    		if($data === null){
    			return true;
    		}
    		$money = $this->money->myMoney($player);
    		$cost = 600000;
    		if($money >= $cost*$data[0]){
    			$this->money->reduceMoney($player, $cost*$data[0]);
    			$this->token->addToken($player, $data[0]);
    			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Tất Cả§2 ".$data[0]." Tokens.");
    		}else{
    			$player->sendMessage("§l§c•§e Bạn Không Đủ §aXu§e Để Mua§2 ".$data[0]." Tokens.");
    		}
    	});
    	$form->setTitle("§l§c•§9 Menu Mua Gói §c•");
    	$form->addSlider("§l§c•§e Số §2Tokens§e Muốn Mua§a: ", 1, 100);
    	$form->sendToPlayer($player);
    }
}