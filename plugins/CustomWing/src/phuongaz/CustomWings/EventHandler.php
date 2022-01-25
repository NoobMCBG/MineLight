<?php

namespace phuongaz\CustomWings;

use pocketmine\event\player\PlayerDeathEvent;

use pocketmine\event\Listener;

Class EventHandler implements Listener{

	public function __construct(){

	}

	public function onDeath(PlayerDeathEvent $event) :void{
		$player = $event->getPlayer();
		Loader::getInstance()->removeWing($player);
	}
}