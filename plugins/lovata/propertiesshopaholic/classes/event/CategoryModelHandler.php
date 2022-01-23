<?php namespace Lovata\PropertiesShopaholic\Classes\Event;

use October\Rain\Database\Collection;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertySetCollection;

/**
 * Class ExtendCategoryModel
 * @package Lovata\PropertiesShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Category::extend(function ($obElement) {

            /** @var Category $obElement */
            $this->addModelRelationConfig($obElement);

            /** @var Category $obElement */
            $obElement->bindEvent('model.beforeDelete', function () use ($obElement) {
                $this->beforeDelete($obElement);
            });
        });

        CategoryItem::extend(function ($obItem) {
            $this->extendCategoryItem($obItem);
        });
    }

    /**
     * Add relation config in category model
     * @param Category $obElement
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Models\CategoryTest
     */
    protected function addModelRelationConfig($obElement)
    {
        $obElement->purgeable[] = 'product_property_array';
        $obElement->purgeable[] = 'offer_property_array';
        $obElement->addFillable('inherit_property_set');
        $obElement->addCachedField('inherit_property_set');

        $obElement->belongsToMany['property_set'] = [
            PropertySet::class,
            'table'    => 'lovata_properties_shopaholic_set_category_link',
            'key'      => 'category_id',
            'otherKey' => 'set_id',
            'order'    => 'sort_order asc',
        ];

        $obElement->addDynamicMethod('getProductPropertyAttribute', function () use ($obElement) {
            $obPropertyList = $this->getPropertyList($obElement, Product::class);

            return $obPropertyList;
        });

        $obElement->addDynamicMethod('getOfferPropertyAttribute', function () use ($obElement) {
            $obPropertyList = $this->getPropertyList($obElement, Offer::class);

            return $obPropertyList;
        });
    }

    /**
     * Get property list
     * @param \Lovata\Shopaholic\Models\Category $obCategory
     * @param string                             $sModelClass
     * @return  \October\Rain\Database\Collection|\Lovata\PropertiesShopaholic\Models\Property[]
     */
    protected function getPropertyList($obCategory, $sModelClass) : Collection
    {
        $obResultList = $obCategory->product_property_array;
        if (!empty($obResultList) && $obResultList instanceof Collection) {
            return $obResultList;
        }

        $obResultList = Collection::make();

        //Get property set list for category
        $arPropertySetList = $this->getPropertySetForCategory($obCategory);

        //Get global property list
        $obGlobalPropertySetList = PropertySet::isGlobal()->get();
        foreach ($obGlobalPropertySetList as $obPropertySet) {
            if (isset($arPropertySetList[$obPropertySet->id])) {
                continue;
            }

            $arPropertySetList[$obPropertySet->id] = $obPropertySet;
        }

        if (empty($arPropertySetList)) {
            $obCategory->product_property_array = $obResultList;

            return $obResultList;
        }

        //Process property list
        foreach ($arPropertySetList as $obPropertySet) {
            //Get property list
            if ($sModelClass == Product::class) {
                $obPropertyList = $obPropertySet->product_property;
            } else {
                $obPropertyList = $obPropertySet->offer_property;
            }

            if ($obPropertyList->isEmpty()) {
                continue;
            }

            //Process property list and add property object to result array
            foreach ($obPropertyList as $obProperty) {
                if (!empty($obResultList->find($obProperty->id))) {
                    continue;
                }

                $obResultList->add($obProperty);
            }
        }

        $obResultList->sortBy('sort_order');

        $obCategory->product_property_array = $obResultList;

        return $obResultList;
    }

    /**
     * Get property set from:
     * 1. Category object
     * 2. From parent category, if enabled inherit_property_set flag in category object or enabled property_inheriting_property_set setting
     * @param Category                $obCategory
     * @param PropertySet[] $arResult
     * @return PropertySet[]
     */
    protected function getPropertySetForCategory($obCategory, $arResult = [])
    {
        foreach ($obCategory->property_set as $obPropertySet) {
            if (isset($arResult[$obPropertySet->id])) {
                continue;
            }

            $arResult[$obPropertySet->id] = $obPropertySet;
        }

        if (!$obCategory->inherit_property_set && !Settings::getValue('property_inheriting_property_set')) {
            return $arResult;
        }

        $obParentCategory = $obCategory->parent;
        if (!empty($obParentCategory)) {
            $arResult = $this->getPropertySetForCategory($obParentCategory, $arResult);
        }

        return $arResult;
    }

    /**
     * Before delete event listener
     * @param Category $obElement
     */
    protected function beforeDelete($obElement)
    {
        $obElement->property_set()->detach();
    }

    /**
     * Add product property field in category item
     * @param CategoryItem $obItem
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Item\CategoryItemTest::testProductPropertyField
     */
    protected function extendCategoryItem($obItem)
    {
        $obItem->arExtendResult[] = 'addPropertySetIDList';
        $obItem->addDynamicMethod('addPropertySetIDList', function () use ($obItem) {

            /** @var Category $obCategory */
            $obCategory = $obItem->getObject();

            //Get property set ID list
            $arPropertySetIdList = $obCategory->property_set->lists('id');

            $obItem->setAttribute('property_set_id', $arPropertySetIdList);
        });

        $obItem->addDynamicMethod('getPropertySetAttribute', function ($obItem) {
            /** @var CategoryItem $obItem */
            $obPropertySetList = PropertySetCollection::make();
            $obPropertySetList = $this->getPropertySetForCategoryItem($obItem, $obPropertySetList);
            $obGlobalPropertySetList = PropertySetCollection::make()->isGlobal();
            $obPropertySetList->merge($obGlobalPropertySetList->getIDList());

            return $obPropertySetList;
        });

        $obItem->addDynamicMethod('getProductPropertyListAttribute', function ($obItem) {
            /** @var CategoryItem $obItem */
            $arPropertyList = $obItem->property_set->getProductPropertyList();

            return $arPropertyList;
        });

        $obItem->addDynamicMethod('getOfferPropertyListAttribute', function ($obItem) {
            /** @var CategoryItem $obItem */
            $arPropertyList = $obItem->property_set->getOfferPropertyList();

            return $arPropertyList;
        });
    }

    /**
     * @param CategoryItem          $obCategoryItem
     * @param PropertySetCollection $obPropertySetList
     * @return PropertySetCollection
     */
    protected function getPropertySetForCategoryItem($obCategoryItem, $obPropertySetList)
    {
        $obPropertySetList->merge($obCategoryItem->property_set_id);
        if (!$obCategoryItem->inherit_property_set && !Settings::getValue('property_inheriting_property_set')) {
            return $obPropertySetList;
        }

        $obParentCategoryItem = $obCategoryItem->parent;
        if ($obParentCategoryItem->isNotEmpty()) {
            $obPropertySetList = $this->getPropertySetForCategoryItem($obParentCategoryItem, $obPropertySetList);
        }

        return $obPropertySetList;
    }
}
