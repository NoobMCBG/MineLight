<?php

namespace NoobMCBG\CreditsAPI;

use NoobMCBG\CreditsAPI\CreditsAPI;

use pocketmine\event\plugin\PluginEvent;

class CreditsEvent extends PluginEvent {
  
  public function __construct(CreditsAPI $main){
    $this->main = $main;
  }
}