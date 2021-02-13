<?php

namespace AriefaL0677\lobbycore;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\item\Item;
use pocketmine\utils\{Config, TextFormat};
use BlockHorizons\Fireworks\item\Fireworks;
use BlockHorizons\Fireworks\entity\FireworksRocket;
use _64FF00\PurePerms\PurePerms;
use SeeDevice\SeeDevice;
use AriefaL0677\lobbycore\command\LBCommand;
use AriefaL0677\lobbycore\form\FormInformation;
use AriefaL0677\lobbycore\form\FormProfile;
use AriefaL0677\lobbycore\form\FormTraveling;
use AriefaL0677\lobbycore\form\FormSizePlayer;

class Loader extends PluginBase{

	const PREFIX = "§l§9[§eLobby§6Core§9]§r";

	public static $instance;

	public $hidePlayer = [];

	public $flyPlayer = [];
	public $flyCooldown;

	public $fwPlayer = [];
	public $fwCooldown;

	public function onLoad() {
		$this->loadFormClass();
		$commands = [new LBCommand($this)];
		$this->getServer()->getCommandMap()->registerAll("LobbyCore", $commands);
	}

	public function onEnable(){
		self::$instance = $this;
		$this->saveResource("config.yml");
		$this->saveResource("form.yml");
		$this->saveResource("information.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->form = new Config($this->getDataFolder() . "form.yml", Config::YAML);
		$this->info = new Config($this->getDataFolder() . "information.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getLogger()->info("§6Enable Plugin §bLobby§7Core§aX");
		if($this->getServer()->getPluginManager()->getPlugin("Fireworks") === null) {
			$this->getLogger()->warning("Fireworks are required to complement the plugin's performance, download it here: https://github.com/BlockHorizons/Fireworks/tree/4.0/");
		}
		if($this->getServer()->getPluginManager()->getPlugin("PurePerms") === null) {
			$this->getLogger()->warning("PurePerms are required to complement the plugin's performance, download it here: https://poggit.pmmp.io/p/PurePerms/1.4.3");
		}
		if($this->getServer()->getPluginManager()->getPlugin("SeeDevice") === null) {
			$this->getLogger()->warning("SeeDevice are required to complement the plugin's performance, download it here: https://github.com/Palente/SeeDevice/tree/master/");
		}
	}
	
	public static function getInstance(): Loader {
		return self::$instance;
	}
	
	public function getConfigs(){
		return $this->config->getAll();
	}
	
	public function getForm(){
		return $this->form->getAll();
	}
	
	private function loadFormClass() : void {
		$this->formInfo = new FormInformation($this);
		$this->formProf = new FormProfile($this);
		$this->formTrav = new FormTraveling($this);
		$this->formSize = new FormSizePlayer($this);
	}
	
	function getFormInfo() : FormInformation {
		return $this->formInfo;
	}
	
	function getFormProf() : FormProfile {
		return $this->formProf;
	}
	
	function getFormTraveling() : FormTraveling {
		return $this->formTrav;
	}
	
	function getFormSizePlayer() : FormSizePlayer {
		return $this->formSize;
	}
	
	public function getFirstPlayer($player, string $type = "date"): string{
		if(strtolower($type) === "date"){
			$date = date("l, j F Y", ($first = $player->getFirstPlayed() / 1000));
			$time = date("h:ia", $first);
			return $date;
		}
		if(strtolower($type) === "time"){
			$date = date("l, j F Y", ($first = $player->getFirstPlayed() / 1000));
			$time = date("h:ia", $first);
			return $time;
		}
	}
	
	public function getLastPlayer($player, string $type = "date"): string{
		if(strtolower($type) === "date"){
			$date = date("l, j F Y", ($last = $player->getLastPlayed() / 1000));
			$time = date("h:ia", $last);
			return $date;
		}
		if(strtolower($type) === "time"){
			$date = date("l, j F Y", ($last = $player->getLastPlayed() / 1000));
			$time = date("h:ia", $last);
			return $time;
		}
	}
	
	public function calculateTime(int $time): string {
        return gmdate("i:s", $time);
    }
	
	public function inWorld(string $level){
		return array_search($level, $this->getConfigs()["world-list"]) !== false;
	}
	
	public function addFireworks(Position $pos){
		// Spawn rocket
		$data = new Fireworks();
		$data->addExplosion(mt_rand(1, 5), $this->getColor(), "",true, false);
		$data->setFlightDuration(mt_rand(1, 2));

		$nbt = Entity::createBaseNBT($pos, null, lcg_value() * 360, 90);
		$rocket = new FireworksRocket($pos->getLevel(), $nbt, $data);

		$rocket->spawnToAll();
	}
	
	public function getColor(): string {
		$colors = [Fireworks::COLOR_BLACK, Fireworks::COLOR_RED, Fireworks::COLOR_DARK_GREEN, Fireworks::COLOR_BROWN,
				   Fireworks::COLOR_BLUE, Fireworks::COLOR_DARK_PURPLE, Fireworks::COLOR_DARK_AQUA, Fireworks::COLOR_GRAY,
				   Fireworks::COLOR_DARK_GRAY, Fireworks::COLOR_PINK, Fireworks::COLOR_GREEN, Fireworks::COLOR_YELLOW,
				   Fireworks::COLOR_LIGHT_AQUA, Fireworks::COLOR_DARK_PINK, Fireworks::COLOR_GOLD, Fireworks::COLOR_WHITE];
		return $colors[rand(0, 15)];
	}
	
	public function onLobbyCore($player){
		$config = $this->getConfigs()["item"];
		$i = 0;
		while($i < 8){
			if($i !== 0 && $i !== 1 && $i !== 6 && $i !== 7 && $i !== 8){
				$player->getInventory()->setItem($i, Item::get(Item::AIR));
			}
			$i++;
		}
		if($config["info"] === true){
			$player->getInventory()->setItem(0, Item::get(Item::BOOK)->setCustomName("§l§6Information\n§r§fClick right or tap hold\n§fOpen to form Information"));
		}
		if($config["travel"] === true){
			$player->getInventory()->setItem(1, Item::get(Item::COMPASS)->setCustomName("§l§eTravel\n§r§fClick right or tap hold\n§fOpen to form Warps"));
		}
		if($config["profile"] === true){
			$player->getInventory()->setItem(6, Item::get(397, 3, 1)->setCustomName("§l§bProfile\n§r§fClick right or tap hold\n§fShow your profile"));
		}
		if($config["fiture"] === true){
			$player->getInventory()->setItem(7, Item::get(Item::EMERALD)->setCustomName("§l§9Fiture\n§r§fClick right or tap hold"));
		}
		if($config["hidden"] === true){
			$player->getInventory()->setItem(8, Item::get(Item::BLAZE_ROD)->setCustomName("§l§cHide Players\n§r§fClick right or tap hold\n§fHidden or show players"));
		}
	}
	
	public function onFiture($player){
		$config = $this->getConfigs()["item"];
		$i = 0;
		while($i < 8){
			if($i !== 0 && $i !== 1 && $i !== 2 && $i !== 8){
				$player->getInventory()->setItem($i, Item::get(Item::AIR));
			}
			$i++;
		}
		if($config["fly"] === true){
			$player->getInventory()->setItem(0, Item::get(Item::FEATHER)->setCustomName("§l§9Fly Mode\n§r§fClick right or tap hold\n§fTurn On or Off mode fly"));
		}
		if($config["firework"] === true){
			$player->getInventory()->setItem(1, Item::get(401)->setCustomName("§l§3Fireworks Launcher\n§r§fClick right or tap hold\n§fTo launcher fireworks"));
		}
		if($config["size"] === true){
			$player->getInventory()->setItem(2, Item::get(38)->setCustomName("§l§2Size Player\n§r§fClick right or tap hold\n§fOpen form"));
		}
		$player->getInventory()->setItem(8, Item::get(Item::NETHER_STAR)->setCustomName("§l§cBack\n§r§fClick right or tap hold\n§fBack to menu core"));
	}
	
	public function getMsg($player, string $message): string{
		$message = str_replace("{prefix}", self::PREFIX, $message);
		$message = str_replace("{player}", $player->getName(), $message);
		$message = str_replace("{ipAdd}", $player->getAddress(), $message);
		$message = str_replace("{ping}", $player->getPing(), $message);
		$message = str_replace("{OS}", $this->getPlayerOS($player), $message);
		$message = str_replace("{device}", $this->getPlayerDev($player), $message);
		$message = str_replace("{f_date}", $this->getFirstPlayer($player, "date"), $message);
		$message = str_replace("{f_time}", $this->getFirstPlayer($player, "time"), $message);
		$message = str_replace("{l_date}", $this->getLastPlayer($player, "date"), $message);
		$message = str_replace("{l_time}", $this->getLastPlayer($player, "time"), $message);
		$message = str_replace("{rank}", $this->getPlayerRank($player), $message);
		$message = str_replace("{size}", $player->getScale(), $message);
		$message = str_replace("{line}", "\n", $message);
		$message = str_replace("&", "§", $message);
		return $message;
	}
	
	public function getPlayerRank(Player $player): string{
		$purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
		if ($purePerms instanceof PurePerms) {
			$group = $purePerms->getUserDataMgr()->getData($player)['group'];
			if ($group !== null) {
				return $group;
			}else{
				return "No Rank";
			}
		}else{
			return "Plugin not found";
		}
	}
	
	public function getPlayerOS(Player $player): string{
		$seeDevice = $this->getServer()->getPluginManager()->getPlugin("SeeDevice");
		if ($seeDevice instanceof SeeDevice) {
			$oS = $seeDevice->getUos($player);
			return $oS;
		}else{
			return "Plugin not found";
		}
	}
	
	public function getPlayerDev(Player $player): string{
		$seeDevice = $this->getServer()->getPluginManager()->getPlugin("SeeDevice");
		if ($seeDevice instanceof SeeDevice) {
			$device = $seeDevice->getUsd($player);
			return $device;
		}else{
			return "Plugin not found";
		}
	}
	
	public function onDisable(){
		$this->getServer()->getLogger()->info("§cDisabled Plugin §bLobby§7Core§aX");
	}
}
