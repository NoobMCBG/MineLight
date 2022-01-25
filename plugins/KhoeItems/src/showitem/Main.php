<?php

namespace showitem;

use pocketmine\Player;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\command\Command;
use pocketmine\command\Commandsender;
use pocketmine\utils\Config;
use libs\muqsit\invmenu\InvMenu;
use libs\muqsit\invmenu\InvMenuHandler;

class Main extends PB {
	
	public $item;
	
	public function onEnable():void{
		$this->menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		for($i = 0;$i<=5;$i++)$this->item[$i] = null;
		$this->getLogger()->info("§l§c Plugin KhoeItems code by LetTIHL");
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
         
	}

	public function onCommand(CommandSender $player, Command $command, String $label, array $args) : bool {
        switch($command->getName()){
            case "showitem":
                $name = $player->getName();
                $item = $player->getInventory()->getItemInHand();
                $cus = $item->getCustomName();
                $this->getServer()->broadcastMessage("§l§c•§e Người Chơi §b$name §eVừa Khoe item§b $cus §b/seeitem §eđể xem");
                $this->item[4] = $this->item[3]; 
                $this->item[3] = $this->item[2];                     
                $this->item[2] = $this->item[1];
                $this->item[1] = $this->item[0];
                $lore = $item->getLore();
                if($lore == null){
                $item->setLore(array("§l§c•§e Item của §b$name "));
                }else $item->setLore(array($lore[0],"§l§c•§e Item của §b$name "));
                $this->item[0] = $item;
            break;
            case "seeitem":
                $this->menu->readonly();
                $this->menu->setName("§e§lItem");
                $inventory = $this->menu->getInventory();
                for($i = 0; $i<=4;$i++){
                    if(!$this->item[$i] == null)$inventory->setItem($i, $this->item[$i]);
                }
                $this->menu->send($player);
           return true;
        }
        return true;
	}
}