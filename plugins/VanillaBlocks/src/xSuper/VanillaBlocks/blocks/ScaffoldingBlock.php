<?php

namespace xSuper\VanillaBlocks\blocks;

use JavierLeon9966\ExtendedBlocks\block\PlaceholderTrait;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ScaffoldingBlock extends Transparent {
    use PlaceholderTrait;

    public function __construct(int $meta = 0)
    {
        parent::__construct(VanillaBlockIds::SCAFFOLDING, $meta, "Scaffolding");
    }

    public function getBlastResistance(): float
    {
        return 0;
    }

    public function getHardness(): float
    {
        return 0;
    }
}



