<?php namespace Lovata\PropertiesShopaholic\Classes\Store\Group;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\PropertiesShopaholic\Models\Group;

/**
 * Class SortingListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\Group
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
        $arElementIDList = (array) Group::orderBy('sort_order', 'asc')->lists('id');

        return $arElementIDList;
    }
}
