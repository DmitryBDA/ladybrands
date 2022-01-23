<?php namespace Lovata\WishListShopaholic\Classes\Event;

use Input;
use Lovata\WishListShopaholic\Classes\Helper\WishListHelper;

use Lovata\Shopaholic\Components\ProductData;
use Lovata\Shopaholic\Components\ProductPage;
use Lovata\Shopaholic\Components\ProductList;

/**
 * Class ExtendProductComponent
 * @package Lovata\WishListShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendProductComponent
{
    /** @var ProductList|ProductPage|ProductData */
    protected $obComponent;

    /**
     * Add listeners
     */
    public function subscribe()
    {
        ProductList::extend(function ($obComponent) {
            /** @var ProductList $obComponent */
            $this->obComponent = $obComponent;
            $this->addWishListMethods();
        });

        ProductData::extend(function ($obComponent) {
            /** @var ProductData $obComponent */
            $this->obComponent = $obComponent;
            $this->addWishListMethods();
        });

        ProductPage::extend(function ($obComponent) {
            /** @var ProductPage $obComponent */
            $this->obComponent = $obComponent;
            $this->addWishListMethods();
        });
    }

    /**
     * Add wish list methods to product component
     */
    protected function addWishListMethods()
    {
        //Add 'add' method
        $this->obComponent->addDynamicMethod('onAddToWishList', function () {

            $iProductID = Input::get('product_id');

            /** @var WishListHelper $obWishListHelper */
            $obWishListHelper = app(WishListHelper::class);

            $obWishListHelper->add($iProductID);
            $arProductIDList = $obWishListHelper->getList();

            return $arProductIDList;
        });

        //Add 'remove' method
        $this->obComponent->addDynamicMethod('onRemoveFromWishList', function () {

            $iProductID = Input::get('product_id');

            /** @var WishListHelper $obWishListHelper */
            $obWishListHelper = app(WishListHelper::class);

            $obWishListHelper->remove($iProductID);
            $arProductIDList = $obWishListHelper->getList();

            return $arProductIDList;
        });

        //Add 'clear' method
        $this->obComponent->addDynamicMethod('onClearWishList', function () {

            /** @var WishListHelper $obWishListHelper */
            $obWishListHelper = app(WishListHelper::class);

            $obWishListHelper->clear();
        });
    }
}
