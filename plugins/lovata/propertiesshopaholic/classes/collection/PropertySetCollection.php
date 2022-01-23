<?php namespace Lovata\PropertiesShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\PropertiesShopaholic\Classes\Item\PropertySetItem;
use Lovata\PropertiesShopaholic\Classes\Store\PropertySetListStore;

/**
 * Class PropertySetCollection
 * @package Lovata\PropertiesShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see     \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertySetCollectionTest
 *
 * Filter for Shopaholic
 * @method \Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection getProductPropertyCollection(\Lovata\Shopaholic\Classes\Collection\ProductCollection $obProductList = null)
 * @method \Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection getOfferPropertyCollection(\Lovata\Shopaholic\Classes\Collection\ProductCollection $obProductList = null, \Lovata\Shopaholic\Classes\Collection\OfferCollection $obOfferList = null)
 */
class PropertySetCollection extends ElementCollection
{
    const ITEM_CLASS = PropertySetItem::class;

    /**
     * Sort list
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertySetCollectionTest::testSortCollectionMethod()
     * @return $this
     */
    public function sort()
    {
        if (!$this->isClear() && $this->isEmpty()) {
            return $this->returnThis();
        }

        $arResultIDList = PropertySetListStore::instance()->sorting->get();

        return $this->applySorting($arResultIDList);
    }
    /**
     * Apply filter by is_global field
     * @return $this
     */
    public function isGlobal()
    {
        $arResultIDList = PropertySetListStore::instance()->is_global->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * Filter collection by code
     * @param string|array $arCodeList
     * @return $this
     */
    public function code($arCodeList)
    {
        $obList = clone $this;
        if (empty($arCodeList)) {
            return $obList->clear();
        }

        if (!is_array($arCodeList)) {
            $arCodeList = [$arCodeList];
        }

        //Get all properties
        $arPropertyList = $this->all();

        $arResultIDList = [];

        /** @var PropertySetItem $obPropertySet */
        foreach ($arPropertyList as $obPropertySet) {
            if (!in_array($obPropertySet->code, $arCodeList)) {
                continue;
            }

            $arResultIDList[] = $obPropertySet->id;
        }

        return $obList->intersect($arResultIDList)->copy();
    }

        /**
     * Get array with product property data
     * @return array
     */
    public function getProductPropertyList()
    {
        $arResult = $this->getPropertyList('product_property_list');

        return $arResult;
    }

    /**
     * Get array with offer property data
     * @return array
     */
    public function getOfferPropertyList()
    {
        $arResult = $this->getPropertyList('offer_property_list');

        return $arResult;
    }

    /**
     * Get array with property data
     * @param string $sField
     * @return array
     */
    protected function getPropertyList($sField) : array
    {
        $arResult = [];

        //Get all property sets
        $arPropertySetList = $this->sort()->all();
        if (empty($arPropertySetList)) {
            return $arResult;
        }

        /** @var PropertySetItem $obPropertySetItem */
        foreach ($arPropertySetList as $obPropertySetItem) {
            //Get property list
            $arPropertyList = $obPropertySetItem->$sField;
            if (empty($arPropertyList) || !is_array($arPropertyList)) {
                continue;
            }

            //Process property list
            foreach ($arPropertyList as $iPropertyID => $arPropertyData) {
                if (isset($arResult[$iPropertyID])) {
                    continue;
                }

                $arResult[$iPropertyID] = $arPropertyData;
            }
        }

        return $arResult;
    }
}
