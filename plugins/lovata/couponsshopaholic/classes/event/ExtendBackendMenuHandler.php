<?php namespace Lovata\CouponsShopaholic\Classes\Event;

use Backend;
use Lovata\Toolbox\Classes\Event\AbstractBackendMenuHandler;

/**
 * Class ExtendBackendMenuHandler
 * @package Lovata\CouponsShopaholic\Classes\Event
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
            'label' => 'lovata.couponsshopaholic::lang.menu.coupon_group',
            'url'           => Backend::url('lovata/couponsshopaholic/coupongroups'),
            'icon'          => 'oc-icon-ticket',
            'permissions'   => ['shopaholic-menu-promo-coupon'],
            'order'         => 600,
        ];

        $obManager->addSideMenuItem('Lovata.Shopaholic', 'shopaholic-menu-promo', 'shopaholic-menu-promo-coupon-group', $arMenuItemData);
    }
}
