<?php namespace Lovata\DiscountsShopaholic\Classes\Store\Product;

use DB;
use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\BrandCollection;
use Lovata\Shopaholic\Classes\Collection\CategoryCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class ListByDiscountStore
 * @package Lovata\DiscountsShopaholic\Classes\Store\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByDiscountStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = array_merge(
            $this->getByProductRelation(),
            $this->getByOfferRelation(),
            $this->getByBrandRelation(),
            $this->getByCategoryRelation(),
            $this->getByTagRelation()
        );
        $arElementIDList = array_unique($arElementIDList);

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between product and discount
     * @return array
     */
    protected function getByProductRelation() : array
    {
        $arElementIDList = (array) DB::table('lovata_discounts_shopaholic_discount_product')->where('discount_id', $this->sValue)->lists('product_id');
        $arElementIDList = ProductCollection::make()->active()->intersect($arElementIDList)->getIDList();

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between offer and discount
     * @return array
     */
    protected function getByOfferRelation() : array
    {
        /** @var Discount $obDiscount */
        $obDiscount = Discount::with('offer')->find($this->sValue);
        if (empty($obDiscount) || empty($obDiscount->offer) || $obDiscount->offer->isEmpty()) {
            return [];
        }

        $arElementIDList = [];
        foreach ($obDiscount->offer as $obOffer) {
            if (!$obOffer->active) {
                continue;
            }

            $arElementIDList[] = $obOffer->product_id;
        }

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between brand and discount
     * @return array
     */
    protected function getByBrandRelation() : array
    {
        $arBrandIDList = (array) DB::table('lovata_discounts_shopaholic_discount_brand')->where('discount_id', $this->sValue)->lists('brand_id');
        if (empty($arBrandIDList)) {
            return [];
        }

        $obBrandList = BrandCollection::make($arBrandIDList)->active();
        if ($obBrandList->isEmpty()) {
            return [];
        }

        $arElementIDList = (array) Product::whereIn('brand_id', $obBrandList->getIDList())->lists('id');

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between category and discount
     * @return array
     */
    protected function getByCategoryRelation() : array
    {
        $arCategoryIDList = (array) DB::table('lovata_discounts_shopaholic_discount_category')->where('discount_id', $this->sValue)->lists('category_id');
        if (empty($arCategoryIDList)) {
            return [];
        }

        $obCategoryList = CategoryCollection::make($arCategoryIDList)->active();

        $arElementIDList = ProductCollection::make()->category($obCategoryList->getIDList(), true)->getIDList();

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between tag and discount
     * @return array
     */
    protected function getByTagRelation() : array
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return [];
        }

        $arTagIDList = (array) DB::table('lovata_discounts_shopaholic_discount_tag')->where('discount_id', $this->sValue)->lists('tag_id');
        if (empty($arTagIDList)) {
            return [];
        }

        $arElementIDList = (array) DB::table('lovata_tagsshopaholic_tag_product')->whereIn('tag_id', $arTagIDList)->lists('product_id');

        return $arElementIDList;
    }
}
