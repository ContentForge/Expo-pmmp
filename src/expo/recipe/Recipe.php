<?php

namespace expo\recipe;

use expo\item\ItemManager;
use expo\util\ItemCounter;
use expo\workbench\Workbench;
use form\CustomForm;
use pocketmine\item\Item;
use pocketmine\player\Player;

class Recipe {

    private string $id;
    private Item $result;
    private array $reagents;
    private array $data;
    private string $workbenchId;
    private ?Workbench $workbench = null;

    public function __construct(string $id, Item $result, array $reagents, array $data, string $workbenchId) {
        $this->id = $id;
        $this->result = $result;
        $this->reagents = $reagents;
        $this->data = $data;
    }

    public function initWorkbench(Workbench $workbench): void {
        $this->workbench = $workbench;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getResult(): Item {
        return $this->result;
    }

    public function getReagents(): array {
        return $this->reagents;
    }

    public function getData(): array {
        return $this->data;
    }

    public function getWorkbench(): ?Workbench {
        return $this->workbench;
    }

    protected function getCounterSteps(): array {
        $counts = [];
        foreach ([1, 2, 3, 4, 6, 8, 16, 24, 32, 48, 56, 64] as $c) {
            if ($this->result->getMaxStackSize() < $c) break;

            $counts[] = $c;
        }
        return $counts;
    }

    protected function canCraftAndGetError(Player $player, int $count): ?string {
        $result = clone $this->result;
        $result->setCount($result->getCount() * $count);

        if (!$player->getInventory()->canAddItem($result)) {
            return "Нет свободного места в инвентаре под новые предметы";
        }

        $counter = new ItemCounter($player->getInventory());
        foreach ($this->reagents as $reagent) {
            $item = clone $reagent;
            $item->setCount($item->getCount() * $count);
            if (!$counter->hasItem($item)) {
                return "Недостаточно реагентов для крафта";
            }
        }

        return null;
    }

    protected function craft(Player $player, int $count): Item {
        $counter = new ItemCounter($player->getInventory());
        $reagents = [];
        foreach ($this->reagents as $reagent) {
            $item = clone $reagent;
            $item->setCount($item->getCount() * $count);
            $reagents[] = $item;
        }

        $counter->removeItems($reagents);
        $result = clone $this->result;
        $result->setCount($result->getCount() * $count);
        return $result;
    }

    protected function generateHeader(Player $player, ItemCounter $itemCounter, string $error = null, int $crafted = 0): CustomForm {
        $itemName = ItemManager::getInstance()->getItemDictionaryData($this->result)->getRuName();

        $form = new CustomForm(function (Player $player, array $data) {
            $count = $this->getCounterSteps()[$data['count'] ?? 0];
            echo $count . PHP_EOL;
            $error = $this->canCraftAndGetError($player, $count);

            if ($error !== null) {
                $this->sendForm($player, $error);
                return;
            }

            $player->getInventory()->addItem($this->craft($player, $count));
            $this->sendForm($player, crafted: $count);
        }, function (Player $player) {
            $this->workbench?->sendCraftList($player);
        });

        $form->setTitle("Создание предмета");

        if ($error !== null) {
            $form->addLabel("§cНе удалось создать предмет: ". $error ."§f\n");
        }

        if ($crafted > 0) {
            $form->addLabel("§eВы успешно создали {$itemName} ". ($crafted * $this->result->getCount()) ."шт!§f\n");
        }

        $form->addLabel($this->createReagentsList($player, $itemCounter));

        return $form;
    }

    protected function createReagentsList(Player $player, ItemCounter $itemCounter): string {
        $itemName = ItemManager::getInstance()->getItemDictionaryData($this->result)->getRuName();
        $lines = [];
        foreach ($this->reagents as $item) {
            $has = $itemCounter->hasItem($item);
            $itemData = ItemManager::getInstance()->getItemDictionaryData($item);
            $lines[] = " - §". ($has? "2" : "4") . $itemData->getRuName() . " {$item->getCount()}шт§f (". $itemCounter->countItem($item) .")";
        }

        return "Для создания §3{$itemName} {$this->result->getCount()}шт§f вам потребуется:\n" . implode("\n", $lines);
    }

    protected function createItemCounter(Player $player, CustomForm $form, ItemCounter $counter): void {
        $counts = $this->getCounterSteps();
        $formatCounts = [];

        if (count($counts) == 1) {
            $form->addLabel("§7[Данный предмет можно создавать только поштучно]");
        } else {
            $canCraftNext = true;
            foreach ($counts as $c) {
                $cc = $c * $this->result->getCount();

                if ($canCraftNext) {
                    foreach ($this->reagents as $item) {
                        $cItem = clone $item;
                        $cItem->setCount($cItem->getCount() * $c);
                        if ($counter->countItem($item) < $cItem->getCount()) {
                            $canCraftNext = false;
                            break;
                        }
                    }
                }
                $key = ($canCraftNext? "§2" : "§4") ."{$cc} шт";

                $formatCounts[$key] = $c;
            }

            $form->addStepSlider("Количество", array_keys($formatCounts), key: "count");
        }
    }

    public function sendForm(Player $player, string $error = null, int $crafted = 0): void {
        $itemCounter = new ItemCounter($player->getInventory());
        $form = $this->generateHeader($player, $itemCounter, $error, $crafted);
        $this->createItemCounter($player, $form, $itemCounter);

        $form->sendToPlayer($player);
    }
}