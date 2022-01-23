<?php namespace Lovata\CouponsShopaholic\Classes\Event\User;

use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\CouponsShopaholic\Classes\Collection\CouponCollection;

/**
 * Class UserModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\User
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class UserModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName)) {
            return;
        }

        $sModelClass = UserHelper::instance()->getUserModel();
        $this->addCouponRelation($sModelClass);
    }

    /**
     * Add order relation in User model
     * @param string $sModelClass
     */
    protected function addCouponRelation($sModelClass)
    {
        if (empty($sModelClass) || !class_exists($sModelClass)) {
            return;
        }

        $sModelClass::extend(function($obUser) {
            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->addDynamicMethod('getCouponListAttribute', function() use ($obUser) {
                return CouponCollection::make()->visibleToUser($obUser->id);
            });
        });
    }
}
