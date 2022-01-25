<?php

namespace NoobMCBG\Eternity;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\sound\Sound;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\command\Command;
use pocketmine\command\Commandsender;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\Armor;
use NoobMCBG\Eternity\task\OffTask;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener {
    /** @var Config */
    private $cfg;
    /** @var Main */
    public static $api;
    public $cooldown;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->cooldown = new Config($this->getDataFolder() . "cooldown.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new OffTask($this), 20 * 60);
        self::$api = $this;
    }

      public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool {   
        switch($cmd->getName()){
            case "eternity":
                if(!$sender instanceof Player){
                    $sender->sendMessage("§cPlease run command in-game");
                    return true;
                }
             if(!$sender->hasPermission("eternity.command")){
                 $sender->sendMessage("§l§cYou not permission to use command!");
             }
             $this->eternity($sender);
             break;
        }
             return true;
    }

    public static function getAPI(): Main {
        return self::$api;
    }	
    
    public function eternity($player){
        $form = new SimpleForm(function(Player $player, $data){
            if($data == null){
                return true;
            }
            switch($data){
                case 0:
                break;
                case 1:
                    $this->config->set(strtolower($player->getName()), "on");
                    $this->config->save();
                    $this->cooldown->set(strtolower($player->getName()), 60);
                    $this->cooldown->save();
                    $player->sendMessage("§l§c•§e Kĩ Năng§a Eternity§e Của Bạn Đã Được§a Bật");
                break;
                case 2:
                    $this->config->set(strtolower($player->getName()), "off");
                    $this->config->save();
                    unset($this->cooldown->getAll()[strtolower($player->getName())]);
                    $this->cooldown->save();
                    $player->sendMessage("§l§c•§e Kĩ Năng§a Eternity§e Của Bạn Đã Được§c Tắt");
                break;
            }
        });
        $form->setTitle("§l§c•§9 Eternity §c•");
        $form->addButton("§l§c•§9 Thoát Menu §c•");
        $form->addButton("§l§c•§2 Bật Kĩ Năng §c•");
        $form->addButton("§l§c•§4 Tắt Kĩ Năng §c•");
        $form->sendToPlayer($player);
    }
}
