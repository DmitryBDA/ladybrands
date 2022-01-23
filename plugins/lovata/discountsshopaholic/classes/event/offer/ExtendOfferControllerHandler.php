<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Offer;

use Lovata\Toolbox\Classes\Event\AbstractExtendRelationConfigHandler;

use Lovata\Shopaholic\Controllers\Offers;

/**
 * Class ExtendOfferControllerHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOfferControllerHandler extends AbstractExtendRelationConfigHandler
{
    /**
     * Get path to config file
     * @return string
     */
    protected function getConfigPath() : string
    {
        return '$/lovata/discountsshopaholic/config/discount_config_relation.yaml';
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Offers::class;
    }
}
