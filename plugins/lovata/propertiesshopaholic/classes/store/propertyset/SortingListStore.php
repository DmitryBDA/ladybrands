<?php namespace Lovata\PropertiesShopaholic\Classes\Store\PropertySet;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\PropertiesShopaholic\Models\PropertySet;

/**
 * Class SortingListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\PropertySet
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
        $arElementIDList = (array) PropertySet::orderBy('sort_order', 'asc')->lists('id');

        return $arElementIDList;
    }
}
