<?php

declare(strict_types=1);

namespace M82B;

use M82B\item\M82B;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\inventory\Inventory;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use ReflectionClass;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;
use const pocketmine\RESOURCE_PATH;

class Main extends PluginBase implements Listener {

    public $time;

    public function onEnable(){
        $this->cooldown = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new CooldownTask($this), 20 * 1);
    }

    public function onUse(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getCustomName() == "M82B"){
            $this->cooldown->set(strtolower($player->getName()), 2);
            $player->sendPopup("§l§a|§c||||||||");
            sleep(0.2);
            $player->sendPopup("§l§a||§c|||||||");
            sleep(0.2);
            $player->sendPopup("§l§a|||§c||||||");
            sleep(0.2);
            $player->sendPopup("§l§a||||§c|||||");
            sleep(0.2);
            $player->sendPopup("§l§a|||||§c||||");
            sleep(0.2);
            $player->sendPopup("§l§a||||||§c||||");
            sleep(0.2);
            $player->sendPopup("§l§a|||||||§c|||");
            sleep(0.2);
            $player->sendPopup("§l§a||||||||§c||");
            sleep(0.2);
            $player->sendPopup("§l§a|||||||||§c|");
            sleep(0.2);
            $player->sendPopup("§l§a|||||||||");
            $packet = new PlaySoundPacket();
            $packet->soundName = "random.explode";
            $packet->x = $player->getX();
            $packet->y = $player->getY();
            $packet->z = $player->getZ();
            $packet->volume = 1;
            $packet->pitch = 1;
            $player->sendDataPacket($packet);     
        }
    }
}