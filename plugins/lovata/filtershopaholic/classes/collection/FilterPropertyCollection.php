<?php namespace Lovata\FilterShopaholic\Classes\Collection;

use Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection;

/**
 * Class FilterPropertyCollection
 * @package Lovata\FilterShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class FilterPropertyCollection extends PropertyCollection
{
    /**
     * Set category relation data
     * @param array $arPropertyList
     *
     * @return $this
     */
    public function setPropertySetRelation($arPropertyList)
    {
        parent::setPropertySetRelation($arPropertyList);
        $this->initFilterOn();

        return $this->returnThis();
    }

    /**
     * Get properties with filter type
     * @param string $sFilterType
     * @return $this
     */
    public function type($sFilterType)
    {
        $obThis = clone $this;
        if (empty($sFilterType)) {
            $obThis->clear();
            return $obThis;
        }

        if (empty($this->arPropertySetRelation) || $this->isEmpty()) {
            return $obThis;
        }

        $arResult = [];
        foreach ($this->arPropertySetRelation as $arPropertyData) {
            if (empty($arPropertyData) || !isset($arPropertyData['filter_type']) || $arPropertyData['filter_type'] != $sFilterType) {
                continue;
            }

            $arResult[] = $arPropertyData['id'];
        }

        $obThis->intersect($arResult);
        return $obThis;
    }

    /**
     * Get filter type for property
     * @deprecated
     * @param int $iPropertyID
     *
     * @return string|null
     */
    public function getFilterType($iPropertyID)
    {
        /** @var \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem $obPropertyItem */
        $obPropertyItem = $this->find($iPropertyID);

        return $obPropertyItem->filter_type;
    }

    /**
     * Get filter name for property
     * @deprecated
     * @param int $iPropertyID
     *
     * @return string|null
     */
    public function getFilterName($iPropertyID)
    {
        /** @var \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem $obPropertyItem */
        $obPropertyItem = $this->find($iPropertyID);

        return $obPropertyItem->filter_name;
    }

    /**
     * Remove properties with flag "filter" off
     */
    protected function initFilterOn()
    {
        if (empty($this->arPropertySetRelation) || $this->isEmpty()) {
            return;
        }

        $arResult = [];
        foreach ($this->arPropertySetRelation as $arPropertyData) {
            if (empty($arPropertyData) || !array_key_exists('in_filter', $arPropertyData) || !$arPropertyData['in_filter']) {
                continue;
            }

            $arResult[] = $arPropertyData['id'];
        }

        $this->intersect($arResult);
    }
}
