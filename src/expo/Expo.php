<?php

namespace expo;

use expo\item\ItemManager;
use expo\player\PlayerManager;
use pocketmine\plugin\PluginBase;

class Expo extends PluginBase {

    private PlayerManager $playerManager;
    private ItemManager $itemManager;

    protected function onLoad(): void {
        $this->playerManager = PlayerManager::getInstance()->init($this);
        $this->itemManager = ItemManager::getInstance()->init($this);
    }

    protected function onEnable(): void {
        $this->playerManager->registerEvents();
        $this->itemManager->registerEvents();
    }

    protected function onDisable(): void {

    }
}