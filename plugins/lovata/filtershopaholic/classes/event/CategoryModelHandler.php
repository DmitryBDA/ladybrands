<?php namespace Lovata\FilterShopaholic\Classes\Event;

use System\Classes\PluginManager;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection;

/**
 * Class CategoryModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        CategoryItem::extend(function ($obItem) {
            $this->extendCategoryItem($obItem);
        });
    }

    /**
     * Extend category item
     * @param CategoryItem $obItem
     */
    protected function extendCategoryItem($obItem)
    {
        if (empty($obItem) || !$obItem instanceof CategoryItem) {
            return;
        }

        $this->addProductPropertyField($obItem);
        $this->addOfferPropertyField($obItem);
    }

    /**
     * Add product property field in category item
     * @param CategoryItem $obItem
     */
    protected function addProductPropertyField($obItem)
    {
        if (empty($obItem) || !$obItem instanceof CategoryItem) {
            return;
        }

        $obItem->addDynamicMethod('getProductFilterPropertyAttribute', function ($obItem) {
            /** @var CategoryItem $obItem */
            $obPropertyCollection = $obItem->getAttribute('product_filter_property');
            if (!empty($obPropertyCollection) && $obPropertyCollection instanceof FilterPropertyCollection) {
                return $obPropertyCollection;
            }

            $obPropertyCollection = FilterPropertyCollection::make()
                ->sort()
                ->active()
                ->setCategory($obItem)
                ->setModel(Product::class)
                ->setPropertySetRelation($obItem->product_property_list);

            $obItem->setAttribute('product_filter_property', $obPropertyCollection);

            return $obPropertyCollection;
        });
    }

    /**
     * Add offer property field in category item
     * @param CategoryItem $obItem
     */
    protected function addOfferPropertyField($obItem)
    {
        if (empty($obItem) || !$obItem instanceof CategoryItem) {
            return;
        }

        $obItem->addDynamicMethod('getOfferFilterPropertyAttribute', function ($obItem) {
            /** @var CategoryItem $obItem */
            $obPropertyCollection = $obItem->getAttribute('offer_filter_property');
            if (!empty($obPropertyCollection) && $obPropertyCollection instanceof FilterPropertyCollection) {
                return $obPropertyCollection;
            }

            $obPropertyCollection = FilterPropertyCollection::make()
                ->sort()
                ->active()
                ->setCategory($obItem)
                ->setModel(Offer::class)
                ->setPropertySetRelation($obItem->offer_property_list);

            $obItem->setAttribute('offer_filter_property', $obPropertyCollection);

            return $obPropertyCollection;
        });
    }
}
