<?php

namespace NoobMCBG\ItemEffect;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\Listener as L;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\item\Item;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use jojoe77777\FormAPI\CustomForm;

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("mlem :>");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "itemeffect":
                if(!$sender instanceof Player){
                	$sender->sendMessage("SỬ DỤNG LỆNH TRONG TRÒ CHƠI !");
                	return true;
                }
                if(!$sender->hasPermission("itemeffect.command.*")){
                	$sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
                }else{
                	if(count($args) == 0){
                		$sender->sendMessage("§l§c•§e Sử Dụng:§b /itemeffect help");
                	}
                	if(count($args) == 1){
                		$sender->sendMessage("§l§c•§e Sử Dụng:§b /itemeffect help");
                		if($args[0] == "help"){
                			if(!$sender->hasPermission("itemeffect.command.help")){
                				$sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
                			}else{
                				$sender->sendMessage("§l§c•§e Các Lệnh Về ItemEffect §c•\n§l§b- /itemeffect add§e - Để Add Effect Vào Item !\n§l§b- /itemeffect remove§e - Để Xóa Effect Cho Item");
                			}
                		}
                		if($args[0] == "add"){
                			if(!$sender->hasPermission("itemeffect.command.add")){
                				$sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
                			}else{
                                $this->MenuAddEffect($sender);
                			}
                		}
                		if($args[0] == "remove"){
                			if(!$sender->hasPermission("itemeffect.command.add")){
                				$sender->sendMessage("§l§c•§e Bạn Không Có Quyền Sử Dụng Lệnh Này !");
                			}else{
                				if($sender->getInventory()->getItemInHand()->getId() == 0){
                					$sender->sendMessage("§l§c•§e Phải là Item chứ không phải tay không !");
                				}else{
                				    $item = $sender->getInventory()->getItemInHand();
                				    $id = $item->getId();
                				    $cn = $item->getCustomName();
                				    $amount = $item->getCount();
                				    $it = Item::get($id, 0, $amount);
                				    $it->setCustomName($cn);
                                    $inv = $sender->getInventory();
                                    $inv->setItemInHand($it);
                                    $sender->sendMessage("§l§c•§e Đã Xóa Effect Cho Item §aThành Công !");
                                }
                			}
                		}
                	}
                }
            break;
		}
		return true;
	}

	public function MenuAddEffect($player){
		$form = new CustomForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			if($data[0] == "Poison"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Poison §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Poison §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Wither"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Wither §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Wither §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Strength"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Strength §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Strength §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Speed"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Speed §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Speed §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Blindness"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Blindness §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Blindness §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Absorption"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Absorption §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Absorption §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Slowness"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Slowness §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Slowness §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "HealthBoost"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Health Boost §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Health Boost §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Weakness"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Weakness §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Weakness §c•"));
                    $it->setCustomName($cn);
                }
            }
            if($data[0] == "Hunger"){
				if($player->getInventory()->getItemInHand()->getId() == 0){
					$player->sendMessage("§l§c•§e Item bắt Buộc Phải Là item chứ không phải tay không !");
				}else{
			        $inv = $player->getInventory();
			        $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getId();
                    $cn = $item->getCustomName();
                    $amount = $item->getCount();
                    $it = Item::get($i, 0, $amount);
                    $it->setLore(array("§l§c•§b Hunger §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Hunger §c•"));
                    $it->setCustomName($cn);
                }
            }
		});
		$form->setTitle("§l§c•§9 Menu ItemEffect §c•");
		$form->addDropdown("§l§c•§e Chọn Hiệu Ứng:§a ", ["Poison", "Wither", "Strength", "Speed", "Blindness", "Absorption", "Slowness", "HealthBoost", "Weakness", "Hunger"]);
		$form->sendToPlayer($player);
	}

	public function onMove(PlayerMoveEvent $ev){
		$player = $ev->getPlayer();
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Poison §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Poison §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::POISON), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Wither §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Wither §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::WITHER), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Strength §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Strength §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::STRENGTH), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Speed §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Speed §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Blindness §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Blindness §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Absorption §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Absorption §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Health Boost §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Health Boost §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::HEALTH_BOOST), 20*2, 1, true));
		}
		if($player->getInventory()->getItemInHand()->getLore() == "§l§c•§b Hunger §c•\n§l§c•§e Khi Cầm Item Này Bạn Sẽ Bị Hiệu Ứng Hunger §c•"){
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::HUNGER), 20*2, 1, true));
		}
	}
}