<?php

namespace KNVN\QuyenVIP;

use pocketmine\command\{Command, CommandSender, ConsoleCommand};
use pocketmine\event\Listener;
use pocketmine\item\enchantment\{Enchantment, EnchantInstance};
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};

class Main extends PluginBase implements Listener{
   public $config;
   
   public function onEnable(): void{
    //Infor Plugin
    $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    
   //Please do not copy the idea, do not change the creator's name and copyright!
    $this->getLogger()->info("§l§a-§b-§c-§f-§r-");
    $this->getLogger()->info("Plugin đã được bật!");
    $this->getLogger()->info("§c Author By ClickedTran");
    $this->getLogger()->info("§c Copyright By KingNightVN");
    $this->getLogger()->info("§l§a-§b-§c-§f-§r-");
    $this->getLogger()->info("§c Don't Copy Idea, Edit Author And Copyright! ");
    //copyrightbyKingNightVN!
    
    //Config
    @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->saveDefaultConfig();
        $this->reloadConfig();
   }
   
   public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args): bool{
     if($sender instanceof Player){
        switch(strtolower($cmd->getName())){
                case "quyenvip":
                $this->quyenvip($sender);
                break;
                case "qvip":
                $this->quyenvip($sender);
                break;
      }
        }else{
                $sender->sendMessage("Vui Lòng Dùng Lệnh Ở Trong Game!");
         }
                return true;
    }
      
   public function quyenvip($sender){
   $form = new SimpleForm(function(Player $sender, $data){
         $result = $data;
       if($result == null){
        return true;
       }
      switch($result){
      case 0:
      break;
     }
    });
    $form->setTitle("§a§l ♦§eQuyền §cLIGHT§a§l ♦");
    $form->setContent("§l§aĐây Là Các Đặc Quyền Của Light
 \n\n§l§cLight§f §eI§f: \n§f - Fly (/fly)\n§f - Food (/food)\n§f - Health (/heal)
      \n§l§cLight§f §eII§f: \n§f - Fly (/fly)\n§f - Teleport (/tp)\n§f - Food (/food)\n§f - Health (/heal)
      \n§l§cLight§f §eIII§f: \n§f - Fly (/fly)\n§f - Teleport (/tp)\n§f - Food (/food)\n§f - Health (/heal)\n§f - Jump (/jump)\n§f - Speed (/speed)
      \n§l§cLight§f §eIV§f: \n§f - Fly (/fly)\n§f - Teleport (/tp)\n§f - Food (/food)\n§f - Health (/heal)\n§f - Jump (/jump)\n§f - Speed (/speed)\n§f - Night Vision (/night)
      \n§l§cLight§f §eV§f: \n§f - Fly (/fly)\n§f - Teleport (/tp)\n§f - Food (/food)\n§f - Health (/heal)\n§f - Jump (/jump)\n§f - Speed (/speed)\n§f - Night Vision (/night)\n§f - Nickname (/nick)\n§f - Time (/time)
      \n§l§cLight§f §eVI§f: \n§f - Fly (/fly)\n§f - Teleport (/tp)\n§f - Food (/food)\n§f - Health (/heal)\n§f - Jump (/jump)\n§f -Speed (/speed)\n§f - Night Vision (/night)\n§f - Nickname (/nick)\n§f - Time (/time)\n§f - X3 EXP Mine (/pickaxeleveling)
      \n§l§cLight§f §eVII§f: \n§f - Fly (/fly)\n§f - Teleport (/tp)\n§f - Food (/food)\n§f - Health (/heal)\n§f - Jump (/jump)\n§f - Speed (/speed)\n§f - Night Vision (/night)\n§f - Nickname (/nick)\n§f - Time (/time)\n§f - X3 EXP Mine (/pickaxeleveling)\n§f - Size (/size)\n§f - Wings (/wings)"
   );
    $form->addButton("§l§a♦ §cTHOÁT §a♦");
    $form->sendToPlayer($sender);
   }
}