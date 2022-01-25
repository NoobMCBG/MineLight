<?php

namespace NoobMCBG\SkyBlockUI;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};

class Main extends PB implements L {

	public function onEnable(){
		$this->getLogger()->info("Enable SkyBlockUI by NoobMCBG");
		$this->saveDefaultConfig();
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "sb":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("SỬ DỤNG TRG GAME");
			    	return true;
			    }else{
			    	$this->MenuSkyBlock($sender);
			    }
			break;
		}
		return true;
	}

	public function MenuSkyBlock($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
		    switch($data){
		    	case 0:
		    	break;
		    	case 1:
		    	    $this->HomeIsland($player);
		    	break;
		    	case 2:
		    	    $this->ClaimIsland($player);
		    	break;
		    	case 3:
		    	    $this->RandomIsland($player);
		    	break;
		    	case 4:
		    	   $this->InfoIsland($player);
		    	break;
		    	case 5:
		    	    $this->WarpIsland($player);
		    	break;
		    	case 6:
		    	    $this->FriendIsland($player);
		    	break;
		    	case 7:
		    	   $this->HomeListIsland($player);
		    	break;
		    	case 8:
		    	    $this->GiveIsland($player);
		    	break;
		    	case 9:
		    	    $this->DeleteIsland($player);
		    	break;
		    }
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->addButton("§l§c•§9 Thoát Menu §c•");
		$form->addButton("§l§c•§9 Về Đảo §c•");
		$form->addButton("§l§c•§9 Nhận Đảo §c•");
		$form->addButton("§l§c•§9 Tìm Đảo §c•");
		$form->addButton("§l§c•§9 Thông Tin Đảo §c•");
		$form->addButton("§l§c•§9 Thăm Đảo Người Khác §c•");
		$form->addButton("§l§c•§9 Cài Đặt Bạn Bè Đảo §c•");
		$form->addButton("§l§c•§9 Tất Cả Đảo Của Bạn §c•");
		$form->addButton("§l§c•§9 Tặng Đảo §c•");
		$form->addButton("§l§c•§9 Xóa Đảo §c•");
		$form->sendToPlayer($player);
	}

	public function HomeIsland($player){
	    $form = new CustomForm(function(Player $player, $data){
	    	if($data == null){
	    		$this->getServer()->getCommandMap()->dispatch($player, "warp sb");
	    		$this->getServer()->getCommandMap()->dispatch($player, "is home");
	    		return true;
	    	}
	    	if(is_numeric($data[0])){
	    		$this->HomeIsland($player);
	    		$player->sendMessage("§l§c•§e Số Đảo Bắt Buộc Phải Là Số !");
	    		$packet = new PlaySoundPacket();
		        $packet->soundName = "random.explode";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
	    	}
	    	$this->getServer()->getCommandMap()->dispatch($player, "warp sb");
	    	$this->getServer()->getCommandMap()->dispatch($player, "is home ".$data[0]);
	    	$packet = new PlaySoundPacket();
		    $packet->soundName = "random.levelup";
		    $packet->x = $player->getPosition()->getX();
		    $packet->y = $player->getPosition()->getY();
		    $packet->z = $player->getPosition()->getZ();
		    $packet->volume = 1;
		    $packet->pitch = 1;
		    $player->sendDataPacket($packet);
	    });
	    $form->setTitle("§l§c•§9 Menu SkyBlock §c•");
	    $form->addInput("§l§c•§e Nhập Số Đảo Muốn Về:§a ", "2");
	    $form->sendToPlayer($player);
	}

	public function ClaimIsland($player){
		$this->getServer()->getCommandMap()->dispatch($player, "is claim");
		$packet = new PlaySoundPacket();
		$packet->soundName = "random.levelup";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
		$player->sendDataPacket($packet);
	}

	public function RandomIsland($player){
		$this->getServer()->getCommandMap()->dispatch($player, "warp sb");
		$this->getServer()->getCommandMap()->dispatch($player, "is auto");
		$packet = new PlaySoundPacket();
		$packet->soundName = "random.click";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
		$player->sendDataPacket($packet);
	}

	public function InfoIsland($player){
		$this->getServer()->getCommandMap()->dispatch($player, "is info");
		$packet = new PlaySoundPacket();
		$packet->soundName = "random.click";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
		$player->sendDataPacket($packet);
	}

	public function WarpIsland($player){
		$form = new CustomForm(function(Player $player, $data){
			if($data == null){
				$this->MenuSkyBlock($player);
				return true;
			}
			if($data[0] == null){
				$this->WarpIsland($player);
				$packet = new PlaySoundPacket();
		        $packet->soundName = "random.explode";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
			}else{
			    $this->getServer()->getCommandMap()->dispatch($player, "warp sb");
			    $this->getServer()->getCommandMap()->dispatch($player, "is warp ".$data[0]);
			    $packet = new PlaySoundPacket();
		        $packet->soundName = "random.levelup";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
		    }
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->addInput("§l§c•§e Nhập ID Đảo Muốn Thăm:§a ", "-2,5");
		$form->sendToPlayer($player);
	}

	public function FriendIsland($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				$this->MenuSkyBlock($player);
				return true;
			}
			switch($data){
				case 0:
				    $this->MenuSkyBlock($player);
				break;
				case 1:
				    $this->AddFriend($player);
				break;
				case 2:
				    $this->RemoveFriend($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->addButton("§l§c•§9 Quay Lại §c•");
		$form->addButton("§l§c•§9 Thêm Người Vào Đảo §c•");
		$form->addButton("§l§c•§9 Xóa Người Khỏi Đảo");
		$form->sendToPlayer($player);
	}

	public function AddFriend($player){
		$form = new CustomForm(function(Player $player, $data){
			if($data == null){
				$this->FriendIsland($player);
				return true;
			}
			if(isset($data[0])){
				$this->AddFriend($player);
				$packet = new PlaySoundPacket();
		        $packet->soundName = "random.explode";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
            }
			$p = $this->getServer()->getPlayer($data[0]);
			$this->getServer()->getCommandMap()->dispatch($player, "is addhelper ".$p->getName());
			$packet = new PlaySoundPacket();
		    $packet->soundName = "random.levelup";
		    $packet->x = $player->getPosition()->getX();
		    $packet->y = $player->getPosition()->getY();
		    $packet->z = $player->getPosition()->getZ();
		    $packet->volume = 1;
		    $packet->pitch = 1;
		    $player->sendDataPacket($packet);
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->addInput("§l§c•§e Nhập Tên Người Muốn Thêm:§a ", "NoobOfBlind");
		$form->sendToPlayer($player);
	}

	public function RemoveFriend($player){
		$form = new CustomForm(function(Player $player, $data){
			if($data == null){
				$this->FriendIsland($player);
				return true;
			}
			if(isset($data[0])){
				$this->AddFriend($player);
				$packet = new PlaySoundPacket();
		        $packet->soundName = "random.explode";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
			}
			$p = $this->getServer()->getPlayer($data[0]);
			$this->getServer()->getCommandMap()->dispatch($player, "is removehelper ".$p->getName());
			$packet = new PlaySoundPacket();
		    $packet->soundName = "random.explode";
		    $packet->x = $player->getPosition()->getX();
		    $packet->y = $player->getPosition()->getY();
		    $packet->z = $player->getPosition()->getZ();
		    $packet->volume = 1;
		    $packet->pitch = 1;
		    $player->sendDataPacket($packet);
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->addInput("§l§c•§e Nhập Tên Người Muốn Xóa:§a ", "ClickedTran");
		$form->sendToPlayer($player);
	}

	public function HomeListIsland($player){
		$this->getServer()->getCommandMap()->dispatch($player, "is homes");
		$packet = new PlaySoundPacket();
		$packet->soundName = "mob.enderdragon.growl";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
		$player->sendDataPacket($packet);
	}

	public function GiveIsland($player){
		$form = new CustomForm(function(Player $player, $data){
			if($data == null){
				$this->MenuSkyBlock($player);
				return true;
			}
			if(isset($data[0])){
				$this->GiveIsland($player);
				$packet = new PlaySoundPacket();
		        $packet->soundName = "random.explode";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
			}
			$p = $this->getServer()->getPlayer($data[0]);
			if(!$p instanceof Player){
			    $player->sendMessage("§l§c•§e Người Chơi§b {$data[0]} §eKhông Online !");
		        return true;
		    }else{	
		    	$this->getServer()->getCommandMap()->dispatch($player, "is give ".$p->getName());
			    $this->getServer()->getCommandMap()->dispatch($player, "is give ".$p->getName()." confirm");
			    $packet = new PlaySoundPacket();
		        $packet->soundName = "random.levelup";
		        $packet->x = $player->getPosition()->getX();
		        $packet->y = $player->getPosition()->getY();
		        $packet->z = $player->getPosition()->getZ();
		        $packet->volume = 1;
		        $packet->pitch = 1;
		        $player->sendDataPacket($packet);
		    }
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->addInput("§l§c•§e Nhập Tên Người Muốn Tặng Đảo:§a", "NoobOfBlind");
		$form->sendToPlayer($player);
	}

	public function DeleteIsland($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				$this->MenuSkyBlock($player);
				return true;
			}
			switch($data){
				case 0:
				    $this->MenuSkyBlock($player);
				break;
				case 1:
				    $this->DeleteConfirm($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu SkyBlock §c•");
		$form->setContent("§l§c•§e Bạn Có Chắc Muốn Xóa Đảo Này Chứ ?");
		$form->addButton("§l§c•§2 Không Xóa §c•");
		$form->addButton("§l§c•§4 Xóa Đảo §c•");
		$form->sendToPlayer($player);
	}

	public function DeleteConfirm($player){
		$this->getServer()->getCommandMap()->dispatch($player, "is dispose");
		$this->getServer()->getCommandMap()->dispatch($player, "is dispose confirm");
		$packet = new PlaySoundPacket();
		$packet->soundName = "random.levelup";
		$packet->x = $player->getPosition()->getX();
		$packet->y = $player->getPosition()->getY();
		$packet->z = $player->getPosition()->getZ();
		$packet->volume = 1;
		$packet->pitch = 1;
	    $player->sendDataPacket($packet);
	}
}