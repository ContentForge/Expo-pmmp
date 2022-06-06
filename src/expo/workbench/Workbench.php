<?php

namespace expo\workbench;

use expo\player\PlayerData;
use expo\player\PlayerManager;
use pocketmine\player\Player;
use pocketmine\world\Position;

class Workbench {

    public final function sendToPlayer(Player|PlayerData $player, ?Position $block = null): void {
        if ($player instanceof Player) $player = PlayerManager::getInstance()->get($player);

        if (!$this->canOpen($player)) return;
        $this->open($player, $block);
    }

    protected function canOpen(PlayerData $playerData): bool {
        return true;
    }

    protected function open(PlayerData $playerData, ?Position $block): void {

    }

    public function sendCraftList(Player|PlayerData $player): void {
        if ($player instanceof Player) $player = PlayerManager::getInstance()->get($player);

        //TODO
    }

    public function getSoundOnCraft(): ?string {
        return null;
    }
}