<?php namespace Lovata\FilterShopaholic\Classes\Store\FilterValue;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\OfferCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class FilterByPropertyStore
 * @package Lovata\Toolbox\Classes\Store\FilterValue
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * Cached data:
 *  [
 *      'product'       => ['property_id_1' => [...product ID list that have value with property_id == property_id_1...], [...]]
 *      'product_offer' => ['property_id_1' => [...product ID list, offers that have value with property_id == property_id_1...], [...]]
 *      'offer'         => ['property_id_1' => [...offer ID list that have value with property_id == property_id_1...], [...]]
 *  ]
 */
class FilterByPropertyStore extends AbstractStoreWithParam
{
    protected static $instance;

    /** @var PropertyValue */
    protected $obValue;

    /**
     * Get value id list by property ID and value slug
     * Usage:
     * 1. Used in product/offer filtration by properties
     * @see \Lovata\FilterShopaholic\Classes\Helper\PropertyFilterHelper::getResultCheckboxFilter()
     * @see \Lovata\FilterShopaholic\Classes\Helper\PropertyFilterHelper::getResultSelectFilter()
     *
     * Result example:
     * ['product_id_1', 'product_id_2', ...]
     *
     * @param int                                                          $iPropertyID
     * @param string                                                       $sPropertyValue
     * @param string                                                       $sModelClass
     * @param string                                                       $sResultModel
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection|null $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null   $obOfferList
     * @return array
     */
    public function getListByPropertyValue($iPropertyID, $sPropertyValue, $sModelClass, $sResultModel, $obProductList = null, $obOfferList = null) : array
    {
        $arCachedList = $this->getDataForModel($sPropertyValue, $sModelClass, $sResultModel);
        if (empty($arCachedList) || empty($iPropertyID) || !isset($arCachedList[$iPropertyID])) {
            return [];
        }

        $arResult = $arCachedList[$iPropertyID];

        //For correct filtration products by properties of offers, it is necessary to use only active offers or those that were passed to $obOfferList variable
        if (!empty($arResult) && $sModelClass == Offer::class && $sResultModel == Product::class && !empty($obOfferList)) {
            $arResult = $this->applyFilterByOfferList($iPropertyID, $sPropertyValue, $arResult, $obProductList, $obOfferList);
        }

        return $arResult;
    }

    /**
     * Get element ID list from cache or database
     * We override the parent class method, because we need to get the property value object by slug value
     * @param string $sFilterValue
     * @return array
     */
    public function get($sFilterValue) : array
    {
        if (empty($sFilterValue)) {
            return [];
        }

        $this->sValue = $sFilterValue;
        if (array_key_exists($this->getCacheKey(), $this->arCachedList) && is_array($this->arCachedList[$this->getCacheKey()])) {
            return $this->arCachedList[$this->getCacheKey()];
        }

        //Get value object by slug
        $this->obValue = PropertyValue::getBySlug($sFilterValue)->first();
        if (empty($this->obValue)) {
            $this->arCachedList[$this->getCacheKey()] = [];
            return [];
        }

        $arElementIDList = $this->getIDList();
        $this->arCachedList[$this->getCacheKey()] = $arElementIDList;

        return $arElementIDList;
    }

    /**
     * Get element ID list from database, without cache
     * We override the parent class method, because we need to get the property value object by slug value
     * @param string $sFilterValue
     * @return array
     */
    public function getNoCache($sFilterValue) : array
    {
        if (empty($sFilterValue)) {
            return [];
        }

        $this->sValue = $sFilterValue;

        //Get value object by slug
        $this->obValue = PropertyValue::getBySlug($sFilterValue)->first();
        if (empty($this->obValue)) {
            return [];
        }

        $arElementIDList = $this->getIDListFromDB();

        return $arElementIDList;
    }

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        if (empty($this->obValue)) {
            return [];
        }

        $obElementList = PropertyValueLink::getByValue($this->obValue->id)->get();
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
            if (!isset($arResult['product'][$obPropertyValueLink->property_id])) {
                $arResult['product'][$obPropertyValueLink->property_id] = [];
                $arResult['offer'][$obPropertyValueLink->property_id] = [];
                $arResult['product_offer'][$obPropertyValueLink->property_id] = [];
            }

