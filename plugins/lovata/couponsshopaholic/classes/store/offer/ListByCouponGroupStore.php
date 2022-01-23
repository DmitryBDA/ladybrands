<?php namespace Lovata\CouponsShopaholic\Classes\Store\Offer;

use DB;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\Shopaholic\Classes\Collection\OfferCollection;

/**
 * Class ListByCouponGroupStore
 * @package Lovata\CouponsShopaholic\Classes\Store\Offer
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
        $arElementIDList = (array) DB::table('lovata_coupons_shopaholic_group_offer')->where('group_id', $this->sValue)->lists('offer_id');
        $arElementIDList = OfferCollection::make()->active()->intersect($arElementIDList)->getIDList();

        return $arElementIDList;
    }
}
