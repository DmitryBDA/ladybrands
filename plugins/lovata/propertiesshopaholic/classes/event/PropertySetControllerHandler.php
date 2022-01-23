<?php namespace Lovata\PropertiesShopaholic\Classes\Event;

use System\Classes\PluginManager;
use Lovata\PropertiesShopaholic\Controllers\PropertySets;

/**
 * Class PropertySetControllerHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertySetControllerHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        PropertySets::extend(function($obController) {
            $this->extendConfig($obController);
        });
    }

    /**
     * Extend category controller
     * @param PropertySets $obController
     */
    protected function extendConfig($obController)
    {
        if(PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic') && !PluginManager::instance()->isDisabled('Lovata.FilterShopaholic')) {
            $obController->relationConfig = $obController->mergeConfig(
                $obController->relationConfig,
                '$/lovata/propertiesshopaholic/config/category_config_relation_with_filter.yaml'
            );
        } else {
            $obController->relationConfig = $obController->mergeConfig(
                $obController->relationConfig,
                '$/lovata/propertiesshopaholic/config/category_config_relation.yaml'
            );
        }
    }
}
