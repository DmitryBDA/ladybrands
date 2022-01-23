<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Offer;

use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromCSV;
use Lovata\Shopaholic\Classes\Import\ImportOfferModelFromXML;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\XmlImportSettings;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class ExtendOfferImportFromXML
 * @package Lovata\PropertiesShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOfferImportFromXML
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen(ImportOfferModelFromXML::EXTEND_FIELD_LIST, function ($arFieldList) {
            $arPropertyList = $this->getPropertyListFields();
            $arFieldList = array_merge($arFieldList, $arPropertyList);

            return $arFieldList;
        }, 900);

        $obEvent->listen(ImportOfferModelFromXML::EXTEND_IMPORT_DATA, function ($arImportData, $obParseNode) {
            $arImportData = $this->parsePropertyValue($arImportData, $obParseNode);

            return $arImportData;
        }, 900);

        $obEvent->listen(ImportOfferModelFromCSV::EVENT_BEFORE_IMPORT, function ($sModelClass, $arImportData) {
            if ($sModelClass != Offer::class) {
                return null;
            }

            $arImportData = $this->preparePropertyArrayToSave($arImportData);

            return $arImportData;
        }, 900);
    }

    /**
     * Parse property values
     * @param array                                       $arImportData
     * @param \Lovata\Toolbox\Classes\Helper\ParseXMLNode $obParseNode
     * @return array
     */
    protected function parsePropertyValue($arImportData, $obParseNode)
    {
        $sPathToPropertyList = XmlImportSettings::getValue('offer_property_list_path');
        $sPathToPropertyID = XmlImportSettings::getValue('offer_property_id_path');
        $sPathToPropertyValue = XmlImportSettings::getValue('offer_property_value_path');
        if (empty($sPathToPropertyList) || empty($sPathToPropertyID) || empty($sPathToPropertyValue)) {
            return $arImportData;
        }

        $arPropertyNodeList = $obParseNode->getNode()->findListByPath($sPathToPropertyList);
        if (empty($arPropertyNodeList)) {
            return $arImportData;
        }

        /** @var \Lovata\Toolbox\Classes\Helper\ImportXMLNode $obPropertyNode */
        foreach ($arPropertyNodeList as $obPropertyNode) {
            $iPropertyID = $obPropertyNode->getValueByPath($sPathToPropertyID);
            $sValue = $obPropertyNode->getValueByPath($sPathToPropertyValue);
            if (empty($iPropertyID)) {
                continue;
            }

            //Get property by external ID
            $obProperty = Property::getByExternalID($iPropertyID)->first();
            if (empty($obProperty)) {
                continue;
            }

            array_set($arImportData, 'property.'.$obProperty->id, $sValue);
        }

        return $arImportData;
    }

    /**
     * Prepare property array to save
     * @param array $arImportData
     * @return array|null
     */
    protected function preparePropertyArrayToSave($arImportData)
    {
        if (empty($arImportData)) {
            return null;
        }

        foreach ($arImportData as $sKey => $sValue) {
            if (!preg_match('%^property\.[0-9]+$%', $sKey)) {
                continue;
            }

            $sValue = trim($sValue);

            if (!is_array($sValue)) {
                $arValueList = explode('|', $sValue);
            } else {
                $arValueList = $sValue;
            }

            array_set($arImportData, $sKey, $arValueList);
        }

        $arPropertyList = (array) array_get($arImportData, 'property');
        foreach ($arPropertyList as $sKey => $sValue) {
            $obProperty = Property::find((int) $sKey);
            if (empty($obProperty) || $obProperty->type != Property::TYPE_TAG_LIST || !is_array($sValue)) {
                continue;
            }

            $arPropertyList[$sKey] = implode(',', $sValue);
        }

        $arImportData['property'] = $arPropertyList;

        //Get offer model
        $sExternalID = array_get($arImportData, 'external_id');
        if (empty($sExternalID)) {
            return $arImportData;
        }

        $obOffer = Offer::getByExternalID($sExternalID)->first();
        if (empty($obOffer)) {
            return $arImportData;
        }

        //Process existing property array
        $arOldPropertyList = $obOffer->property;
        $arNewPropertyList = array_get($arImportData, 'property');
        if (empty($arNewPropertyList) || empty($arOldPropertyList)) {
            return $arImportData;
        }

        foreach ($arOldPropertyList as $iKey => $sValue) {
            $sNewValue = array_get($arNewPropertyList, $iKey);
            if ($sNewValue !== null) {
                continue;
            }

            $arNewPropertyList[$iKey] = $sValue;
        }

        $arImportData['property'] = $arNewPropertyList;

        return $arImportData;
    }


    /**
     * Get property list and array with property fields
     * @return array
     */
    protected function getPropertyListFields() : array
    {
        $arPropertyList = (array) Property::active()->lists('name', 'id');
        if (empty($arPropertyList)) {
            return [];
        }

        $arResult = [];
        foreach ($arPropertyList as $iPropertyID => $sName) {
            $arResult['property.'.$iPropertyID] = $sName;
        }

        return $arResult;
    }
}
