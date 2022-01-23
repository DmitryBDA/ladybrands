<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Discount;

use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Controllers\Discounts;

/**
 * Class ExtendDiscountFieldsHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Discount
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendDiscountFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend backend fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        if (PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        $obWidget->removeField('tag');
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Discount::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Discounts::class;
    }
}
