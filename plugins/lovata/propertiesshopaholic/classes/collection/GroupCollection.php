<?php namespace Lovata\PropertiesShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\PropertiesShopaholic\Classes\Item\GroupItem;
use Lovata\PropertiesShopaholic\Classes\Store\GroupListStore;

/**
 * Class GroupCollection
 * @package Lovata\PropertiesShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see     \Lovata\PropertiesShopaholic\Tests\Unit\Collection\GroupCollectionTest
 */
class GroupCollection extends ElementCollection
{
    const ITEM_CLASS = GroupItem::class;

    /**
     * Sort list
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\GroupCollectionTest::testSortCollectionMethod()
     * @return $this
     */
    public function sort()
    {
        $arResultIDList = GroupListStore::instance()->sorting->get();

        return $this->applySorting($arResultIDList);
    }

    /**
     * Checking, collection has group with code
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\GroupCollectionTest::testHasCodeCollectionMethod()
     * @param string $sCode
     * @return bool
     */
    public function hasCode($sCode)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $arGroupList = $this->all();

        /** @var GroupItem $obGroupItem */
        foreach ($arGroupList as $obGroupItem) {
            if ($obGroupItem->code == $sCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get group by code
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\GroupCollectionTest::testGetByCodeCollectionMethod()
     * @param string $sCode
     *
     * @return GroupItem
     */
    public function getByCode($sCode)
    {
        if ($this->isEmpty() || empty($sCode)) {
            return GroupItem::make(null);
        }

        $arGroupList = $this->all();

        /** @var GroupItem $obGroupItem */
        foreach ($arGroupList as $obGroupItem) {
            if ($obGroupItem->code == $sCode) {
                return $obGroupItem;
            }
        }

        return GroupItem::make(null);
    }
}
