<?php namespace Lovata\CouponsShopaholic\Classes\Event\CouponGroup;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;
use Lovata\CouponsShopaholic\Classes\Store\OfferListStore;
use Lovata\CouponsShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class CouponGroupRelationHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\CouponGroup
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponGroupRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param CouponGroup $obModel
     * @param array    $arAttachedIDList
     * @param array    $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearCachedList($obModel);
    }

    /**
     * After detach event handler
     * @param CouponGroup $obModel
     * @param array    $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearCachedList($obModel);
    }

    /**
     * Clear cached list
     * @param CouponGroup $obModel
     */
    protected function clearCachedList($obModel)
    {
        if ($this->sRelationName == 'shipping_type') {
            ShippingTypeListStore::instance()->coupon_group->clear($obModel->id);
            return;
        }

        ProductListStore::instance()->coupon_group->clear($obModel->id);
        ProductListStore::instance()->coupon_group->clear($obModel->id, true);
        if ($this->sRelationName == 'offer') {
            OfferListStore::instance()->coupon_group->clear($obModel->id);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return CouponGroup::class;
    }

    /**
     * Get relation name
     * @return array
     */
    protected function getRelationName()
    {
        return ['product', 'offer', 'brand', 'category', 'shipping_type', 'tag'];
    }
}
