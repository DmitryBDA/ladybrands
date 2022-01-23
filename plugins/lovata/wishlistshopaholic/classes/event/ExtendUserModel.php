<?php namespace Lovata\WishListShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Helper\UserHelper;

/**
 * Class ExtendUserModel
 * @package Lovata\WishListShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendUserModel
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sUserModel = UserHelper::instance()->getUserModel();
        if (empty($sUserModel)) {
            return;
        }

        $sUserModel::extend(function ($obUser) {
            /** @var \Lovata\Buddies\Models\User|\RainLab\User\Models\User */
            $obUser->addJsonable('product_wish_list');
        });
    }
}
