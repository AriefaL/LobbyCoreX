<?php

namespace AriefaL0677\lobbycore\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use AriefaL0677\lobbycore\Loader;

class FlyCooldown extends Task{
	
	private $plugin;

	private $playerName;

	public function __construct(Loader $plugin, $playerName) {
		$this->plugin = $plugin;
		$this->playerName = $playerName;
		$this->plugin->flyCooldown = $this->plugin->getConfigs()["cooldown"]["fly"];
	}

	public function onRun(int $currentTick) {
		$pl = $this->plugin;
		$player = $pl->getServer()->getPlayerExact($this->playerName);
		if ($player instanceof Player) {
			if($pl->flyCooldown >= 1){
				$cooldown = $pl->calculateTime($pl->flyCooldown);
				$player->sendTip(str_replace(["{prefix}", "{player}", "{cooldown}", "&"], [Loader::PREFIX, $player->getName(), $cooldown, "§"], $pl->getConfigs()["message"]["cooldown"]));
			}
			if($pl->flyCooldown === 0){
				$player->setAllowFlight(false);
				$player->setFlying(false);
				$player->sendTip("§l§9Fly Mode §cOFF");
				unset($pl->flyPlayer[$player->getName()]);
				$pl->getScheduler()->cancelTask($this->getTaskId());
			}
			$pl->flyCooldown--;
		}
	}
}