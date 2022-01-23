<?php namespace Lovata\LabelsShopaholic\Classes\Store\Product;

use DB;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

/**
 * Class ListByLabelStore
 * @package Lovata\LabelsShopaholic\Classes\Store\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListByLabelStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) DB::table('lovata_labels_shopaholic_product_label')->where('label_id', $this->sValue)->lists('product_id');

        return $arElementIDList;
    }
}