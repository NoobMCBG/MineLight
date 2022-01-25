<?php
namespace DisableTNT;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\ExplosionPrimeEvent;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(TextFormat::GREEN . "Enable Plugin");
	}
	public function onExplosionPrime(ExplosionPrimeEvent $event){
		$event->setBlockBreaking(false);
	}
	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED . "Disable Plugin");
	}
}
