<?php

namespace AriefaL0677\lobbycore\command;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use AriefaL0677\lobbycore\Loader;

class LBCommand extends Command implements PluginIdentifiableCommand {
	
	public $plugin;
	
	public $mode = [];
	
	public function __construct(Loader $plugin) {
		$this->plugin = $plugin;
		parent::__construct("lobbycore", "LobbyCore commmand", \null, ["lb"]);
		$this->setPermission("lb.cmd.setinfo");
	}
	
	public function execute(CommandSender $player, string $commandLabel, array $args) {
		$pl = $this->plugin;
		if(!$this->testPermission($player)) {
			return false;
		}
		if(!isset($args[0])) {
			$player->sendMessage(Loader::PREFIX . " §cUsage: §f/lb setinfo <content>");
			return false;
		}
		switch($args[0]) {
			case "setinfo":
				if(!isset($args[1])){
					$player->sendMessage($pl->getMsg($player, $pl->getConfigs()["message"]["nocontect"]));
					break;
				}
				
				if(isset($args[1])) {
					array_shift($args);
					$content = str_replace(["&", "{line}"], ["§", "\n"], trim(implode(" ", $args)));
					$pl->info->set("Text", $content);
					$pl->info->save();
					$player->sendMessage(str_replace(["&", "{line}"], ["§", "\n"], $pl->getConfigs()["message"]["succcontect"]));
					break;
				}
			break;
            default:
                $player->sendMessage(Loader::PREFIX . " §cUsage: §f/lb setinfo <content>");
			break;
        }
    }
	
	public function getPlugin(): Plugin {
        return $this->plugin;
    }
}
