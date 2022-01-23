<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Offer;

use DB;
use Lovata\Shopaholic\Controllers\Offers;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class ExtendOfferControllerHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOfferControllerHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Offers::extend(function ($obController) {
            /** @var Offers $obController */
            if (is_array($obController->importExportConfig)) {
                $arConfig = $obController->importExportConfig;
            } else {
                $arConfig = (array)$obController->makeConfig('$/lovata/shopaholic/controllers/offers/'.$obController->importExportConfig);
            }

            $arFiledList = (array) array_get($arConfig, 'import.list.columns');
            $arFiledList = array_merge($arFiledList, $this->getPropertyListConfig());

            array_set($arConfig, 'import.list.columns', $arFiledList);
            $obController->importExportConfig = $arConfig;
        });
    }

    /**
     * Get property list and prepare config
     * @return array
     */
    protected function getPropertyListConfig() : array
    {
        $arPropertyIDList = (array) DB::table('lovata_properties_shopaholic_set_offer_link')->groupBy('property_id')->lists('property_id');
        if (empty($arPropertyIDList)) {
            return [];
        }

        $arPropertyList = (array) Property::whereIn('id', $arPropertyIDList)->active()->lists('name', 'id');
        if (empty($arPropertyList)) {
            return [];
        }

        $arResult = [];
        foreach ($arPropertyList as $iPropertyID => $sName) {
            $arResult['property.'.$iPropertyID.''] = [
                'label' => $sName,
            ];
        }

        return $arResult;
    }
}
