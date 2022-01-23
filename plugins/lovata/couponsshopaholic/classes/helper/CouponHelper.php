<?php namespace Lovata\CouponsShopaholic\Classes\Helper;

use Input;
use October\Rain\Support\Traits\Singleton;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Classes\Store\OfferListStore;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

use Lovata\OrdersShopaholic\Models\OrderPromoMechanism;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

/**
 * Class CouponHelper
 * @package Lovata\CouponsShopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponHelper
{
    use Singleton;

    /** @var Coupon */
    protected $obCoupon;

    /** @var \Lovata\CouponsShopaholic\Models\CouponGroup */
    protected $obCouponGroup;

    /** @var \Lovata\CouponsShopaholic\Models\CouponGroup[]|\October\Rain\Database\Collection */
    protected $obActiveGroupList;

    protected function init()
    {
        $this->obActiveGroupList = CouponGroup::active()->currentActive()->get();
    }

    /**
     * Return true, if coupon is available
     * @param string $sCode
     * @param null   $iUserID
     * @return bool
     * @throws \Exception
     */
    public function check($sCode, $iUserID = null)
    {
        $sCode = trim($sCode);
        if (empty($sCode) || $this->obActiveGroupList->isEmpty()) {
            return false;
        }

        //Get coupon by code
        $this->obCoupon = Coupon::getByCode($sCode)->first();
        if (empty($this->obCoupon)) {
            return false;
        }

        $this->obCouponGroup = $this->obCoupon->coupon_group;
        if (empty($this->obCouponGroup)) {
            $this->obCoupon->delete();
            return false;
        }

        if (empty($this->obActiveGroupList->firstWhere('id', $this->obCouponGroup->id)) || empty($this->obCouponGroup->mechanism)) {
            return false;
        }

        $bResult = $this->checkByUserID($iUserID) && $this->checkByUsage($iUserID);

        return $bResult;
    }

    /**
     * Attach coupon to user cart
     * @param string $sCode
     * @return bool
     * @throws \Exception
     */
    public function addToCart($sCode)
    {
        $sCode = trim($sCode);

        $iUserID = UserHelper::instance()->getUserID();
        if (!$this->check($sCode, $iUserID)) {
            return false;
        }

        //Get cart object
        $obCart = CartProcessor::instance()->getCartObject();
        if (empty($obCart)) {
            return false;
        }

        //Get attached coupon list
        $arCouponIDList = (array) $obCart->coupon()->lists('id');
        if (in_array($this->obCoupon->id, $arCouponIDList)) {
            return true;
        }

        $obCart->coupon()->add($this->obCoupon);

        CartProcessor::instance()->updateCartData();

        return true;
    }

    /**
     * Detach coupon from cart
     * @param string $sCode
     * @return bool
     */
    public function removeFromCart($sCode)
    {
        $sCode = trim($sCode);
        if (empty($sCode)) {
            return false;
        }

        //Get cart object
        $obCart = CartProcessor::instance()->getCartObject();
        $obCoupon = Coupon::getByCode($sCode)->first();
        if (empty($obCart) || empty($obCoupon)) {
            return false;
        }

        $obCart->coupon()->remove($obCoupon);

        CartProcessor::instance()->updateCartData();

        return true;
    }

    /**
     * Detach all coupons form cart
     * @return bool
     */
    public function clearCouponList()
    {
        //Get cart object
        $obCart = CartProcessor::instance()->getCartObject();
        if (empty($obCart)) {
            return false;
        }

        $obCart->coupon()->detach();

        CartProcessor::instance()->updateCartData();

        return true;
    }

    /**
     * Get coupon list, attached to cart object
     * @return Coupon[]|null|\October\Rain\Database\Collection
     * @throws \Exception
     */
    public function getAppliedCouponList()
    {
        //Get cart object
        $obCart = CartProcessor::instance()->getCartObject();
        if (empty($obCart)) {
            return null;
        }

        $iUserID = UserHelper::instance()->getUserID();

        return $this->getCouponListFromCart($iUserID);
    }

    /**
     * Get coupon ID list, visible to user
     * @param int $iUserID
     * @return array
     */
    public function getVisibleIDListToUser($iUserID) : array
    {
        if ($this->obActiveGroupList->isEmpty()) {
            return [];
        }

        //Prepare query
        $obQuery = Coupon::with('coupon_group')->whereIn('group_id', $this->obActiveGroupList->lists('id'))->notHidden();
        if (!empty($iUserID)) {
            $obQuery->where(function ($obQuery) use ($iUserID) {
                /** @var Coupon $obQuery */
                $obQuery->whereNull('user_id')->orWhere('user_id', $iUserID);
            })->orderBy('user_id', 'desc');
        } else {
            $obQuery->whereNull('user_id');
        }

        //Get coupon ID list
        $obCouponList = $obQuery->get();
        if ($obCouponList->isEmpty()) {
            return [];
        }

        $arProcessedGroupID = [];
        $arResult = [];
        foreach ($obCouponList as $obCoupon) {
            if (in_array($obCoupon->group_id, $arProcessedGroupID) || $obCoupon->hidden) {
                continue;
            }

            $this->obCoupon = $obCoupon;
            $this->obCouponGroup = $obCoupon->coupon_group;
            if (!$this->checkByUsage($iUserID)) {
                continue;
            }

            $arProcessedGroupID[] = $obCoupon->group_id;
            $arResult[] = $obCoupon->id;
        }

        rsort($arResult);

        return $arResult;
    }

    /**
     * Attach coupons to Order
     * @param \Lovata\OrdersShopaholic\Models\Order $obOrder
     * @throws \Exception
     */
    public function attachCouponListToOrder($obOrder)
    {
        if (empty($obOrder)) {
            return;
        }

        //Cet coupon list
        $arCouponList = $this->getCouponList($obOrder->user_id);
        if (empty($arCouponList)) {
            return;
        }

        $arRelationData = [];

        //Process coupon list and attach coupon to Order model
        foreach ($arCouponList as $obCoupon) {
            $this->attachCouponToOrder($obOrder, $obCoupon);

            $arRelationData[$obCoupon->id] = ['code' => $obCoupon->code];
        }


        $obOrder->coupon()->sync($arRelationData);
    }

    /**
     * Return true, if coupon is available for user
     * @param int $iUserID
     * @return bool
     */
    protected function checkByUserID($iUserID)
    {
        if (empty($this->obCoupon)) {
            return false;
        }

        $bResult = empty($this->obCoupon->user_id) || (!empty($iUserID) && $iUserID == $this->obCoupon->user_id);

        return $bResult;
    }

    /**
     * Return true, if coupon group is
     * @param int $iUserID
     * @return bool
     */
    protected function checkByUsage($iUserID)
    {
        //Check coupon usage count
        if ($this->obCoupon->max_usage !== null) {
            $iMaxUsage = $this->obCoupon->max_usage;
        } elseif ($this->obCouponGroup->max_usage_per_user !== null) {
            $iMaxUsage = $this->obCouponGroup->max_usage_per_user;
        } else {
            $iMaxUsage = $this->obCouponGroup->max_usage;
        }

        if (!empty($iUserID)) {
            $iTotalUsage = $this->obCoupon->order()->getByUser($iUserID)->count();;
        } else {
            $iTotalUsage = $this->obCoupon->total_usage;
        }

        $bResult = $iMaxUsage == 0 || ($iMaxUsage > 0 && $iTotalUsage < $iMaxUsage);

        return $bResult;
    }

    /**
     * Get coupon list from cart and "coupon" request array
     * @param int $iUserID
     * @return array|\Lovata\CouponsShopaholic\Models\Coupon[]
     * @throws \Exception
     */
    protected function getCouponList($iUserID)
    {
        //Get coupon list from user cart
        $arResult = $this->getCouponListFromCart($iUserID);

        //Get coupon list from request
        $arRequestCouponList = $this->getCouponListFromRequest($iUserID);

        if (!empty($arRequestCouponList)) {
            foreach ($arRequestCouponList as $obCoupon) {
                $arResult[$obCoupon->id] = $obCoupon;
            }
        }

        return $arResult;
    }

    /**
     * Get coupon list from user cart
     * @param int $iUserID
     * @return array|\Lovata\CouponsShopaholic\Models\Coupon[]
     * @throws \Exception
     */
    protected function getCouponListFromCart($iUserID)
    {
        //Get cart object
        $obCart = CartProcessor::instance()->getCartObject();
        if (empty($obCart)) {
            return [];
        }

        //Get coupon collection
        $obCouponList = $obCart->coupon;
        if ($obCouponList->isEmpty()) {
            return [];
        }

        $arResult = [];

        //Process coupon collection and add to result array available coupons
        foreach ($obCouponList as $obCoupon) {
            if (!$this->check($obCoupon->code, $iUserID)) {
                continue;
            }

            $arResult[$obCoupon->id] = $obCoupon;
        }

        return $arResult;
    }

    /**
     * Get coupon list from user request
     * @param int $iUserID
     * @return array|\Lovata\CouponsShopaholic\Models\Coupon[]
     * @throws \Exception
     */
    protected function getCouponListFromRequest($iUserID)
    {
        $arCouponCodeList = Input::get('coupon');
        if (empty($arCouponCodeList)) {
            return [];
        }

        if (!is_array($arCouponCodeList)) {
            $arCouponCodeList = [$arCouponCodeList];
        }

        $arResult = [];

        //Process coupon collection and add to result array available coupons
        foreach ($arCouponCodeList as $sCouponCode) {
            $sCouponCode = trim($sCouponCode);
            if (empty($sCouponCode)) {
                continue;
            }

            if (!$this->check($sCouponCode, $iUserID)) {
                continue;
            }

            $obCoupon = Coupon::getByCode($sCouponCode)->first();
            if (empty($obCoupon)) {
                continue;
            }

            $arResult[$obCoupon->id] = $obCoupon;
        }

        return $arResult;
    }

    /**
     * Attach coupon object to order object
     * @@param \Lovata\OrdersShopaholic\Models\Order $obOrder
     * @param Coupon $obCoupon
     */
    protected function attachCouponToOrder($obOrder, $obCoupon)
    {
        if (empty($obOrder) || empty($obCoupon)) {
            return;
        }

        //Get coupon group
        $obCouponGroup = $obCoupon->coupon_group;
        if (empty($obCouponGroup)) {
            return;
        }

        //Get promo mechanism object
        $obPromoMechanism = $obCouponGroup->mechanism;
        if (empty($obPromoMechanism)) {
            return;
        }

        $arElementData = [
            'code' => $obCoupon->code,
        ];

        $arProductIDList = ProductListStore::instance()->coupon_group->get($obCouponGroup->id, true);

        $arOfferIDList = OfferListStore::instance()->coupon_group->get($obCouponGroup->id);;

        $arElementData['product_list'] = $arProductIDList;
        $arElementData['offer_list'] = $arOfferIDList;

        try {
            $arPromoMechanismData = [
                'order_id'       => $obOrder->id,
                'mechanism_id'   => $obPromoMechanism->id,
                'name'           => $obPromoMechanism->name,
                'type'           => $obPromoMechanism->type,
                'increase'       => $obPromoMechanism->increase,
                'priority'       => $obPromoMechanism->priority,
                'discount_value' => $obPromoMechanism->discount_value,
                'discount_type'  => $obPromoMechanism->discount_type,
                'final_discount' => $obPromoMechanism->final_discount,
                'property'       => $obPromoMechanism->property,
                'element_id'     => $obCoupon->id,
                'element_type'   => Coupon::class,
                'element_data'   => $arElementData,
            ];

            $obOrder->order_promo_mechanism()->add(OrderPromoMechanism::create($arPromoMechanismData));
        } catch (\Exception $obException) {
            return;
        }
    }
}