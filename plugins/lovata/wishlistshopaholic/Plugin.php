<?php namespace Lovata\WishListShopaholic;

use Event;
use System\Classes\PluginBase;

use Lovata\WishListShopaholic\Classes\Event\ExtendUserModel;
use Lovata\WishListShopaholic\Classes\Event\ExtendProductItem;
use Lovata\WishListShopaholic\Classes\Event\ExtendProductComponent;
use Lovata\WishListShopaholic\Classes\Event\ExtendProductCollection;

/**
 * Class Plugin
 * @package Lovata\WishListShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
     * Add event listeners
     */
    protected function addEventListener()
    {
        Event::subscribe(ExtendProductCollection::class);
        Event::subscribe(ExtendProductItem::class);
        Event::subscribe(ExtendProductComponent::class);
        Event::subscribe(ExtendUserModel::class);
    }
}
