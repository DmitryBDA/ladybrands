<?php namespace Lovata\CouponsShopaholic\Classes\Store\Coupon;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\CouponsShopaholic\Models\Coupon;

/**
 * Class NotHiddenListStore
 * @package Lovata\CouponsShopaholic\Classes\Store\Coupon
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * Saving to the cache array with IDs of not hidden elements
 *
 * Cache data:
 * ['element_id_1', 'element_id_2', ...]
 *
 * Clear cache in:
 * @see \Lovata\CouponsShopaholic\Classes\Event\Coupon\CouponModelHandler::afterSave()
 * @see \Lovata\CouponsShopaholic\Classes\Event\Coupon\CouponModelHandler::afterDelete()
 */
class NotHiddenListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Coupon::notHidden()->lists('id');

        return $arElementIDList;
    }
}
