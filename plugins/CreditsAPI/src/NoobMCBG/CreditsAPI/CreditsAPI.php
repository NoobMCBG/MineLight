<?php

namespace NoobMCBG\CreditsAPI;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use NoobMCBG\CreditsAPI\CreditsChangeEvent;
use NoobMCBG\CreditsAPI\CreditsEvent;

class CreditsAPI extends PluginBase implements Listener {
  
  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->credits = new Config($this->getDataFolder() . "credits.yml", Config::YAML);
  }
  
  public function onDisable(){
    $this->credits->save();
  }
  
  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    if(!$this->credits->exists($player->getName())){
      $this->credits->set($player->getName(), 0);
      $this->credits->save();
      $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
    }
  }
  
  public function reduceCredits($player, $credits){
    if($player instanceof Player){
      if(is_numeric($credits)){
         $this->credits->set($player->getName(), ($this->credits->get($player->getName()) - $credits));
         $this->credits->save();
         $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
      }
    }
  }
  
  public function addCredits($player, $credits){
    if($player instanceof Player){
      if(is_numeric($credits)){
         $this->credits->set($player->getName(), ($this->credits->get($player->getName()) + $credits));
         $this->credits->save();
         $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
      }
    }
  }
  
  public function myCredits($player){
    if($player instanceof Player){
      
      return ($this->credits->get($player->getName()));
    }
  }
  
  public function getAllCredits(){
    return $this->credits->getAll();
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool{
    switch($cmd->getName()){
      case "mycredits":
        if($sender instanceof Player){
          $credits = $this->myCredits($sender);
          $sender->sendMessage("§l§c•§e Só Credits Của Bạn:§f $credits Credits");
        }else{
          $sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Game !");
        }
        break;
        
        case "setcredits":
          if($sender instanceof Player){
            if($sender->hasPermission("creditsapi.command.setcredits")){
              if(isset($args[0])){
                if(isset($args[1])){
                  $player = $this->getServer()->getPlayer($args[0]);
                  if(!is_numeric($args[1])){
                    $sender->sendMessage("§cPlease enter the amount in digits!");
                    return true;
                  }
                  if(!$player instanceof Player){
                    $sender->sendMessage("§cPlayer " . $args[0] . " not online!");
                    return true;
                  }
                  
                  $this->credits->set($player->getName(), $args[1]);
                  $this->credits->save();
                  $sender->sendMessage("§l§c•§e Đã Chỉnh Số Credits Của Người Chơi§a " . $args[0] . "§e Thành§f " . $args[1] . " Credits");
                  $player->sendMessage("§l§c•§e Số Credits Của Bạn Đã Được Chỉnh Thành:§f " . $args[1] . " Credits");
                  $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
                }else{
                  $sender->sendMessage("§l§c•§e Sử Dụng:§b /setcredits <player> <credits>");
                }
              }else{
                $sender->sendMessage("§l§c•§e Sử Dụng:§b /setcredits <player> <credits>");
              }
            }
          }else{
            $sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Game !");
          }
          break;
          case "givecredits":
          if($sender instanceof Player){
            if($sender->hasPermission("creditsapi.command.givecredits")){
              if(isset($args[0])){
                if(isset($args[1])){
                  $player = $this->getServer()->getPlayer($args[0]);
                  if(!is_numeric($args[1])){
                    $sender->sendMessage("§cPlease enter the amount in digits!");
                    return true;
                  }
                  if(!$player instanceof Player){
                    $sender->sendMessage("§cPlayer " . $args[0] . " not online!");
                    return true;
                  }
                  
                  $this->addCredits($player->getName(), $args[1]);
                  $sender->sendMessage("§l§c•§e Đã Lấy§f " . $args[1] . " Credits§e Cho Người Chơi§b " . $args[0]);
                  $player->sendMessage("§l§c•§e Bạn Đã Nhận Được:§f " . $args[1] . " Credits");
                  $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
                }else{
                  $sender->sendMessage("§l§c•§e Sử Dụng:§b /givecredits <player> <credits>");
                }
              }else{
                $sender->sendMessage("§l§c•§e Sử Dụng:§b /givecredits <player> <credits>");
              }
            }
          }else{
            $sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Game !");
          }
          break;
          case "topcredits":
            $creditsall = $this->getAllCredits();
            arsort($creditsall);
            $creditsall = array_slice($creditsall, 0, 9);
            $top = 1;
            foreach($creditsall as $name => $count){
              $sender->sendMessage("§l§c•§e TOP§d " . $top . "§a " . $name . " §evới§f " . $count . " Credits");
              $top++;
            }
            break;
            
            case "paycredits":
              if($sender instanceof Player){
                if(isset($args[0])){
                  if(isset($args[1])){
                    $player2 = $this->getServer()->getPlayer($args[0]);
                    $credits = $this->myCredits($sender);
                    if(!$player2 instanceof Player){
                      $sender->sendMessage("§l§c•§e Người Chơi§b " . $args[0] . " §eKhông Online !");
                      return true;
                    }
                    if(!is_numeric($args[1])){
                      $sender->sendMessage("§l§c•§e Số §fCredits§e Chuyển Bắt Buộc Phải Là Số !");
                      return true;
                    }
                    if($args[0] === $sender->getName()){
                      $sender->sendMessage("§l§c•§e Bạn Không Thể Chuyển§f Credits§e Cho Chính Mình !");
                      return true;
                    }
                    if($credits >= $args[1]){
                      $this->reduceCredits($sender, $args[1]);
                      $this->addCredits($player2, $args[1]);
                      $sender->sendMessage("§l§c•§e Đã Chuyển Thành Công§f " . $args[1] . " Credits§e Cho Người Chơi§b " . $args[0]);
                      $player2->sendMessage("§l§c•§e Người Chơi§b " . $sender->getName() . " §eVừa Chuyển Cho Bạn§f " . $args[1] . " Credits");
                    }else{
                      $sender->sendMessage("§l§c•§e Bạn Không Đủ Credits");
                      return true;
                    }
                  }else{
                    $sender->sendMessage("§l§c•§e Sử Dụng:§b /paycredits <player> <credits>");
                  }
                }else{
                  $sender->sendMessage("§l§c•§e Sử Dụng:§b /paycredits <player> <credits>");
                }
              }else{
                $sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Game !");
              }
              break;
    }
    return true;
  }
}
