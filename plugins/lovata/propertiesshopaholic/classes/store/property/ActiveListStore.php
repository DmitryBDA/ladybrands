<?php namespace Lovata\PropertiesShopaholic\Classes\Store\Property;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class ActiveListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store\Property
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 */
class ActiveListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Property::active()->lists('id');

        return $arElementIDList;
    }
}
