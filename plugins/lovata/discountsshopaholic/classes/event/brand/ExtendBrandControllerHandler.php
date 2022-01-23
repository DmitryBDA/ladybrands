<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Brand;

use Lovata\Toolbox\Classes\Event\AbstractExtendRelationConfigHandler;

use Lovata\Shopaholic\Controllers\Brands;

/**
 * Class ExtendBrandControllerHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Brand
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendBrandControllerHandler extends AbstractExtendRelationConfigHandler
{
    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Brands::class;
    }

    /**
     * Get path to config file
     * @return string
     */
    protected function getConfigPath() : string
    {
        return '$/lovata/discountsshopaholic/config/discount_config_relation.yaml';
    }
}
