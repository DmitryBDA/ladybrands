<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Product;

use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class ExtendProductCollectionHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendProductCollectionHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        ProductCollection::extend(function ($obList) {
            $this->extendProductCollection($obList);
        });
    }

    /**
     * Extend product collection object
     * @param ProductCollection $obList
     */
    protected function extendProductCollection($obList)
    {
        $obList->addDynamicMethod('discount', function ($iDiscountID) use ($obList) {

            $arElementIDList = ProductListStore::instance()->discount->get($iDiscountID);

            return $obList->intersect($arElementIDList);
        });
    }
}
