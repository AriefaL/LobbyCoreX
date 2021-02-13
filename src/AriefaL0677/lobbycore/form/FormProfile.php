<?php

namespace AriefaL0677\lobbycore\form;

use AriefaL0677\lobbycore\Loader;
use libs\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FormProfile{

	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function onProfile(Player $player){
		$pl = $this->plugin;
		$config = $pl->getForm()["formProfile"];
		$value = $config["content"];
		$form = new SimpleForm(function (Player $player, $data){
			$result = $data;
			if($result === null) {
				return true;
			}
			switch($result){
				case 0:
					
				break;
			}
		});
		$form->setTitle($pl->getMsg($player, $config["title"]));
		$name = null;
		if($value["name"] === true){
			$name = "§l§o§aYour Nick Name: §r§f".$player->getName()."\n \n";
		}
		$rank = null;
		if($value["rank"] === true){
			$rank = "§l§o§aRank / Roleplay: §r§f".$pl->getPlayerRank($player)."\n \n";
		}
		$ipAdd = null;
		if($value["ipAdd"] === true){
			$ipAdd = "§l§o§aIp-Address: §r§f".$player->getAddress()."\n \n";
		}
		$os = null;
		if($value["os"] === true){
			$os = "§l§o§aYour Device: §r§f".$pl->getPlayerOS($player)." at ".$pl->getPlayerDev($player)."\n \n";
		}
		$ping = null;
		if($value["ping"] === true){
			if($player->getPing() >= 0){
				$ping = "§l§o§aPing: §r§a".$player->getPing()."ms\n \n";
			}
			if($player->getPing() >= 100){
				$ping = "§l§o§aPing: §r§e".$player->getPing()."ms\n \n";
			}
			if($player->getPing() >= 200){
				$ping = "§l§o§aPing: §r§c".$player->getPing()."ms\n \n";
			}
		}
		$firstJoin = null;
		if($value["firstJoin"] === true){
			$date = date("l, j F Y", ($first = $player->getFirstPlayed() / 1000));
			$time = date("h:ia", $first);
			$firstJoin = "§l§o§aFirst Join Server: §r§f".$date." at ".$time."\n \n";
		}
		$lastJoin = null;
		if($value["lastJoin"] === true){
			$date = date("l, j F Y", ($last = $player->getLastPlayed() / 1000));
			$time = date("h:ia", $last);
			$lastJoin = "§l§o§aLast Join Server: §r§f".$date." at ".$time."\n";
		}
		$form->setContent($name.$rank.$ipAdd.$os.$ping.$firstJoin.$lastJoin);
		
		$array = [];
		if(isset($config["button"])){
			$array = $config["button"];
		}
		
		$Text = "CLOSE";
		if(isset($array[0])){
			$Text = $pl->getMsg($player, $array[0]);
		}
		
		$imageType = -1;
		if(isset($array[1])){
			$imageType = $array[1];
		}
		
		$Url = "";
		if(isset($array[2])){
			$Url = $array[2];
		}
		$form->addButton($Text, $imageType, $Url, 0);
		
		$form->sendToPlayer($player);
		return $form;
	}
}
