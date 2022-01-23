<?php namespace Lovata\DiscountsShopaholic\Classes\Event;

use Backend;
use Lovata\Toolbox\Classes\Event\AbstractBackendMenuHandler;

/**
 * Class ExtendBackendMenuHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendBackendMenuHandler extends AbstractBackendMenuHandler
{
    /**
     * Add menu items
     * @param \Backend\Classes\NavigationManager $obManager
     */
    protected function addMenuItems($obManager)
    {
        $arMenuItemData = [
            'label' => 'lovata.discountsshopaholic::lang.menu.discount',
            'url'           => Backend::url('lovata/discountsshopaholic/discounts'),
            'icon'          => 'oc-icon-percent',
            'permissions'   => ['shopaholic-menu-promo-discount'],
            'order'         => 600,
        ];

        $obManager->addSideMenuItem('Lovata.Shopaholic', 'shopaholic-menu-promo', 'shopaholic-menu-promo-discount', $arMenuItemData);
    }
}
