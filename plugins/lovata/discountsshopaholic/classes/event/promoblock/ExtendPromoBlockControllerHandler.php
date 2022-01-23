<?php namespace Lovata\DiscountsShopaholic\Classes\Event\PromoBlock;

use Lovata\Toolbox\Classes\Event\AbstractExtendRelationConfigHandler;

use Lovata\Shopaholic\Controllers\PromoBlocks;

/**
 * Class ExtendPromoBlockControllerHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\PromoBlock
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendPromoBlockControllerHandler extends AbstractExtendRelationConfigHandler
{
    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return PromoBlocks::class;
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
