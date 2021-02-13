<?php

namespace AriefaL0677\lobbycore\form;

use AriefaL0677\lobbycore\Loader;
use libs\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FormInformation{

	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function onInformation(Player $player){
		$pl = $this->plugin;
		$config = $pl->getForm()["formInfo"];
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
		$form->setContent($pl->info->get("Text"));
		
		$array = [];
		if(isset($config["button"])){
			$array = $config["button"];
		}
		
		$Text = "CLOSE";
		if(isset($array[0])){
			$Text = $array[0];
		}
		
		$imageType = -1;
		if(isset($array[1])){
			$imageType = $array[1];
		}
		
		$Url = "";
		if(isset($array[2])){
			$Url = $array[2];
		}
		$form->addButton($pl->getMsg($player, $Text), $imageType, $Url, 0);
		
		$form->sendToPlayer($player);
		return $form;
	}
}
