<?php namespace Lovata\CouponsShopaholic\Classes\Event\Product;

use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Classes\Store\ProductListStore as MainProductListStore;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class ExtendProductCollectionHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendProductCollectionHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        ProductCollection::extend(function ($obList) {
            $this->extendProductCollection($obList);
        });
    }

    /**
     * Extend product collection object
     * @param ProductCollection $obList
     */
    protected function extendProductCollection($obList)
    {
        $obList->addDynamicMethod('couponGroup', function ($iCouponID) use ($obList) {

            $arElementIDList = ProductListStore::instance()->coupon_group->get($iCouponID);
            if (empty($arElementIDList)) {
                $arElementIDList = MainProductListStore::instance()->sorting->get(MainProductListStore::SORT_NO);
            }

            return $obList->intersect($arElementIDList);
        });
    }
}
