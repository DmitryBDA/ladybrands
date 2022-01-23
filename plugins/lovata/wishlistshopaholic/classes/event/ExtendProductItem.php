<?php namespace Lovata\WishListShopaholic\Classes\Event;

use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

/**
 * Class ExtendProductItem
 * @package Lovata\WishListShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendProductItem
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        ProductItem::extend(function($obProductItem) {
            /** @var ProductItem $obProductItem */
            $this->addInWishListMethod($obProductItem);
        });
    }

    /**
     * Add "inWishList" method to Item class
     * @param ProductItem $obProductItem
     */
    protected function addInWishListMethod($obProductItem)
    {
        $obProductItem->addDynamicMethod('inWishList', function () use ($obProductItem) {
            //Get products in wish list
            $obProductList = ProductCollection::make()->wishList();

            return $obProductList->has($obProductItem->id);
        });
    }
}
