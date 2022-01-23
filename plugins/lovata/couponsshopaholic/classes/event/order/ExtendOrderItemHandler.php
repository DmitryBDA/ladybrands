<?php namespace Lovata\CouponsShopaholic\Classes\Event\Order;

use DB;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;

/**
 * Class ExtendOrderItemHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Order
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOrderItemHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        OrderItem::extend(function ($obOrderItem) {
            /** @var OrderItem $obOrderItem */
            $this->extendItem($obOrderItem);
        });
    }

    /**
     * Extend item object
     * @param OrderItem $obOrderItem
     */
    protected function extendItem($obOrderItem)
    {
        $obOrderItem->arExtendResult[] = 'addCouponList';

        $obOrderItem->addDynamicMethod('addCouponList', function () use ($obOrderItem) {
            $arCouponList = DB::table('lovata_coupons_shopaholic_order_coupon')->where('order_id', $obOrderItem->id)->lists('code');
            $obOrderItem->setAttribute('coupon_list', $arCouponList);
        });
    }
}
