<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Offer;

use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;

use Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection;
use Lovata\PropertiesShopaholic\Classes\Helper\CommonPropertyHelper;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLinkListStore;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class ExtendOfferModel
 * @package Lovata\PropertiesShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferModelHandler extends ModelHandler
{
    /** @var  Offer */
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

        /** @var Offer $obElement */
        Offer::extend(function ($obElement) {
            /** @var Offer $obElement */
            $this->addPropertyMethods($obElement);
        });

        OfferItem::extend(function ($obItem) {
            $this->extendOfferItem($obItem);
        });

        OfferItem::$arQueryWith[] = 'property_value';
    }

    /**
     * Add dynamic method for property attribute
     * @param Offer $obElement
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

                    if (\RainLab\Translate\Classes\Translator::instance()->getDefaultLocale() == $sLangCode) {
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
     * @param OfferItem $obItem
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Item\OfferItemTest::testPropertyField
     */
    protected function extendOfferItem($obItem)
    {
        if (empty($obItem) || !$obItem instanceof OfferItem) {
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
            /** @var OfferItem $obItem */
            $obPropertyCollection = $obItem->getAttribute('property');
            if (!empty($obPropertyCollection) && $obPropertyCollection instanceof PropertyCollection) {
                return $obPropertyCollection;
            }

            //Get product item
            $obProductItem = $obItem->product;
            if ($obProductItem->isEmpty()) {
                return PropertyCollection::make();
            }

            $obCategoryItem = $obProductItem->category;
            if ($obCategoryItem->isEmpty()) {
                return PropertyCollection::make();
            }

            $obPropertyCollection = PropertyCollection::make()
                ->sort()
                ->active()
                ->setItem($obItem)
                ->setModel(Offer::class)
                ->setPropertySetRelation($obCategoryItem->offer_property_list);

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
        $bClearCache = $this->isFieldChanged('product_id') || $this->isFieldChanged('active');

        if ($bClearCache) {
            $this->clearPropertyValueLinkByPropertyID();
            $this->clearPropertyValueLinkByCategoryID();
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        if ($this->obElement->active) {
            $this->clearPropertyValueLinkByPropertyID();
            $this->clearPropertyValueLinkByCategoryID();
        }
    }

    /**
     * After restore event handler
     */
    protected function afterRestore()
    {
        if ($this->obElement->active) {
            $this->clearPropertyValueLinkByPropertyID();
            $this->clearPropertyValueLinkByCategoryID();
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Offer::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return OfferItem::class;
    }

    /**
     * Clear property value links, cached by property ID
     */
    protected function clearPropertyValueLinkByPropertyID()
    {
        //Get property value link list
        $obPropertyValueLinkList = PropertyValueLink::getByElementType(Offer::class)->getByElementID($this->obElement->id)->get();
        if ($obPropertyValueLinkList->isEmpty()) {
            return;
        }

        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {

            if ($this->isFieldChanged('product_id')) {
                $obPropertyValueLink->product_id = $this->obElement->product_id;
                $obPropertyValueLink->save();
            }

            PropertyValueLinkListStore::instance()->property->clear($obPropertyValueLink->property_id);
        }
    }

    /**
     * Clear property value links, cached by product category ID
     */
    protected function clearPropertyValueLinkByCategoryID()
    {
        PropertyValueLinkListStore::instance()->category->clearByOffer($this->obElement, $this->obElement->product_id);
        if (!$this->isFieldChanged('product_id')) {
            return;
        }

        PropertyValueLinkListStore::instance()->category->clearByOffer($this->obElement, $this->obElement->getOriginal('product_id'));
    }
}
