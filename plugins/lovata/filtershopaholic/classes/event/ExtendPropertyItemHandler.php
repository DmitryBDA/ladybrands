<?php namespace Lovata\FilterShopaholic\Classes\Event;

use System\Classes\PluginManager;

/**
 * Class ExtendPropertyItemHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendPropertyItemHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem::extend(function ($obItem) {
            /** @var \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem $obItem */
            $obItem->addDynamicMethod('getFilterNameAttribute', function ($obItem) {
                /** @var \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem $obItem */
                $sFilterName = $obItem->getAttribute('filter_name');
                $sResult = !empty($sFilterName) ? $sFilterName : $obItem->name;

                return $sResult;
            });
        });
    }
}
