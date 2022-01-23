<?php namespace Lovata\DiscountsShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\DiscountsShopaholic\Classes\Item\DiscountItem;
use Lovata\DiscountsShopaholic\Classes\Store\DiscountListStore;

/**
 * Class DiscountCollection
 * @package Lovata\DiscountsShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class DiscountCollection extends ElementCollection
{
    const ITEM_CLASS = DiscountItem::class;

    /**
     * Apply filter by promo block ID
     * @param int $iPromoBlockID
     * @return $this
     */
    public function promoBlock($iPromoBlockID)
    {
        $arElementIDList = DiscountListStore::instance()->promo_block->get($iPromoBlockID);

        return $this->intersect($arElementIDList);
    }
}
