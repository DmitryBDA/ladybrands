<?php namespace Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Classes\Collection\OfferCollection;

use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class ListByPropertyStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * Cached data:
 *  [
 *      'product'       => ['value_id_1' => [...product ID list that have value with id == value_id_1...], [...]]
 *      'product_offer' => ['value_id_1' => [...product ID list, offers that have value with id == value_id_1...], [...]]
 *      'offer'         => ['value_id_1' => [...offer ID list that have value with id == value_id_1...], [...]]
 *  ]
 */
class ListByPropertyStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get product id list by property ID and value ID
     * Usage:
     * 1. Used in PropertyValueItem::isDisabled method
     * @see \Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem::isDisabled()
     *
     * Result example:
     * ['product_id_1', 'product_id_2', ...]
     *
     * @param int                                                   $iPropertyID
     * @param int                                                   $iValueID
     * @param string                                                $sModelClass
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection $obOfferList
     * @return array
     */
    public function getProductListByValueID($iPropertyID, $iValueID, $sModelClass, $obOfferList = null) : array
    {
        if (empty($iValueID)) {
            return [];
        }

        //Get array from cache
        $arCacheData = $this->getDataForModelByValue($iPropertyID, $sModelClass, Product::class, $iValueID);
        if (empty($arCacheData)) {
            return [];
        }

        if ($sModelClass != Offer::class || empty($obOfferList)) {
            return $arCacheData;
        }

        if ($obOfferList->isEmpty()) {
            return [];
        }

        //Get cached data for offers
        $arOfferCacheData = $this->getDataForModelByValue($iPropertyID, Offer::class, Offer::class, $iValueID);
        if (empty($arOfferCacheData) || empty(array_intersect($arOfferCacheData, $obOfferList->getIDList()))) {
            return [];
        }

        return $arCacheData;
    }

    /**
     * Get value id list by property ID and product collection
     * Usage:
     * 1. Used for render list of property values in filter panel (only for custom product list)
     * @see \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem::getPropertyValueAttribute()
     *
     * Result example:
     * ['value_id_1', 'value_id_2', ...]
     *
     * @param int                                                          $iPropertyID
     * @param string                                                       $sModelClass
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection|null $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null   $obOfferList
     * @return array
     */
    public function getValueByProductList($iPropertyID, $sModelClass, $obProductList = null, $obOfferList = null) : array
    {
        if (!empty($obProductList) && $obProductList->isEmpty()) {
            return [];
        }

        //Get array from cache
        $arCacheData = $this->getDataForModel($iPropertyID, $sModelClass, Product::class);
        if (empty($arCacheData)) {
            return [];
        }

        if ($sModelClass == Offer::class) {
            $arCacheData = $this->applyFilterByOfferList($iPropertyID, $arCacheData, $obOfferList);
        }

        if (empty($obProductList)) {
            return array_keys($arCacheData);
        }

        $arProductIDList = $obProductList->getIDList();

        $arResult = [];
        foreach ($arCacheData as $iValueID => $arElementIDList) {
            if (empty($arElementIDList)) {
                continue;
            }

            $arCommonIDList = array_intersect($arProductIDList, $arElementIDList);
            if (empty($arCommonIDList)) {
                continue;
            }

            $arResult[] = $iValueID;
        }

        return $arResult;
    }

    /**
     * Get list by property ID
     * Usage:
     * 1. Used to filter products/offers with filter type == "between"
     * @see \Lovata\FilterShopaholic\Classes\Helper\PropertyFilterHelper::getResultBetweenFilter
     *
     * Result example:
     * ['value_id_1' => [...product/offer ID list that have value with id == value_id_1...], [...]]
     *
     * @param int                                                        $iPropertyID
     * @param string                                                     $sModelClass
     * @param string                                                     $sResultModel
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null $obOfferList
     * @return array
     */
    public function getListByProperty($iPropertyID, $sModelClass, $sResultModel, $obOfferList = null)
    {
        //Get array from cache
        $arResult = $this->getDataForModel($iPropertyID, $sModelClass, $sResultModel);
        if ($sModelClass == Offer::class && $sResultModel == Product::class) {
            $arResult = $this->applyFilterByOfferList($iPropertyID, $arResult, $obOfferList);
        }

        return $arResult;
    }

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $obElementList = PropertyValueLink::getByProperty($this->sValue)->get();
        if ($obElementList->isEmpty()) {
            return [];
        }

        $arResult = [
            'product'       => [],
            'offer'         => [],
            'product_offer' => [],
        ];

        $obProductActiveList = ProductCollection::make()->active();
        $obOfferActiveList = OfferCollection::make()->active();

        //Prepare result array
        /** @var PropertyValueLink $obPropertyValueLink */
        foreach ($obElementList as $obPropertyValueLink) {
            if (!isset($arResult['product'][$obPropertyValueLink->value_id])) {
                $arResult['product'][$obPropertyValueLink->value_id] = [];
                $arResult['offer'][$obPropertyValueLink->value_id] = [];
                $arResult['product_offer'][$obPropertyValueLink->value_id] = [];
            }

            if ($obPropertyValueLink->element_type == Offer::class && $obOfferActiveList->has($obPropertyValueLink->element_id)) {
                $arResult['offer'][$obPropertyValueLink->value_id][] = $obPropertyValueLink->element_id;

                if ($obProductActiveList->has($obPropertyValueLink->product_id)) {
                    $arResult['product_offer'][$obPropertyValueLink->value_id][] = $obPropertyValueLink->product_id;
                }
            } elseif ($obProductActiveList->has($obPropertyValueLink->element_id)) {
                $arResult['product'][$obPropertyValueLink->value_id][] = $obPropertyValueLink->product_id;
            }
        }

        $this->prepareCacheArray($arResult['product']);
        $this->prepareCacheArray($arResult['offer']);
        $this->prepareCacheArray($arResult['product_offer']);

        foreach ($arResult['product_offer'] as $iValueID => $arProductIDList) {
            if (!isset($arResult['offer'][$iValueID])) {
                unset($arResult['product_offer'][$iValueID]);
            }
        }

        return $arResult;
    }

    /**
     * Get cached array for model
     * @param int    $iPropertyID
     * @param string $sModelName
     * @param string $sResultModel
     * @return array
     */
    protected function getDataForModel($iPropertyID, $sModelName, $sResultModel) : array
    {
        //Get array from cache
        $arCacheData = $this->get($iPropertyID);
        if (empty($arCacheData)) {
            return [];
        } elseif ($sModelName == Offer::class && $sResultModel == Product::class) {
            return $arCacheData['product_offer'];
        } elseif ($sModelName == Offer::class && $sResultModel == Offer::class) {
            return $arCacheData['offer'];
        }

        return $arCacheData['product'];
    }

    /**
     * Get cached array for model
     * @param int    $iPropertyID
     * @param string $sModelName
     * @param string $sResultModel
     * @param int    $iValueID
     * @return array
     */
    protected function getDataForModelByValue($iPropertyID, $sModelName, $sResultModel, $iValueID) : array
    {
        //Get array from cache
        $arCacheData = $this->get($iPropertyID);
        if (empty($arCacheData) || empty($sModelName)) {
            return [];
        }

        if ($sModelName == Offer::class && $sResultModel == Product::class) {
            $sFieldCode = 'product_offer';
        } elseif ($sModelName == Product::class && $sResultModel == Product::class) {
            $sFieldCode = 'product';
        } else {
            $sFieldCode = 'offer';
        }

        $arPropertyCacheData = $arCacheData[$sFieldCode];
        if (empty($arPropertyCacheData) || !isset($arPropertyCacheData[$iValueID])) {
            return [];
        }

        return $arPropertyCacheData[$iValueID];
    }

    /**
     * Remove empty element ID lists and apply array_unique
     * @param array                             $arCacheData
     */
    protected function prepareCacheArray(&$arCacheData)
    {
        if (empty($arCacheData)) {
            return;
        }

        foreach ($arCacheData as $iValueID => &$arProductIDList) {
            if (empty($arProductIDList)) {
                unset($arCacheData[$iValueID]);
                continue;
            }

            $arProductIDList = array_unique($arProductIDList);
        }
    }

    /**
     * Apply filter by offer collection
     * @param int                                                        $iPropertyID
     * @param array                                                      $arProductCacheData
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null $obOfferList
     * @return array
     */
    protected function applyFilterByOfferList($iPropertyID, $arProductCacheData, $obOfferList) : array
    {
        if (empty($obOfferList)) {
            return $arProductCacheData;
        }

        //Get array from cache
        $arOfferCachedData = $this->getDataForModel($iPropertyID, Offer::class, Offer::class);
        if (empty($arOfferCachedData)) {
            return [];
        }

        foreach ($arProductCacheData as $iValueID => $arProductIDList) {
            if (isset($arOfferCachedData[$iValueID]) && !empty(array_intersect($arOfferCachedData[$iValueID], $obOfferList->getIDList()))) {
                continue;
            }

            unset($arProductCacheData[$iValueID]);
        }

        return $arProductCacheData;
    }
}
