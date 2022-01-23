<?php namespace Lovata\CouponsShopaholic\Classes\Event\PromoBlock;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\PromoBlock;
use Lovata\Shopaholic\Classes\Item\PromoBlockItem;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\CouponGroupListStore;
use Lovata\CouponsShopaholic\Classes\Collection\CouponGroupCollection;

/**
 * Class PromoBlockModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\PromoBlock
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PromoBlockModelHandler extends ModelHandler
{
    /** @var PromoBlock */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        PromoBlock::extend(function ($obElement) {
            $this->extendPromoBlockModel($obElement);
        });

        $obEvent->listen(PromoBlock::EVENT_GET_PRODUCT_LIST, function ($iPromoBlockID) {
            return $this->getProductIDList($iPromoBlockID);
        });
    }

    /**
     * Extend brand model
     * @param PromoBlock $obElement
     */
    protected function extendPromoBlockModel($obElement)
    {
        $obElement->hasMany['coupon_group'] = [CouponGroup::class];
    }

    /**
     * Get product ID list by relation between promo block and coupons
     * @param int $iPromoBlockID
     * @return array
     */
    protected function getProductIDList($iPromoBlockID) : array
    {
        $obCouponGroupList = CouponGroupCollection::make()->promoBlock($iPromoBlockID);
        if ($obCouponGroupList->isEmpty()) {
            return [];
        }

        $arResult = [];
        /** @var \Lovata\CouponsShopaholic\Classes\Item\CouponGroupItem $obCouponGroupItem */
        foreach ($obCouponGroupList as $obCouponGroupItem) {
            $arProductIDList = $obCouponGroupItem->product->getIDList();
            if (empty($arProductIDList)) {
                continue;
            }

            $arResult = array_merge($arResult, $arProductIDList);
        }

        $arResult = array_unique($arResult);

        return $arResult;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        CouponGroupListStore::instance()->promo_block->clear($this->obElement->id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PromoBlock::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return PromoBlockItem::class;
    }
}
