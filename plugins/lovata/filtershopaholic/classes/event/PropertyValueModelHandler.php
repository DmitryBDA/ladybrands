<?php namespace Lovata\FilterShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem;
use Lovata\FilterShopaholic\Classes\Store\FilterValueStore;

/**
 * Class PropertyValueModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertyValueModelHandler extends ModelHandler
{
    /** @var  PropertyValue */
    protected $obElement;

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PropertyValue::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return PropertyValueItem::class;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        $this->checkFieldChanges('slug', FilterValueStore::instance()->property);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        FilterValueStore::instance()->property->clear($this->obElement->slug);
    }
}
