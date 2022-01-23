<?php namespace Lovata\PropertiesShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\PropertiesShopaholic\Classes\Store\Group\SortingListStore;

/**
 * Class GroupListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property SortingListStore $sorting
 */
class GroupListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
    }
}