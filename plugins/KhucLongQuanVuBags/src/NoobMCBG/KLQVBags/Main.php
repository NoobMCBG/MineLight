<?php

namespace NoobMCBG\KLQVBags;

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
		$this->getLogger()->info("Đã Hoẹt Động :>");
		$this->saveDefaultConfig();
		$this->ce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
	}

	public function CustomEnchants(){
		return $this->ce;
	}

	public function onUse(PlayerInteractEvent $ev){
		$player = $ev->getPlayer();
		$inv = $player->getInventory();
		if($inv->getItemInHand()->getCustomName() == "§l§c•§a Hộp Khúc Long Quan Vũ §c•"){
			$chance = $this->getConfig()->get("chance");
			if(mt_rand(1, 100) <= $chance){
                switch(mt_rand(1, 4)){
                	case 1:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Khúc Long Quan Vũ§e Và Đã Trúng§a Mũ Khúc Long Quan Vũ");
                	    $item = Item::get(314, 0, 1);
                	    $item->setCustomName("§l§c•§e Mũ Khúc Long Quan Vũ §c•");
                	    $item->setLore(array("§l§c•§e Mũ Khúc LOng Quan Vũ Chỉ Có Đặc Quyền Trong Hộp§d Khúc Long Quan Vũ\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 20)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 2 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 1 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 1 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                	case 2:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Khúc Long Quan Vũ§e Và Đã Trúng§a Áo Khúc Long Quan Vũ");
                	    $item = Item::get(315, 0, 1);
                	    $item->setCustomName("§l§c•§e Áo Khúc Long Quan Vũ §c•");
                	    $item->setLore(array("§l§c•§e Áo Khúc Long Quan Vũ Chỉ Có Đặc Quyền Trong Hộp§d Khúc Long Quan Vũ\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 20)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 2 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 1 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 1 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                    case 3:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Khúc Long Quan Vũ§e Và Đã Trúng§a Quần Khúc Long Quan Vũ");
                	    $item = Item::get(316, 0, 1);
                	    $item->setCustomName("§l§c•§e Quần Khúc Long Quan Vũ §c•");
                	    $item->setLore(array("§l§c•§e Quần Khúc Long Quan Vũ Chỉ Có Đặc Quyền Trong Hộp§d Khúc Long Quan Vũ\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 20)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 2 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 1 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 1 ".$player->getName());
                	    $inv->addItem($item);
                	break;
                	case 4:
                	    $inv->setItemInHand(Item::get(Item::AIR));
                	    $this->getServer()->broadcastMessage("§l§c•§e Người Chơi§b ".$player->getName()."§e Vừa Mở§d Hộp Khúc Long Quan Vũ§e Và Đã Trúng§a Giày Khúc Long Quan Vũ");
                	    $item = Item::get(317, 0, 1);
                	    $item->setCustomName("§l§c•§e Giày Khúc Long Quan Vũ §c•");
                	    $item->setLore(array("§l§c•§e Giày Khúc Long Quan Vũ Chỉ Có Đặc Quyền Trong Hộp§d Khúc Long Quan Vũ\n\n§l§c•§e Giảm Sát Thương:§a ∎∎∎∎∎∎§6∎§c∎∎∎\n§l§c•§e Phản Sát Thương:§a ∎∎∎∎∎∎∎§6∎§c∎∎\n§l§c•§e Độ Hiếm:§b Bình Thường"));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(26, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 20)));
                	    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(1, 20)));
                	    //CustomEnchant (Glowing)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Glowing 2 ".$player->getName());
                	    //CustomEnchant (Armored)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Armored 2 ".$player->getName());
                	    //CustomEnchant (Tank)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Tank 1 ".$player->getName());
                	    //CustomEnchant (Poisoned)
                	    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "ce enchant Poisoned 1 ".$player->getName());
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