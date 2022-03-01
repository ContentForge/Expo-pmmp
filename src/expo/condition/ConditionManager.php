<?php

namespace expo\condition;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class ConditionManager {
    use SingletonTrait;

    private array $conditions = [];

    public function init(){
        $this->initDefaultConditions();
    }

    private function initDefaultConditions(): void {

    }

    public function findCondition(string $id): ?Condition {
        return $this->conditions[$id] ?? null;
    }
}