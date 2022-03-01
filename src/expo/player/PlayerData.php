<?php

namespace expo\player;

use pocketmine\player\Player;

final class PlayerData {

    private Player $player;
    private string $filePath;

    public function __construct(Player $player, string $filePath) {
        $this->player = $player;
        $this->filePath = $filePath;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function init(): void {
        if(file_exists($this->filePath)){
            $data = json_decode(file_get_contents($this->filePath), true);

            //...
        }
    }

    public function save(): void {
        file_put_contents($this->filePath, json_encode([
            //...
        ]));
    }
}