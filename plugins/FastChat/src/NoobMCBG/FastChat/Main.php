<?php

namespace NoobMCBG\FastChat;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\Listener as L;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\utils\Config;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("Đã Hoạt Động Plugin\n\n-=-=-=-=-=-=-=-=-=-\nFastChat v1.0.0 by NoobMCBG\n-=-=-=-=-=-=-=-=-=-\n\n");
		@mkdir($this->getDataFolder());
		$this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
		$this->saveDefaultConfig();
		$this->chat = new Config($this->getDataFolder() . "chat.yml", Config::YAML);
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "fastchat":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi");
			    	return true;
			    }
			    if(count($args) == 1){
			    	if($args[0] == "help"){
			    		if(!$sender->hasPermission("fastchat.command.help")){
			    			$sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này");
			    		}else{
			    		    $sender->sendMessage("§l§c•§e Cách Sử Dụng FastChat:\n§l§b- /fastchat help§e - Để Hiển Thị Cách Sử Dụng FastChat\n§l§b- /fastchat create§e - Để Tạo 1 Đoạn Chat Nhận Quà");
			    		}
			    	}
			    	if($args[0] == "create"){
			    		if(!$sender->hasPermission("fastchat.command.create")){
                            $sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này");
			    		}else{
			    			$this->MenuCreateChat($sender);
			    		}
			    	}
			    }
			break;
		}
		return true;
	}

	public function MenuCreateChat($player){
		$form = new CustomForm(function(Player $player, $data){
            if($data[0] == null || $data[1] == null || $data[2] == null || $data[3] == null){
            	$player->sendMessage("§l§c•§e Bạn Không Được Bỏ Trống Ở Đây !");
            }
            if(!$this->chat->exists(["message"][$data[0]])){
			    $this->chat->set(strtolower(["message"][$data[0]]), ["xu" => $data[1], "token" => $data[2], "time" => $data[3], "count" => 1]);
			    $this->chat->save();
                $sender->sendMessage("§l§c•§e Đoạn Chat §a".$data[0]."§e Đã Được Tạo Thành Công !");
            }else{
                $sender->sendMessage("§l§c•§e Đoạn Chat Này Đã Tồn Tại");
            }
		});
		$form->setTitle("§l§c•§9 FastChat §c•");
		$form->addInput("§l§c•§e Đoạn Chat Để Nhận Quà:§a ");
		$form->addSlider("§l§c•§e Số Xu Nhận Được Từ Đoạn Chat:§a ", 100, 100000);
		$form->addSlider("§l§c•§e Số Tokens Nhận Được Từ Đoạn Chat:§a ", 0, 10);
		$form->addSlider("§l§c•§e Thời Gian Hết Hạn Sau Khi Broadcast:§a ", 1, 60);
		$form->sendToPlayer($player);
	}

	public function onChat(PlayerChatEvent $ev){
		$player = $ev->getPlayer();
		$ev->setCancelled(true);
        $data = explode(" ", $ev->getMessage());
        if(!$this->chat->exists($data[0])){
            $player->sendMessage("§l§c•§e Đoạn Chat Này Không Tồn Tại !");
            return true;
        }
        	$xu = $this->chat->get(strtolower(["message"][$data[0]]))["xu"];
        	$token = $this->chat->get(strtolower(["message"][$data[0]]))["token"];
        	$time = $this->chat->get(strtolower(["message"][$data[0]]))["time"];
        	$count = $this->chat->get(strtolower(["message"][$data[0]]))["count"];
        if($count == 0){
            $player->sendMessage("§l§c•§e Đoạn Chat Này Đã Có Người Nhập Rồi !");
            return true;
        }
        if($time == 0){
        	$player->sendMessage("§l§c•§e Đoạn Chat Này Đã Hết Hạn Nhập Rồi !");
        }
        $this->chat->set(strtolower(["message"][$data[0]]), ["xu" => $xu, "tokens" => $token, "time" => $time, "count" => 0]);
        $this->money->addMoney($player, $money);
        $this->token->addToken($player, $token);
        $player->sendMessage("§l§c•§e Bạn Đã Nhận Được Quà Từ Đoạn Chat §aThành Công !");
        $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Nhập Đoạn Chat§a ".$data[0]."§e Và Đã Nhận Được Quà Lớn !");
	}

	public function initTasks(){
        $mtime = intval($this->cfg["message-broadcast"]["time"]) * 20;
        $this->mtask = $this->getScheduler()->scheduleRepeatingTask(new DelayTask($this), $mtime);
    }
}