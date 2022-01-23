<?php namespace Lovata\CouponsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\CouponsShopaholic\Classes\Store\Coupon\ListByGroupStore;
use Lovata\CouponsShopaholic\Classes\Store\Coupon\HiddenListStore;
use Lovata\CouponsShopaholic\Classes\Store\Coupon\NotHiddenListStore;

/**
 * Class CouponListStore
 * @package Lovata\CouponsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByGroupStore   $group
 * @property HiddenListStore    $hidden
 * @property NotHiddenListStore $not_hidden
 */
class CouponListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('group', ListByGroupStore::class);
        $this->addToStoreList('hidden', HiddenListStore::class);
        $this->addToStoreList('not_hidden', NotHiddenListStore::class);
    }
}
