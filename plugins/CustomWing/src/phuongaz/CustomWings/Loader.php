<?php 

namespace phuongaz\CustomWings;

use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\LevelException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\event\{Listener, block\BlockBreakEvent, player\PlayerInteractEvent};
use pocketmine\utils\TextFormat as TF;
use phuongaz\CustomWings\{
	Forms\ShowWingsForm,
	Entitties\WingsEntity,
	Entitties\DropEntity,
	Commands\WingsCommands
};

Class Loader extends PluginBase {

	public static $wings = [];
	public static $players = [];
	public static $skins = [];
	public static $instance;
	public static $entity;

	public function onEnable() :void
	{
		$this->getServer()->getCommandMap()->register("wings", new WingsCommands());
		$this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
		$this->saveDefaultConfig();
		self::$instance = $this;
		$this->saveResource("Angel.png");
		$this->saveResource("Angel.json");
		//$this->saveResource("Devil.png");
		//$this->saveResource("Devil.json");
		$angelJson = file_get_contents($this->getDataFolder() . "Angel.json");
		foreach (glob($this->getDataFolder() . "*.png") as $imagePath) {
			$json = $angelJson;
			$fileName = pathinfo($imagePath, PATHINFO_FILENAME);
			$jsonPath = pathinfo($imagePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $fileName . ".json";
			if (file_exists($jsonPath)) $json = file_get_contents($jsonPath);
			$skin = $this->getSkin($fileName);
			// new Skin("wing.$fileName", self::fromImage(imagecreatefrompng($imagePath)), "", "geometry.wing.$fileName", $json);
			if (!$skin->isValid()) {
				var_dump(strlen($skin->getSkinData()));
				$this->getLogger()->error("Resulting skin of $fileName is not valid");
				continue;
			}
			self::$skins[$fileName] = $skin;
			ShowWingsForm::addWings($fileName);

		}
		Entity::registerEntity(DropEntity::class, true);
		Entity::registerEntity(WingsEntity::class, true, ['wing']);
		self::$skins["none"] = null;

	}

	public static function getInstance() {
		return self::$instance;
	}

	public static function fromImage($img)
	{
		$bytes = '';
		for ($y = 0; $y < imagesy($img); $y++) {
			for ($x = 0; $x < imagesx($img); $x++) {
				$rgba = @imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		@imagedestroy($img);
		return $bytes;
	}



   public function convertSize($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        return $dst;
    }

	public function getSkin(string $filename){
		$path = $this->getDataFolder(). $filename.".png";
        if(!file_exists($path)) return null;

        $size = getimagesize($path);

        $path = $this->imgTricky($path, $filename, [$size[0], $size[1], 4]);
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($img);
        return new Skin("Standard_CustomSlim", $skinbytes, "", "geometry.wing.".$filename, file_get_contents($this->getDataFolder(). $filename .".json"));
	}

    public function imgTricky(string $skinPath, string $stuffName, array $size)
    {
        $path = $this->getDataFolder();
        $down = imagecreatefrompng($skinPath);
        $upper = null;
        if ($size[0] * $size[1] * $size[2] >= 65536) {
            $upper = $this->resize_image($skinPath, 128, 128);
        } else {
            $upper = $this->resize_image($skinPath, 64, 64);
        }
       // $upper = $this->resize_image($path, 64, 64);
        //var_dump($upper);
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));
        imagealphablending($down, true);
        imagesavealpha($down, true);
        imagecopymerge($down, $upper, 0, 0, 0, 0, $size[0], $size[1], 100);
        imagepng($down, $path . "$stuffName.png");
        return $this->getDataFolder() . "$stuffName.png";

    }

    public function resize_image($file, $w, $h, $crop = FALSE)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }

	public function checkPermission($player,?string $wing) :bool{
		if($player->hasPermission($wing. ".wing")){
			return true;
		}
		return false;
	}

	public static function wearsWing(?Entity $player): bool
	{
		return $player instanceof Player && $player->getGenericFlag(Entity::DATA_FLAG_CHESTED);
	}

	public static function addWing(Player $player, bool $ride = true, String $type = "default"): WingsEntity
	{	
		if(!empty(self::$players[strtolower($player->getName())])){
			$wing = self::$players[strtolower($player->getName())];
			$wing->close();
			unset($wing);
		}
		if (self::$skins[$type] instanceof Skin) {
			$player->setGenericFlag(Entity::DATA_RIDER_ROTATION_LOCKED, true);
			$nbt = Entity::createBaseNBT($player, null, $player->getYaw());
			$entity = new WingsEntity(self::$skins[$type], $player->getLevel(), $nbt);
			self::$players[strtolower($player->getName())] = $entity;
			$entity->setOwningEntity($player);
			$player->getLevel()->addEntity($entity);
			$entity->spawnToAll();
			if ($ride) {
				$player->setGenericFlag(Entity::DATA_FLAG_CHESTED, true);
			}
			return $entity;
		}
	}

	public function removeWing($player){
		if(empty(self::$players[strtolower($player->getName())])); return;
		if(self::$players[strtolower($player->getName())] !== null){
			$wing = self::$players[strtolower($player->getName())];
			$wing->close();
			unset(self::$players[strtolower($player->getName())]);
		}
	}

	public static function sendForm($player) :void{ 
		$form = new ShowWingsForm();
		$form->startForm($player);
	}

	public static function dropWing(string $name, Position $pos, string $namew = ""){
		$skin = self::$skins[$name];
		$nbt = Entity::createBaseNBT($pos);
        $nbt->setTag(new CompoundTag("Skin", [
            "Data" => new StringTag("Data", $skin->getSkinData()),
            "Name" => new StringTag("Name", "Wing")
        ]));
		$entity = new DropEntity($pos->getLevel(), $nbt);
		$entity->setSkin($skin);
		$entity->setCTName($name);
		$entity->setNameTagVisible(true);
		$entity->setNameTagAlwaysVisible(true);
		$entity->setScale(0.3);
		$entity->spawnToAll();
	}
}