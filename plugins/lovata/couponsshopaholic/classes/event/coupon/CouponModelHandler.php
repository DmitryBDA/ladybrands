<?php namespace Lovata\CouponsShopaholic\Classes\Event\Coupon;

use Lovata\Toolbox\Classes\Event\ModelHandler;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\CouponsShopaholic\Classes\Item\CouponItem;
use Lovata\CouponsShopaholic\Classes\Store\CouponListStore;

/**
 * Class CouponModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Coupon
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponModelHandler extends ModelHandler
{
    /** @var Coupon*/
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Coupon::extend(function ($obElement) {
            $this->extendModel($obElement);
        });
    }

    /**
     * Extend model object
     * @param Coupon $obElement
     */
    protected function extendModel($obElement)
    {
        $sUserModel = UserHelper::instance()->getUserModel();
        if (empty($sUserModel)) {
            return;
        }

        $obElement->belongsTo['user'] = [$sUserModel];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('group_id', CouponListStore::instance()->group);
        $this->checkFieldChanges('hidden', CouponListStore::instance()->hidden);
        $this->checkFieldChanges('hidden', CouponListStore::instance()->not_hidden);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        $this->obElement->cart()->detach();

        $this->clearCacheNotEmptyValue('group_id', CouponListStore::instance()->group);
        $this->clearCacheNotEmptyValue('hidden', CouponListStore::instance()->hidden);
        $this->clearCacheEmptyValue('hidden', CouponListStore::instance()->not_hidden);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Coupon::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return CouponItem::class;
    }
}
