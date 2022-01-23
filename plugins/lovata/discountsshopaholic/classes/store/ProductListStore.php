<?php namespace Lovata\DiscountsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\DiscountsShopaholic\Classes\Store\Product\ListByDiscountStore;

/**
 * Class ProductListStore
 * @package Lovata\DiscountsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByDiscountStore    $discount
 */
class ProductListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('discount', ListByDiscountStore::class);
    }
}
