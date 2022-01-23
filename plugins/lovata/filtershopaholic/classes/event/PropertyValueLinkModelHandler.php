<?php namespace Lovata\FilterShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\FilterShopaholic\Classes\Store\FilterValueStore;

/**
 * Class PropertyValueLinkModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA PropertyValueLink
 */
class PropertyValueLinkModelHandler extends ModelHandler
{
    /** @var PropertyValueLink */
    protected $obElement;
    
    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PropertyValueLink::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return null;
    }

    /**
     * After save event handler
     * @throws \Exception
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('value_id') && !$this->isFieldChanged('property_id') && !$this->isFieldChanged('product_id')) {
            return;
        }

        $this->clearCacheByValueID($this->obElement->value_id);
        if ($this->isFieldChanged('value_id')) {
            $this->clearCacheByValueID($this->obElement->getOriginal('value_id'));
        }
    }

    /**
     * After delete event handler
     * @throws \Exception
     */
    protected function afterDelete()
    {
        $this->clearCacheByValueID($this->obElement->value_id);
    }

    /**
     * Clear filter cache by value ID
     * @param int $iValueID
     */
    protected function clearCacheByValueID($iValueID)
    {
        if (empty($iValueID)) {
            return;
        }

        $obPropertyValue = PropertyValue::find($iValueID);
        if (!empty($obPropertyValue)) {
            FilterValueStore::instance()->property->clear($obPropertyValue->slug);
        }
    }
}
