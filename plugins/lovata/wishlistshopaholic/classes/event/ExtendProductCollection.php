<?php namespace Lovata\WishListShopaholic\Classes\Event;

use Lovata\WishListShopaholic\Classes\Helper\WishListHelper;

use Lovata\Shopaholic\Classes\Collection\ProductCollection;

/**
 * Class ExtendProductCollection
 * @package Lovata\WishListShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendProductCollection
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        ProductCollection::extend(function($obProductList) {
            /** @var ProductCollection $obProductList */
            $this->addWishListMethod($obProductList);
        });
    }

    /**
     * Add "wishList" method to collection class
     * @param ProductCollection $obProductList
     */
    protected function addWishListMethod($obProductList)
    {
        $obProductList->addDynamicMethod('wishList', function () use ($obProductList) {
            /** @var WishListHelper $obWishListHelper */
            $obWishListHelper = app(WishListHelper::class);

            $arProductIDList = $obWishListHelper->getList();

            return $obProductList->intersect($arProductIDList);
        });
    }
}
