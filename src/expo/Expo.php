<?php

namespace expo;

use expo\item\ItemManager;
use pocketmine\plugin\PluginBase;

class Expo extends PluginBase {
    private ItemManager $itemManager;

    protected function onLoad(): void {
        $this->itemManager = ItemManager::getInstance()->init($this);
    }

    protected function onEnable(): void {
        $this->itemManager->registerEvents();
    }

    protected function onDisable(): void {

    }
}