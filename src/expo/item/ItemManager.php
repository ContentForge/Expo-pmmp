<?php

namespace expo\item;

use expo\item\dictionary\DictionaryItemData;
use expo\item\dictionary\ItemData;
use http\Exception\InvalidArgumentException;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\serializer\ItemTypeDictionary;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use ReflectionProperty;
use ReflectionClass;

final class ItemManager {
    use SingletonTrait;

    private PluginBase $plugin;
    private array $dictionary = [];
    private array $items = [];
    private ReflectionProperty $coreToNetMap;
    private ReflectionProperty $netToCoreMap;
    private ReflectionProperty $itemTypeMap;
    private array $itemTypeEntries = [];
    private array $coreToNetValues = [];
    private array $netToCoreValues = [];
    private array $entries = [];
    private ItemComponentPacket $packet;

    public function init(PluginBase $plugin): ItemManager {
        $this->plugin = $plugin;

        $this->packet = ItemComponentPacket::create([]);

        $ref = new ReflectionClass(ItemTranslator::class);
        $this->coreToNetMap = $ref->getProperty("simpleCoreToNetMapping");
        $this->netToCoreMap = $ref->getProperty("simpleNetToCoreMapping");
        $this->coreToNetMap->setAccessible(true);
        $this->netToCoreMap->setAccessible(true);
        $this->coreToNetValues = $this->coreToNetMap->getValue(ItemTranslator::getInstance());
        $this->netToCoreValues = $this->netToCoreMap->getValue(ItemTranslator::getInstance());
        $ref_1 = new ReflectionClass(ItemTypeDictionary::class);
        $this->itemTypeMap = $ref_1->getProperty("itemTypes");
        $this->itemTypeMap->setAccessible(true);
        $this->itemTypeEntries = $this->itemTypeMap->getValue(GlobalItemTypeDictionary::getInstance()->getDictionary());

        return $this;
    }

    public function registerEvents(): void {
        $this->plugin->getServer()->getPluginManager()->registerEvents(new ItemListener(), $this->plugin);
    }

    public function registerItem(Item $item): void {
        if(!$item instanceof CustomItem){
            throw new InvalidArgumentException("Предмет не расширяет интерфейс CustomItem");
        }

        $this->updateItemsList($item);

        $this->items[] = $item;
        $this->plugin->getLogger()->info("Предмет '{$item->getRuName()}' был успешно зарегистрирован!");
    }

    private function updateItemsList(Item|CustomItem $item): void {
        $id = $item->getRuntimeId();

        $this->coreToNetValues[$item->getId()] = $id;
        $this->netToCoreValues[$id] = $item->getId();

        $this->itemTypeEntries[] = new ItemTypeEntry($item->getFullId(), $id, true);
        $this->entries[] = new ItemComponentPacketEntry($item->getFullId(), new CacheableNbt($item->getComponentData()));

        $new = clone $item;
        StringToItemParser::getInstance()->register($item->getName(), fn() => $new);
        ItemFactory::getInstance()->register($item, true);
        CreativeInventory::getInstance()->add($item);

        $this->netToCoreMap->setValue(ItemTranslator::getInstance(), $this->netToCoreValues);
        $this->coreToNetMap->setValue(ItemTranslator::getInstance(), $this->coreToNetValues);
        $this->itemTypeMap->setValue(GlobalItemTypeDictionary::getInstance()->getDictionary(), $this->itemTypeEntries);

        $this->packet = ItemComponentPacket::create($this->entries);
    }

    public function getSpecialPacket(): ItemComponentPacket {
        return $this->packet;
    }

    public static function getItemId(Item $item): string {
        if($item->isNull()) return '0';
        if(!$item instanceof Durable) return $item->getId() . ":" . $item->getMeta();
        return $item->getId() . "";
    }

    public function addItemToDictionary(Item $item, string $name, string $texture): void {
        $id = $this->getItemId($item);
        $this->dictionary[$id] = [
            'name' => $name,
            'texture' => $texture,
        ];
    }

    public function getItemDictionaryData(Item $item): DictionaryItemData {
        if($item->isNull()) return ItemData::VOID_ITEM();
        if($item instanceof CustomItem) return $item;

        $id = $this->getItemId($item);
        return $this->dictionary[$id] ?? new ItemData($item->getVanillaName(), "");
    }

    public function getItem(int $id): ?CustomItem {
        return $this->items[$id] ?? null;
    }
}