<?php namespace Lovata\LabelsShopaholic\Classes\Store\Label;

use DB;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

/**
 * Class ListByProductStore
 * @package Lovata\LabelsShopaholic\Classes\Store\Label
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByProductStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) DB::table('lovata_labels_shopaholic_product_label')->where('product_id', $this->sValue)->lists('label_id');

        return $arElementIDList;
    }
}