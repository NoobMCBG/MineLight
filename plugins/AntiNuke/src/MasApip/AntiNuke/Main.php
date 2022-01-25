<?php

namespace MasApip\AntiNuke;

use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

	private $breakTimes = [];

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onPlayerInteract(PlayerInteractEvent $event) : void{
		if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
			$this->breakTimes[$event->getPlayer()->getRawUniqueId()] = floor(microtime(true) * 20);
		}
	}

	public function onBlockBreak(BlockBreakEvent $event) : void{
		if(!$event->getInstaBreak()){
			do{
				$player = $event->getPlayer();
				if(!isset($this->breakTimes[$uuid = $player->getRawUniqueId()])){
					foreach($this->getServer()->getOnlinePlayers() as $staff){
						if($player->hasPermission("antinuke.notice")){
							$staff->sendMessage("§e§l•§a Người Chơi {$player->getName()} §a Đập Block Lan Rộng, Nghi Ngờ Về Nuke");
						}
					}
					$this->getLogger()->debug("§l§e• §aNgười Chơi§c " . $player->getName() . "§a Đập Block Lan Rộng, Nghi Ngờ Về Nuke ");
					$event->setCancelled();
					break;
				}

				$target = $event->getBlock();
				$item = $event->getItem();

				$expectedTime = ceil($target->getBreakTime($item) * 6);

				if($player->hasEffect(Effect::HASTE)){
					$expectedTime *= 1 - (0.2 * $player->getEffect(Effect::HASTE)->getEffectLevel());
				}

				if($player->hasEffect(Effect::MINING_FATIGUE)){
					$expectedTime *= 1 + (0.3 * $player->getEffect(Effect::MINING_FATIGUE)->getEffectLevel());
				}

				$expectedTime -= 1;

				$actualTime = ceil(microtime(true) * 20) - $this->breakTimes[$uuid = $player->getRawUniqueId()];

				if($actualTime < $expectedTime){
					foreach($this->getServer()->getOnlinePlayers() as $staff){
						if($player->hasPermission("antihack.notice")){
							$staff->sendMessage("§e§l•§a Người Chơi§c {$player->getName()}§a Đào Block Rất Nhanh, Có Thể Là Haste");
						}
					}
					$this->getLogger()->debug("§l§e•§aNgười Chơi §c" . $player->getName() . "§a Đào Block Rất Nhanh, Có Thể Là Haste");
					$event->setCancelled();
					break;
				}

				unset($this->breakTimes[$uuid]);
			}while(false);
		}
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		unset($this->breakTimes[$event->getPlayer()->getRawUniqueId()]);
	}
}
