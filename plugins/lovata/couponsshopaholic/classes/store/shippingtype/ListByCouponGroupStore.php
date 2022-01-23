<?php namespace Lovata\CouponsShopaholic\Classes\Store\ShippingType;

use DB;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;

/**
 * Class ListByCouponGroupStore
 * @package Lovata\CouponsShopaholic\Classes\Store\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByCouponGroupStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) DB::table('lovata_coupons_shopaholic_group_shipping_type')->where('group_id', $this->sValue)->lists('shipping_type_id');
        $arElementIDList = ShippingTypeCollection::make()->active()->intersect($arElementIDList)->getIDList();

        return $arElementIDList;
    }
}
