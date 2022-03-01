<?php

namespace expo\condition;

use expo\player\PlayerData;
use http\Exception\InvalidArgumentException;

abstract class Condition {

    private string $id;

    public function __construct(string $id) {
        $this->id = strtolower($id);
    }

    public function getId(): string {
        return $this->id;
    }

    public abstract function check(PlayerData $playerData, array $params): bool;

    public static function fromArray(array $data): ConditionObject {
        $cond = ConditionManager::getInstance()->findCondition($data['type']);
        if($cond === null) throw new InvalidArgumentException("Условия '{$data['type']}' не сушествует");
        return new ConditionObject($cond, $data['params']);
    }
}