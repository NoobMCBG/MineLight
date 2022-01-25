<?php

namespace phuongaz\CustomWings\Forms;


use jojoe77777\FormAPI\{
	SimpleForm,
	CustomForm
};

use phuongaz\CustomWings\Loader;

use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
Class ShowWingsForm {

	private static $wings = [];

	public function ShowForm($player) :void{
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			$wing = self::$wings[$data];
			$perm = Loader::getInstance()->checkPermission($player, $wing);
			if($perm){
				Loader::getInstance()->addWing($player, true, $wing);
				Loader::getInstance()->removeWing($player);
				Loader::getInstance()->addWing($player, true, $wing);
				$player->sendMessage(TF::DARK_BLUE. TF::BOLD. "♦ ". TF::WHITE ."You have worn the wing successfully");				
			}else $player->sendMessage(TF::YELLOW. TF::BOLD. "♦ ". TF::RED ."You are not allowed to use this wing");
		});
		$form->setTitle(TF::BOLD. TF::WHITE. "ɷ ". TF::DARK_BLUE. "WINGS" . TF::WHITE. " ɷ");
		foreach(self::$wings as $wing) { 
			$form->addButton(TF::BOLD . TF::WHITE. "꒰ " .TF::DARK_BLUE. $wing .TF::WHITE." ꒱");
		}
		$form->sendToPlayer($player);
	}

	public function startForm($player) :void{
		$form = new SimpleForm(function(Player $player, ?int $data){
			if(is_null($data)) return;
			if($data == 0) $this->ShowForm($player);
			if(!empty(Loader::$players[strtolower($player->getName())])){
				if($data == 1) {
					Loader::getInstance()->removeWing($player);
					$player->sendMessage(TF::DARK_BLUE. TF::BOLD. "♦ ". TF::WHITE  ."You turn off the wing");						
				}

			}
		});
		$form->setTitle(TF::BOLD. TF::WHITE. "ɷ ". TF::DARK_BLUE. "WINGS" . TF::WHITE. " ɷ");
		$form->addButton(TF::BOLD. TF::YELLOW. "⚑ ".TF::DARK_RED. "Show wings" .TF::YELLOW. " ⚑");
		$form->addButton(TF::BOLD. TF::YELLOW. "⚑ ".TF::DARK_RED. "Remove Wings". TF::YELLOW. " ⚑");
		$form->sendToPlayer($player);
	}

	public static function addWings(string $wing) :void {
		self::$wings[] = $wing;
	}
}