<?php

namespace NoobMCBG\StartedItem;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;

class Main extends PB implements L {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->check = new Config($this->getDataFolder() . "check.yml", Config::YAML);
        $this->getLogger()->info("Enable StartedItem");
	}

	public function onJoin(PlayerJoinEvent $ev){
		if(!$this->check->exists(strtolower($ev->getPlayer()->getName()))){
			$this->check->set(strtolower($ev->getPlayer()->getName()), true);
			$this->check->save();
			$i1 = Item::get(306, 0, 1)->setCustomName("§l§c•§a Mũ Người Mới §c•");
			$i1->setLore(array("§l§c•§e Mũ Dành Cho Người Chơi Mới Tham Gia Server !"));
			$i1->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 3)));
			$i1->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 3)));
			$i1->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 3)));
			$i2 = Item::get(307, 0, 1)->setCustomName("§l§c•§a Áo Người Mới §c•");
			$i2->setLore(array("§l§c•§e Mũ Dành Cho Người Chơi Mới Tham Gia Server !"));
			$i2->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 3)));
			$i2->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 3)));
			$i2->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 3)));
			$i3 = Item::get(308, 0, 1)->setCustomName("§l§c•§a Quần Người Mới §c•");
			$i3->setLore(array("§l§c•§e Mũ Dành Cho Người Chơi Mới Tham Gia Server !"));
			$i3->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 3)));
			$i3->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 3)));
			$i3->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 3)));
			$i4 = Item::get(309, 0, 1)->setCustomName("§l§c•§a Giày Người Mới §c•");
			$i4->setLore(array("§l§c•§e Mũ Dành Cho Người Chơi Mới Tham Gia Server !"));
			$i4->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 3)));
			$i4->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(0, 3)));
			$i4->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(5, 3)));
			$ev->getPlayer()->getInventory()->addItem($i1);
			$ev->getPlayer()->getInventory()->addItem($i2);
			$ev->getPlayer()->getInventory()->addItem($i3);
			$ev->getPlayer()->getInventory()->addItem($i4);
		}
	}

	public function onQuit(PlayerQuitEvent $ev){
		$this->check->save();
	}
}