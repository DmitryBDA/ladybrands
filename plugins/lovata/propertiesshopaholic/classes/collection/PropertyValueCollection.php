<?php namespace Lovata\PropertiesShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem;

/**
 * Class PropertyValueCollection
 * @package Lovata\PropertiesShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA PropertyValue
 */
class PropertyValueCollection extends ElementCollection
{
    const ITEM_CLASS = PropertyValueItem::class;

    protected $iPropertyID;
    protected $sModelName;

    /**
     * Filter value list, and remove items with empty value
     * @return $this
     */
    public function sort()
    {
        if ($this->isEmpty()) {
            return $this->returnThis();
        }

        $arElementList = [];
        $arItemList = $this->all();

        /** @var PropertyValueItem $obValueItem */
        foreach ($arItemList as $obValueItem) {
            $arElementList[$obValueItem->id] = $obValueItem->value;
        }

        asort($arElementList, SORT_NATURAL);
        uasort($arElementList, function ($sValuePrev, $sValueNext) {

            $sValuePrev = str_replace(',', '.', $sValuePrev);
            $sValueNext = str_replace(',', '.', $sValueNext);
            $sValuePrev = (float) $sValuePrev;
            $sValueNext = (float) $sValueNext;

            if ($sValueNext == $sValuePrev) {
                return 0;
            }

            if ($sValueNext > $sValuePrev) {
                return -1;
            }

            return 1;
        });

        $this->arElementIDList = array_keys($arElementList);

        return $this->returnThis();
    }

    /**
     * Get property value
     * @param string $sSeparator
     *
     * @return string|null
     */
    public function getValueString($sSeparator = ', ')
    {
        return $this->implode('value', $sSeparator);
    }

    /**
     * Set model class name
     * @param string $sModelName
     * @return $this
     */
    public function setModel($sModelName)
    {
        $this->sModelName = $sModelName;

        return $this->returnThis();
    }

    /**
     * Set property ID
     * @param int $iPropertyID
     * @return $this
     */
    public function setPropertyID($iPropertyID)
    {
        $this->iPropertyID = $iPropertyID;

        return $this->returnThis();
    }

    /**
     * Clone collection object
     * @return  $this
     */
    public function copy()
    {
        /** @var PropertyValueCollection $obList */
        $obList = parent::copy();
        $obList->setModel($this->sModelName);
        $obList->setPropertyID($this->iPropertyID);

        return $obList;
    }

    /**
     * Make element item
     * @param int                                          $iElementID
     * @param \Lovata\PropertiesShopaholic\Models\Property $obElement
     * @return PropertyValueItem
     */
    protected function makeItem($iElementID, $obElement = null)
    {
        /** @var PropertyValueItem $obItem */
        $obItem = parent::makeItem($iElementID, $obElement);
        $obItem->setPropertyID($this->iPropertyID);
        $obItem->setModel($this->sModelName);

        return $obItem;
    }

    /**
     * Make element item from cache only
     * @param int $iElementID
     * @return PropertyValueItem
     */
    protected function makeItemOnlyCache($iElementID, $obElement = null)
    {
        /** @var PropertyValueItem $obItem */
        $obItem = parent::makeItemOnlyCache($iElementID);
        if ($obItem->isEmpty()) {
            return $obItem;
        }

        $obItem->setPropertyID($this->iPropertyID);
        $obItem->setModel($this->sModelName);

        return $obItem;
    }
}
