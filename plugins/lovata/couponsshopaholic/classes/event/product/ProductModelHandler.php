<?php namespace Lovata\CouponsShopaholic\Classes\Event\Product;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\ProductItem;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class ProductModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Product */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Product::extend(function ($obElement) {
            $this->extendModel($obElement);
        });
    }

    /**
     * Extend model object
     * @param Product $obElement
     */
    protected function extendModel($obElement)
    {
        $obElement->belongsToMany['coupon_group'] = [
            CouponGroup::class,
            'table'    => 'lovata_coupons_shopaholic_group_product',
            'otherKey' => 'group_id',
        ];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if ($this->isFieldChanged('active')) {
            $this->clearCachedList();
        }

        if ($this->isFieldChanged('brand_id')) {
            $this->clearProductListByBrandID($this->obElement->brand_id);
            $this->clearProductListByBrandID($this->obElement->getOriginal('brand_id'));
        }

        if ($this->isFieldChanged('category_id')) {
            $this->clearProductListByCategory($this->obElement->category);

            $iOldCategoryID = $this->obElement->getOriginal('category_id');
            //Get old category object
            if (!empty($iOldCategoryID)) {
                $obOldCategory = Category::find($iOldCategoryID);
                $this->clearProductListByCategory($obOldCategory);
            }
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->clearProductListByCategory($this->obElement->category);
        $this->clearProductListByBrandID($this->obElement->brand_id);

        if (!$this->obElement->active) {
            return;
        }

        $this->clearCachedList();
    }

    /**
     * Clear product cached list by coupon group ID (Relation between product and coupon group)
     */
    protected function clearCachedList()
    {
        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_product')->where('product_id', $this->obElement->id)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
        }
    }

    /**
     * Clear product cached list by coupon group ID (Relation between brand and coupon group)
     * @param int $iBrandID
     */
    protected function clearProductListByBrandID($iBrandID)
    {
        if (empty($iBrandID)) {
            return;
        }

        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_brand')->where('brand_id', $iBrandID)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
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
    protected function getModelClass()
    {
        return Product::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ProductItem::class;
    }
}
