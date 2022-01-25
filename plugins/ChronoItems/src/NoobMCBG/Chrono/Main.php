<?php

namespace NoobMCBG\Chrono;

use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use pocketmine\utils\TextFormat as TF; 

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent as CL;

use pocketmine\command\{CommandSender, Command, ConsoleCommandSender};
use pocketmine\utils\Config;

use pocketmine\item\Item;
use pocketmine\inventory\Inventory;

class Main extends PB implements L {
		
	public $cfg;
    public static $api;
    public $time;
    public $mode;
	
	public function onLoad(){
		$this->getLogger()->info("Loading Plugin");
	}
	
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	    $this->user = new Config($this->getDataFolder() . "item.yml", Config::YAML);
		$this->saveResource("config.yml");
		$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->mode = new Config($this->getDataFolder() . "mode.yml", Config::YAML);
        $this->time = new Config($this->getDataFolder() . "time.yml", Config::YAML);
		$cfg->save();
		$this->getLogger()->info("Enable Plugin");
        $this->getScheduler()->scheduleRepeatingTask(new OffTask($this), 20 * 1); //1 giây
	}
	
	public function onDisable() {
		$this->getLogger()->info("Disable Plugin");
	}

    public static function getAPI(): Main {
        return self::$api;
    }
	
	public function getNameItem($player){
		if($player instanceof Player){
	        $name = $player->getName();
		}
		$this->user->load($this->getDataFolder() . "item.yml", Config::YAML);
		$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$cfg->save();
	    $itemname = $cfg->get("item-name");
		return $itemname;
	}
	
	public function getLore(){
	    $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
	    $cfg->save();
	    $lore = $cfg->get("item-lore");
	    return $lore;
    }
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
                $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$cfg->save();
		switch($cmd->getName()){
		       case "giveshield":
                   if(!$sender->hasPermission("giveshield.command")){
                       $sender->sendMessage($cfg->get("no-permission"));
                   }
                   if(!isset($args[0])){
                       $sender->sendMessage("§l§eSử Dụng:§b /giveshield <player>");
                       return true;
                   }else{
                      $player = $this->getServer()->getPlayer($args[0]);
                      if(!$player == null){
                          if($player->isOnline()) {
                              $p = $player;
                              $inv = $player->getInventory();
			                  $item = Item::get(342, 0, 1);
                              $itemname = $this->getNameItem($player);
                              $item->setCustomName($itemname);
                              $item->setLore(array($this->getLore()));
                              $inv->addItem($item);
                          }
                      }
                   }
             break;
        }
		return true;
	}
	
	 public function setItem(CL $event){
		$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$cfg->save();
		$player = $event->getPlayer();
        $name = $player->getName();
		$item = $player->getInventory()->getItemInHand();
		if ($item->getCustomName() == $cfg->get("item-name")){
                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "setuperm ".$player." buildertools.command.hsphere");
                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "setuperm ".$player." buildertools.command.undo");
		            $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "rca ".$name." /hsphere 241:2 4");
                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "unsetuperm ".$player." buildertools.command.hsphere");
                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "unsetuperm ".$player." buildertools.command.undo");
                    $inventory = $player->getInventory();
            	    $player->getInventory()->removeItem($item);
                    $this->mode->set(strtolower($player->getName()), "on");
                    $this->mode->save();
                    $this->time->set(strtolower($player->getName()), 8);
                    $this->time->save();
		}
	}
}
