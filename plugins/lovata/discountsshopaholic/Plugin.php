<?php namespace Lovata\DiscountsShopaholic;

use Event;
use System\Classes\PluginBase;
//Console command
use Lovata\DiscountsShopaholic\Classes\Console\UpdateCatalogPrice;
//Events
use Lovata\DiscountsShopaholic\Classes\Event\ExtendBackendMenuHandler;
use Lovata\DiscountsShopaholic\Classes\Event\ExtendFieldHandler;
//Brand events
use Lovata\DiscountsShopaholic\Classes\Event\Brand\BrandModelHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Brand\BrandRelationHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Brand\ExtendBrandControllerHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Brand\ExtendBrandFieldsHandler;
//Category events
use Lovata\DiscountsShopaholic\Classes\Event\Category\CategoryModelHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Category\CategoryRelationHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Category\ExtendCategoryControllerHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Category\ExtendCategoryFieldsHandler;
//Discount events
use Lovata\DiscountsShopaholic\Classes\Event\Discount\ExtendDiscountFieldsHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Discount\DiscountModelHandler;
//Offer events
use Lovata\DiscountsShopaholic\Classes\Event\Offer\OfferModelHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Offer\OfferRelationHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Offer\ExtendOfferControllerHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Offer\ExtendOfferFieldsHandler;
//Product events
use Lovata\DiscountsShopaholic\Classes\Event\Product\ProductModelHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Product\ProductRelationHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Product\ExtendProductCollectionHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Product\ExtendProductControllerHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Product\ExtendProductFieldsHandler;
//Promo block events
use Lovata\DiscountsShopaholic\Classes\Event\PromoBlock\PromoBlockModelHandler;
use Lovata\DiscountsShopaholic\Classes\Event\PromoBlock\ExtendPromoBlockControllerHandler;
use Lovata\DiscountsShopaholic\Classes\Event\PromoBlock\ExtendPromoBlockFieldsHandler;
//Tag events
use Lovata\DiscountsShopaholic\Classes\Event\Tag\TagModelHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Tag\TagRelationHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Tag\ExtendTagControllerHandler;
use Lovata\DiscountsShopaholic\Classes\Event\Tag\ExtendTagFieldsHandler;

/**
 * Class Plugin
 * @package Lovata\DiscountsShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = ['Lovata.Shopaholic', 'Lovata.Toolbox'];

    /**
     * Register artisan command
     */
    public function register()
    {
        $this->registerConsoleCommand('shopaholic:discount.update_catalog_price', UpdateCatalogPrice::class);
    }
    
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
        Event::subscribe(ExtendBackendMenuHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
        //Brand events
        Event::subscribe(BrandModelHandler::class);
        Event::subscribe(BrandRelationHandler::class);
        Event::subscribe(ExtendBrandControllerHandler::class);
        Event::subscribe(ExtendBrandFieldsHandler::class);
        //Category events
        Event::subscribe(CategoryModelHandler::class);
        Event::subscribe(CategoryRelationHandler::class);
        Event::subscribe(ExtendCategoryControllerHandler::class);
        Event::subscribe(ExtendCategoryFieldsHandler::class);
        //Discount events
        Event::subscribe(ExtendDiscountFieldsHandler::class);
        Event::subscribe(DiscountModelHandler::class);
        //Offer events
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(OfferRelationHandler::class);
        Event::subscribe(ExtendOfferControllerHandler::class);
        Event::subscribe(ExtendOfferFieldsHandler::class);
        //Product events
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(ProductRelationHandler::class);
        Event::subscribe(ExtendProductCollectionHandler::class);
        Event::subscribe(ExtendProductControllerHandler::class);
        Event::subscribe(ExtendProductFieldsHandler::class);
        //Promo blocks events
        Event::subscribe(PromoBlockModelHandler::class);
        Event::subscribe(ExtendPromoBlockControllerHandler::class);
        Event::subscribe(ExtendPromoBlockFieldsHandler::class);
        //Tag events
        Event::subscribe(TagModelHandler::class);
        Event::subscribe(TagRelationHandler::class);
        Event::subscribe(ExtendTagControllerHandler::class);
        Event::subscribe(ExtendTagFieldsHandler::class);
    }
}
