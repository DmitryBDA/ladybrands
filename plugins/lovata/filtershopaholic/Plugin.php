<?php namespace Lovata\FilterShopaholic;

use Event;
use System\Classes\PluginBase;
use System\Classes\PluginManager;

use Lovata\FilterShopaholic\Classes\Event\CategoryModelHandler;
use Lovata\FilterShopaholic\Classes\Event\ExtendFieldHandler;
use Lovata\FilterShopaholic\Classes\Event\ExtendPropertyItemHandler;
use Lovata\FilterShopaholic\Classes\Event\OfferModelHandler;
use Lovata\FilterShopaholic\Classes\Event\PriceModelHandler;
use Lovata\FilterShopaholic\Classes\Event\ProductModelHandler;
use Lovata\FilterShopaholic\Classes\Event\PropertySetModelHandler;
use Lovata\FilterShopaholic\Classes\Event\PropertyValueModelHandler;
use Lovata\FilterShopaholic\Classes\Event\PropertyValueLinkModelHandler;

/**
 * Class Plugin
 * @package Lovata\FilterShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    const TYPE_BETWEEN = 'between';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT = 'select';
    const TYPE_SWITCH = 'switch';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT_BETWEEN = 'select_between';

    /** @var array Plugin dependencies */
    public $require = ['Lovata.Shopaholic', 'Lovata.Toolbox'];

    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\FilterShopaholic\Components\FilterPanel' => 'FilterPanel',
        ];
    }

    /**
     * Plugin boot method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    /**
     * Add event listeners
     */
    protected function addEventListener()
    {
        Event::subscribe(ExtendFieldHandler::class);
        Event::subscribe(CategoryModelHandler::class);
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(PriceModelHandler::class);

        if(PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            Event::subscribe(PropertyValueModelHandler::class);
            Event::subscribe(PropertySetModelHandler::class);
            Event::subscribe(ExtendPropertyItemHandler::class);
            Event::subscribe(PropertyValueLinkModelHandler::class);
        }
    }
}
