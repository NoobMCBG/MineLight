<?php

namespace KOB;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\{Command, CommandSender};
use pocketmine\{Player, Server};
use pocketmine\item\Item;
use pocketmine\block\Block;
use KOB\task\OffKOB;
class Main extends PB implements Listener
{

    /** @Var Config */
    private $cfg;
    /** @var Main */
    public static $api;
    public $time;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->time = new Config($this->getDataFolder() . "time.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new OffKOB($this), 20 * 60);
        self::$api = $this;
    }

    public static function getAPI(): Main
    {
        return self::$api;
    }
      public function onCommand(CommandSender $s, Command $cmd, string $label, array $args):bool
    {   
        if($cmd->getName() == 'kingofblock'){
            if (!$s->hasPermission("kingofblock.command")) {
$s->sendMessage("§cYou do not have permission to use this command");
return true;
}else{
            $this->formopen($s);
        }
        return true;
    }
}
    public function formopen(Player $player)
    {
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createSimpleForm(function (Player $player, $data) {
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                case 0:
                    break;
                case 1:
                    $this->config->set(strtolower($player->getName()), "on");
                    $this->config->save();
                    $this->time->set(strtolower($player->getName()), 60);
                    $this->time->save();
                    $player->sendMessage("§l§c●§e Kĩ Năng§a KingOfBlock§e Của Bạn Đã Được§a bật.");
                    break;
                case 2:
                    $this->config->set(strtolower($player->getName()), "off");
                    $this->config->save();
                    $all = $this->time->getAll();
                    unset($all[strtolower($player->getName())]);
                    $this->time->setAll($all);
                    $this->time->save();
                    $player->sendMessage("§l§c●§e Kĩ Năng§a KingOfBlock§e Của Bạn Đã Được§c Tắt.");
                    break;
            }
        });
        $form->setTitle("§l§c●§9 KingOfBlock §c●");
        $form->addButton("§l§c●§9 Thoát Menu §c●");
        $form->addButton("§l§c●§2 Bật Kĩ Năng §c●");
        $form->addButton("§l§c●§4 Tắt Kĩ Năng §c●");
        $form->sendToPlayer($player);
    }
}