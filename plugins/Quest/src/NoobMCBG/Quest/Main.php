<?php

namespace NoobMCBG\Quest;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent, PlayerDeathEvent};
use pocketmine\event\block\{BlockBreakEvent, BlockPlaceEvent};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\Config;
use jojoe77777\FormAPI\{SimpleForm, ModalForm};

class Main extends PB implements L {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("\n\n\n-=-=-=-=-=-=-=-=-=-=-\n\n         Quest By NoobMCBG\n\n-=-=-=-=-=-=-=-=-=-=-\n\n\n");
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
        $this->player = new Config($this->getDataFolder() . "player.yml", Config::YAML);
        $this->qmb = new Config($this->getDataFolder() . "breakquest.yml", Config::YAML);
        $this->qmp = new Config($this->getDataFolder() . "placequest.yml", Config::YAML);
        $this->qmk = new Config($this->getDataFolder() . "killquest.yml", Config::YAML);
        $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->topquest = new Config($this->getDataFolder() . "topquest.yml", Config::YAML);
	}

	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
        if(!$this->player->exists($player->getName())){
            $this->player->set(strtolower($player->getName()), ["Break" => 0, "Place" => 0, "Kill" => 0]);
            $this->player->save();
        }
        if(!$this->qmb->exists($player->getName())){
            $this->qmb->set(strtolower($player->getName()), "off");
            $this->qmb->save();
        }
        if(!$this->qmp->exists($player->getName())){
            $this->qmp->set(strtolower($player->getName()), "off");
            $this->qmp->save();
        }
        if(!$this->qmk->exists($player->getName())){
            $this->qmk->set(strtolower($player->getName()), "off");
            $this->qmk->save();
        }
        if(!$this->topquest->exists($player->getName())){
            $this->topquest->set(strtolower($player->getName()), 0);
            $this->topquest->save();
        }
	}

	public function onQuit(PlayerQuitEvent $ev){
		$this->player->save();
		$this->qmb->save();
		$this->qmp->save();
		$this->qmk->save();
		$this->topquest->save();
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
		switch($cmd->getName()){
			case "quest":
			if(!$sender instanceof Player){
                $sender->sendMessage("??cH??y S??? D???ng L???nh Trong Tr?? Ch??i");
                return true;
            }else{
            	$this->MenuNhiemVu($sender);
            }
		}
		return true;
	}

	public function MenuNhiemVu($player){
		$form = new SimpleForm(function(Player $player, $data){
            if($data == null){
        	    return true;
            }
            switch($data){
            	case 0:
            	break;
            	case 1:
            	    $this->BreakQuest($player);
            	break;
            	case 2:
            	    $this->PlaceQuest($player);
            	break;
            	case 3:
            	    $this->KillQuest($player);
            	break;
            	case 4:
            	    $this->TopQuest($player);
            	break;
            }
		});
		$form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
		$form->addButton("??l??3?????4 Tho??t ??3???");
		$b = $this->player->get(strtolower($player->getName()))["Break"];
		$p = $this->player->get(strtolower($player->getName()))["Place"];
		$k = $this->player->get(strtolower($player->getName()))["Kill"];
		$form->setContent("??l??c?????e Nhi???m V??? Ph?? Block:??a ".$b." Kh???i\n??l??c?????e Nhi???m V??? ?????t Block:??a ".$p." Kh???i\n??l??c?????e Nhi???m V??? Gh???t Ng?????i:??a ".$k." Ng?????i");
		$break = ($this->qmb->get(strtolower($player->getName()) == "on") ? "??l??2??ang L??m Nhi???m V???" : "??l??4Ch??a Nh???n Nhi???m V???");
		$form->addButton("??l??3?????2 Ph?? Block ??3???\n??lTr???ng Th??i: ".$break);
		$place = ($this->qmp->get(strtolower($player->getName()) == "on") ? "??l??2??ang L??m Nhi???m V???" : "??l??4Ch??a Nh???n Nhi???m V???");
		$form->addButton("??l??3?????2 ?????t Block ??3???\n??lTr???ng Th??i: ".$place);
		$kill = ($this->qmk->get(strtolower($player->getName()) == "on") ? "??l??2??ang L??m Nhi???m V???" : "??l??4Ch??a Nh???n Nhi???m V???");
		$form->addButton("??l??3?????2 Gh???t Ng?????i Ch??i ??3???\n??lTr???ng Th??i: ".$kill);
		$xephang = $this->topquest->get(strtolower($player->getName()));
		$form->addButton("??l??3?????2 TOP Nhi???m V??? ??3???\n??lX???p H???ng:??9 ".$xephang);
		$form->sendToPlayer($player);
	}

	public function BreakQuest($player){
		$form = new SimpleForm(function(Player $player, $data){
            if($data == null){
            	return true;
            }
            switch($data){
            	case 0:
            	if($this->qmb->get(strtolower($player->getName())) == "on"){
            		$player->sendMessage("??l??c?????e B???n ???? Nh???n Nhi???m V??? N??y Tr?????c ???? R???i !");
            	}else{
            		$this->qmp->set(strtolower($player->getName()), "on");
            	}
            	break;
            	case 1:
            	$this->BreakOff($player);
            	break;
            }
		});
        $form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
        $form->addButton("??l??3?????2 Nh???n Nhi???m V??? ??3???");
        $form->addButton("??l??3?????4 H???y L??m Nhi???m V??? ??3???");
        $form->sendToPlayer($player);
	}

	public function PlaceQuest($player){
		$form = new SimpleForm(function(Player $player, $data){
            if($data == null){
            	return true;
            }
            switch($data){
            	case 0:
            	if($this->qmp->get(strtolower($player->getName())) == "on"){
            		$player->sendMessage("??l??c?????e B???n ???? Nh???n Nhi???m V??? N??y Tr?????c ???? R???i !");
            	}else{
            		$this->qmp->set(strtolower($player->getName()), "on");
            	}
            	break;
            	case 1:
            	$this->PlaceOff($player);
            	break;
            }
		});
        $form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
        $form->addButton("??l??3?????2 Nh???n Nhi???m V??? ??3???");
        $form->addButton("??l??3?????4 H???y L??m Nhi???m V??? ??3???");
        $form->sendToPlayer($player);
	}

	public function KillQuest($player){
		$form = new SimpleForm(function(Player $player, $data){
            if($data == null){
            	return true;
            }
            switch($data){
            	case 0:
            	if($this->qmk->get(strtolower($player->getName())) == "on"){
            		$player->sendMessage("??l??c?????e B???n ???? Nh???n Nhi???m V??? N??y Tr?????c ???? R???i !");
            	}else{
            		$this->qmk->set(strtolower($player->getName()), "on");
            	}
            	break;
            	case 1:
            	$this->KillOff($player);
            	break;
            }
		});
        $form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
        $form->addButton("??l??3?????2 Nh???n Nhi???m V??? ??3???");
        $form->addButton("??l??3?????4 H???y L??m Nhi???m V??? ??3???");
        $form->sendToPlayer($player);
	}

	public function BreakOff($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->qmb->set(strtolower($player->getName()), "off");
				$break = $this->player->get(strtolower($player->getName()))["Break"];
		        $place = $this->player->get(strtolower($player->getName()))["Place"];
		        $kill = $this->player->get(strtolower($player->getName()))["Kill"];
		        $this->player->set(strtolower($player->getName()), ["Break" => 0, "Place" => $place, "Kill" => $kill]);
				$this->MessageOff($player);
			    return true;
			}
		});
		$form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
		$form->setContent("??l??c?????e B???n C?? Ch???c Ch???n Mu???n H???y Nhi???m V??? ??bPh?? Block??e Kh??ng ?");
		$form->setButton1("??l??3?????2 Ch???c Ch???n ??3???");
		$form->setButton2("??l??3?????4 Kh??ng Ch???c Ch???n ??3???");
		$form->sendToPlayer($player);
	}

    public function PlaceOff($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->qmp->set(strtolower($player->getName()), "off");
				$break = $this->player->get(strtolower($player->getName()))["Break"];
		        $place = $this->player->get(strtolower($player->getName()))["Place"];
		        $kill = $this->player->get(strtolower($player->getName()))["Kill"];
		        $this->player->set(strtolower($player->getName()), ["Break" => $break, "Place" => 0, "Kill" => $kill]);
				$this->MessageOff($player);
			    return true;
			}
		});
		$form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
		$form->setContent("??l??c?????e B???n C?? Ch???c Ch???n Mu???n H???y Nhi???m V??? ??b?????t Block??e Kh??ng ?");
		$form->setButton1("??l??3?????2 Ch???c Ch???n ??3???");
		$form->setButton2("??l??3?????4 Kh??ng Ch???c Ch???n ??3???");
		$form->sendToPlayer($player);
	}

	public function KillOff($player){
		$form = new ModalForm(function(Player $player, $data){
			if($data === true){
				$this->qmk->set(strtolower($player->getName()), "off");
				$break = $this->player->get(strtolower($player->getName()))["Break"];
		        $place = $this->player->get(strtolower($player->getName()))["Place"];
		        $kill = $this->player->get(strtolower($player->getName()))["Kill"];
		        $this->player->set(strtolower($player->getName()), ["Break" => $break, "Place" => $place, "Kill" => 0]);
				$this->MessageOff($player);
			    return true;
			}
		});
		$form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
		$form->setContent("??l??c?????e B???n C?? Ch???c Ch???n Mu???n H???y Nhi???m V??? ??bGh???t Ng?????i Ch??i??e Kh??ng ?");
		$form->setButton1("??l??3?????2 Ch???c Ch???n ??3???");
		$form->setButton2("??l??3?????4 Kh??ng Ch???c Ch???n ??3???");
		$form->sendToPlayer($player);
	}

	public function MessageOff($player){
		$form = new SimpleForm(function(Player $player, $data){
			if($data == null){
				return true;
			}
			switch($data){
				case 0:
				    $this->MenuNhiemVu($player);
				break;
			}
		});
		$form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
		$form->addButton("??l??3?????2 Quay L???i ??3???");
		$form->sendToPlayer($player);
	}

	public function onBreak(BlockBreakEvent $ev){
		$player = $ev->getPlayer();
		if($this->qmb->get(strtolower($player->getName())) == "on"){
		    $break = $this->player->get(strtolower($player->getName()))["Break"];
		    $place = $this->player->get(strtolower($player->getName()))["Place"];
		    $kill = $this->player->get(strtolower($player->getName()))["Kill"];
		    $this->player->set(strtolower($player->getName()), ["Break" => $break+1, "Place" => $place, "Kill" => $kill]);
		    if($this->player->get(strtolower($player->getName()))["Break"] == $this->getConfig()->get("break-max")){
			    $this->player->set(strtolower($player->getName()), ["Break" => 0, "Place" => $place, "Kill" => $kill]);
			    $this->money->addMoney($player, $this->getConfig()->get("money-break-quest"));
			    $this->qmb->set(strtolower($player->getName()), "off");
			    $topquest = $this->topquest->get(strtolower($player->getName()));
			    $this->topquest->set(strtolower($player->getName()), $topquest+1);
			    $cq = $this->checkquest->get(strtolower($player->getName()));
			    $this->checkquest->set(strtolower($player->getName()), $cq+1);
		    }
		}
	}

	public function onPlace(BlockPlaceEvent $ev){
		$player = $ev->getPlayer();
		if($this->qmp->get(strtolower($player->getName())) == "on"){
		    $break = $this->player->get(strtolower($player->getName()))["Break"];
		    $place = $this->player->get(strtolower($player->getName()))["Place"];
		    $kill = $this->player->get(strtolower($player->getName()))["Kill"];
		    $this->player->set(strtolower($player->getName()), ["Break" => $break, "Place" => $place+1, "Kill" => $kill]);
		    if($this->player->get(strtolower($player->getName()))["Place"] == $this->getConfig()->get("place-max")){
		    	$this->player->set(strtolower($player->getName()), ["Break" => $break, "Place" => 0, "Kill" => $kill]);
		    	$this->money->addMoney($player, $this->getConfig()->get("money-place-quest"));
		    	$this->qmp->set(strtolower($player->getName()), "off");
		    	$topquest = $this->topquest->get(strtolower($player->getName()));
			    $this->topquest->set(strtolower($player->getName()), $topquest+1);
		    	$cq = $this->checkquest->get(strtolower($player->getName()));
		    	$this->checkquest->set(strtolower($player->getName()), $cq+1);
		    }
		}
	}

	public function onKill(PlayerDeathEvent $event){
		$player = $event->getPlayer();
		$entity = $player->getLastDamageCause();
		if($entity instanceof EntityDamageByEntityEvent){
			$damager = $entity->getDamager();
			if($damager instanceof Player){
				$break = $this->player->get(strtolower($player->getName()))["Break"];
		        $place = $this->player->get(strtolower($player->getName()))["Place"];
		        $kill = $this->player->get(strtolower($player->getName()))["Kill"];
		        $this->player->set(strtolower($player->getName()), ["Break" => $break, "Place" => $place, "Kill" => $kill+1]);
		        if($this->player->get(strtolower($player->getName()))["Kill"] == $this->getConfig()->get("kill-max")){
		    	    $this->player->set(strtolower($player->getName()), ["Break" => $break, "Place" => $place, "Kill" => 0]);
		    	    $this->money->addMoney($player, $this->getConfig()->get("money-kill-quest"));
		    	    $this->qmk->set(strtolower($player->getName()), "off");
		    	    $topquest = $this->topquest->get(strtolower($player->getName()));
			        $this->topquest->set(strtolower($player->getName()), $topquest+1);
		    	    $cq = $this->checkquest->get(strtolower($player->getName()));
		    	    $this->checkquest->set(strtolower($player->getName()), $cq+1);
		    	}
			}
		}
	}

	public function TopQuest($player){
		$topquest = $this->topquest->getAll();
		$msg = "";
		$msg1 = "";
		if(count($topquest) > 0){
			arsort($topquest);
			$i = 1;
			foreach($topquest as $name => $quest){
				$msg .= "??l??c?????e TOP??3 " . $i . "??a " . $name . " ??c??? ??b" . $quest . " ??eNhi???m V???\n";
				$msg1 .= "??l??c?????e TOP??3 " . $i . "??a " . $name . " ??c??? ??b" . $quest . " ??eNhi???m V???\n";
				if($i >= 100){
					break;
				}
				++$i;
			}
		}
		
		$form = new SimpleForm(function (Player $player, ?int $data = null){
			$result = $data;
			switch($result){
				case 0:
				break;
			}
		});
		$form->setTitle("??l??6?????2 Nhi???m V??? ??6???");
		$form->setContent("??l??c?????e Danh S??ch TOP 100 Ng?????i Ho??n Th??nh Nhi???m V???:");
		$form->setContent($msg);
		$form->addButton("??l??3?????4 Tho??t ??3???");
		$form->sendToPlayer($player);
		return true;
	}

    //Reset Nhi???m V???
	public function CheckTime(){
		date_default_timezone_set('Asia/Ho_Chi_Minh');
        $h = date("h");
        $m = date("m");
        $s = date("s");
        if($h == 23 || $m == 59 || $s == 59){
           $allplayer = $this->player->getAll();
           $this->player->set(strtolower($allplayer), ["Break" => 0, "Place" => 0, "Kill" => 0]);
           $ab = $this->qmb->getAll();
           $ap = $this->qmb->getAll();
           $ak = $this->qmb->getAll();
           $this->qmb->set(strtolower($ab), "off");
           $this->qmp->set(strtolower($ap), "off");
           $this->qmk->set(strtolower($ak), "off");
           $this->getServer()->broadcastMessage($this->getConfig()->get("msg-reset"));
        }
    }
}