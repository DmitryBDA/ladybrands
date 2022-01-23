<?php namespace Lovata\PropertiesShopaholic\Updates;

use Seeder;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;

/**
 * Class SeederTransferPropertySettingsValues
 * @package Lovata\Toolbox\Updates
 */
class SeederTransferPropertySettingsValues extends Seeder
{
    /**
     * Run seeder
     */
    public function run()
    {
        //Get property list with type "select"/"checkbox"
        $obPropertyList = Property::whereIn('type', [Property::TYPE_SELECT, Property::TYPE_CHECKBOX])->get();
        if ($obPropertyList->isEmpty()) {
            return;
        }

        /** @var Property $obProperty */
        foreach ($obPropertyList as $obProperty) {
            $this->processPropertyValueList($obProperty);
        }
    }

    /**
     * Process Property value list from settings
     * @param Property $obProperty
     * @throws \Exception
     */
    protected function processPropertyValueList($obProperty)
    {
        $arSettings = $obProperty->settings;
        if (empty($arSettings) || empty($arSettings['list'])) {
            return;
        }

        $arValueList = array_pluck($arSettings['list'], 'value');
        if (empty($arValueList)) {
            return;
        }

        foreach ($arValueList as $sValue) {
            $obPropertyValue = $this->getValueObjectBySlug($sValue);
            if (empty($obPropertyValue)) {
                continue;
            }

            $obProperty->property_value()->add($obPropertyValue);
        }
    }


    /**
     * Get property value object by slug
     * @param string $sValue
     * @return PropertyValue
     */
    protected function getValueObjectBySlug($sValue)
    {
        if (!PropertyValue::hasValue($sValue)) {
            return null;
        }

        $sSlug = PropertyValue::getSlugValue($sValue);

        $obValue = PropertyValue::getBySlug($sSlug)->first();
        if (!empty($obValue)) {
            return $obValue;
        }

        try {
            $obValue = PropertyValue::create([
                'value' => $sValue,
                'slug'  => $sSlug,
            ]);
        } catch (\Exception $obException) {
            return null;
        }

        return $obValue;
    }
}
