<?php namespace Lovata\PropertiesShopaholic\Classes\Store\PropertySet;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\PropertiesShopaholic\Models\PropertySet;

/**
 * Class GlobalListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\PropertySet
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class GlobalListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) PropertySet::isGlobal()->lists('id');

        return $arElementIDList;
    }
}
