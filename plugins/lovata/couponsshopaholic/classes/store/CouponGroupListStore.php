<?php namespace Lovata\CouponsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\CouponsShopaholic\Classes\Store\CouponGroup\ListByPromoBlockStore;

/**
 * Class CouponGroupListStore
 * @package Lovata\CouponsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByPromoBlockStore $promo_block
 */
class CouponGroupListStore extends AbstractListStore
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
