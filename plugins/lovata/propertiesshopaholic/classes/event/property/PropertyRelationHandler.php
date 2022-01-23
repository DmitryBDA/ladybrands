<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Property;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class PropertyRelationHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event\Property
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertyRelationHandler extends AbstractModelRelationHandler
{
    /**
     * After detach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     * @throws \Exception
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }
        
        foreach ($arAttachedIDList as $iValueID) {
            $this->removePropertyValue($iValueID);
        }
    }

    /**
     * Get proeprty value object and remove it, if value has not value links
     * @param int $iValueID
     * @throws \Exception
     */
    protected function removePropertyValue($iValueID)
    {
        //Find property links by value ID
        $obPropertyValueLink = PropertyValueLink::getByValue($iValueID)->get();
        
        //Get property object
        $obPropertyValue = PropertyValue::find($iValueID);
        if (empty($obPropertyValue) || $obPropertyValueLink->isNotEmpty() || $obPropertyValue->property->isNotEmpty()) {
            return;
        }

        $obPropertyValue->delete();
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() :string
    {
        return Property::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'property_value';
    }
}
