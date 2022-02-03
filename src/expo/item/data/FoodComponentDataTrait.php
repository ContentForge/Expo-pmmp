<?php

namespace expo\item\data;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

trait FoodComponentDataTrait {
    use BasicComponentDataTrait;

    public function canAlwaysEat(): bool {
        return false;
    }

    public function canStartUsingItem(Player $player): bool {
        if($this->canAlwaysEat()) return true;
        return parent::canStartUsingItem($player);
    }

    public abstract function getFoodRestore(): int;

    public abstract function getSaturationRestore(): float;

    public function getComponentData(): CompoundTag {
        return CompoundTag::create()
            ->setTag("components", CompoundTag::create()
                ->setTag("item_properties", CompoundTag::create()
                    ->setInt("max_stack_size", $this->getMaxStackSize())
                    ->setInt("use_duration", 32)
                    ->setInt("use_animation", 1)
                    ->setInt("creative_category", 3)
                    ->setString("creative_group", "itemGroup.name.miscFood")
                    ->setTag("minecraft:icon", CompoundTag::create()
                        ->setString("texture", $this->getTextureId())
                    )
                )
                ->setTag('minecraft:food', CompoundTag::create()
                    ->setByte('can_always_eat', $this->canAlwaysEat()? 1 : 0)
                    ->setFloat('nutrition', $this->getFoodRestore())
                    ->setString('saturation_modifier', 'high')
                )
                ->setShort("minecraft:identifier", $this->getRuntimeId())
                ->setTag("minecraft:display_name", CompoundTag::create()
                    ->setString("value", $this->generateDisplayName())
                )
            );
    }
}