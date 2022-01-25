<?php

declare(strict_types=1);

namespace HoiPhuc;

use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\block\Solid;
use pocketmine\block\Stair;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{

    /** @var array $sittingData */
    public $sittingData = [];

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (!$sender instanceof Player){
            return true;
        }

        switch (strtolower($command->getName())) {
            case "hoiphuc":
                if ($this->isSitting($sender)) {
                    $this->unsetSit($sender);
                    $sender->sendMessage("§l§d>§e Bạn Đã Dừng Hồi Phục Và Tu Luyện");
                } else {
                    $this->sit($sender, $sender->getLevelNonNull()->getBlock($sender->asPosition()->add(0, -0.5)));
                     $sender->sendMessage("§l§d>§e Bạn Đang Hồi Phục Và Tu Luyện");                   
                  
                }
                break;
            case "hoiphuckick":
                if (isset($args[0])) {
                    $player = $this->getServer()->getPlayer($args[0]);

                    if ($player !== null) {
                        if ($this->isSitting($player)) {
                            $this->unsetSit($player);
                        }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Player: '$args[0]' not found!");
                    }
                }
                    return false;
        }

        return true;
    }

    public function isSitting(Player $player): bool
    {
        return isset($this->sittingData[$player->getLowerCaseName()]);
    }

    public function sit(Player $player, Block $block)
    {
        if ($block instanceof Stair or $block instanceof Slab) {
            $pos = $block->asVector3()->add(0.5, 1.5, 0.5);
        } elseif ($block instanceof Solid) {
            $pos = $block->asVector3()->add(0.5, 2.1, 0.5);
        } else return;



        foreach ($this->sittingData as $playerName => $data) {
            if ($pos->equals($data['pos'])) {

                return;
            }
        }

        if ($this->isSitting($player)) {
            return;
        }

        $this->setSit($player, $this->getServer()->getOnlinePlayers(), new Position($pos->x, $pos->y, $pos->z, $this->getServer()->getLevelByName($player->getLevel()->getFolderName())));
    }

    public function setSit(Player $player, array $viewers, Position $pos, int $eid = 0)
    {
        if ($eid === 0) {
            $eid = Entity::$entityCount++;
        }

        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $eid;
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[Entity::WOLF]; // i love wolf

        $pk->position = $pos->asVector3();
        $pk->metadata = [Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, (1 << Entity::DATA_FLAG_IMMOBILE | 1 << Entity::DATA_FLAG_SILENT | 1 << Entity::DATA_FLAG_INVISIBLE)]];

        $link = new SetActorLinkPacket();
        $link->link = new EntityLink($eid, $player->getId(), EntityLink::TYPE_RIDER, true, true);
        $player->setGenericFlag(Entity::DATA_FLAG_RIDING, true);

        $this->getServer()->broadcastPacket($viewers, $pk);
        $this->getServer()->broadcastPacket($viewers, $link);

        if ($this->isSitting($player)) {
            return;
        }

        $this->sittingData[$player->getLowerCaseName()] = [
            'eid' => $eid,
            'pos' => $pos
        ];
    }

    public function unsetSit(Player $player)
    {
        $pk1 = new RemoveActorPacket();
        $pk1->entityUniqueId = $this->sittingData[$player->getLowerCaseName()]['eid'];

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->sittingData[$player->getLowerCaseName()]['eid'], $player->getId(), EntityLink::TYPE_REMOVE, true, true);

        unset($this->sittingData[$player->getLowerCaseName()]);

        $player->setGenericFlag(Entity::DATA_FLAG_RIDING, false);


        $this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk1);
        $this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
    }

    public function optimizeRotation(Player $player)
    {
        $pk = new MoveActorAbsolutePacket();
        $pk->position = $this->sittingData[$player->getLowerCaseName()]['pos'];
        $pk->entityRuntimeId = $this->sittingData[$player->getLowerCaseName()]['eid'];
        $pk->xRot = $player->getPitch();
        $pk->yRot = $player->getYaw();
        $pk->zRot = $player->getYaw();

        $this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
    }
}
