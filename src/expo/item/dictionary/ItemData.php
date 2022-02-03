<?php

namespace expo\item\dictionary;

class ItemData implements DictionaryItemData {

    private static ?ItemData $voidItem = null;

    public function __construct(private string $ruName, private string $texture) {

    }

    public function getRuName(): string {
        return $this->ruName;
    }

    public function getTexturePath(): string {
        return $this->texture;
    }

    public static function VOID_ITEM(): ItemData {
        if(self::$voidItem === null) self::$voidItem = new ItemData("unknown", "");
        return self::$voidItem;
    }
}