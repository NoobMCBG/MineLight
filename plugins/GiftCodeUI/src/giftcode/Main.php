<?php

namespace giftcode;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};

class Main extends PluginBase implements Listener {
    
    
public function onEnable() {
		$this->getLogger()->info("\n\n\n§l§4Plugin giftcode Code By LetTIHL\n\n\n");
        $this->getServer()->getPluginManager()->registerEvents($this ,$this);
       $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
@mkdir($this->getDataFolder(), 0744, true);
       $this->code = new Config($this->getDataFolder()."code.yml",Config::YAML);
}

	public function onCommand(CommandSender $sender, Command $command, String $label, array $args) : bool {
        switch($command->getName()){
            case "taocode":
                if($sender->isOp()){
                    $this->create($sender);
                }else{
                    $sender->sendMessage("§cLỗi Plugin Lỗi Yêu Cầu Cài Bản Mới");
                }
            break;
            case "nhapcode":
            $this->giftcode($sender);    
            return true;
        }
        return true;
	}
	 public function create($sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $sender, $data){
            if($data == null){
                return;
            }
            if($data[0] == null||$data[1] == null||$data[2] == null){
               $sender->sendMessage("§l§c•§e Bạn không được bỏ trống !");
               return;
            }            
 			if(!(is_numeric($data[1]))){
          $sender->sendMessage("§l§c•§e Không Thể Nhập Chữ Ở Đây");
              return;
			}           
            if(!($data[1] > 0)){
               $sender->sendMessage("§l§c•§e yêu cầu dữ liệu số > 1");
               return;
            }            
			    
                 if(!$this->code->exists($data[0])){
			$this->code->set(strtolower($data[0]), ["money" => $data[1], "count" => 1, "cm" => $data[2]]);
			$this->code->save();
          $sender->sendMessage("§l§c• §eTạo GiftCode §b".$data[0]."§e Thành Công");
                 }else{
                     $sender->sendMessage("§b§lGiftcode Đã Tồn tại");
                 }
        });
        $form->setTitle("§l§c•§9 Tạo Giftcode §c•");
		$form->addInput("§l§eNhập ID GiftCode:");
		$form->addInput("§l§eSố§a VNĐ§e Nhận Được Khi Nhập");
		$form->addInput("§l§eLệnh khi nhập code thành công");		
        $form->sendToPlayer($sender);
	}
	
	
	 public function giftcode($sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $sender, $data){
            if($data == null){
              return;  
            }

            if(!$this->code->exists($data[0])){
          $sender->sendMessage("§b§lgiftcode không tồn tại");
          return;
    }
            $money = $this->code->get(strtolower($data[0]))["money"];
            $count = $this->code->get(strtolower($data[0]))["count"];
            $command = $this->code->get(strtolower($data[0]))["cm"]; 
            $cm = str_replace(["{player}", "{pl}"], [$sender->getName(), ''], $command);
            if($count == 0){
              $sender->sendMessage("§l§c•§e GiftCode Này Bạn Đã Nhập Trước Đs Rồi !");
              return;
            }
			$this->code->set(strtolower($data[0]), ["money" => $money, "count" => 0, "cm" => $cm]);             $this->code->save;
            $this->money->addMoney($sender, $money);
            $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), $cm);
          $sender->sendMessage("§l§c•§e Sử dụng GiftCode Thành Công");
          $sender->sendMessage("§l§c•§e + §a$money VNĐ§e Vào Tài Khoản Của bạn");          
        });
        $form->setTitle("§l§c•§9 GiftCode §c•");
	$form->addInput("§l§c•§e Nhập GiftCode ở Đây");
        $form->sendToPlayer($sender);
	 }
}