<?php namespace Lovata\CouponsShopaholic\Classes\Event\ShippingType;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class ShippingTypeModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var ShippingType */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        ShippingType::extend(function ($obElement) {
            $this->extendShippingTypeModel($obElement);
        });
    }

    /**
     * Extend model
     * @param ShippingType $obElement
     */
    protected function extendShippingTypeModel($obElement)
    {
        $obElement->belongsToMany['coupon_group'] = [
            CouponGroup::class,
            'table'    => 'lovata_coupons_shopaholic_group_shipping_type',
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

        $this->clearCachedList();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->coupon_group()->detach();
    }

    /**
     * Clear product cached list by coupon group ID (Relation between shipping type and coupon group)
     */
    protected function clearCachedList()
    {
        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_shipping_type')->where('shipping_type_id', $this->obElement->id)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ShippingTypeListStore::instance()->coupon_group->clear($iCouponGroupID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return ShippingType::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ShippingTypeItem::class;
    }
}
