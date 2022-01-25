<?php

namespace NoobMCBG\BBDBags;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\network\mcpe\protocol\PlayerSoundPacket;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use jojoe77777\FormAPI\SimpleForm;
use DaPigGuy\PiggyCustomEnchants\Main as CE;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;

class Main extends PB implements L {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("Enable");
		$this->saveDefaultConfig();
		$this->ce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
	}

	public function CustomEnchants(){
		return $this->ce;
	}

	public function onUse(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		$inv = $player->getInventory();
		if($inv->getItemInHand()->getCustomName() == "§l§c•§a Hộp Bạch Bao Đao §c•"){
			$chance = $this->getConfig()->get("chance");
			if(mt_rand(1, 100) <= $chance){
                switch(mt_rand(1, 4)){
                	case 1:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Bạch Bao Đao§e Và Đã Trúng§a Mũ Bạch Bao Đao");
                	    $item = Item::get(306, 0, 1);
                	    $item->setCustomName("§l§c•§e Mũ Bạch Bao Đao §c•");
                	    $item->setLore(array("§l§c•§e Mũ Bạch Bao Đao Chỉ Có Đặc Quyền Trong Hộp§d Bạch Bao Đao\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 30)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 3 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 2 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 2 ".$player->getName());
                	    //CustomEnchant (Obsidian Shield)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant 405 2 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                	case 2:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Bạch Bao Đao§e Và Đã Trúng§a Áo Bạch Bao Đao");
                	    $item = Item::get(307, 0, 1);
                	    $item->setCustomName("§l§c•§e Áo Bạch Bao Đao §c•");
                	    $item->setLore(array("§l§c•§e Áo Bảo Long Quan Vũ Chỉ Có Đặc Quyền Trong Hộp§d Bạch Bao Đao\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 30)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 3 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 2 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 2 ".$player->getName());
                	    //CustomEnchant (Obsidian Shield)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant 405 2 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                    case 3:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Bảo Long Quan Vạch Bao Đao§a Quần Bạch Bao Đao");
                	    $item = Item::get(308, 0, 1);
                	    $item->setCustomName("§l§c•§e Quần Bạch Bao Đao §c•");
                	    $item->setLore(array("§l§c•§e Quần Bạch Bao Đao Chỉ Có Đặc Quyền Trong Hộp§d Bạch Bao Đao\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 30)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 3 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 2 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 2 ".$player->getName());
                	    //CustomEnchant (Obsidian Shield)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant 405 2 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                	case 4:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Bạch Bao Đao§e Và Đã Trúng§a Giày Bạch Bao Đao");
                	    $item = Item::get(309, 0, 1);
                	    $item->setCustomName("§l§c•§e Giày Bạch Bao Đao §c•");
                	    $item->setLore(array("§l§c•§e Giày Bạch Bao Đao Chỉ Có Đặc Quyền Trong Hộp§d Bạch Bao Đao\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 30)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 30)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 3 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 2 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 2 ".$player->getName());
                	    //CustomEnchant (Obsidian Shield)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant 405 2 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                }
			}else{
				$inv->setItemInHand(Item::get(Item::AIR));
				$player->sendMessage("§l§c•§e Bạn Đã Mở Hộp Và Không Trúng Quà, Chúc Bạn May Mắn Lần Sau");
			}
		}
	}
}