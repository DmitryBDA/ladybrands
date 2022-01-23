<?php namespace Lovata\PropertiesShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink\ListByPropertyStore;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink\ListByCategoryStore;

/**
 * Class PropertyValueLinkListStore
 * @package Lovata\PropertiesShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property ListByPropertyStore $property
 * @property ListByCategoryStore $category
 */
class PropertyValueLinkListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('property', ListByPropertyStore::class);
        $this->addToStoreList('category', ListByCategoryStore::class);
    }
}