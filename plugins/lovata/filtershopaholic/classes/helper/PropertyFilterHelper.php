<?php namespace Lovata\FilterShopaholic\Classes\Helper;

use Lovata\FilterShopaholic\Classes\Store\FilterValueStore;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLinkListStore;

/**
 * Class PropertyFilterHelper
 * @package Lovata\FilterShopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertyFilterHelper
{
    /**
     * Get property ID list for filter with type "checkbox"
     * @param int                                                          $iPropertyID
     * @param array                                                        $arFilterList
     * @param string                                                       $sModel
     * @param string                                                       $sResultModel
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection|null $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null   $obOfferList
     *
     * @return null|array
     */
    public function getResultCheckboxFilter($iPropertyID, $arFilterList, $sModel, $sResultModel, $obProductList = null, $obOfferList = null)
    {
        if (empty($iPropertyID) || empty($sModel) || empty($arFilterList) || !is_array($arFilterList) || empty($sResultModel)) {
            return null;
        }

        $arResult = [];

        //Process filter value list
        foreach ($arFilterList as $sValue) {

            //Get product list for filter value
            $arProductIDList = FilterValueStore::instance()->property->getListByPropertyValue($iPropertyID, $sValue, $sModel, $sResultModel, $obProductList, $obOfferList);
            if (empty($arProductIDList)) {
                continue;
            }

            $arResult = array_merge($arResult, $arProductIDList);
        }

        return array_unique($arResult);
    }

    /**
     * Get property ID list for filter with type "select"
     * @param int                                                          $iPropertyID
     * @param string                                                       $sFilterValue
     * @param string                                                       $sModel
     * @param string                                                       $sResultModel
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection|null $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null   $obOfferList
     *
     * @return null|array
     */
    public function getResultSelectFilter($iPropertyID, $sFilterValue, $sModel, $sResultModel, $obProductList = null, $obOfferList = null)
    {
        if (is_array($sFilterValue)) {
            $sFilterValue = array_shift($sFilterValue);
        }

        if (empty($iPropertyID) || empty($sFilterValue) || empty($sModel) || empty($sResultModel)) {
            return null;
        }

        $arResult = FilterValueStore::instance()->property->getListByPropertyValue($iPropertyID, $sFilterValue, $sModel, $sResultModel, $obProductList, $obOfferList);
        if (empty($arResult)) {
            $arResult = [];
        }

        return $arResult;
    }

    /**
     * Get property ID list for filter with type "between"
     * @param int                                                        $iPropertyID
     * @param array                                                      $arFilterList
     * @param string                                                     $sModel
     * @param string                                                     $sResultModel
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null $obOfferList
     *
     * @return null|array
     */
    public function getResultBetweenFilter($iPropertyID, $arFilterList, $sModel, $sResultModel, $obOfferList = null)
    {
        if (empty($iPropertyID) || empty($sModel) || empty($arFilterList) || !is_array($arFilterList) || empty($sResultModel)) {
            return null;
        }

        $fValueMin = array_shift($arFilterList);
        $fValueMin = str_replace(',', '.', $fValueMin);
        $fValueMin = preg_replace("/[^0-9\-\.]/", "", $fValueMin);

        $fValueMax = array_shift($arFilterList);
        $fValueMax = str_replace(',', '.', $fValueMax);
        $fValueMax = preg_replace("/[^0-9\-\.]/", "", $fValueMax);

        if (empty($fValueMax) && empty($fValueMin)) {
            return null;
        }

        //Get value list
        $arValueList = PropertyValueLinkListStore::instance()->property->getListByProperty($iPropertyID, $sModel, $sResultModel, $obOfferList);
        if (empty($arValueList)) {
            return null;
        }

        $arResult = [];
        /** @var \Lovata\PropertiesShopaholic\Models\PropertyValue $obValue */
        foreach ($arValueList as $iValueID => $arElementIDList) {

            $obValue = PropertyValueItem::make($iValueID);
            if (empty($obValue) || empty($arElementIDList)) {
                continue;
            }

            $fValue = $obValue->value;
            if (empty($fValue) && $fValue !== '0' && $obValue !== 0) {
                continue;
            }

            $fValue = str_replace(',', '.', $fValue);
            $fValue = preg_replace("/[^0-9\.]/", "", $fValue);
            $fValue = (float) $fValue;
            if ($fValueMin !== '' && $fValue < (float) $fValueMin) {
                continue;
            }

            if ($fValueMax !== '' && $fValue > (float) $fValueMax) {
                continue;
            }

            $arResult = array_merge($arResult, $arElementIDList);
        }

        $arResult = array_unique($arResult);

        return $arResult;
    }
}