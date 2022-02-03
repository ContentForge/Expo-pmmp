<?php

namespace expo\item;

use expo\item\dictionary\DictionaryItemData;
use pocketmine\nbt\tag\CompoundTag;

interface CustomItem extends DictionaryItemData {

    public function getComponentData(): CompoundTag;

    public function getRuntimeId(): int;

    public function getFullId(): string;
}