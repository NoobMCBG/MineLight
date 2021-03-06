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
          $sender->sendMessage("??l??c?????e S?? Credits C???a B???n:??f $credits Credits");
        }else{
          $sender->sendMessage("??l??c?????e H??y S??? D???ng L???nh Trong Game !");
        }
        break;
        
        case "setcredits":
          if($sender instanceof Player){
            if($sender->hasPermission("creditsapi.command.setcredits")){
              if(isset($args[0])){
                if(isset($args[1])){
                  $player = $this->getServer()->getPlayer($args[0]);
                  if(!is_numeric($args[1])){
                    $sender->sendMessage("??cPlease enter the amount in digits!");
                    return true;
                  }
                  if(!$player instanceof Player){
                    $sender->sendMessage("??cPlayer " . $args[0] . " not online!");
                    return true;
                  }
                  
                  $this->credits->set($player->getName(), $args[1]);
                  $this->credits->save();
                  $sender->sendMessage("??l??c?????e ???? Ch???nh S??? Credits C???a Ng?????i Ch??i??a " . $args[0] . "??e Th??nh??f " . $args[1] . " Credits");
                  $player->sendMessage("??l??c?????e S??? Credits C???a B???n ???? ???????c Ch???nh Th??nh:??f " . $args[1] . " Credits");
                  $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
                }else{
                  $sender->sendMessage("??l??c?????e S??? D???ng:??b /setcredits <player> <credits>");
                }
              }else{
                $sender->sendMessage("??l??c?????e S??? D???ng:??b /setcredits <player> <credits>");
              }
            }
          }else{
            $sender->sendMessage("??l??c?????e H??y S??? D???ng L???nh Trong Game !");
          }
          break;
          case "givecredits":
          if($sender instanceof Player){
            if($sender->hasPermission("creditsapi.command.givecredits")){
              if(isset($args[0])){
                if(isset($args[1])){
                  $player = $this->getServer()->getPlayer($args[0]);
                  if(!is_numeric($args[1])){
                    $sender->sendMessage("??cPlease enter the amount in digits!");
                    return true;
                  }
                  if(!$player instanceof Player){
                    $sender->sendMessage("??cPlayer " . $args[0] . " not online!");
                    return true;
                  }
                  
                  $this->addCredits($player->getName(), $args[1]);
                  $sender->sendMessage("??l??c?????e ???? L???y??f " . $args[1] . " Credits??e Cho Ng?????i Ch??i??b " . $args[0]);
                  $player->sendMessage("??l??c?????e B???n ???? Nh???n ???????c:??f " . $args[1] . " Credits");
                  $this->getServer()->getPluginManager()->callEvent(new CreditsChangeEvent($this, $player));
                }else{
                  $sender->sendMessage("??l??c?????e S??? D???ng:??b /givecredits <player> <credits>");
                }
              }else{
                $sender->sendMessage("??l??c?????e S??? D???ng:??b /givecredits <player> <credits>");
              }
            }
          }else{
            $sender->sendMessage("??l??c?????e H??y S??? D???ng L???nh Trong Game !");
          }
          break;
          case "topcredits":
            $creditsall = $this->getAllCredits();
            arsort($creditsall);
            $creditsall = array_slice($creditsall, 0, 9);
            $top = 1;
            foreach($creditsall as $name => $count){
              $sender->sendMessage("??l??c?????e TOP??d " . $top . "??a " . $name . " ??ev???i??f " . $count . " Credits");
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
                      $sender->sendMessage("??l??c?????e Ng?????i Ch??i??b " . $args[0] . " ??eKh??ng Online !");
                      return true;
                    }
                    if(!is_numeric($args[1])){
                      $sender->sendMessage("??l??c?????e S??? ??fCredits??e Chuy???n B???t Bu???c Ph???i L?? S??? !");
                      return true;
                    }
                    if($args[0] === $sender->getName()){
                      $sender->sendMessage("??l??c?????e B???n Kh??ng Th??? Chuy???n??f Credits??e Cho Ch??nh M??nh !");
                      return true;
                    }
                    if($credits >= $args[1]){
                      $this->reduceCredits($sender, $args[1]);
                      $this->addCredits($player2, $args[1]);
                      $sender->sendMessage("??l??c?????e ???? Chuy???n Th??nh C??ng??f " . $args[1] . " Credits??e Cho Ng?????i Ch??i??b " . $args[0]);
                      $player2->sendMessage("??l??c?????e Ng?????i Ch??i??b " . $sender->getName() . " ??eV???a Chuy???n Cho B???n??f " . $args[1] . " Credits");
                    }else{
                      $sender->sendMessage("??l??c?????e B???n Kh??ng ????? Credits");
                      return true;
                    }
                  }else{
                    $sender->sendMessage("??l??c?????e S??? D???ng:??b /paycredits <player> <credits>");
                  }
                }else{
                  $sender->sendMessage("??l??c?????e S??? D???ng:??b /paycredits <player> <credits>");
                }
              }else{
                $sender->sendMessage("??l??c?????e H??y S??? D???ng L???nh Trong Game !");
              }
              break;
    }
    return true;
  }
}
