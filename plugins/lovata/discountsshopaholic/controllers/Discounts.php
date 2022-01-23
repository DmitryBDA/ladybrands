<?php namespace Lovata\DiscountsShopaholic\Controllers;

use Lang;
use Flash;
use Event;
use BackendMenu;
use Backend\Classes\Controller;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;

use Lovata\DiscountsShopaholic\Classes\Processor\CatalogPriceProcessor;

/**
 * Class Discounts
 * @package Lovata\DiscountsShopaholic\Controllers
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Discounts extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ReorderController',
        'Backend.Behaviors.RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * Discounts constructor.
     */
    public function __construct()
    {
        CurrencyHelper::instance()->disableActiveCurrency();

        parent::__construct();
        BackendMenu::setContext('Lovata.Shopaholic', 'shopaholic-menu-promo', 'shopaholic-menu-promo-discount');
    }

    /**
     * Ajax handler onReorder event
     *
     * @return mixed
     */
    public function onReorder()
    {
        $obResult = parent::onReorder();
        Event::fire('shopaholic.discount.update.sorting');

        return $obResult;
    }

    /**
     * Start of updating catalog prices
     */
    public function onUpdateCatalogPrices()
    {
        CatalogPriceProcessor::instance()->run();
        if (CatalogPriceProcessor::instance()->isQueueOn()) {
            Flash::success(Lang::get('lovata.discountsshopaholic::lang.message.update_catalog_prices_started'));
        } else {
            Flash::success(Lang::get('lovata.discountsshopaholic::lang.message.update_catalog_prices_success'));
        }
    }
}
