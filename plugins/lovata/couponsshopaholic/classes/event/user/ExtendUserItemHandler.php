<?php namespace Lovata\CouponsShopaholic\Classes\Event\User;

use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\CouponsShopaholic\Classes\Collection\CouponCollection;

/**
 * Class ExtendUserItemHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\User
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendUserItemHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName) || $sUserPluginName != 'Lovata.Buddies') {
            return;
        }

        $this->addCouponAttribute();
    }

    /**
     * Extend class UserItem and add "getCouponAttribute" method
     */
    public function addCouponAttribute()
    {
        \Lovata\Buddies\Classes\Item\UserItem::extend(function($obUserItem) {
            /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
            $obUserItem->addDynamicMethod('getCouponAttribute', function () use($obUserItem) {
                $obCouponList = CouponCollection::make()->visibleToUser($obUserItem->id);

                return $obCouponList;
            });
        });
    }
}
