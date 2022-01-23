<?php namespace Lovata\LabelsShopaholic;

use Event;
use Backend;
use System\Classes\PluginBase;

//Labels events
use Lovata\LabelsShopaholic\Classes\Event\Label\LabelModelHandler;
use Lovata\LabelsShopaholic\Classes\Event\Label\LabelRelationHandler;
//Product events
use Lovata\LabelsShopaholic\Classes\Event\Product\ProductModelHandler;
use Lovata\LabelsShopaholic\Classes\Event\Product\ProductRelationHandler;
use Lovata\LabelsShopaholic\Classes\Event\Product\ExtendProductControllerHandler;
use Lovata\LabelsShopaholic\Classes\Event\Product\ExtendProductFieldsHandler;

/**
 * Class Plugin
 * @package Lovata\LabelsShopaholic
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /** @var array Plugin dependencies */
    public $require = ['Lovata.Toolbox', 'Lovata.Shopaholic'];

    /**
     * Boot plugin method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    /**
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\LabelsShopaholic\Components\LabelData' => 'LabelData',
            'Lovata\LabelsShopaholic\Components\LabelPage' => 'LabelPage',
            'Lovata\LabelsShopaholic\Components\LabelList' => 'LabelList',
        ];
    }

    /**
     * @return array
     */
    public function registerSettings()
    {
        return [
            'shopaholic-menu-label' => [
                'label'       => 'lovata.labelsshopaholic::lang.menu.label',
                'description' => 'lovata.labelsshopaholic::lang.menu.label_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'url'         => Backend::url('lovata/labelsshopaholic/labels'),
                'icon'        => 'icon-tags',
                'permissions' => ['shopaholic-menu-labels'],
                'order'       => 1600,
            ],
        ];
    }

    /**
     * Add event listeners
     */
    private function addEventListener()
    {
        //Labels events
        Event::subscribe(LabelModelHandler::class);
        Event::subscribe(LabelRelationHandler::class);
        //Product events
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(ProductRelationHandler::class);
        Event::subscribe(ExtendProductControllerHandler::class);
        Event::subscribe(ExtendProductFieldsHandler::class);
    }
}
