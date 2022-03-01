<?php

namespace expo\condition;

use expo\player\PlayerData;

class ConditionObject {

    private Condition $condition;
    private array $params;

    public function __construct(Condition $condition, array $data) {
        $this->condition = $condition;
        $this->params = $data['params'];
    }

    public function check(PlayerData $playerData): bool {
        return $this->condition->check($playerData, $this->params);
    }
}