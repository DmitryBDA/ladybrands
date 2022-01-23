<?php namespace Lovata\PropertiesShopaholic\Classes\Event;

use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyProductLink;
use Lovata\PropertiesShopaholic\Classes\Store\PropertySetListStore;
use Lovata\PropertiesShopaholic\Classes\Item\PropertySetItem;

/**
 * Class PropertySetModelHandler
 * @package Lovata\Shopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertySetModelHandler extends ModelHandler
{
    /** @var  PropertySet */
    protected $obElement;

    /**
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        PropertySet::extend(function ($obElement) {
            $this->addModelRelationConfig($obElement);

            /** @var PropertySet $obElement */
            $obElement->bindEvent('model.beforeDelete', function () use ($obElement) {
                $this->beforeDelete($obElement);
            });
        });

        $obEvent->listen('shopaholic.property.property_set.update.sorting', function () {
            $this->clearSortingList();
        });
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        parent::afterCreate();
        $this->clearSortingList();
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('is_global', PropertySetListStore::instance()->is_global);
    }

    /**
     * Before delete event handler
     * @var PropertySet $obElement
     */
    protected function beforeDelete($obElement)
    {
        $obElement->category()->detach();
        $obElement->product_property()->detach();
        $obElement->offer_property()->detach();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->clearSortingList();
        parent::afterDelete();

        if ($this->obElement->is_global) {
            PropertySetListStore::instance()->is_global->clear();
        }
    }

    /**
     * Clear sorting list
     */
    protected function clearSortingList()
    {
        PropertySetListStore::instance()->sorting->clear();
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PropertySet::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return PropertySetItem::class;
    }

    /**
     * Add relation config in category model
     * @param PropertySet $obElement
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Models\CategoryTest
     */
    protected function addModelRelationConfig($obElement)
    {
        $arPivotData = ['groups'];
        if (PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')) {
            $arPivotData = array_merge($arPivotData, ['in_filter', 'filter_type', 'filter_name']);
        }

        //Add relation with addition property
        $obElement->belongsToMany['product_property'] = [
            Property::class,
            'table'      => 'lovata_properties_shopaholic_set_product_link',
            'key'        => 'set_id',
            'otherKey'   => 'property_id',
            'order'      => 'sort_order asc',
            'pivot'      => $arPivotData,
            'pivotModel' => PropertyProductLink::class,
        ];

        $obElement->belongsToMany['offer_property'] = [
            Property::class,
            'table'      => 'lovata_properties_shopaholic_set_offer_link',
            'key'        => 'set_id',
            'otherKey'   => 'property_id',
            'order'      => 'sort_order asc',
            'pivot'      => $arPivotData,
            'pivotModel' => PropertyProductLink::class,
        ];
    }
}
