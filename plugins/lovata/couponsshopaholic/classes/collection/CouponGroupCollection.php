<?php namespace Lovata\CouponsShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\CouponsShopaholic\Classes\Item\CouponGroupItem;
use Lovata\CouponsShopaholic\Classes\Store\CouponGroupListStore;

/**
 * Class CouponGroupCollection
 * @package Lovata\CouponsShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponGroupCollection extends ElementCollection
{
    const ITEM_CLASS = CouponGroupItem::class;

    /**
     * Apply filter by promo block ID
     * @param int $iPromoBlockID
     * @return $this
     */
    public function promoBlock($iPromoBlockID)
    {
        $arElementIDList = CouponGroupListStore::instance()->promo_block->get($iPromoBlockID);

        return $this->intersect($arElementIDList);
    }
}
