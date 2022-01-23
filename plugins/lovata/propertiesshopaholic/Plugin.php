<?php namespace Lovata\PropertiesShopaholic;

use Event;
use Backend;
use System\Classes\PluginBase;

use Lovata\PropertiesShopaholic\Classes\Event\CategoryModelHandler;
use Lovata\PropertiesShopaholic\Classes\Event\ExtendFieldHandler;
use Lovata\PropertiesShopaholic\Classes\Event\GroupModelHandler;
//Offer event list
use Lovata\PropertiesShopaholic\Classes\Event\Offer\OfferModelHandler;
use Lovata\PropertiesShopaholic\Classes\Event\Offer\ExtendOfferControllerHandler;
use Lovata\PropertiesShopaholic\Classes\Event\Offer\ExtendOfferImportFromXML;
//Product event list
use Lovata\PropertiesShopaholic\Classes\Event\Product\ProductModelHandler;
use Lovata\PropertiesShopaholic\Classes\Event\Product\ExtendProductControllerHandler;
use Lovata\PropertiesShopaholic\Classes\Event\Product\ExtendProductImportFromXML;
//Property event list
use Lovata\PropertiesShopaholic\Classes\Event\Property\PropertyModelHandler;
use Lovata\PropertiesShopaholic\Classes\Event\Property\PropertyRelationHandler;
//Property set event list
use Lovata\PropertiesShopaholic\Classes\Event\PropertySetModelHandler;
use Lovata\PropertiesShopaholic\Classes\Event\PropertySetControllerHandler;
use Lovata\PropertiesShopaholic\Classes\Event\PropertyValueLinkModelHandler;
//PropertyValue event list
use Lovata\PropertiesShopaholic\Classes\Event\PropertyValue\PropertyValueModelHandler;

/**
 * Class Plugin
 * @package Lovata\PropertiesShopaholic
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = ['Lovata.Shopaholic', 'Lovata.Toolbox'];

    /**
     * Plugin boot method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    /**
     * @return array
     */
    public function registerSettings()
    {
        return [
            'shopaholic-menu-property'     => [
                'label'       => 'lovata.propertiesshopaholic::lang.menu.property',
                'description' => 'lovata.propertiesshopaholic::lang.menu.property_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'url'         => Backend::url('lovata/propertiesshopaholic/properties'),
                'icon'        => 'icon-th-list',
                'permissions' => ['shopaholic-menu-property'],
                'order'       => 1550,
            ],
            'shopaholic-menu-property-set' => [
                'label'       => 'lovata.propertiesshopaholic::lang.menu.property_set',
                'description' => 'lovata.propertiesshopaholic::lang.menu.property_set_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'url'         => Backend::url('lovata/propertiesshopaholic/propertysets'),
                'icon'        => 'icon-th-large',
                'permissions' => ['shopaholic-menu-property'],
                'order'       => 1600,
            ],
            'shopaholic-menu-group'        => [
                'label'       => 'lovata.propertiesshopaholic::lang.menu.group',
                'description' => 'lovata.propertiesshopaholic::lang.menu.group_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'url'         => Backend::url('lovata/propertiesshopaholic/groups'),
                'icon'        => 'icon-list-alt',
                'permissions' => ['shopaholic-menu-group'],
                'order'       => 1700,
            ],
        ];
    }

    /**
     * Add event listeners
     */
    protected function addEventListener()
    {
        Event::subscribe(CategoryModelHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
        Event::subscribe(GroupModelHandler::class);
        //Offer event list
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(ExtendOfferControllerHandler::class);
        Event::subscribe(ExtendOfferImportFromXML::class);
        //Product event list
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(ExtendProductControllerHandler::class);
        Event::subscribe(ExtendProductImportFromXML::class);
        //Property event list
        Event::subscribe(PropertyModelHandler::class);
        Event::subscribe(PropertyRelationHandler::class);
        //Property set event list
        Event::subscribe(PropertySetModelHandler::class);
        Event::subscribe(PropertySetControllerHandler::class);
        Event::subscribe(PropertyValueLinkModelHandler::class);
        //Property value event list
        Event::subscribe(PropertyValueModelHandler::class);
    }
}
