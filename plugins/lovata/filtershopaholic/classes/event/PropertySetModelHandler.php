<?php namespace Lovata\FilterShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;

use Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertySetCollection;

/**
 * Class PropertySetModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertySetModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        PropertySetCollection::extend(function ($obPropertySetList) {
            /** @var PropertySetCollection $obPropertySetList */
            $this->addGetProductPropertyCollectionMethod($obPropertySetList);
            $this->addGetOfferPropertyCollectionMethod($obPropertySetList);
        });
    }

    /**
     * @param PropertySetCollection $obPropertySetList
     */
    protected function addGetProductPropertyCollectionMethod($obPropertySetList)
    {
        $obPropertySetList->addDynamicMethod('getProductPropertyCollection', function($obProductList = null) use ($obPropertySetList) {

            $obPropertyCollection = FilterPropertyCollection::make()
                ->sort()
                ->active()
                ->setModel(Product::class)
                ->setProductList($obProductList)
                ->setPropertySetRelation($obPropertySetList->getProductPropertyList());

            return $obPropertyCollection;
        });
    }

    /**
     * @param PropertySetCollection $obPropertySetList
     */
    protected function addGetOfferPropertyCollectionMethod($obPropertySetList)
    {
        $obPropertySetList->addDynamicMethod('getOfferPropertyCollection', function($obProductList = null, $obOfferList = null) use ($obPropertySetList) {

            $obPropertyCollection = FilterPropertyCollection::make()
                ->sort()
                ->active()
                ->setModel(Offer::class)
                ->setProductList($obProductList)
                ->setOfferList($obOfferList)
                ->setPropertySetRelation($obPropertySetList->getOfferPropertyList());

            return $obPropertyCollection;
        });
    }
}
