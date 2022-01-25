<?php

namespace NoobMCBG\SettingUI;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use jojoe77777\FormAPI;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener {
    
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
        switch($cmd->getName()){
        case "setting":
        if(!($sender instanceof Player)){
                $sender->sendMessage("§cVui lòng dùng lệnh trong Game");
                return true;
        }
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                    case 0:
                    $this->ScoreHud($sender);
                        break;
                    case 1:
                        break;
            }
        });
        $form->setTitle("§l§3♣ §2Cài Đặt§3 ♣");
        $form->setContent("§l§eHãy Chọn Các Nút Để Cài Đặt Bên Dưới");
        $form->addButton("§l§c•§9 Bảng Thông Tin §c•", 0, "textures/other/edit");
        $form->addButton("§l§c•§9 Thoát Cài Đặt §c•", 0, "textures/other/exit");
        $form->sendToPlayer($sender);
        }
return true;
}
     public function ScoreHud(Player $player){ 
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                    case 0:
					$command = "sh on";
					             $this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
                    case 1:
					$command = "sh off";
					             $this->getServer()->getCommandMap()->dispatch($sender, $command);
                        break;
                    case 2:
                        break;
            }
        });
        $form->setTitle("§l§3♣ §2Cài Đặt§3 ♣");
        $form->setContent("§l§eHãy Chọn Các Nút Để Cài Đặt Bên Dưới");
        $form->addButton("§l§c•§2 Bật Bảng Thông Tin §c•", 0, "textures/other/on");
        $form->addButton("§l§c•§4 Tắt Bảng Thông Tin §c•", 0, "textures/other/off");
        $form->addButton("§l§c•§9 Thoát Cài Đặt §c•", 0, "textures/other/exit");       
        $form->sendToPlayer($player);
        }
}