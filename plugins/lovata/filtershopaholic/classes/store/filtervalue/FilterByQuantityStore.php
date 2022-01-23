<?php namespace Lovata\FilterShopaholic\Classes\Store\FilterValue;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\Shopaholic\Models\Offer;

/**
 * Class FilterByQuantityStore
 * @package Lovata\Toolbox\Classes\Store\FilterValue
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class FilterByQuantityStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arProductIDList = (array) Offer::active()
            ->getByQuantity(0, '>')
            ->groupBy('product_id')
            ->lists('product_id');

        return $arProductIDList;
    }
}
