<?php

declare(strict_types = 1);

/**
 * @name ToolLevelAddon
 * @version 1.0.0
 * @main JackMD\ScoreHud\Addons\ToolLevelAddon
 * @depend ToolLevels
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use pocketmine\Player;

	class ToolLevelAddon extends AddonBase{

		/** @var toollevel */
		private $toollevel;

		public function onEnable(): void{
			$this->toollevel = $this->getServer()->getPluginManager()->getPlugin("ToolLevels");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{tl}" => $this->toollevel->getCap($player)
			];
		}
	}
}