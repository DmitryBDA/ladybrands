<?php namespace Lovata\CouponsShopaholic\Classes\Event\Coupon;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Event\AbstractBackendColumnHandler;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\CouponsShopaholic\Controllers\CouponGroups;

/**
 * Class ExtendCouponColumnsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Coupon
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendCouponColumnsHandler extends AbstractBackendColumnHandler
{
    /**
     * Extend backend columns
     * @param \Backend\Widgets\Lists $obWidget
     */
    protected function extendColumns($obWidget)
    {
        $this->removeUserField($obWidget);
    }

    /**
     * Remov user field
     * @param \Backend\Widgets\Lists $obWidget
     */
    protected function removeUserField($obWidget)
    {
        $sUserModel = UserHelper::instance()->getUserModel();
        if (!empty($sUserModel)) {
            return;
        }

        $obWidget->removeColumn('max_usage');
        $obWidget->removeColumn('user_email');
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Coupon::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return CouponGroups::class;
    }
}
