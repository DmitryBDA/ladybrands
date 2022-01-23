<?php namespace Lovata\PropertiesShopaholic\Controllers;

use Event;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Class Groups
 * @package Lovata\PropertiesShopaholic\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Groups extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ReorderController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    /**
     * Groups constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Lovata.PropertiesShopaholic', 'shopaholic-menu-group');
    }

    /**
     * Ajax handler onReorder event
     */
    public function onReorder()
    {
        $obResult = parent::onReorder();
        Event::fire('shopaholic.property.group.update.sorting');

        return $obResult;
    }
}