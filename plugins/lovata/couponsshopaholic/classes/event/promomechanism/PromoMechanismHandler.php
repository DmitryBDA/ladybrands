<?php namespace Lovata\CouponsShopaholic\Classes\Event\PromoMechanism;

use Lang;
use Lovata\Shopaholic\Classes\Collection\OfferCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Item\CouponGroupItem;
use Lovata\CouponsShopaholic\Classes\Helper\CouponHelper;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;
use Lovata\CouponsShopaholic\Classes\Store\OfferListStore;
use Lovata\CouponsShopaholic\Classes\Store\ShippingTypeListStore;

use Lovata\OrdersShopaholic\Models\PromoMechanism;
use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\CartPromoMechanismProcessor;

/**
 * Class PromoMechanismHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PromoMechanismHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen(CartPromoMechanismProcessor::EVENT_GET_MECHANISM_LIST, function ($obPromoProcessor) {
            $this->addCouponPromoMechanism($obPromoProcessor);
        });

        PromoMechanism::extend(function ($obElement) {
            /** @var PromoMechanism $obElement */
            $obElement->bindEvent('model.afterSave', function () use ($obElement) {
                $this->clearCouponGroupCache($obElement->id);
            });

            $obElement->bindEvent('model.afterDelete', function () use ($obElement) {
                $this->clearCouponGroupCache($obElement->id);
            });
        });
    }

    /**
     * Fins active coupons and apply promo mechanism to cart
     * @param CartPromoMechanismProcessor $obPromoProcessor
     * @throws \Exception
     */
    protected function addCouponPromoMechanism($obPromoProcessor)
    {
        if (empty($obPromoProcessor)) {
            return;
        }

        //get user cart object
        $obCart = $obPromoProcessor->getCartObject();
        if (empty($obCart)) {
            return;
        }

        //Get coupon list
        $obCouponList = $obCart->coupon;
        if ($obCouponList->isEmpty()) {
            return;
        }

        foreach ($obCouponList as $obCoupon) {
            if (!CouponHelper::instance()->check($obCoupon->code, $obCart->user_id)) {
                continue;
            }

            $this->applyCouponMechanism($obCoupon, $obPromoProcessor);
        }
    }

    /**
     * Apply coupon promo mechanism to cart
     * @param \Lovata\CouponsShopaholic\Models\Coupon $obCoupon
     * @param CartPromoMechanismProcessor             $obPromoProcessor
     */
    protected function applyCouponMechanism($obCoupon, $obPromoProcessor)
    {
        $obCouponGroup = $obCoupon->coupon_group;
        if (empty($obCouponGroup)) {
            return;
        }

        $obMechanism = $obCouponGroup->getPromoMechanismObject();
        if (empty($obMechanism)) {
            return;
        }

        $arProductIDList = ProductListStore::instance()->coupon_group->get($obCouponGroup->id, true);
        $obProductList = ProductCollection::make()->intersect($arProductIDList);

        $arOfferIDList = OfferListStore::instance()->coupon_group->get($obCouponGroup->id);
        $obOfferList = OfferCollection::make()->intersect($arOfferIDList);

        $arShippingTypeIDList = ShippingTypeListStore::instance()->coupon_group->get($obCouponGroup->id);
        $obShippingTypeList = ShippingTypeCollection::make()->intersect($arShippingTypeIDList);

        $obMechanism->setCheckPositionCallback(function ($obPosition) use ($obProductList, $obOfferList) {
            /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition */
            if (empty($obPosition)) {
                return false;
            }

            $obOfferItem = $obPosition->offer;
            if (empty($obOfferItem) || $obOfferItem->isEmpty()) {
                return false;
            }

            $bCheckOfferList = $obOfferList->isNotEmpty() && $obOfferList->has($obOfferItem->id);
            $bCheckProductList = $obProductList->isNotEmpty() && $obProductList->has($obOfferItem->product_id);

            $bResult = $bCheckOfferList || $bCheckProductList || ($obOfferList->isEmpty() && $obProductList->isEmpty());

            return $bResult;
        });

        $obMechanism->setCheckShippingTypeCallback(function ($obShippingType) use ($obShippingTypeList) {
            if (empty($obShippingType)) {
                return false;
            }

            $bResult = $obShippingTypeList->isEmpty() ||$obShippingTypeList->has($obShippingType->id);

            return $bResult;
        });

        $sResult = Lang::get('lovata.couponsshopaholic::lang.message.coupon_discount_info', ['code' => $obCoupon->code]);
        $obMechanism->setRelatedDescription($sResult);

        $obPromoProcessor->addMechanism($obMechanism);
    }

    /**
     * Clear cache of coupon groups by promo mechanism ID
     * @param int $iMechanismID
     */
    protected function clearCouponGroupCache($iMechanismID)
    {
        if (empty($iMechanismID)) {
            return;
        }

        //Get coupon group list
        $obCouponGroupList = CouponGroup::getByPromoMechanism($iMechanismID)->get();
        if ($obCouponGroupList->isEmpty()) {
            return;
        }

        /** @var CouponGroup $obCouponGroup */
        foreach ($obCouponGroupList as $obCouponGroup) {
            CouponGroupItem::clearCache($obCouponGroup->id);
        }
    }
}
