<?php

namespace expo\player;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class PlayerManager implements Listener {
    use SingletonTrait;

    private PluginBase $plugin;
    private array $players = [];
    private string $path;

    public function init(PluginBase $plugin): PlayerManager {
        $this->plugin = $plugin;
        $this->path = $plugin->getDataFolder() . "players/";
        @mkdir($this->path);

        return $this;
    }

    public function registerEvents(): void {
        $pluginManager = $this->plugin->getServer()->getPluginManager();

        $pluginManager->registerEvent(PlayerLoginEvent::class, function(PlayerLoginEvent $event){
            $player = $event->getPlayer();
            $playerData = new PlayerData($player, $this->path . $player->getXuid());

            $playerData->init();
            $this->players[$player->getXuid()] = $playerData;
        }, EventPriority::HIGHEST, $this->plugin);

        $pluginManager->registerEvent(PlayerQuitEvent::class, function(PlayerQuitEvent $event){
            $player = $event->getPlayer();

            if(!isset($this->players[$player->getXuid()])) return;

            $playerData = $this->players[$player->getXuid()];
            $playerData->save();
            unset($this->players[$player->getXuid()]);
        }, EventPriority::HIGHEST, $this->plugin);
    }

    public function get(Player $player): PlayerData {
        return $this->players[$player->getXuid()];
    }
}