<?php
declare(strict_types = 1);

/**
 * @name NgayDiSanAddon
 * @version 1.0.0
 * @main JackMD\ScoreHud\Addons\NgayDiSanAddon
 * @depend NgayDiSan
 */
namespace JackMD\ScoreHud\Addons
{
	use JackMD\ScoreHud\addon\AddonBase;
	use NoobMCBG\NDS\Main;
	use pocketmine\Player;

	class NgayDiSanAddon extends AddonBase{

		/** @var NgayDiSan */
		private $nds;

		public function onEnable(): void{
			$this->nds = $this->getServer()->getPluginManager()->getPlugin("NgayDiSan");
		}

		/**
		 * @param Player $player
		 * @return array
		 */
		public function getProcessedTags(Player $player): array{
			return [
				"{nds}" => $this->nds->getNDS()
			];
		}
	}
}