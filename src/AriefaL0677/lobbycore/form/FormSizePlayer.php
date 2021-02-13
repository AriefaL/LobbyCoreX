<?php

namespace AriefaL0677\lobbycore\form;

use AriefaL0677\lobbycore\Loader;
use libs\FormAPI\CustomForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\sound\EndermanTeleportSound;

class FormSizePlayer{
	
	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function onSizePlayer(Player $player){
		$pl = $this->plugin;
		$config = $pl->getForm()["formSize"];
		$form = new CustomForm(function (Player $player, $data){
			$pl = $this->plugin;
			if($data !== null) {
				if(!is_numeric($data[1])){
					$player->sendMessage($pl->getMsg($player, $pl->getConfigs()["message"]["nombchangesize"]));
					return false;
				}
				
				if(!isset($data[1])){
					$player->sendMessage($pl->getMsg($player, $pl->getConfigs()["message"]["falchangesize"]));
					return false;
				}
				
				if($data[1] > 15) {
					$player->sendMessage($pl->getMsg($player, $pl->getConfigs()["message"]["maxchangesize"]));
					return false;
				}
				
				if($data[1] <= 0) {
					$player->sendMessage($pl->getMsg($player, $pl->getConfigs()["message"]["minchangesize"]));
					return false;
				}
				
				$player->setScale((float)$data[1]);
				$player->getLevel()->addSound(new EndermanTeleportSound($player));
				$player->sendMessage($pl->getMsg($player, $pl->getConfigs()["message"]["succchangesize"]));
			}
		});
		$form->setTitle($pl->getMsg($player, $config["title"]));
		$form->addLabel($pl->getMsg($player, $config["content"]));
		if(strtolower($config["type"]) === "slider"){
			$array = [];
			if(isset($config["slider"])){
				$array = $config["slider"];
			}
			$text = "Select to change size:";
			if(isset($array[0])){
				$text = $pl->getMsg($player, $array[0]);
			}
			$min = 1;
			if(isset($array[1])){
				$min = $array[1];
			}
			$max = 15;
			if(isset($array[2])){
				$max = $array[2];
			}
			$default = $player->getScale();
			$form->addSlider($text, $min, $max, -1, $default);
		}
		if(strtolower($config["type"]) === "input"){
			$array = [];
			if(isset($config["input"])){
				$array = $config["input"];
			}
			$text = "Select to change size";
			if(isset($array[0])){
				$text = $pl->getMsg($player, $array[0]);
			}
			$placeholder = "Type Integer!";
			if(isset($array[1])){
				$placeholder = $pl->getMsg($player, $array[1]);
			}
			$default = null;
			if(isset($array[2]) && strtolower($array[2]) === "default"){
				$default = $player->getScale();
			}
			$form->addInput($text, $placeholder, $default);
		}
		$form->sendToPlayer($player);
		return $form;
	}
}
