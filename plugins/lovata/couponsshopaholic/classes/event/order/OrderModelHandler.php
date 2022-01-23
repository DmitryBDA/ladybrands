<?php namespace Lovata\CouponsShopaholic\Classes\Event\Order;

use Lang;

use Lovata\Shopaholic\Classes\Collection\OfferCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderPromoMechanism;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;
use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OrderPromoMechanismProcessor;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\CouponsShopaholic\Classes\Helper\CouponHelper;

/**
 * Class OrderModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Order
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderModelHandler
{
    /** @var Order */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        Order::extend(function ($obElement) {
            $this->extendModel($obElement);

            /** @var \Model $obElement */
            $obElement->bindEvent('model.afterDelete', function () use ($obElement) {
                $this->obElement = $obElement;
                $this->afterDelete();
            });
        });

        $obEvent->listen(OrderProcessor::EVENT_UPDATE_ORDER_AFTER_CREATE, function ($obOrder) {
            /** @var \Lovata\OrdersShopaholic\Models\Order $obOrder */
            $this->attachCouponToOrder($obOrder);
        });

        $obEvent->listen(OrderProcessor::EVENT_ORDER_CREATED, function ($obOrder) {
            CouponHelper::instance()->clearCouponList();
        });

        $obEvent->listen(OrderPromoMechanismProcessor::EVENT_MECHANISM_ADD_CHECK_CALLBACK_METHOD, function ($obMechanism, $iElementID, $sElementType, $arElementData) {
            /** @var \Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism $obMechanism */
            $obEventMechanism = $this->addCheckCallback($obMechanism, $iElementID, $sElementType, $arElementData);

            return $obEventMechanism;
        });

        $obEvent->listen(OrderPromoMechanism::EVENT_GET_DESCRIPTION, function ($obOrderMechanism) {
            /** @var OrderPromoMechanism $obOrderMechanism */
            $sResult = null;
            if ($obOrderMechanism->element_type == Coupon::class) {
                $sResult = Lang::get('lovata.couponsshopaholic::lang.message.coupon_discount_info', ['code' => array_get($obOrderMechanism->element_data, 'code')]);
            }

            return $sResult;
        });
    }

    /**
     * Extend model
     * @param Order $obElement
     */
    protected function extendModel($obElement)
    {
        $obElement->belongsToMany['coupon'] = [
            Coupon::class,
            'table' => 'lovata_coupons_shopaholic_order_coupon',
            'pivot' => ['code'],
        ];
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->coupon()->detach();
    }

    /**
     * Attach coupons to Order
     * @param \Lovata\OrdersShopaholic\Models\Order $obOrder
     * @throws \Exception
     */
    protected function attachCouponToOrder($obOrder)
    {
        CouponHelper::instance()->attachCouponListToOrder($obOrder);
    }

    /**
     * Add check callback method
     * @param \Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism $obMechanism $obMechanism
     * @param int                                                                      $iElementID
     * @param string                                                                   $sElementType
     * @param array                                                                    $arElementData
     * @return null|\Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism
     */
    protected function addCheckCallback($obMechanism, $iElementID, $sElementType, $arElementData)
    {
        if (empty($obMechanism) || !$obMechanism instanceof InterfacePromoMechanism || $sElementType != Coupon::class) {
            return null;
        }

        $arProductIDList = (array) array_get($arElementData, 'product_list');
        $obProductList = ProductCollection::make()->intersect($arProductIDList);

        $arOfferIDList = (array) array_get($arElementData, 'offer_list');
        $obOfferList = OfferCollection::make()->intersect($arOfferIDList);

        $arShippingTypeIDList = (array) array_get($arElementData, 'shipping_type_list');
        $obShippingTypeList = ShippingTypeCollection::make()->intersect($arShippingTypeIDList);

        $obMechanism->setCheckPositionCallback(function ($obOrderPosition) use ($obProductList, $obOfferList) {
            /** @var \Lovata\OrdersShopaholic\Models\OrderPosition $obOrderPosition */
            if (empty($obOrderPosition)) {
                return false;
            }

            $obOffer = $obOrderPosition->offer;
            if (empty($obOffer)) {
                return false;
            }

            $bCheckOfferList = $obOfferList->isNotEmpty() && $obOfferList->has($obOffer->id);
            $bCheckProductList = $obProductList->isNotEmpty() && $obProductList->has($obOffer->product_id);

            $bResult = $bCheckOfferList || $bCheckProductList || ($obOfferList->isEmpty() && $obProductList->isEmpty());

            return $bResult;
        });

        $obMechanism->setCheckShippingTypeCallback(function ($obShippingType) use ($obShippingTypeList) {
            if (empty($obShippingType)) {
                return false;
            }

            $bResult = $obShippingTypeList->isEmpty() || $obShippingTypeList->has($obShippingType->id);

            return $bResult;
        });

        return $obMechanism;
    }
}
