<?php namespace Lovata\CouponsShopaholic\Classes\Event\Coupon;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\CouponsShopaholic\Controllers\CouponGroups;

/**
 * Class ExtendCouponFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Coupon
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendCouponFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend backend fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $this->removeUserField($obWidget);
    }

    /**
     * Remov user field
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function removeUserField($obWidget)
    {
        $sUserModel = UserHelper::instance()->getUserModel();
        if (!empty($sUserModel)) {
            return;
        }

        $obWidget->removeField('user');
        $obWidget->removeField('max_usage');
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
