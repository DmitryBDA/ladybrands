<?php namespace Lovata\PropertiesShopaholic\Classes\Store\Property;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class SortingListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\Property
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 */
class SortingListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Property::orderBy('sort_order', 'asc')->lists('id');

        return $arElementIDList;
    }
}
