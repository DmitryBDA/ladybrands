<?php namespace Lovata\DiscountsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\DiscountsShopaholic\Classes\Store\Discount\ListByPromoBlockStore;

/**
 * Class DiscountListStore
 * @package Lovata\DiscountsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByPromoBlockStore $promo_block
 */
class DiscountListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('promo_block', ListByPromoBlockStore::class);
    }
}
