<?php

namespace AlvinMask\DinoUI;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\entity\Skin;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChangeSkinEvent;

use AlvinMask\DinoUI\Form\CustomForm;

class Main extends PluginBase implements Listener{

    const LIST_DINO = ["§f§lKomodo", "§f§lQuadruped", "§f§lDiplodocus", "§f§lRobosaurus", "§c§lTắt Skin"];

    public $skin = [];

    public function onEnable(){
     $this->getServer()->getLogger()->Info(base64_decode("UGx1Z2luIERpbm9VSSBEaWJ1YXQgT2xlaCBBbHZpbk1hc2s="));
     if(($this->getDescription()->getAuthors()[0] !== base64_decode("QWx2aW5NYXNr")) || ($this->getDescription()->getName() !== base64_decode("RGlub1VJ"))){
      $this->getServer()->getLogger()->Info(base64_decode("TWFhZiBBbmRhIFRlbGFoIE1lbmd1YmFoIFViYWggUGx1Z2luIERpbm9VSQ=="));
      $this->getServer()->shutdown();
     }
     $this->getServer()->getPluginManager()->registerEvents($this, $this);
     foreach(["komodo.txt", "quadruped.txt", "diplodocus.txt", "robosaurus.txt", "geometry.json"] as $file){
      $this->saveResource($file);
     }
    }

    public function onCommand(CommandSender $p, Command $commad, string $label, array $args):bool{
     switch($commad->getName()){
      case "dinoui":{
       if($p instanceof Player){
        if($p->isOp()){
         $form = new CustomForm(function(Player $p, $result){
          if($result === null){
           return;
          }
          switch($result[0]){
           case 0:{
            $name = "Komodo";
            break;
           }
           case 1:{
            $name = "Quadruped";
            break;
           }
           case 2:{
            $name = "Diplodocus";
            break;
           }
           case 3:{
            $name = "Robosaurus";
            break;
           }
           case 4:{
            $name = null;
            break;
           }
          }
          if($name === null){
           $p->setSkin($this->skin[$p->getName()]);
           $p->sendSkin();
           $p->sendMessage("§f§l•§e Bạn Đã Tắt Skin§f".$name.".");
           return false;
          }
          $p->setSkin(new Skin($p->getSkin()->getSkinId(), base64_decode(file_get_contents($this->getDataFolder().strtolower($name).".txt")), "", "geometry.".strtolower($name), file_get_contents($this->getDataFolder()."geometry.json")));
          $p->sendSkin();
          $p->sendMessage("§f§l•§e Bạn Đã Chỉnh Skin Thành §r§f".$name.".");
         });
         $form->setTitle("§f§l•§bMenu DinoUI§f•");
         $form->addDropdown("§e§lChọn Dino§r§f:", self::LIST_DINO);
         $p->sendForm($form);
        }else{
         $p->sendMessage("§e Chỉ Có OP Mới Được Sử Dụng Lệnh Này§f!");
        }
       }else{
        $p->sendMessage("§c§lVui Lòng Sử Dụng Lệnh Này Trong Sever§r§f!");
       }
       break;
      }
     }
     return true;
    }

    public function onPlayerJoin(PlayerJoinEvent $e){
     $p = $e->getPlayer();
     $this->skin[$p->getName()] = $p->getSkin();
    }

    public function onPlayerQuit(PlayerQuitEvent $e){
     $p = $e->getPlayer();
     unset($this->skin[$p->getName()]);
    }

    public function onPlayerChangeSkin(PlayerChangeSkinEvent $e){
     $p = $e->getPlayer();
     $this->skin[$p->getName()] = $p->getSkin();
    }

}