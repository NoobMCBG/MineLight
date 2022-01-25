<?php

declare(strict_types=1);

namespace UIShop;

use onebone\economyapi\EconomyAPI;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Server;
use pocketmine\utils\Config;
use UIShop\libs\jojoe77777\FormAPI\SimpleForm;
use pocketmine\utils\TextFormat as TF;
use UIShop\libs\jojoe77777\FormAPI\CustomForm;
use UIShop\libs\JackMD\ConfigUpdater\ConfigUpdater;

//   _____       _ _         _____ _          _ _____
//  / ____|     | | |       |  __ (_)        | |  __ \
// | (___   __ _| | |_ _   _| |__) |__  _____| | |  | | _____   ______
//  \___ \ / _` | | __| | | |  ___/ \ \/ / _ \ | |  | |/ _ \ \ / /_  /
//  ____) | (_| | | |_| |_| | |   | |>  <  __/ | |__| |  __/\ V / / /
// |_____/ \__,_|_|\__|\__, |_|   |_/_/\_\___|_|_____/ \___| \_/ /___|
//                      __/ |
//                     |___/

class Main extends PluginBase
{

    public $msg;
    // For Config Updates
    private const CONFIG_VERSION = 1;

    // For shop.yml Updates! (Changes more xD)
    private const SHOP_VERSION = 1;

