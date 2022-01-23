<?php namespace Lovata\CouponsShopaholic\Classes\Event\Category;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class CategoryModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Category
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Category */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Category::extend(function ($obElement) {
            $this->extendCategoryModel($obElement);
        });

        $obEvent->listen('shopaholic.category.update.sorting', function () {
            $this->clearAllCategories();
        });
    }

    /**
     * Extend model object
     * @param Category $obElement
     */
    protected function extendCategoryModel($obElement)
    {
        $obElement->belongsToMany['coupon_group'] = [
            CouponGroup::class,
            'table'    => 'lovata_coupons_shopaholic_group_category',
            'otherKey' => 'group_id',
        ];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('active')) {
            return;
        }

        $this->clearProductList($this->obElement);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->coupon_group()->detach();
        if (!$this->obElement->active) {
            return;
        }

        $this->clearProductList($this->obElement);
    }

    /**
     * Clear product cached list by coupon group ID (Relation between category and coupon group)
     * @param Category $obCategory
     */
    protected function clearProductList($obCategory)
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

        $this->clearProductList($obCategory->parent);
    }

    /**
     * Clear product cached list by coupon group ID (Relation between category and coupon group)
     */
    protected function clearAllCategories()
    {
        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_category')->groupBy('group_id')->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Category::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return CategoryItem::class;
    }
}
