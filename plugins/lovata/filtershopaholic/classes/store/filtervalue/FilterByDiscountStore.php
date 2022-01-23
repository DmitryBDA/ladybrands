<?php namespace Lovata\FilterShopaholic\Classes\Store\FilterValue;

use DB;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

/**
 * Class FilterByDiscountStore
 * @package Lovata\Toolbox\Classes\Store\FilterValue
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class FilterByDiscountStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arProductIDList = (array) DB::table('lovata_shopaholic_prices')
            ->select('lovata_shopaholic_offers.product_id')
            ->whereNull('lovata_shopaholic_prices.price_type_id')
            ->where('lovata_shopaholic_prices.old_price', '>', 0)
            ->where('lovata_shopaholic_offers.active', true)
            ->whereNull('lovata_shopaholic_offers.deleted_at')
            ->where('lovata_shopaholic_prices.item_type', Offer::class)
            ->join('lovata_shopaholic_offers', 'lovata_shopaholic_offers.id', '=', 'lovata_shopaholic_prices.item_id')
            ->lists('product_id');

        return $arProductIDList;
    }
}
