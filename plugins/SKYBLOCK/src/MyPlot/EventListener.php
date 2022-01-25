<?php
namespace MyPlot;

use pocketmine\block\Sapling;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\Listener;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\utils\Config;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use pocketmine\block\Lava;
use pocketmine\item\Item;
use pocketmine\block\Water;

class EventListener implements Listener
{
    /** @var MyPlot */
    private $plugin;

    public function __construct(MyPlot $plugin){
        $this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onLevelLoad(LevelLoadEvent $event) {
        if ($event->getLevel()->getProvider()->getGenerator() === "myplot") {
            $settings = $event->getLevel()->getProvider()->getGeneratorOptions();
            if (isset($settings["preset"]) === false or $settings["preset"] === "") {
                return;
            }
            $settings = json_decode($settings["preset"], true);
            if ($settings === false) {
                return;
            }
            $levelName = $event->getLevel()->getName();
            $filePath = $this->plugin->getDataFolder() . "worlds/" . $levelName . ".yml";
            $config = $this->plugin->getConfig();
            $default = [
			"MaxPlotsPerPlayer" => $config->getNested("DefaultWorld.MaxPlotsPerPlayer"),
                "RestrictEntityMovement" => $config->getNested("DefaultWorld.RestrictEntityMovement"),
                "ClaimPrice" => $config->getNested("DefaultWorld.ClaimPrice"),
                "ClearPrice" => $config->getNested("DefaultWorld.ClearPrice"),
                "DisposePrice" => $config->getNested("DefaultWorld.DisposePrice"),
                "ResetPrice" => $config->getNested("DefaultWorld.ResetPrice"),
            ];
            $config = new Config($filePath, Config::YAML, $default);
            foreach (array_keys($default) as $key) {
                $settings[$key] = $config->get($key);
            }
            $this->plugin->addLevelSettings($levelName, new PlotLevelSettings($levelName, $settings));
        }
    }
    
    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $id = $event->getItem()->getId();
        $count = $event->getItem()->getCount();
        $food = $player->getFood();
        if($food <= 17){
            if($id == 297){
               $item = Item::get(297, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 297){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
               }
            }
        if($food <= 17){
           if($id == 260){
               $item = Item::get(260, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 260){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
       }
       if($food <= 17){
           if($id == 319){
               $item = Item::get(319, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 319){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
        if($food <= 17){
           if($id == 320){
               $item = Item::get(320, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 320){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
          if($food <= 17){
           if($id == 360){
               $item = Item::get(360, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 360){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
          if($food <= 17){
           if($id == 363){
               $item = Item::get(363, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 363){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
         }
          if($food <= 17){
           if($id == 364){
               $item = Item::get(364, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 364){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
         }
         if($food <= 17){
           if($id == 365){
               $item = Item::get(360, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 365){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
         }
          if($food <= 17){
           if($id == 366){
               $item = Item::get(366, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 366){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
          }
          if($food <= 17){
           if($id == 391){
               $item = Item::get(391, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 391){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
          }
          if($food <= 17){
           if($id == 392){
               $item = Item::get(392, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 392){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
          if($food <= 17){
           if($id == 400){
               $item = Item::get(400, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 400){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
          }
          if($food <= 17){
           if($id == 411){
               $item = Item::get(411, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 411){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
          if($food <= 17){
           if($id == 412){
               $item = Item::get(412, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 412){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
         }
          if($food <= 17){
           if($id == 423){
               $item = Item::get(423, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 423){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
          if($food <= 17){
           if($id == 424){
               $item = Item::get(424, 0, $count - 1);
        	   $player->getInventory()->setItemInHand($item);
        	   $player->setFood($food + 3);
            }
        }
        if($food > 17){
          if($id == 424){
            $item = Item::get($id, 0, $count - 1);
        	$player->getInventory()->setItemInHand($item);
        	$player->setFood(20);
            }
        }
   }
    
    public function onLevelUnload(LevelUnloadEvent $event) {
        $levelName = $event->getLevel()->getName();
        $this->plugin->unloadLevelSettings($levelName);
    }

    public function onBlockPlace(BlockPlaceEvent $event) {
        $this->onEventOnBlock($event);
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $this->onEventOnBlock($event);
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $this->onEventOnBlock($event);
    }

    public function onExplosion(EntityExplodeEvent $event) {
        $levelName = $event->getEntity()->getLevel()->getName();
        if (!$this->plugin->isLevelLoaded($levelName))
            return;

        $plot = $this->plugin->getPlotByPosition($event->getPosition());
        if ($plot === null) {
            $event->setCancelled(true);
            return;
        }
        $beginPos = $this->plugin->getPlotPosition($plot);
        $endPos = clone $beginPos;
        $plotSize = $this->plugin->getLevelSettings($levelName)->plotSize;
        $endPos->x += $plotSize;
        $endPos->z += $plotSize;
        $blocks = array_filter($event->getBlockList(), function($block) use($beginPos, $endPos) {
            if ($block->x >= $beginPos->x and $block->z >= $beginPos->z and $block->x < $endPos->x and $block->z < $endPos->z) {
                return true;
            }
            return false;
        });
        $event->setBlockList($blocks);
    }

    /**
     * @param BlockPlaceEvent|BlockBreakEvent|PlayerInteractEvent $event
     */
    private function onEventOnBlock($event) {
        $levelName = $event->getBlock()->getLevel()->getName();
        if (!$this->plugin->isLevelLoaded($levelName)) {
            return;
        }
        $plot = $this->plugin->getPlotByPosition($event->getBlock());
        if ($plot !== null) {
            $username = $event->getPlayer()->getName();
            if ($plot->owner == $username or $plot->isHelper($username) or $event->getPlayer()->hasPermission("myplot.admin.build.plot")) {
                if (!($event instanceof PlayerInteractEvent and $event->getBlock() instanceof Sapling))
                    return;

                /*
                 * Prevent growing a tree near the edge of a plot
                 * so the leaves won't go outside the plot
                 */
                $block = $event->getBlock();
                $maxLengthLeaves = (($block->getDamage() & 0x07) == Sapling::SPRUCE) ? 3 : 2;
                $beginPos = $this->plugin->getPlotPosition($plot);
                $endPos = clone $beginPos;
                $beginPos->x += $maxLengthLeaves;
                $beginPos->z += $maxLengthLeaves;
                $plotSize = $this->plugin->getLevelSettings($levelName)->plotSize;
                $endPos->x += $plotSize - $maxLengthLeaves;
                $endPos->z += $plotSize - $maxLengthLeaves;

                if ($block->x >= $beginPos->x and $block->z >= $beginPos->z and $block->x < $endPos->x and $block->z < $endPos->z) {
                    return;
                }
            }
        } elseif ($event->getPlayer()->hasPermission("myplot.admin.build.road")) {
            return;
        }
        $event->setCancelled(true);
    }

    public function onEntityMotion(EntityMotionEvent $event) {
        $levelName = $event->getEntity()->getLevel()->getName();
        if (!$this->plugin->isLevelLoaded($levelName))
            return;

        $settings = $this->plugin->getLevelSettings($levelName);
        if ($settings->restrictEntityMovement and !($event->getEntity() instanceof Player)) {
            $event->setCancelled(true);
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event) {
        if (!$this->plugin->getConfig()->get("ShowPlotPopup", true))
            return;

        $levelName = $event->getPlayer()->getLevel()->getName();
        if (!$this->plugin->isLevelLoaded($levelName))
            return;

        $plot = $this->plugin->getPlotByPosition($event->getTo());
        if ($plot !== null and $plot !== $this->plugin->getPlotByPosition($event->getFrom())) {
            $plotName = TextFormat::GREEN . $plot;
            $popup = $this->plugin->getLanguage()->translateString("popup", [$plotName]);
            if ($plot->owner != "") {
                $owner = TextFormat::GREEN . $plot->owner;
                $ownerPopup = $this->plugin->getLanguage()->translateString("popup.owner", [$owner]);
                $paddingSize = floor((strlen($popup) - strlen($ownerPopup)) / 2);
                $paddingPopup = str_repeat(" ", max(0, -$paddingSize));
                $paddingOwnerPopup = str_repeat(" ", max(0, $paddingSize));
                $popup = TextFormat::WHITE . $paddingPopup . $popup . "\n" .
                         TextFormat::WHITE . $paddingOwnerPopup . $ownerPopup;
            } else {
                $ownerPopup = $this->plugin->getLanguage()->translateString("popup.available");
                $paddingSize = floor((strlen($popup) - strlen($ownerPopup)) / 2);
                $paddingPopup = str_repeat(" ", max(0, -$paddingSize));
                $paddingOwnerPopup = str_repeat(" ", max(0, $paddingSize));
                $popup = TextFormat::WHITE . $paddingPopup . $popup . "\n" .
                         TextFormat::WHITE . $paddingOwnerPopup . $ownerPopup;
            }
            $event->getPlayer()->sendMessage($popup);
        }
    }
}
