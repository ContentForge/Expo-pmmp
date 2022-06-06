<?php

namespace expo\util;

use expo\item\ItemManager;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;

class ItemCounter {

    private Inventory $inventory;
    private array $data = [];

    public function __construct(Inventory $inventory) {
        $this->inventory = $inventory;

        $this->count();
    }

    public function count(): void {
        $data = [];
        foreach($this->inventory->getContents() as $item){
            $key = ItemManager::getItemId($item);
            $data[$key] = ($data[$key] ?? 0) + $item->getCount();
        }
        $this->data = $data;
    }

    public function countItem(Item $item): int {
        return $this->data[ItemManager::getItemId($item)] ?? 0;
    }

    public function hasItem(Item $item): bool {
        return $this->countItem($item) >= $item->getCount();
    }

    public function removeItems(array $items): void {
        $temp = [];
        foreach ($items as $item) {
            $temp[ItemManager::getItemId($item)] = $item->getCount();
        }

        foreach($this->inventory->getContents() as $slot => $item){
            if ($item->isNull()) continue;

            $key = ItemManager::getItemId($item);
            foreach ($temp as $id => $count) {
                if ($key !== $id) continue;
                if ($count === 0) break;

                $c = min($item->getCount(), $count);
                $temp[$id] -= $c;
                $item->setCount($item->getCount() - $c);
                $this->inventory->setItem($slot, $item);
                break;
            }
        }

        $this->count();
    }
}