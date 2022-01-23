<?php namespace Lovata\CouponsShopaholic\Classes\Event\Offer;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Item\OfferItem;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\OfferListStore;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class OfferModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Offer */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Offer::extend(function ($obElement) {
            $this->extendModel($obElement);
        });
    }

    /**
     * Extend model object
     * @param Offer $obElement
     */
    protected function extendModel($obElement)
    {
        $obElement->belongsToMany['coupon_group'] = [
            CouponGroup::class,
            'table'    => 'lovata_coupons_shopaholic_group_offer',
            'otherKey' => 'group_id',
        ];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('active') && !$this->isFieldChanged('product_id')) {
            return;
        }

        $this->clearCachedList();
        $this->clearCachedListByProduct($this->obElement->product_id);
        if ($this->isFieldChanged('product_id')) {
            $this->clearCachedListByProduct($this->obElement->getOriginal('product_id'));
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        if (!$this->obElement->active) {
            return;
        }

        $this->clearCachedList();
        $this->clearCachedListByProduct($this->obElement->product_id);
    }

    /**
     * Clear product cached list by coupon group ID (Relation between offer and coupon group)
     */
    protected function clearCachedList()
    {
        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_offer')->where('offer_id', $this->obElement->id)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
            OfferListStore::instance()->coupon_group->clear($iCouponGroupID);
        }
    }

    /**
     * Clear product cached list by coupon group ID (Relation between product and coupon group)
     * @param int $iProductID
     */
    protected function clearCachedListByProduct($iProductID)
    {
        if (!Settings::getValue('check_offer_active') || empty($iProductID)) {
            return;
        }

        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_product')->where('product_id', $iProductID)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Offer::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return OfferItem::class;
    }
}
