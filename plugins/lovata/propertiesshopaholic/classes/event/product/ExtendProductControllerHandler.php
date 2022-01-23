<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Product;

use DB;

use Lovata\Shopaholic\Controllers\Products;
use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class ExtendProductControllerHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendProductControllerHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Products::extend(function ($obController) {
            /** @var Products $obController */
            if (is_array($obController->importExportConfig)) {
                $arConfig = $obController->importExportConfig;
            } else {
                $arConfig = (array)$obController->makeConfig('$/lovata/shopaholic/controllers/products/'.$obController->importExportConfig);
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
        $arPropertyIDList = (array) DB::table('lovata_properties_shopaholic_set_product_link')->groupBy('property_id')->lists('property_id');
        if (empty($arPropertyIDList)) {
            return [];
        }

        $arPropertyList = (array) Property::whereIn('id', $arPropertyIDList)->active()->lists('name', 'id');
        if (empty($arPropertyList)) {
            return [];
        }

        $arResult = [];
        foreach ($arPropertyList as $iPropertyID => $sName) {
            $arResult['property.'.$iPropertyID] = [
                'label' => $sName,
            ];
        }

        return $arResult;
    }
}
