<?php namespace Lovata\CouponsShopaholic\Classes\Store\Coupon;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\CouponsShopaholic\Models\Coupon;

/**
 * Class ListByGroupStore
 * @package Lovata\CouponsShopaholic\Classes\Store\Coupon
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByGroupStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Coupon::getByGroup($this->sValue)->lists('id');

        return $arElementIDList;
    }
}
