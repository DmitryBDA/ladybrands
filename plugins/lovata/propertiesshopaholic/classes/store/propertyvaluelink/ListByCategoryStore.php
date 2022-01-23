<?php namespace Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink;

use Lovata\Toolbox\Classes\Collection\CollectionStore;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithTwoParam;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class ListByCategoryStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * Cached data:
 *  [
 *      'product'       => ['value_id_1', 'value_id_2', ...]
 *      'product_offer' => ['value_id_1', 'value_id_2', ...]
 *  ]
 */
class ListByCategoryStore extends AbstractStoreWithTwoParam
{
    protected static $instance;

    /** @var array */
    protected $arValueIDList = [];

    /**
     * Get value id list by property ID and category ID
     * Usage:
     * 1. Used for render list of property values in filter panel (only category catalog page)
     * @see \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem::getPropertyValueAttribute()
     *
     * Result example:
     * ['value_id_1', 'value_id_2', ...]
     *
     * @param int    $iPropertyID
     * @param int    $iCategoryID
     * @param string $sModelClass
     * @return array
     */
    public function getValueByCategory($iPropertyID, $iCategoryID, $sModelClass) : array
    {
        //Get array from cache
        $arCacheData = $this->get($iPropertyID, $iCategoryID);
        if (empty($arCacheData) || empty($sModelClass)) {
            return [];
        } elseif ($sModelClass == Offer::class) {
            return $arCacheData['product_offer'];
        }

        return $arCacheData['product'];
    }

    /**
     * Clear cache by product ID
     * @param Product $obProduct
     * @param array   $arPropertyIDList
     */
    public function clearByProduct($obProduct, $arPropertyIDList = null)
    {
        if (empty($obProduct)) {
            return;
        }

        $arCategoryIDList = $this->getProductCategoryIDList($obProduct);

        if (empty($arPropertyIDList)) {
            //Get property value link list
            $arPropertyIDList = (array) PropertyValueLink::getByElementType(Product::class)
                ->getByElementID($obProduct->id)
                ->groupBy('property_id')
                ->lists('property_id');
        }

        $this->clearByCategoryAndPropertyList($arCategoryIDList, $arPropertyIDList);
    }

    /**
     * Clear cache by offer ID
     * @param Offer $obOffer
     * @param int   $iProductID
     * @param array $arPropertyIDList
     */
    public function clearByOffer($obOffer, $iProductID, $arPropertyIDList = null)
    {
        if (empty($obOffer) || empty($iProductID)) {
            return;
        }

        //Get product object
        $obProduct = Product::withTrashed()->find($iProductID);
        if (empty($obProduct)) {
            return;
        }

        $arCategoryIDList = $this->getProductCategoryIDList($obProduct);

        if (empty($arPropertyIDList)) {
            //Get property value link list
            $arPropertyIDList = (array) PropertyValueLink::getByElementType(Offer::class)
                ->getByElementID($obOffer->id)
                ->groupBy('property_id')
                ->lists('property_id');
        }

        $this->clearByCategoryAndPropertyList($arCategoryIDList, $arPropertyIDList);
    }

    /**
     * Clear cache by category ID
     * @param int $iCategoryID
     */
    public function clearByCategoryID($iCategoryID)
    {
        //Get property ID list
        $arPropertyIDList = (array) Property::lists('id');
        if (empty($arPropertyIDList) || empty($iCategoryID)) {
            return;
        }

        $arCategoryIDList = [$iCategoryID];

        $this->clearByCategoryAndPropertyList($arCategoryIDList, $arPropertyIDList);
    }

    /**
     * Clear element ID list
     * @param mixed $sFilterValue
     * @param mixed $sAdditionalParam
     */
    public function clear($sFilterValue, $sAdditionalParam = null)
    {
        parent::clear($sFilterValue, $sAdditionalParam);

        $sKey = 'active|category'.$this->sAdditionParam;
        CollectionStore::instance()->clear(ProductCollection::class.'@'.$sKey);
    }

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        //Get product collection by category ID
        $obProductList = ProductCollection::make()->category($this->sAdditionParam);

        $arProductIDList = $obProductList->getIDList();

        //Get array from cache
        $arCacheData = ListByPropertyStore::instance()->get($this->sValue);
        if (empty($arCacheData) || empty($arProductIDList)) {
            return [];
        }

        $arResult = [
            'product'       => $this->prepareCacheArray($arCacheData['product'], $arProductIDList),
            'product_offer' => $this->prepareCacheArray($arCacheData['product_offer'], $arProductIDList),
        ];

        return $arResult;
    }

    /**
     * Prepare cached array
     * @param array $arCacheData
     * @param array $arProductIDList
     * @return array
     */
    protected function prepareCacheArray($arCacheData, $arProductIDList) : array
    {
        if (empty($arCacheData)) {
            return [];
        }

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

        $arResult = array_unique($arResult);

        return $arResult;
    }

    /**
     * Get category ID list for product
     * @param Product $obProduct
     * @return array
     */
    protected function getProductCategoryIDList($obProduct) : array
    {
        if (empty($obProduct)) {
            return [];
        }

        $arResult = [];

        if (!empty($obProduct->category_id)) {
            $arResult[] = $obProduct->category_id;
        }

        $arAdditionalCategoryIDList = (array) $obProduct->additional_category->lists('id');
        if (!empty($arAdditionalCategoryIDList)) {
            $arResult = array_merge($arResult, $arAdditionalCategoryIDList);
        }

        return $arResult;
    }

    /**
     * Clear cache by category ID list and property ID list
     * @param array $arCategoryIDList
     * @param array $arPropertyIDList
     */
    protected function clearByCategoryAndPropertyList($arCategoryIDList, $arPropertyIDList)
    {
        if (empty($arCategoryIDList) || empty($arPropertyIDList)) {
            return;
        }

        foreach ($arCategoryIDList as $icategoryID) {
            foreach ($arPropertyIDList as $iPropertyID) {
                $this->clear($iPropertyID, $icategoryID);
            }
        }
    }
}
