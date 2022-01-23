<?php namespace Lovata\LabelsShopaholic\Classes\Store\Label;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\LabelsShopaholic\Models\Label;

/**
 * Class ActiveListStore
 * @package Lovata\LabelsShopaholic\Classes\Store\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
        $arLabelsIDList = (array) Label::active()->lists('id');

        return $arLabelsIDList;
    }
}
