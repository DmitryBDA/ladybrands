<?php namespace Lovata\CouponsShopaholic\Classes\Event\Cart;

use Input;
use Lang;
use Kharanenka\Helper\Result;
use Lovata\CouponsShopaholic\Classes\Helper\CouponHelper;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Components\Cart;

/**
 * Class ExtendCartComponentHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Cart
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendCartComponentHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Cart::extend(function ($obComponent) {
            /** @var Cart $obComponent */
            $this->addCouponMethod($obComponent);
            $this->removeCouponMethod($obComponent);
            $this->clearCouponListMethod($obComponent);
            $this->getCouponListMethod($obComponent);
        });
    }

    /**
     * Extend Cart component and add method "onAddCoupon"
     * @param Cart $obComponent
     */
    protected function addCouponMethod($obComponent)
    {
        $obComponent->addDynamicMethod('onAddCoupon', function() use ($obComponent) {
            $sCode = Input::get('coupon');

            if (CouponHelper::instance()->addToCart($sCode)) {
                Result::setTrue(CartProcessor::instance()->getCartData());
            } else {
                $sMessage = Lang::get('lovata.couponsshopaholic::lang.message.error_coupon_can_not_applied');
                Result::setFalse(CartProcessor::instance()->getCartData())->setMessage($sMessage);
            }

            return Result::get();
        });
    }

    /**
     * Extend Cart component and add method "onRemoveCoupon"
     * @param Cart $obComponent
     */
    protected function removeCouponMethod($obComponent)
    {
        $obComponent->addDynamicMethod('onRemoveCoupon', function() use ($obComponent) {
            $sCode = Input::get('coupon');

            CouponHelper::instance()->removeFromCart($sCode);

            Result::setTrue(CartProcessor::instance()->getCartData());

            return Result::get();
        });
    }

    /**
     * Extend Cart component and add method "onClearCouponList"
     * @param Cart $obComponent
     */
    protected function clearCouponListMethod($obComponent)
    {
        $obComponent->addDynamicMethod('onClearCouponList', function() use ($obComponent) {
            CouponHelper::instance()->clearCouponList();

            Result::setTrue(CartProcessor::instance()->getCartData());

            return Result::get();
        });
    }

    /**
     * Extend Cart component and add method "getCouponListMethod"
     * @param Cart $obComponent
     */
    protected function getCouponListMethod($obComponent)
    {
        $obComponent->addDynamicMethod('getAppliedCouponList', function() {
            $obCouponList = CouponHelper::instance()->getAppliedCouponList();

            return $obCouponList;
        });
    }
}
