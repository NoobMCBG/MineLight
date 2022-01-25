<?php

declare(strict_types=1);

namespace NhanAZ\CustomJoinSound;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Main extends PluginBase implements Listener {

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority HIGHEST
	 */
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$packet = new PlaySoundPacket();
		$packet->soundName = "random.levelup";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
		$player->sendDataPacket($packet);
	}
}
