<?php namespace Lovata\DiscountsShopaholic\Classes\Store\Discount;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class ListByPromoBlockStore
 * @package Lovata\DiscountsShopaholic\Classes\Store\Discount
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
        $arElementIDList = (array) Discount::active()->getByPromoBlock($this->sValue)->lists('id');

        return $arElementIDList;
    }
}
