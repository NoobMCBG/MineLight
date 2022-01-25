<?php

namespace NoobMCBG\CreditsAPI;

use NoobMCBG\CreditsAPI\CreditsAPI;

use pocketmine\Player;

use NoobMCBG\CreditsAPI\CreditsEvent;

class CreditsChangeEvent extends CreditsEvent{
  
  public function __construct(CreditsAPI $main, $player){
    $this->main = $main;
    $this->player = $player;
  }
  
  public function getPlayer(){
    return $this->player;
  }
  
  public function getCredits(){
    return $this->main->myCredits($this->player);
  }
}