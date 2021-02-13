<?php

namespace AriefaL0677\lobbycore;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent, PlayerRespawnEvent, PlayerInteractEvent, PlayerDropItemEvent};
use pocketmine\utils\{Config, TextFormat};
use pocketmine\item\Item;
use pocketmine\level\Position;
use AriefaL0677\lobbycore\task\FlyCooldown;
use AriefaL0677\lobbycore\task\FireworkCooldown;

class EventListener implements Listener {

	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$pl = $this->plugin;
		$player->setScale(1);
		
		foreach($pl->getServer()->getOnlinePlayers() as $players) {
			if($players instanceof Player){
				$player->showPlayer($players); // Showing
			}
		}
		
		if(isset($pl->hidePlayer[$player->getName()])){
			unset($pl->hidePlayer[$player->getName()]);
		}
		
		if($pl->inWorld($player->getLevel()->getName())){
			$player->getInventory()->clearAll();
			$player->setGamemode($player::ADVENTURE);
			$pl->onLobbyCore($player);
		}
	}
	
	public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$pl = $this->plugin;
		
		if(isset($pl->flyPlayer[$player->getName()])){
			$player->setAllowFlight(false);
			$player->setFlying(false);
			unset($pl->flyPlayer[$player->getName()]);
			unset($pl->flyCooldown[$player->getName()]);
		}
		
		if(isset($pl->fwPlayer[$player->getName()])){
			unset($pl->fwPlayer[$player->getName()]);
			unset($pl->fwCooldown[$player->getName()]);
		}
	}
	
	public function onRespawn(PlayerRespawnEvent $event){
		$player = $event->getPlayer();
		$pl = $this->plugin;
		$player->setScale(1);
		
		if($pl->inWorld($player->getLevel()->getName())){
			$player->getInventory()->clearAll();
			$player->setGamemode($player::ADVENTURE);
			$pl->onLobbyCore($player);
		}
	}
	
	public function onDrop(PlayerDropItemEvent $event){
		$player = $event->getPlayer();
		$pl = $this->plugin;
		$item = $event->getItem();
		
		if(!$player instanceof Player) return;
		
		if($pl->inWorld($player->getLevel()->getName())){
			switch($item->getName()){
				case "§l§6Information\n§r§fClick right or tap hold\n§fOpen to form Information":
				case "§l§eTravel\n§r§fClick right or tap hold\n§fOpen to form Warps":
				case "§l§bProfile\n§r§fClick right or tap hold\n§fShow your profile":
				case "§l§9Fiture\n§r§fClick right or tap hold":
				case "§l§cHide Players\n§r§fClick right or tap hold\n§fHidden or show players":
				case "§l§9Fly Mode\n§r§fClick right or tap hold\n§fTurn On or Off mode fly":
				case "§l§3Fireworks Launcher\n§r§fClick right or tap hold\n§fTo launcher fireworks":
				case "§l§2Size Player\n§r§fClick right or tap hold\n§fOpen form":
				case "§l§cBack\n§r§fClick right or tap hold\n§fBack to menu core":
					$event->setCancelled(true);
				break;
			}
		}
	}
	
	public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
		$pl = $this->plugin;
		$item = $player->getInventory()->getItemInHand();
		
		if(!$player instanceof Player) return;
		
		if($pl->inWorld($player->getLevel()->getName())){
			// Use Firework Cancelled
			if($item->getId() === 401){
				$event->setCancelled(true);
			}
			
			if($event->getAction() === $event::RIGHT_CLICK_AIR){
				switch($item->getName()){
					case "§l§6Information\n§r§fClick right or tap hold\n§fOpen to form Information":
						$pl->getFormInfo()->onInformation($player);
					break;
					case "§l§eTravel\n§r§fClick right or tap hold\n§fOpen to form Warps":
						$pl->getFormTraveling()->onTraveling($player);
					break;
					case "§l§bProfile\n§r§fClick right or tap hold\n§fShow your profile":
						$configForm = $pl->getForm()["formProfile"];
						if(!isset($configForm["type"])){
							$player->sendMessage(Loader::PREFIX." §cYou type used don't work! Please edit in form.yml");
							return false;
						}
						if(strtolower($configForm["type"]) === "form"){
							$pl->getFormProf()->onProfile($player);
							return false;
						}
						if(strtolower($configForm["type"]) === "message"){
							$message = $configForm["message"];
							$c = 0;
							foreach ($message as $msg) {
								$player->sendMessage($pl->getMsg($player, $msg));
								$c++;
							}
							return false;
						}
					break;
					case "§l§9Fiture\n§r§fClick right or tap hold":
						$player->getInventory()->clearAll();
						$pl->onFiture($player);
					break;
					case "§l§cHide Players\n§r§fClick right or tap hold\n§fHidden or show players":
						if(!isset($pl->hidePlayer[$player->getName()])){
							foreach($pl->getServer()->getOnlinePlayers() as $players) {
								if($players instanceof Player){
									$player->hidePlayer($players); // Hidden
									$pl->hidePlayer[$player->getName()] = $player->getName();
								}
							}
						}else{
							foreach($pl->getServer()->getOnlinePlayers() as $players) {
								if($players instanceof Player){
									$player->showPlayer($players); // Showing
									unset($pl->hidePlayer[$player->getName()]);
								}
							}
						}
					break;
					case "§l§9Fly Mode\n§r§fClick right or tap hold\n§fTurn On or Off mode fly":
						if(!$player->isCreative()){
							if(!isset($pl->flyPlayer[$player->getName()])){
								$player->setAllowFlight(true);
								$player->setFlying(true);
								$pl->flyPlayer[$player->getName()] = $player->getName();
								$player->sendTip("§l§9Fly Mode §aON");
								$pl->getScheduler()->scheduleRepeatingTask(new FlyCooldown($pl, $player->getName()), 20);
							}
						}else{
							$player->sendTip($pl->getMsg($player, $pl->getConfigs()["message"]["oncreative"]));
						}
					break;
					case "§l§3Fireworks Launcher\n§r§fClick right or tap hold\n§fTo launcher fireworks":
						if($pl->getServer()->getPluginManager()->getPlugin("Fireworks") !== null) {
							if(!isset($pl->fwPlayer[$player->getName()])){
								$pl->addFireworks($player);
								$pl->fwPlayer[$player->getName()] = $player->getName();
								$pl->getScheduler()->scheduleRepeatingTask(new FireworkCooldown($pl, $player->getName()), 20);
							}else{
								$cooldown = $pl->calculateTime($pl->fwCooldown);
								$player->sendTip(str_replace(["{prefix}", "{player}", "{cooldown}", "&"], [Loader::PREFIX, $player->getName(), $cooldown, "§"], $pl->getConfigs()["message"]["cooldown"]));
							}
						}else{
							$player->sendTip("#Plugin Fireworks not found!");
						}
					break;
					case "§l§2Size Player\n§r§fClick right or tap hold\n§fOpen form":
						$pl->getFormSizePlayer()->onSizePlayer($player);
					break;
					case "§l§cBack\n§r§fClick right or tap hold\n§fBack to menu core":
						$player->getInventory()->clearAll();
						$pl->onLobbyCore($player);
					break;
				}
			}
		}
	}
}
