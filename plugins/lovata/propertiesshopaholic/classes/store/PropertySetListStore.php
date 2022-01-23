<?php namespace Lovata\PropertiesShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\PropertiesShopaholic\Classes\Store\PropertySet\SortingListStore;
use Lovata\PropertiesShopaholic\Classes\Store\PropertySet\GlobalListStore;

/**
 * Class PropertySetListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property SortingListStore $sorting
 * @property GlobalListStore $is_global
 */
class PropertySetListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
        $this->addToStoreList('is_global', GlobalListStore::class);
    }
}