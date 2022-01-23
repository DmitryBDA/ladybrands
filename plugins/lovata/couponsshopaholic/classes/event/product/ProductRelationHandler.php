<?php namespace Lovata\CouponsShopaholic\Classes\Event\Product;

use DB;
use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class ProductRelationHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     * @param array $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearProductList($arAttachedIDList);
        $this->checkAdditionalCategoryRelation($arAttachedIDList);
    }

    /**
     * After detach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearProductList($arAttachedIDList);
        $this->checkAdditionalCategoryRelation($arAttachedIDList);
    }

    /**
     * Clear cached product list
     * @param array $arAttachedIDList
     */
    protected function clearProductList($arAttachedIDList)
    {
        if (empty($arAttachedIDList) || $this->sRelationName != 'coupon_group') {
            return;
        }

        foreach ($arAttachedIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
        }
    }

    /**
     * Clear cached product list
     * @param array $arAttachedIDList
     */
    protected function checkAdditionalCategoryRelation($arAttachedIDList)
    {
        if (empty($arAttachedIDList) || $this->sRelationName != 'additional_category') {
            return;
        }

        foreach ($arAttachedIDList as $iCategoryID) {
            //Find category object
            $obCategory = Category::find($iCategoryID);
            if (empty($obCategory) || !$obCategory->active) {
                continue;
            }

            $this->clearProductListByCategory($obCategory);
        }
    }

    /**
     * Clear product cached list by coupon group ID (Relation between category and coupon group)
     * @param \Lovata\Shopaholic\Models\Category $obCategory
     */
    protected function clearProductListByCategory($obCategory)
    {
        if (empty($obCategory)) {
            return;
        }

        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_category')->where('category_id', $obCategory->id)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
        }

        $this->clearProductListByCategory($obCategory->parent);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() :string
    {
        return Product::class;
    }

    /**
     * Get relation name
     * @return array
     */
    protected function getRelationName()
    {
        return ['coupon_group', 'additional_category'];
    }
}