            if ($obPropertyValueLink->element_type == Offer::class && $obOfferActiveList->has($obPropertyValueLink->element_id)) {
                $arResult['offer'][$obPropertyValueLink->property_id][] = $obPropertyValueLink->element_id;

                if ($obProductActiveList->has($obPropertyValueLink->product_id)) {
                    $arResult['product_offer'][$obPropertyValueLink->property_id][] = $obPropertyValueLink->product_id;
                    $arResult['full'][$obPropertyValueLink->property_id][$obPropertyValueLink->product_id][] = $obPropertyValueLink->element_id;
                }
            } elseif ($obProductActiveList->has($obPropertyValueLink->element_id)) {
                $arResult['product'][$obPropertyValueLink->property_id][] = $obPropertyValueLink->product_id;
            }
        }

        $this->prepareCacheArray($arResult['product']);
        $this->prepareCacheArray($arResult['offer']);
        $this->prepareCacheArray($arResult['product_offer']);

        return $arResult;
    }

    /**
     * Get cached array for model
     * @param string $sPropertyValue
     * @param string $sModelClass
     * @param string $sResultModel
     * @return array
     */
    protected function getDataForModel($sPropertyValue, $sModelClass = null, $sResultModel = null) : array
    {
        //Get array from cache
        $arCacheData = $this->get($sPropertyValue);
        if (empty($arCacheData)) {
            $arResult = [];
        } elseif ($sModelClass == Offer::class && $sResultModel == Offer::class) {
            $arResult = $arCacheData['offer'];
        } elseif ($sModelClass == Offer::class && $sResultModel == Product::class) {
            $arResult = $arCacheData['product_offer'];
        } elseif ($sModelClass == Product::class && $sResultModel == Product::class) {
            $arResult = $arCacheData['product'];
        } else {
            $arResult = $arCacheData['full'];
        }

        return $arResult;
    }

    /**
     * Prepare cache array before saving
     * Remove empty lists and apply array_unique() function to product/offer ID list
     * @param array $arCacheData
     */
    protected function prepareCacheArray(&$arCacheData)
    {
        if (empty($arCacheData)) {
            return;
        }

        foreach ($arCacheData as $iPropertyID => &$arProductIDList) {
            if (empty($arProductIDList)) {
                unset($arCacheData[$iPropertyID]);
                continue;
            }

            $arProductIDList = array_unique($arProductIDList);
        }
    }

    /**
     * Apply filter by offer collection
     * For correct filtration products by properties of offers,
     * it is necessary to use only active offers or those that were passed to $obOfferList variable
     * @param int                                                          $iPropertyID
     * @param string                                                       $sPropertyValue
     * @param array                                                        $arResult
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection|null $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection|null   $obOfferList
     * @return array
     */
    protected function applyFilterByOfferList($iPropertyID, $sPropertyValue, $arResult, $obProductList, $obOfferList) : array
    {
        $arFullCachedData = $this->getDataForModel($sPropertyValue);
        $arOfferCachedData = $this->getDataForModel($sPropertyValue, Offer::class, Offer::class);
        if (empty($arOfferCachedData) || empty($arFullCachedData) || !isset($arOfferCachedData[$iPropertyID]) || !isset($arFullCachedData[$iPropertyID])) {
            return [];
        }

        $arProductCachedData = $arFullCachedData[$iPropertyID];
        $arOfferCachedIDList = array_intersect($arOfferCachedData[$iPropertyID], $obOfferList->getIDList());

        if (!empty($obProductList)) {
            $arResult = array_intersect($obProductList->getIDList(), $arResult);
        }

        if (empty($arResult) || empty($arOfferCachedIDList)) {
            return [];
        }

        foreach ($arResult as $iKey => $iProductID) {
            if (isset($arProductCachedData[$iProductID]) && !empty(array_intersect($arProductCachedData[$iProductID], $arOfferCachedIDList))) {
                continue;
            }

            unset($arResult[$iKey]);
        }

        return $arResult;
    }
}
