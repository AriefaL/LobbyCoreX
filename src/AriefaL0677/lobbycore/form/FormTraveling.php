<?php

namespace AriefaL0677\lobbycore\form;

use AriefaL0677\lobbycore\Loader;
use libs\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FormTraveling{

	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function onTraveling(Player $player){
		$pl = $this->plugin;
		$warp = $pl->getForm()["formWarps"];
		$menuwarp = $pl->getForm()["menuWarps"];
        $form = new SimpleForm(function (Player $player, $data = NULL){
            if($data !== NULL) {
				$pl = $this->plugin;
                $warps = array_values($pl->getForm()["formWarps"])[$data];
				$cmd = str_replace(["{player}"], [$player->getName()], $warps["command"]);
				if(isset($cmd)){
					$pl->getServer()->dispatchCommand($player, $cmd);
				}else{
					$player->sendMessage(Loader::PREFIX . " §cDon't work! Check please in Config..");
				}
            }
        });
        $form->setTitle($pl->getMsg($player, $menuwarp["title"]));
		$form->setContent($pl->getMsg($player, $menuwarp["content"]));
		
        foreach ($warp as $warps){
			$array = [];
			if(isset($warps["img"])){
				$array = $warps["img"];
			}
			$imageType = -1;
			if(isset($array[0])){
				$imageType = $array[0];
			}
			
			$Url = "";
			if(isset($array[1])){
				$Url = $array[1];
			}
			$form->addButton(str_replace(
								["{player}", "{warp_name}", "{line}", "&"],
								[$player->getName(), $warps["name"], "\n", "§"],
								$menuwarp["button"]), $imageType, $Url);
        }
        $form->sendToPlayer($player);
        return $form;
    }
}
