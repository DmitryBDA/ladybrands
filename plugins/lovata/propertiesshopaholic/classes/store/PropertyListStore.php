<?php namespace Lovata\PropertiesShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\PropertiesShopaholic\Classes\Store\Property\SortingListStore;
use Lovata\PropertiesShopaholic\Classes\Store\Property\ActiveListStore;

/**
 * Class PropertyListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ActiveListStore  $active
 * @property SortingListStore $sorting
 */
class PropertyListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
        $this->addToStoreList('active', ActiveListStore::class);
    }
}
