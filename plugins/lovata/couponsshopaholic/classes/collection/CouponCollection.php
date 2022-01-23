<?php namespace Lovata\CouponsShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\CouponsShopaholic\Classes\Item\CouponItem;
use Lovata\CouponsShopaholic\Classes\Helper\CouponHelper;
use Lovata\CouponsShopaholic\Classes\Store\CouponListStore;

/**
 * Class CouponCollection
 * @package Lovata\CouponsShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponCollection extends ElementCollection
{
    const ITEM_CLASS = CouponItem::class;

    /**
     * Apply filter by coupon group ID
     * @param int $iGroupID
     * @return $this
     */
    public function group($iGroupID)
    {
        $arElementIDList = CouponListStore::instance()->group->get($iGroupID);

        return $this->intersect($arElementIDList);
    }

    /**
     * Apply filter by hidden field
     * @return $this
     */
    public function hidden()
    {
        $arResultIDList = CouponListStore::instance()->hidden->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * Apply filter by hidden field
     * @return $this
     */
    public function notHidden()
    {
        $arResultIDList = CouponListStore::instance()->not_hidden->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * Get visible coupons for user
     * @param int $iUserID
     * @return $this
     */
    public function visibleToUser($iUserID)
    {
        $arResultIDList = CouponHelper::instance()->getVisibleIDListToUser($iUserID);

        return $this->intersect($arResultIDList);
    }
}
