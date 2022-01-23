<?php namespace Lovata\CouponsShopaholic\Classes\Store\CouponGroup;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\CouponsShopaholic\Models\CouponGroup;

/**
 * Class ListByPromoBlockStore
 * @package Lovata\CouponsShopaholic\Classes\Store\CouponGroup
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByPromoBlockStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) CouponGroup::where('active', true)->getByPromoBlock($this->sValue)->lists('id');

        return $arElementIDList;
    }
}
