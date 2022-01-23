<?php namespace Lovata\CouponsShopaholic\Classes\Event\ShippingType;

use Lovata\Toolbox\Classes\Event\AbstractExtendRelationConfigHandler;

use Lovata\OrdersShopaholic\Controllers\ShippingTypes;

/**
 * Class ExtendShippingTypeControllerHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendShippingTypeControllerHandler extends AbstractExtendRelationConfigHandler
{
    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return ShippingTypes::class;
    }

    /**
     * Get path to config file
     * @return string
     */
    protected function getConfigPath() : string
    {
        return '$/lovata/couponsshopaholic/config/coupon_config_relation.yaml';
    }
}
