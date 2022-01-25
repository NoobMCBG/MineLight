<?php

namespace phuongaz\CustomWings\Entitties;

use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class DropEntity extends Human{

	private $name = "";

    protected function initEntity() : void{
	    $this->setMaxHealth(1);
	    parent::initEntity();
    }

    public function hasMovementUpdate() : bool{
        return false;
    }

    public function setCTName(string $name = ""){
    	$this->name = $name;
    }

    public function attack(EntityDamageEvent $source) : void{
        $source->setCancelled();
        $this->kill();
		parent::attack($source);
    }

	public function onUpdate(int $currentTick): bool {
		$dist = 2;
		foreach($this->level->getPlayers() as $entity){
			if(($d = $entity->distanceSquared($this)) < $dist and $entity->getName() !== "phuongaz"){
				$player = $entity;
				$this->setNameTag($this->name);
				$player->sendMessage("Đã nhặt được cánh ". $this->name);
				Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "setuperm ". $player->getName(). " ".$this->name.".wing");
				$this->flagForDespawn();
			}
		}
		$this->yaw = $this->yaw + 3;
		$this->updateMovement();
		parent::onUpdate($currentTick);
		return !$this->closed;
	}
}