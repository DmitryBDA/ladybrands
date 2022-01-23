<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Product;

use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\ProductItem;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Helper\CommonPropertyHelper;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLinkListStore;

/**
 * Class ExtendProductModel
 * @package Lovata\PropertiesShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler extends ModelHandler
{
    /** @var Product */
    protected $obElement;
    protected $bWithRestore;
    /** @var array */
    protected $arPropertyValueList = [];

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        /** @var Product $obElement */
        Product::extend(function ($obElement) {
            /** @var Product $obElement */
            $this->addPropertyMethods($obElement);
        });

        ProductItem::extend(function ($obItem) {
            $this->extendProductItem($obItem);
        });

        ProductItem::$arQueryWith[] = 'property_value';
        ProductItem::$arQueryWith[] = 'offer.property_value';

        $obEvent->listen('shopaholic.product.category.clear', function ($iCategoryID) {
            if (empty($iCategoryID)) {
                return;
            }

            PropertyValueLinkListStore::instance()->category->clearByCategoryID($iCategoryID);
        });
    }

    /**
     * Add dynamic method for property attribute
     * @param Product $obElement
     */
    protected function addPropertyMethods($obElement)
    {
        $obElement->appends[] = 'property';
        $obElement->fillable[] = 'property';
        $obElement->purgeable[] = 'property';
        $obElement->purgeable[] = 'property_array';

        $obElement->morphMany['property_value'] = [
            PropertyValueLink::class,
            'name' => 'element',
        ];

        $obElement->addDynamicMethod('getPropertyAttribute', function () use ($obElement) {

            if ($obElement->property_array !== null) {
                return $obElement->property_array;
            }

            /** @var CommonPropertyHelper $obCommonPropertyHelper */
            $obCommonPropertyHelper = app()->make(CommonPropertyHelper::class, [$obElement]);
            $obElement->property_array = $obCommonPropertyHelper->getPropertyAttribute();

            //Check Translate plugin
            if (!PluginManager::instance()->hasPlugin('RainLab.Translate') || empty($obElement->property_array)) {
                return $obElement->property_array;
            }

            //Process property list and add methods for Translate plugin
            foreach ($obElement->property_array as $iPropertyID => $sValue) {

                $obElement->addDynamicMethod('getProperty'.$iPropertyID.'AttributeTranslated', function ($sLangCode) use ($iPropertyID, $sValue, $obElement) {

                    if (\RainLab\Translate\Classes\Translator::instance()->getDefaultLocale() == $sLangCode || !PropertyValue::hasValue($sValue)) {
                        return $sValue;
                    }

                    $sSlug = PropertyValue::getSlugValue($sValue);
                    $obPropertyValue = PropertyValue::getBySlug($sSlug)->first();
                    if (empty($obPropertyValue)) {
                        return $sValue;
                    }

                    return $obPropertyValue->getAttributeTranslated('value', $sLangCode);
                });
            }

            return $obElement->property_array;
        });

        $obElement->addDynamicMethod('setPropertyAttribute', function ($arValueList) use ($obElement) {
            $this->arPropertyValueList = $arValueList;
            if (empty($obElement->id)) {
                return;
            }

            /** @var CommonPropertyHelper $obCommonPropertyHelper */
            $obCommonPropertyHelper = app()->make(CommonPropertyHelper::class, [$obElement]);
            $obCommonPropertyHelper->setPropertyAttribute($arValueList);
        });
    }

    /**
     * Extend category item
     * @param ProductItem $obItem
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Item\ProductItemTest::testPropertyField
     */
    protected function extendProductItem($obItem)
    {
        if (empty($obItem) || !$obItem instanceof ProductItem) {
            return;
        }

        $obItem->arExtendResult[] = 'addPropertyValueList';

        $obItem->addDynamicMethod('addPropertyValueList', function () use ($obItem) {
            /** @var Offer $obElement */
            $obElement = $obItem->getObject();
            if (empty($obElement)) {
                return;
            }

            $arPropertyValueList = $this->getPropertyValueList($obElement->property_value);
            $obItem->setAttribute('property_value_array', $arPropertyValueList);
        });

        $obItem->addDynamicMethod('getPropertyAttribute', function ($obItem) {
            /** @var ProductItem $obItem */
            $obPropertyCollection = $obItem->getAttribute('property');
            if (!empty($obPropertyCollection) && $obPropertyCollection instanceof PropertyCollection) {
                return $obPropertyCollection;
            }

            $obCategoryItem = $obItem->category;
            if ($obCategoryItem->isEmpty()) {
                return PropertyCollection::make();
            }

            $obPropertyCollection = PropertyCollection::make()
                ->sort()
                ->active()
                ->setItem($obItem)
                ->setModel(Product::class)
                ->setPropertySetRelation($obCategoryItem->product_property_list);

            $obItem->setAttribute('property', $obPropertyCollection);
            return $obPropertyCollection;
        });
    }

    /**
     * Get property value list
     * @param \October\Rain\Database\Collection|\Lovata\PropertiesShopaholic\Models\PropertyValueLink[] $obPropertyValueLinkList
     * @return array
     */
    protected function getPropertyValueList($obPropertyValueLinkList)
    {
        if ($obPropertyValueLinkList->isEmpty()) {
            return [];
        }

        $arResult = [];
        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {
            if (!isset($arResult[$obPropertyValueLink->property_id])) {
                $arResult[$obPropertyValueLink->property_id] = [];
            }

            $arResult[$obPropertyValueLink->property_id][] = $obPropertyValueLink->value_id;
        }

        return $arResult;
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        $this->obElement->property = $this->arPropertyValueList;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('active')) {
            return;
        }

        PropertyValueLinkListStore::instance()->category->clearByProduct($this->obElement);

        $this->clearPropertyValueLinkByPropertyID();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        if (!$this->obElement->active) {
            return;
        }

        PropertyValueLinkListStore::instance()->category->clearByProduct($this->obElement);

        $this->clearPropertyValueLinkByPropertyID();
    }

    /**
     * After restore event handler
     */
    protected function afterRestore()
    {
        if (!$this->obElement->active) {
            return;
        }

        PropertyValueLinkListStore::instance()->category->clearByProduct($this->obElement);

        $this->clearPropertyValueLinkByPropertyID();
    }

    /**
     * Clear property value links, cached by property ID
     */
    protected function clearPropertyValueLinkByPropertyID()
    {
        //Get property value link list
        $obPropertyValueLinkList = PropertyValueLink::getByElementType(Product::class)->getByElementID($this->obElement->id)->get();
        if ($obPropertyValueLinkList->isEmpty()) {
            return;
        }

        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {
            PropertyValueLinkListStore::instance()->property->clear($obPropertyValueLink->property_id);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Product::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ProductItem::class;
    }
}
