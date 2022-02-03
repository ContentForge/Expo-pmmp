<?php

namespace expo\item\data;

use pocketmine\nbt\tag\CompoundTag;

trait BasicComponentDataTrait {

    public abstract function getVanillaName() : string;

    public abstract function getId(): int;

    public abstract function getMaxStackSize() : int;

    public function getTextureId(): string {
        return $this->getFullId();
    }

    private function generateDisplayName(): string {
        return 'item.' . $this->getFullId() . '.name';
    }

    public function getRuntimeId(): int {
        return $this->getId() + ($this->getId() > 0 ? 5000 : -5000);
    }

    public function getFullId(): string {
        return "custom:".str_replace(' ', '_', strtolower($this->getVanillaName()));
    }

    public function getComponentData(): CompoundTag {
        return CompoundTag::create()
            ->setTag("components", CompoundTag::create()
                ->setTag("item_properties", CompoundTag::create()
                    ->setInt("max_stack_size", $this->getMaxStackSize())
                    ->setTag("minecraft:icon", CompoundTag::create()
                        ->setString("texture", $this->getTextureId())
                    )
                )
                ->setShort("minecraft:identifier", $this->getRuntimeId())
                ->setTag("minecraft:display_name", CompoundTag::create()
                    ->setString("value", $this->generateDisplayName())
                )
            );
    }
}