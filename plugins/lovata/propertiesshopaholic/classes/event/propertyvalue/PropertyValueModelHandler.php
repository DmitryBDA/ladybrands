<?php namespace Lovata\PropertiesShopaholic\Classes\Event\PropertyValue;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem;

/**
 * Class PropertyValueModelHandler
 * @package Lovata\Shopaholic\Classes\Event\PropertyValue
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
     * After delete event handler
     * @throws \Exception
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        $this->obElement->property()->detach();

        $this->removeValueLink();
    }

    /**
     * Remove property value link, if value was removed
     * @throws \Exception
     */
    protected function removeValueLink()
    {
        //Get property value links with value_id = $this->value_id
        $obPropertyValueLinkList = PropertyValueLink::getByValue($this->obElement->id)->get();
        if ($obPropertyValueLinkList->isEmpty()) {
            return;
        }

        /** @var PropertyValueLink $obPropertyValueLink */
        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {
            $obPropertyValueLink->delete();
        }
    }
}
