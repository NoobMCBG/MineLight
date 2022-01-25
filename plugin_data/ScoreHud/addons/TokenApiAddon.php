<?php

declare(strict_types = 1);

/**
 * @name TokenApiAddon
 * @version 1.0.0
 * @main JackMD\ScoreHud\Addons\TokenApiAddon
 * @depend TokenAPI
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class TokenApiAddon extends AddonBase{

		/** @var tokenAPI */
		private $tokenAPI;

		public function onEnable(): void{
			$this->tokenAPI = $this->getServer()->getPluginManager()->getPlugin("TokenAPI");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{token}" => $this->tokenAPI->myToken($player)
			];
		}
	}
}