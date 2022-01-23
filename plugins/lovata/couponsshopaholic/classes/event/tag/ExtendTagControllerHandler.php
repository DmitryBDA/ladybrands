<?php namespace Lovata\CouponsShopaholic\Classes\Event\Tag;

use Lovata\Toolbox\Classes\Event\AbstractExtendRelationConfigHandler;

use System\Classes\PluginManager;

/**
 * Class ExtendTagControllerHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Tag
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendTagControllerHandler extends AbstractExtendRelationConfigHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        parent::subscribe();
    }

    /**
     * Get path to config file
     * @return string
     */
    protected function getConfigPath() : string
    {
        return '$/lovata/couponsshopaholic/config/coupon_config_relation.yaml';
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return \Lovata\TagsShopaholic\Controllers\Tags::class;
    }
}
