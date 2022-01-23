<?php namespace Lovata\LabelsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\LabelsShopaholic\Classes\Store\Product\ListByLabelStore;

/**
 * Class ProductListStore
 * @package Lovata\LabelsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByLabelStore $label
 */
class ProductListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('label', ListByLabelStore::class);
    }
}
