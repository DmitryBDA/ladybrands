<?php namespace Lovata\CouponsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\CouponsShopaholic\Classes\Store\ShippingType\ListByCouponGroupStore;

/**
 * Class ShippingTypeListStore
 * @package Lovata\CouponsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByCouponGroupStore $coupon_group
 */
class ShippingTypeListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('coupon_group', ListByCouponGroupStore::class);
    }
}
