<?php namespace Lovata\CouponsShopaholic;

use Event;
use System\Classes\PluginBase;

//Common events
use Lovata\CouponsShopaholic\Classes\Event\ExtendBackendMenuHandler;
//Brand events
use Lovata\CouponsShopaholic\Classes\Event\Brand\BrandModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Brand\BrandRelationHandler;
use Lovata\CouponsShopaholic\Classes\Event\Brand\ExtendBrandControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\Brand\ExtendBrandFieldsHandler;
//Cart events
use Lovata\CouponsShopaholic\Classes\Event\Cart\CartModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Cart\ExtendCartComponentHandler;
//Category events
use Lovata\CouponsShopaholic\Classes\Event\Category\CategoryModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Category\CategoryRelationHandler;
use Lovata\CouponsShopaholic\Classes\Event\Category\ExtendCategoryControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\Category\ExtendCategoryFieldsHandler;
//Coupon events
use Lovata\CouponsShopaholic\Classes\Event\Coupon\CouponModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Coupon\ExtendCouponColumnsHandler;
use Lovata\CouponsShopaholic\Classes\Event\Coupon\ExtendCouponFieldsHandler;
//Coupon group events
use Lovata\CouponsShopaholic\Classes\Event\CouponGroup\CouponGroupModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\CouponGroup\CouponGroupRelationHandler;
use Lovata\CouponsShopaholic\Classes\Event\CouponGroup\ExtendCouponGroupFieldsHandler;
//Offer events
use Lovata\CouponsShopaholic\Classes\Event\Offer\OfferModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Offer\OfferRelationHandler;
use Lovata\CouponsShopaholic\Classes\Event\Offer\ExtendOfferControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\Offer\ExtendOfferFieldsHandler;
//Order events
use Lovata\CouponsShopaholic\Classes\Event\Order\OrderModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Order\ExtendOrderItemHandler;
//Product events
use Lovata\CouponsShopaholic\Classes\Event\Product\ProductModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Product\ProductRelationHandler;
use Lovata\CouponsShopaholic\Classes\Event\Product\ExtendProductCollectionHandler;
use Lovata\CouponsShopaholic\Classes\Event\Product\ExtendProductControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\Product\ExtendProductFieldsHandler;
//Promo block events
use Lovata\CouponsShopaholic\Classes\Event\PromoBlock\PromoBlockModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\PromoBlock\ExtendPromoBlockControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\PromoBlock\ExtendPromoBlockFieldsHandler;
//Promo mechanism events
use Lovata\CouponsShopaholic\Classes\Event\PromoMechanism\PromoMechanismHandler;
//Shipping type events
use Lovata\CouponsShopaholic\Classes\Event\ShippingType\ShippingTypeModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\ShippingType\ShippingTypeRelationHandler;
use Lovata\CouponsShopaholic\Classes\Event\ShippingType\ExtendShippingTypeControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\ShippingType\ExtendShippingTypeFieldsHandler;
//Tag events
use Lovata\CouponsShopaholic\Classes\Event\Tag\TagModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\Tag\ExtendTagControllerHandler;
use Lovata\CouponsShopaholic\Classes\Event\Tag\ExtendTagFieldsHandler;
//User events
use Lovata\CouponsShopaholic\Classes\Event\User\UserModelHandler;
use Lovata\CouponsShopaholic\Classes\Event\User\ExtendUserItemHandler;

/**
 * Class Plugin
 * @package Lovata\CouponsShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = ['Lovata.OrdersShopaholic', 'Lovata.Shopaholic', 'Lovata.Toolbox'];

    /**
     * Plugin boot method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    /**
     * Add event listeners
     */
    protected function addEventListener()
    {
        //Common events
        Event::subscribe(ExtendBackendMenuHandler::class);
        //Brand events
        Event::subscribe(BrandModelHandler::class);
        Event::subscribe(BrandRelationHandler::class);
        Event::subscribe(ExtendBrandControllerHandler::class);
        Event::subscribe(ExtendBrandFieldsHandler::class);
        //Cart events
        Event::subscribe(CartModelHandler::class);
        Event::subscribe(ExtendCartComponentHandler::class);
        //Category events
        Event::subscribe(CategoryModelHandler::class);
        Event::subscribe(CategoryRelationHandler::class);
        Event::subscribe(ExtendCategoryControllerHandler::class);
        Event::subscribe(ExtendCategoryFieldsHandler::class);
        //Coupon events
        Event::subscribe(CouponModelHandler::class);
        Event::subscribe(ExtendCouponColumnsHandler::class);
        Event::subscribe(ExtendCouponFieldsHandler::class);
        //Coupon group events
        Event::subscribe(CouponGroupModelHandler::class);
        Event::subscribe(CouponGroupRelationHandler::class);
        Event::subscribe(ExtendCouponGroupFieldsHandler::class);
        //Offer events
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(OfferRelationHandler::class);
        Event::subscribe(ExtendOfferControllerHandler::class);
        Event::subscribe(ExtendOfferFieldsHandler::class);
        //Order events
        Event::subscribe(OrderModelHandler::class);
        Event::subscribe(ExtendOrderItemHandler::class);
        //Product events
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(ProductRelationHandler::class);
        Event::subscribe(ExtendProductCollectionHandler::class);
        Event::subscribe(ExtendProductControllerHandler::class);
        Event::subscribe(ExtendProductFieldsHandler::class);
        //Promo block events
        Event::subscribe(PromoBlockModelHandler::class);
        Event::subscribe(ExtendPromoBlockControllerHandler::class);
        Event::subscribe(ExtendPromoBlockFieldsHandler::class);
        //Promo mechanism events
        Event::subscribe(PromoMechanismHandler::class);
        //Shipping type events
        Event::subscribe(ShippingTypeModelHandler::class);
        Event::subscribe(ShippingTypeRelationHandler::class);
        Event::subscribe(ExtendShippingTypeControllerHandler::class);
        Event::subscribe(ExtendShippingTypeFieldsHandler::class);
        //Tag events
        Event::subscribe(TagModelHandler::class);
        Event::subscribe(ExtendTagControllerHandler::class);
        Event::subscribe(ExtendTagFieldsHandler::class);
        //User events
        Event::subscribe(UserModelHandler::class);
        Event::subscribe(ExtendUserItemHandler::class);
    }
}
