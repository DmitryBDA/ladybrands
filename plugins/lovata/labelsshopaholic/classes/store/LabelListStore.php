<?php namespace Lovata\LabelsShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\LabelsShopaholic\Classes\Store\Label\ActiveListStore;
use Lovata\LabelsShopaholic\Classes\Store\Label\SortingListStore;
use Lovata\LabelsShopaholic\Classes\Store\Label\ListByProductStore;

/**
 * Class LabelListStore
 * @package Lovata\LabelsShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ActiveListStore  $active
 * @property SortingListStore $sorting
 * @property ListByProductStore $product
 */
class LabelListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
        $this->addToStoreList('active', ActiveListStore::class);
        $this->addToStoreList('product', ListByProductStore::class);
    }
}
