<?php

namespace NoobMCBG\AutoLight;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\utils\Config;
use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use jojoe77777\FormAPI\{SimpleForm, ModalForm};

class Main extends PB implements L {

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->token = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
		$this->credits = $this->getServer()->getPluginManager()->getPlugin("CreditsAPI");
		$this->pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		$this->ranktime = new Config($this->getDataFolder() . "ranktime.yml", Config::YAML);
		$this->getLogger()->info(":> E na bồ PỜ LÚC GIN AU TU LAI VÊ MỘT CHẤM KHÔNG CHẤM KHÔNG :>");
		$this->getScheduler()->scheduleRepeatingTask(new RankTime($this), 20 * 86400);
		$this->saveDefaultConfig();
	}

	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		$name = $player->getName();
		if(!$this->ranktime->exists($name)){
			$this->ranktime->set(strtolower($name), 0);
			$this->ranktime->save();
		}
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "credits":
			    if(!$sender instanceof Player){
			    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi");
			    	return true;
			    }else{
			    	$this->MenuCredits($sender);
			    }
			break;
		}
		return true;
	}

	public function MenuCredits($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				break;
				case 1:
				    $this->MenuMuaRanks($player);
				break;
				case 2:
				    $this->MenuMuaQuyenLoi($player);
				break;
				case 3:
				    $this->MenuMuaDo($player);
				case 4:
				    $this->getServer()->getCommandMap()->dispatch($player, "napthe");
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Credits §c•");
		$token = $this->token->myToken($player);
		$credits = $this->credits->myCredits($player);
		$form->setContent("§l§c•§e Số Tokens Của Bạn:§2 ".$token."\n§l§c•§e Số Credits Của Bạn:§f $credits");
		$form->addButton("§l§c•§9 Thoát Menu §c•");
		$form->addButton("§l§c•§9 Mua Ranks §c•", 0, "textures/ui/op");
		$form->addButton("§l§c•§9 Mua Quyền Lợi §c•", 0, "textures/other/eletepass");
		$form->addButton("§l§c•§9 Mua Đồ §c•", 0, "textures/other/pvp");
		$form->addButton("§l§c•§9 Nạp Thẻ §c•", 0, "textures/other/donate");
		$form->sendToPlayer($player);
	}

	public function MenuMuaDo($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				    $this->MenuCredits($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Credits §c•");
		$form->setContent("§l§c•§e Xin Lỗi ! Server Chưa Cập Nhật Đồ Trong Menu Credits, Thông Cảm !");
		$form->addButton("§l§c•§9 Quay Lại §c•");
		$form->sendToPlayer($player);
	}

	public function MenuMuaQuyenLoi($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				    $this->MenuCredits($player);
				break;
				case 1:
				   $this->MuaEletePass($player);
				break;
				case 2:
				   $this->OnlineReward($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Credits §c•");
		$form->addButton("§l§c•§9 Quay Lại §c•");
		$eletepass = $player->hasPermission($this->getConfig()->getAll()["SeasonPass"]["permission"]) ? "§l§2Đã Mua" : "§l§4Chưa Mua";
		$form->addButton("§l§c•§9 Tính Năng EletePass§6 (200 Credits) §c•\n§l§0Trạng Thái: ".$eletepass."");
		$onlinereward = $player->hasPermission($this->getConfig()->getAll()["OnlineReward"]["permission"]) ? "§l§2Đã Mua" : "§l§4Chưa Mua";
		$form->addButton("§l§c•§9 Tính Năng OnlineReward§6 (500 Credits) §c•\n§l§0Trạng Thái: $onlinereward");
		$form->sendToPlayer($player);
	}

	public function MuaEletePass($player){
		$credits = $this->credits->myCredits($player);
		$cost = $this->getConfig()->getAll()["SeasonPass"]["cost"];
		if($credits >= $cost){
			$this->credits->reduceCredits($player, $cost);
			$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "setuperm ".$player->getName()." ".$this->getConfig()->getAll()["SeasonPass"]["permission"]);
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Tính Năng§a EletePass");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§f Credits§e Để Mua Tính Năng Này !");
		}
	}

	public function OnlineReward($player){
		$credits = $this->credits->myCredits($player);
		$cost = $this->getConfig()->getAll()["OnlineReward"]["cost"];
		if($credits >= $cost){
			$this->credits->reduceCredits($player, $cost);
			$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender, "setuperm ".$player->getName()." ".$this->getConfig()->getAll()["OnlineReward"]["permission"]);
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Tính Năng§a EletePass");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§f Credits§e Để Mua Tính Năng Này !");
		}
	}

	public function MenuMuaRanks($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				    $this->MenuCredits($player);
				break;
				case 1:
				    $this->Light1($player);
				break;
				case 2:
				    $this->Light2($player);
				break;
				case 3:
				    $this->Light3($player);
				break;
				case 4:
				    $this->Light4($player);
				break;
				case 5:
				    $this->Light5($player);
				break;
				case 6:
				    $this->Light6($player);
				break;
				case 7:
				    $this->Light7($player);
				break;
			}
		});
		$form->setTitle("§l§c•§9 Menu Credits §c•");
		$ranktime = $this->ranktime->get(strtolower($player->getName()));
		$form->setContent("§l§c•§e Ranks Của Bạn Còn:§a $ranktime Ngày");
		$form->addButton("§l§c•§9 Thoát Menu §c•", 0, "textures/other/exit");
		$form->addButton("§l§c•§9 LightI §6(5 Ngày) §c•\n§l§c Giá: §230 Tokens", 0, "textures/other/light1");
		$form->addButton("§l§c•§9 LightII §6(15 Ngày) §c•\n§l§c Giá: §250 Tokens", 0, "textures/other/light2");
		$form->addButton("§l§c•§9 LightIII §6(25 Ngày) §c•\n§l§c Giá: §2100 Tokens", 0, "textures/other/light3");
		$form->addButton("§l§c•§9 LightIV §6(50 Ngày) §c•\n§l§c Giá: §2200 Tokens", 0, "textures/other/light4");
		$form->addButton("§l§c•§9 LightV §6(100 Ngày) §c•\n§l§c Giá: §2300 Tokens", 0, "textures/other/light5");
		$form->addButton("§l§c•§9 LightVI §6(200 Ngày) §c•\n§l§c Giá: §2400 Tokens", 0, "textures/other/light6");
		$form->addButton("§l§c•§9 LightVII §6(300 Ngày) §c•\n§l§c Giá: §2500 Tokens", 0, "textures/other/light7");
        $form->sendToPlayer($player);
	}

	public function getPlayerRank($player){
		return $this->pp->getUserDataMgr()->getData($player)['group'];
	}

	public function Light1($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight1($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightI §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dI§e Là:§2 30 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight1($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightI"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 5);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightI"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+5);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dI");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}

	public function Light2($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight2($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightII §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dII§e Là:§2 50 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight2($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightII"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 15);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRanK($player) == $this->pp->getGroup("LightIII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightII"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+15);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dII");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}

	public function Light3($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight3($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightIII §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dIII§e Là:§2 100 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight3($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightIII"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 25);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIII"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+25);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIII");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}

	public function Light4($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight4($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightIV §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dIV§e Là:§2 200 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight4($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightIV"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 100);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$ranktime = $this->rankktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIV"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+100);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dIV");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}

	public function Light5($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight5($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightV §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dV§e Là:§2 300 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight5($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightV"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 200);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightV"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dV");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$ranktime = $this->rakktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightIV"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dV");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}

	public function Light6($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight6($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightVI §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dVI§e Là:§2 400 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight6($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightVI"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 200);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVI"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+200);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}

	public function Light7($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->BuyLight7($player);
				return true;
			}
		});
		$form->setTitle("§l§c•§9 Menu LightVII §c•");
		$form->setContent("§l§c•§e Giá Ranks §bLight§dVII§e Là:§2 500 Tokens");
		$form->setButton1("§l§c•§2 Chắc Chắn §c•");
		$form->setButton2("§l§c•§c Chưa Chắc Chắn §c•");
		$form->sendToPlayer($player);
	}

	public function BuyLight7($player){
		$token = $this->token->myToken($player);
		$cost = $this->getConfig()->getAll()["MuaRanks"]["LightVII"]["cost"];
		if($token >= $cost){
			$this->token->reduceToken($player, $cost);
			if($this->getPlayerRank($player) == $this->pp->getDefaultGroup()){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $this->ranktime->set(strtolower($player->getName()), 300);
			    $this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVI");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightI")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightIV")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightV")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVI")){
				$ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thêm Ngày Thành Công !");
			}
			if($this->getPlayerRank($player) == $this->pp->getGroup("LightVII")){
				$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			    $this->pp->setGroup($player, $group);
			    $ranktime = $this->ranktime->get(strtolower($player->getName()));
				$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
				$this->ranktime->save();
			    $player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
			}
			$group = $this->pp->getGroup($this->getConfig()->getAll()["MuaRanks"]["LightVII"]["groups"]);
			$this->pp->setGroup($player, $group);
			$ranktime = $this->ranktime->get(strtolower($player->getName()));
			$this->ranktime->set(strtolower($player->getName()), $ranktime+300);
			$this->ranktime->save();
			$player->sendMessage("§l§c•§e Bạn Đã Mua Thành Công Ranks §bLight§dVII");
		}else{
			$player->sendMessage("§l§c•§e Bạn Không Đủ§2 Tokens§e Để Mua Ranks Này !");
		}
	}
}