<?php namespace Lovata\CouponsShopaholic\Classes\Store\Product;

use DB;
use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithTwoParam;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\BrandCollection;
use Lovata\Shopaholic\Classes\Collection\CategoryCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\CouponsShopaholic\Models\CouponGroup;

/**
 * Class ListByCouponGroupStore
 * @package Lovata\CouponsShopaholic\Classes\Store\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByCouponGroupStore extends AbstractStoreWithTwoParam
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
     * Get product ID list by relation between product and coupon
     * @return array
     */
    protected function getByProductRelation() : array
    {
        $arElementIDList = (array) DB::table('lovata_coupons_shopaholic_group_product')->where('group_id', $this->sValue)->lists('product_id');
        $arElementIDList = ProductCollection::make()->active()->intersect($arElementIDList)->getIDList();

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between offer and coupon
     * @return array
     */
    protected function getByOfferRelation() : array
    {
        if ($this->sAdditionParam) {
            return [];
        }

        /** @var CouponGroup $obCouponGroup */
        $obCouponGroup = CouponGroup::with('offer')->find($this->sValue);
        if (empty($obCouponGroup) || empty($obCouponGroup->offer) || $obCouponGroup->offer->isEmpty()) {
            return [];
        }

        $arElementIDList = [];
        foreach ($obCouponGroup->offer as $obOffer) {
            if (!$obOffer->active) {
                continue;
            }

            $arElementIDList[] = $obOffer->product_id;
        }

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between brand and coupon
     * @return array
     */
    protected function getByBrandRelation() : array
    {
        $arBrandIDList = (array) DB::table('lovata_coupons_shopaholic_group_brand')->where('group_id', $this->sValue)->lists('brand_id');
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
     * Get product ID list by relation between category and coupon
     * @return array
     */
    protected function getByCategoryRelation() : array
    {
        $arCategoryIDList = (array) DB::table('lovata_coupons_shopaholic_group_category')->where('group_id', $this->sValue)->lists('category_id');
        if (empty($arCategoryIDList)) {
            return [];
        }

        $obCategoryList = CategoryCollection::make($arCategoryIDList)->active();

        $arElementIDList = ProductCollection::make()->category($obCategoryList->getIDList(), true)->getIDList();

        return $arElementIDList;
    }

    /**
     * Get product ID list by relation between tag and coupon
     * @return array
     */
    protected function getByTagRelation() : array
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return [];
        }

        $arTagIDList = (array) DB::table('lovata_coupons_shopaholic_group_tag')->where('group_id', $this->sValue)->lists('tag_id');
        if (empty($arTagIDList)) {
            return [];
        }

        $obTagList = \Lovata\TagsShopaholic\Classes\Collection\TagCollection::make()->active()->intersect($arTagIDList);
        if ($obTagList->isEmpty()) {
            return [];
        }

        $arElementIDList = (array) DB::table('lovata_tagsshopaholic_tag_product')->whereIn('tag_id', $obTagList->getIDList())->lists('product_id');

        return $arElementIDList;
    }
}
