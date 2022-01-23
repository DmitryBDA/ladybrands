<?php namespace Lovata\CouponsShopaholic\Classes\Event\Cart;

use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\CouponsShopaholic\Models\Coupon;

/**
 * Class CartModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Cart
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartModelHandler
{
    /** @var Cart */
    protected $obElement;

    /**
     * Add listeners
     */
    public function subscribe()
    {
        Cart::extend(function ($obElement) {
            $this->extendModel($obElement);

            /** @var \Model $obElement */
            $obElement->bindEvent('model.afterDelete', function () use ($obElement) {
                $this->obElement = $obElement;
                $this->afterDelete();
            });
        });
    }

    /**
     * Extend model
     * @param Cart $obElement
     */
    protected function extendModel($obElement)
    {
        $obElement->belongsToMany['coupon'] = [
            Coupon::class,
            'table' => 'lovata_coupons_shopaholic_coupon_cart',
        ];
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->coupon()->detach();
    }
}
