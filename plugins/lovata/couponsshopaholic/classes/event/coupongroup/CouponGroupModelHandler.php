<?php namespace Lovata\CouponsShopaholic\Classes\Event\CouponGroup;

use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\ModelHandler;
use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Item\CouponGroupItem;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;
use Lovata\CouponsShopaholic\Classes\Store\CouponGroupListStore;

/**
 * Class CouponModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\CouponGroup
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponGroupModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var CouponGroup */
    protected $obElement;

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('promo_block_id', CouponGroupListStore::instance()->promo_block);
        $this->checkFieldChanges('active', CouponGroupListStore::instance()->promo_block);
    }

    /**
     * After delete event handler
     * @throws \Exception
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        CouponGroupListStore::instance()->promo_block->clear($this->obElement->promo_block_id);

        $this->obElement->brand()->detach();
        $this->obElement->category()->detach();
        $this->obElement->offer()->detach();
        $this->obElement->product()->detach();
        $this->obElement->shipping_type()->detach();

        $obCouponList = $this->obElement->coupon;
        if (!$obCouponList->isEmpty()) {
            foreach ($obCouponList as $obCoupon) {
                $obCoupon->delete();
            }
        }

        ProductListStore::instance()->coupon_group->clear($this->obElement->id);
        ProductListStore::instance()->coupon_group->clear($this->obElement->id, true);

        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        $this->obElement->tag()->detach();
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return CouponGroup::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return CouponGroupItem::class;
    }
}
