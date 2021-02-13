<?php

namespace AriefaL0677\lobbycore\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use AriefaL0677\lobbycore\Loader;

class FireworkCooldown extends Task {

	private $plugin;

	private $playerName;

	public function __construct(Loader $plugin, $playerName) {
		$this->plugin = $plugin;
		$this->playerName = $playerName;
		$this->plugin->fwCooldown = $this->plugin->getConfigs()["cooldown"]["firework"];
	}
	
	public function onRun(int $currentTick) {
		$pl = $this->plugin;
		$player = $pl->getServer()->getPlayerExact($this->playerName);
		if ($player instanceof Player) {
			if($pl->fwCooldown === 0){
				unset($pl->fwPlayer[$player->getName()]);
				$pl->getScheduler()->cancelTask($this->getTaskId());
			}
			$pl->fwCooldown--;
		}
	}
}