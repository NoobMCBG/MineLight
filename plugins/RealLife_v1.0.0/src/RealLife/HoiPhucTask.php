<?php

namespace RealLife;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\Player;
use RealLife\Main as RealLife;
use pocketmine\command\{Command, CommandSender};
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

class HoiPhucTask extends Task{


    public function __construct(RealLife $plugin){

        $this->plugin = $plugin;
    }

    public function onRun($currentTick){
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $name = $player->getName();
            $api = $this->plugin->getServer()->getPluginManager()->getPlugin("HoiPhuc");
            if($api->isSitting($player)){
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(57)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(7)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(22)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(133)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(2)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(5)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(145)));
                $player->getLevel()->addParticle(new DestroyBlockParticle($player->add(0,0.6,0), Block::get(169)));
                $hp = $player->getHealth()/$player->getMaxHealth()*100;
                $ta =  100 - ($player->getFood()/20*100);
                $player->addEffect(new EffectInstance(Effect::getEffect(6), 40, 2));
                $player->setFood(1);
                $player->setFood(2);
                $player->setFood(3);
                $player->setFood(4);
                $player->setFood(5);
                $player->setFood(6);
                $player->setFood(7);
                $player->setFood(8);
                $player->setFood(9);
                $player->setFood(10);
                $player->setFood(11);
                $player->setFood(12);
                $player->setFood(13);
                $player->setFood(14);
                $player->setFood(15);
                $player->setFood(16);
                $player->setFood(17);
                $player->setFood(18);
                $player->setFood(19);
                $player->setFood(20);
                $player->sendPopup("§l§c☞§a Đang Hồi Phục §c☜\n§l§c❤ §f".$hp."%  §f".$ta."§b%%%");
                return;
            }
        }
	}
}