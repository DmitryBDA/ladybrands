<?php namespace Lovata\CouponsShopaholic\Classes\Event\Brand;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Classes\Item\BrandItem;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class BrandModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Brand
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class BrandModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Brand */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Brand::extend(function ($obElement) {
            $this->extendBrandModel($obElement);
        });
    }

    /**
     * Extend brand model
     * @param Brand $obElement
     */
    protected function extendBrandModel($obElement)
    {
        $obElement->belongsToMany['coupon_group'] = [
            CouponGroup::class,
            'table'    => 'lovata_coupons_shopaholic_group_brand',
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

        $this->clearProductList();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->coupon_group()->detach();
    }

    /**
     * Clear product cached list by coupon group ID (Relation between brand and coupon group)
     */
    protected function clearProductList()
    {
        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_brand')->where('brand_id', $this->obElement->id)->lists('group_id');
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
        return Brand::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return BrandItem::class;
    }
}
