<?php namespace Lovata\LabelsShopaholic\Classes\Store\Label;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\LabelsShopaholic\Models\Label;

/**
 * Class SortingListStore
 * @package Lovata\LabelsShopaholic\Classes\Store\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
        $arElementIDList = (array) Label::orderBy('sort_order')->lists('id');

        return $arElementIDList;
    }
}
