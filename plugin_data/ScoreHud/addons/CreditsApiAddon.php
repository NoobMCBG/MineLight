<?php

declare(strict_types = 1);

/**
 * @name CreditsApiAddon
 * @version 1.0.0
 * @main JackMD\ScoreHud\Addons\CreditsApiAddon
 * @depend CreditsAPI
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class CreditsApiAddon extends AddonBase{

		/** @var creditsAPI */
		private $creditsAPI;

		public function onEnable(): void{
			$this->creditsAPI = $this->getServer()->getPluginManager()->getPlugin("CreditsAPI");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{credits}" => $this->creditsAPI->myCredits($player)
			];
		}
	}
}