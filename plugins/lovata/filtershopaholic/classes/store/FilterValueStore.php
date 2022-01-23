<?php namespace Lovata\FilterShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\FilterShopaholic\Classes\Store\FilterValue\FilterByDiscountStore;
use Lovata\FilterShopaholic\Classes\Store\FilterValue\FilterByPropertyStore;
use Lovata\FilterShopaholic\Classes\Store\FilterValue\FilterByQuantityStore;
use Lovata\FilterShopaholic\Classes\Store\FilterValue\FilterOfferByDiscountStore;
use Lovata\FilterShopaholic\Classes\Store\FilterValue\FilterOfferByQuantityStore;
use Lovata\FilterShopaholic\Classes\Store\FilterValue\FilterByQuantityNullStore;

/**
 * Class FilterValueStore
 * @package Lovata\FilterShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property FilterByPropertyStore      $property
 * @property FilterByQuantityStore      $quantity
 * @property FilterByQuantityNullStore  $quantity_null
 * @property FilterByDiscountStore      $discount
 * @property FilterOfferByDiscountStore $offer_discount
 * @property FilterOfferByQuantityStore $offer_quantity
 */
class FilterValueStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('property', FilterByPropertyStore::class);
        $this->addToStoreList('quantity', FilterByQuantityStore::class);
        $this->addToStoreList('quantity_null', FilterByQuantityNullStore::class);
        $this->addToStoreList('discount', FilterByDiscountStore::class);
        $this->addToStoreList('offer_discount', FilterOfferByDiscountStore::class);
        $this->addToStoreList('offer_quantity', FilterOfferByQuantityStore::class);
    }
}
