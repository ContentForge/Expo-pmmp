<?php

namespace expo\item;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;

class ItemListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $player->getNetworkSession()->sendDataPacket(ItemManager::getInstance()->getSpecialPacket());
    }

    public function onDataPacketSend(DataPacketSendEvent $event) : void{
        $packets = $event->getPackets();
        foreach($packets as $packet){
            if($packet instanceof StartGamePacket){
                $packet->levelSettings->experiments = new Experiments([
                    "data_driven_items" => true
                ], true);
            }elseif($packet instanceof ResourcePackStackPacket){
                $packet->experiments = new Experiments([
                    "data_driven_items" => true
                ], true);
            }
        }
    }

}