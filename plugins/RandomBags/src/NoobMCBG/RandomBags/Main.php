<?php

namespace NoobMCBG\RandomBags;

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
		$this->getLogger()->info("§l§6--------------------");
		$this->getLogger()->info("§l§a    RandomBags");
		$this->getLogger()->info("§l§a   Version 1.0.0");
		$this->getLogger()->info("§l§b    by NoobMCBG");
		$this->getLogger()->info("§l§6--------------------");
		$this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
		$this->saveDefaultCOnfig();
	}

	public function TokenAPI(){
		return $this->token;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "random":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
			    	return true;
			    }else{
			    	$this->MenuQuayKit($sender);
			    }
			break;
		}
		return true;
	}

	public function MenuQuayKit($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				Case 0:
				break;
				case 1:
				    $this->KitBaoLongQuanVu($player);
				break;
				case 2:
				    $this->KitKhucLongQuanVu($player);
				break;
				case 3:
				    $this->KitBachBaoDao($player);
				break;
				case 4:
				    $this->KitGozila($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Quay Kit §c•");
		$form->addButton("§l§c•§9 Thoát Menu §c•");
		$form->addButton("§l§c•§9 Bảo Long Quan Vũ §c•");
		$form->addButton("§l§c•§9 Tiếng Thủy Bích Bảo §c•");
		$form->addButton("§l§c•§9 Bạch Bao Đao §c•");
		$form->addButton("§l§c•§9 Gozila §c•");
		$form->sendToPlayer($player);
	}

	public function KitBaoLongQuanVu($player){
		$token = $this->TokenAPI()->myToken($player);
		$cost = $this->getConfig()->getAll()["KitBaoLongQuanVu"]["cost"];
		if($token >= $cost){
			$this->TokenAPI()->reduceToken($player, $cost);
			$item = Item::get(54, 0, 1);
			$item->setCustomName("§l§c•§a Hộp Bảo Long Quan Vũ §c•");
			$item->setLore(array("§l§c•§e Nhấn Hoặc Giữ Đề Nhận 1 Đồ Trong Hộp Đồ §c•"));
		    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 1)));
		    $player->getInventory()->addItem($item);
		    $player->sendMessage("§l§c•§e Đã Mua Thành Công Hộp Đồ !");
		}else{
			$player->sendMessage("§l§c•§e Bạn Cần Đủ§2 ".$this->getConfig()->getAll()["KitBaoLongQuanVu"]["cost"]." Tokens§e Để Mua Hộp Đồ Này !");
		}
	}

	public function KitKhucLongQuanVu($player){
	    $token = $this->TokenAPI()->myToken($player);
		$cost = $this->getConfig()->getAll()["KitKhucLongQuanVu"]["cost"];
		if($token >= $cost){
			$this->TokenAPI()->reduceToken($player, $cost);
			$item = Item::get(54, 0, 1);
			$item->setCustomName("§l§c•§a Hộp Khúc Long Quan Vũ §c•");
			$item->setLore(array("§l§c•§e Nhấn Hoặc Giữ Đề Nhận 1 Đồ Trong Hộp Đồ §c•"));
		    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 1)));
		    $player->getInventory()->addItem($item);
		    $player->sendMessage("§l§c•§e Đã Mua Thành Công Hộp Đồ !");
		}else{
			$player->sendMessage("§l§c•§e Bạn Cần Đủ§2 ".$this->getConfig()->getAll()["KitKhucLongQuanVu"]["cost"]." Tokens§e Để Mua Hộp Đồ Này !");
		}
	}

	public function KitBachBaoDao($player){
		$token = $this->TokenAPI()->myToken($player);
		$cost = $this->getConfig()->getAll()["KitBachBaoDao"]["cost"];
		if($token >= $cost){
			$this->TokenAPI()->reduceToken($player, $cost);
			$item = Item::get(54, 0, 1);
			$item->setCustomName("§l§c•§a Hộp Bạch Bao Đao §c•");
			$item->setLore(array("§l§c•§e Nhấn Hoặc Giữ Đề Nhận 1 Đồ Trong Hộp Đồ §c•"));
		    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 1)));
		    $player->getInventory()->addItem($item);
		    $player->sendMessage("§l§c•§e Đã Mua Thành Công Hộp Đồ !");
		}else{
			$player->sendMessage("§l§c•§e Bạn Cần Đủ§2 ".$this->getConfig()->getAll()["KitBachBaoDao"]["cost"]." Tokens§e Để Mua Hộp Đồ Này !");
		}
	}

	public function KitGozila($player){
		$token = $this->TokenAPI()->myToken($player);
		$cost = $this->getConfig()->getAll()["KitGozila"]["cost"];
		if($token >= $cost){
			$this->TokenAPI()->reduceToken($player, $cost);
			$item = Item::get(54, 0, 1);
			$item->setCustomName("§l§c•§a Hộp Gozila §c•");
			$item->setLore(array("§l§c•§e Nhấn Hoặc Giữ Đề Nhận 1 Đồ Trong Hộp Đồ §c•"));
		    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17, 1)));
		    $player->getInventory()->addItem($item);
		    $player->sendMessage("§l§c•§e Đã Mua Thành Công Hộp Đồ !");
		}else{
			$player->sendMessage("§l§c•§e Bạn Cần Đủ§2 ".$this->getConfig()->getAll()["KitGozila"]["cost"]." Tokens§e Để Mua Hộp Đồ Này !");
		}
	}
}