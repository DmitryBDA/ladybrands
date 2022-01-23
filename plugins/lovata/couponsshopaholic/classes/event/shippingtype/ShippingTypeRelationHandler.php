<?php namespace Lovata\CouponsShopaholic\Classes\Event\ShippingType;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\CouponsShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class ShippingTypeRelationHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     * @param array $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearCachedList($arAttachedIDList);
    }

    /**
     * After detach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearCachedList($arAttachedIDList);
    }

    /**
     * Clear cached product list
     * @param array $arAttachedIDList
     */
    protected function clearCachedList($arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }

        foreach ($arAttachedIDList as $iCouponGroupID) {
            ShippingTypeListStore::instance()->coupon_group->clear($iCouponGroupID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() :string
    {
        return ShippingType::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'coupon_group';
    }
}