    private const MESSAGE_VERSION = 1;

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        $this->saveResource("shop.yml");
        $this->checkConfigs();
    }

    public function checkConfigs(): void
    {
        if ((!$this->getConfig()->exists("config-version")) || ($this->getConfig()->get("config-version") != self::CONFIG_VERSION)) {
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
            $this->saveResource("config.yml");
            $this->getLogger()->critical("Your configuration file is outdated.");
            $this->getLogger()->notice("Your old configuration has been saved as config_old.yml and a new configuration file has been generated. Please update accordingly.");
        }
        $dataConfig = new Config($this->getDataFolder() . "shop.yml", Config::YAML);
        if ((!$dataConfig->exists("shop-version")) || ($dataConfig->get("shop-version") != self::SHOP_VERSION)) {
            rename($this->getDataFolder() . "shop.yml", $this->getDataFolder() . "shop_old.yml");
            $this->saveResource("shop.yml");
            $this->getLogger()->critical("Your shop.yml file is outdated.");
            $this->getLogger()->notice("Your old shop.yml has been saved as shop_old.yml and a new shop.yml file has been generated. Please update accordingly.");
        }
        $dataConfig = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        if ((!$dataConfig->exists("message-version")) || ($dataConfig->get("message-version") != self::MESSAGE_VERSION)) {
            rename($this->getDataFolder() . "message.yml", $this->getDataFolder() . "message_old.yml");
            $this->saveResource("message.yml");
            $this->getLogger()->critical("Your message.yml file is outdated.");
            $this->getLogger()->notice("Your old message.yml has been saved as message_old.yml and a new message.yml file has been generated. Please update accordingly.");
        }

    }

    // For Commands Test Survival
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ("shopui") {
            case "shopui":
                if ($sender instanceof Player) {
                    if ($sender->getGamemode() != 0 and $this->getConfig()->get("Survival") === true) {
                        $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
                        $sender->sendMessage($msg->getNested("messages.Survival"));
                        return true;
                    } else {
                        $cfg = yaml_parse_file($this->getDataFolder() . "shop.yml");
                        $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
                        $this->Category($cfg, $sender, $msg);
                        return true;
                    }
                }
                $sender->sendMessage(TF::RED . "Please use this in-game.");
                break;
        }
        return true;
    }
    // For Commands Test Survival

    // For Categories
    public function Category($cfg, Player $player, Config $msg): void
    {
        $form = new SimpleForm(function (Player $player, int $data = null) use ($cfg, $msg) : void {
            if ($data == 0 && $this->getConfig()->get("Category_ExitButton") === true) {
                $player->sendMessage($msg->getNested("Messages.Thanks2"));
            } else {
                if ($this->getConfig()->get("Category_ExitButton") == true) {
                    $categorys = $data - 1;
                    $this->Items($player, $categorys, $cfg);
                } else {
                    $categorys = $data;
                    $this->Items($player, $categorys, $cfg);
                }
            }
        });
        $money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($player);
        $form->setTitle("§l§d♦ §bShop §d♦");
        $form->setContent("§a➼ §eTiền của bạn:§b $money §axu");
        if ($this->getConfig()->get("Category_ExitButton") == true) {
            $form->addButton($msg->getNested("Messages.Category_ExitButton"));
        }
        foreach ($cfg as $cate => $category) {
            if ($category == self::SHOP_VERSION) {
            } else {
                $list = explode(":", $category["Name"]);
                if (substr($list[1], 0, 4) == "http") {
                    $form->addButton("§f• §b".$list[0]." §f•", 1, "https:" . $list[2]);
                } else {
                    $form->addButton("§f• §b".$list[0]." §f•", 0, $list[1]);
                }
            }
        }
        $form->sendToPlayer($player);
    }
    // For Categories

    // For Items
    public function Items(Player $player, $categorys, $cfg)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) use ($cfg, $categorys) : void {
            if ($data == 0) {
                $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
                $this->Category($cfg, $player, $msg);
            } else {
                $items = $cfg[$categorys];
                foreach ($items["Items"] as $cate => $item) {
                    $list = explode(":", $item);
                }
                if ($list[0] == "cmd") {
                    $command = $data - 1;
                    if ($this->getConfig()->get("command_confirm") === true) {
                        $this->Commandform($player, $cfg, $categorys, $command);
                    } else {
                        $this->Command($player, $cfg, $categorys, $command);
                    }
                } elseif (($data != 0) && ($list[0] != "cmd")) {
                    $item = $data - 1;
                    $this->Confirmation($player, $cfg, $categorys, $item);
                }
            }
        });
        $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($player);
        $form->setTitle("§l§d♦ §bShop §d♦");
        $form->setContent("§l§bYour money: §e$money");
        $form->addButton($msg->getNested("Messages.ExitButton"));
        if (($categorys === null) && ($this->getConfig()->get("Thanks") == true)) {
            #$player->sendMessage($msg->getNested("Messages.Thanks"));
        } else {
            $items = $cfg[$categorys];
            foreach ($items["Items"] as $cate => $item) {
                $list = explode(":", $item);
                if ($list[5] == "Default") {
                    $name = Item::get((int)$list[0], 0, 1)->getName();
                } else {
                    $name = $list[5];
                }
                if ($list[0] == "cmd") {
                    if (substr($list[5], 0, 4) == "http") {
                        $form->addButton($list[1] . " " . $list[2] . $msg->getNested("Messages.Each"), 1, $list[5] . ":" . $list[6]);
                        #$form->addButton("§f• §b".$list[1] . " §f•" . $list[2] , 1, $list[5] . ":" . $list[6]);
                    } else {
                        $form->addButton($list[1] . " " . $list[2] . $msg->getNested("Messages.Each"), 0, $list[5]);
                        #$form->addButton($list[1] . " " . $list[2] , 0, $list[5]);
                    }
                } else {
                    if (substr($list[6], 0, 4) == "http") {
                        #$form->addButton($name . " " . $list[3] . $msg->getNested("Messages.Each"), 1, $list[6] . ":" . $list[7]);
                        $form->addButton("§f• §b".$name . " §f•" , 1, $list[6] . ":" . $list[7]);
                    } else {
                        #$form->addButton($name . " " . $list[3] . $msg->getNested("Messages.Each"), 0, $list[6]);
                        $form->addButton("§f• §b".$name . " §f•" , 0, $list[6]);
                    }
                }
            }
            $form->sendToPlayer($player);
        }
    }

    // For Items
    public function commandform(Player $player, $cfg, $categorys, $command)
    {
        $form = new SimpleForm(function (Player $player, $data) use ($cfg, $categorys, $command) {
            $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
            if ($data == 0) {
                $player->sendMessage($msg->getNested("messages.command_buy_cancel"));
            }
            if ($data == 1) {
                $this->Command($player, $cfg, $categorys, $command);
            }
        });
        $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $form->setContent($msg->getNested("messages.cmd_confirm"));
        $form->addButton($msg->getNested("messages.yes"));
        $form->addButton($msg->getNested("messages.no"));
    }

    // For Commands
    public function Command(Player $player, $cfg, $categorys, $command)
    {
        $items = $cfg[$categorys];
        foreach ($items["Items"] as $cate => $item2) {
            $item1[] = $item2;
        }
        $list = explode(":", $item1[$command]);
        if (EconomyAPI::getInstance()->myMoney($player) > $list[2]) {
            if ($list[3] == "Console") {
                $cmd = str_replace("{player}", $player->getName(), $list[4]);
                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), $cmd);
                EconomyAPI::getInstance()->reduceMoney($player->getName(), $list[2]);
            } elseif ($list[3] == "Player") {
                $cmd = str_replace("{player}", $player->getName(), $list[4]);
                Server::getInstance()->dispatchCommand($player, $cmd);
                EconomyAPI::getInstance()->reduceMoney($player->getName(), $list[2]);
            }
        } else {
            $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
            $player->sendMessage($msg->getNested("messages.Not_enough_money"));
        }
    }
    // For Commands

    // For Confirm Form (LONG BOI)
    public function Confirmation(Player $player, $cfg, $categorys, $item)
    {
        $form = new CustomForm(function (Player $player, $data) use ($cfg, $categorys, $item) {
            if ($data === null) {
                $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
                if ($this->getConfig()->get("Straight_Back") == true) {
                    $player->sendMessage($msg->getNested("Messages.Thanks"));
                } elseif ($this->getConfig()->get("Back_to_Start") == true) {
                    $this->Category($cfg, $player, $msg);
                }
            } else {
                $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
                $money = EconomyAPI::getInstance()->myMoney($player);
                $items = $cfg[$categorys];
                $message = $msg->getNested("Messages.Information");
                foreach ($items["Items"] as $cate => $item2) {
                    $item1[] = $item2;
                }
                $list = explode(":", $item1[$item]);
                $name = Item::get((int)$list[0], 0, 1)->getName();
                $vars = ["{item}" => $name, "{cost}" => $list[4]];
                foreach ($vars as $var => $replacement) {
                    $message = str_replace($var, $replacement, $message);
                }
                if ($this->getConfig()->getNested("Types.Toggle") == true) {
                    if ($this->getConfig()->getNested("Types.Input") == true) {
                        $data1 = (int)$data[2];
                    }
                    if ($this->getConfig()->getNested("Types.Slider") == true) {
                        $data1 = $data[3];
                    }
                    if ($this->getConfig()->getNested("Types.StepSlider") == true) {
                        $numbers = $this->getConfig()->getNested("Slider_Numbers");
                        $data1 = $numbers[$data[4]];
                    }
                }

                if (($data1 == 0) && ($this->getConfig()->get("Thanks")) === true) {
                    #$player->sendMessage($msg->getNested("Messages.Thanks2"));
                } else {
                    if ($data[1] == false) {
                        if ($money >= $list[3] * $data1) {
                            $item = $player->getInventory();
                            if ($list[5] != "Default") {
                                $item->addItem(Item::get((int)$list[0], (int)$list[1], $data1)->setCustomName($data[5]));
                            } elseif ($list[5] == "Default") {
                                $item->addItem(Item::get((int)$list[0], (int)$list[1], $data1));
                            }
                            EconomyAPI::getInstance()->reduceMoney($player, $list[3] * $data1);
                            $message = $msg->getNested("Messages.Paid_for");
                            $vars = ["{amount}" => $data1, "{item}" => $name, "{cost}" => $list[3] * $data1];
                            foreach ($vars as $var => $replace) {
                                $message = str_replace($var, $replace, $message);
                            }
                            $player->sendMessage("");
                        } else {
                            $message = $msg->getNested("Messages.Not_enough_money");
                            $tags = ["{amount}" => $data1, "{item}" => $name, "{cost}" => $list[3] * $data1, "{missing}" => $list[3] * $data1 - $money];
                            foreach ($tags as $tag => $replace) {
                                $message = str_replace($tag, $replace, $message);
                            }
                            $player->sendMessage($message);
                        }
                    }


                    if ($data[1] == true) {
                        if ($player->getInventory()->contains(Item::get((int)$list[0], (int)$list[1], $data1)) === true) {
                            $player->getInventory()->removeItem(Item::get((int)$list[0], (int)$list[1], $data1));
                            EconomyAPI::getInstance()->addMoney($player, $list[4] * $data1);
                            $message = $msg->getNested("Messages.Paid");
                            $vars = ["{amount}" => $data1, "{item}" => $name, "{pay}" => $list[4] * $data1];
                            foreach ($vars as $var => $replacement) {
                                $message = str_replace($var, $replacement, $message);
                            }
                            $player->sendMessage($message);
                        } else {
                            $message = $msg->getNested("Messages.Not_enough_items");
                            $tags = ["{amount}" => $data1, "{item}" => $name, "{pay}" => $list[4] * $data1];
                            foreach ($tags as $tag => $replacement) {
                                $message = str_replace($tag, $replacement, $message);
                            }
                            $player->sendMessage($message);
                        }
                    }
                }
            }
        });


        $msg = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $items = $cfg[$categorys];
        $message = $msg->getNested("Messages.Information");
        foreach ($items["Items"] as $cate => $item2) {
            $item1[] = $item2;
        }
        $list = explode(":", $item1[$item]);
        $name = Item::get((int)$list[0], 0, 1)->getName();
        $vars = ["{item}" => $name, "{cost}" => $list[3], "{sell}" => $list[4]];
        foreach ($vars as $var => $replacement) {
            $message = str_replace($var, $replacement, $message);
        }
        $money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($player);
        $form->setTitle("§l§bAccept to buy $name §b");
        $form->addLabel("§l§bYour money: §e$$money\n\n".$message);
        if ($this->getConfig()->getNested("Types.Toggle") == true) {
            $message2 = $msg->getNested("Messages.BuySell");
            $vars2 = ["{item}" => $name, "{cost}" => $list[3], "{sell}" => $list[4]];
            foreach ($vars2 as $var => $replacement) {
                $message2 = str_replace($var, $replacement, $message2);
            }
            $form->addToggle($message2);
        }
        if ($this->getConfig()->getNested("Types.Input") == true) {
            $form->addInput($msg->getNested("Messages.Input"));
        }
        if ($this->getConfig()->getNested("Types.Slider") == true) {
            $form->addSlider("Amount", $this->getConfig()->getNested("Types.Slider_Minimum"), $this->getConfig()->getNested("Types.Slider_Maximum"));
        }
        if ($this->getConfig()->getNested("Types.StepSlider") == true) {
            $form->addStepSlider("Amount", $this->getConfig()->getNested("Types.Slider_Numbers"));
        }
        $form->sendToPlayer($player);
    }
    // For Confirm Form (LONG BOI)

//    _____       _ _         _____ _          _ _____
//   / ____|     | | |       |  __ (_)        | |  __ \
//  | (___   __ _| | |_ _   _| |__) |__  _____| | |  | | _____   ______
//   \___ \ / _` | | __| | | |  ___/ \ \/ / _ \ | |  | |/ _ \ \ / /_  /
//   ____) | (_| | | |_| |_| | |   | |>  <  __/ | |__| |  __/\ V / / /
//  |_____/ \__,_|_|\__|\__, |_|   |_/_/\_\___|_|_____/ \___| \_/ /___|
//                       __/ |
//                      |___/


}