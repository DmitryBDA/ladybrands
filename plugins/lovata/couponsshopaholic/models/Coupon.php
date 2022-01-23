<?php namespace Lovata\CouponsShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\CodeField;
use Kharanenka\Scope\HiddenField;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class Coupon
 * @package Lovata\CouponsShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                       $id
 * @property int                                       $group_id
 * @property string                                    $code
 * @property bool                                      $hidden
 * @property int                                       $user_id
 * @property int                                       $max_usage
 * @property int                                       $total_usage
 *
 * @property \October\Rain\Argon\Argon                 $created_at
 * @property \October\Rain\Argon\Argon                 $updated_at
 *
 * @property CouponGroup                               $coupon_group
 * @method static CouponGroup|\October\Rain\Database\Relations\BelongsTo coupon_group()
 * @property Order[]|\October\Rain\Database\Collection $order
 * @method static Order|\October\Rain\Database\Relations\BelongsToMany order()
 * @property Cart[]|\October\Rain\Database\Collection  $cart
 * @method static Cart|\October\Rain\Database\Relations\BelongsToMany cart()
 *
 * @method static $this getByGroup($iGroupID)
 * @method static $this hidden()
 * @method static $this notHidden()
 */
class Coupon extends Model
{
    use Validation;
    use CodeField;
    use HiddenField;
    use TraitCached;

    public $table = 'lovata_coupons_shopaholic_coupons';

    public $rules = [
        'group_id' => 'required',
        'code'     => 'required|unique:lovata_coupons_shopaholic_coupons',
    ];

    public $attributeNames = [
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    public $casts = [
        'max_usage' => 'integer',
    ];

    public $fillable = [
        'group_id',
        'code',
        'hidden',
        'user_id',
        'max_usage',
    ];

    public $cached = [
        'id',
        'group_id',
        'code',
    ];

    public $dates = ['created_at', 'updated_at'];

    public $belongsTo = [
        'coupon_group' => [
            CouponGroup::class,
            'key' => 'group_id',
        ],
    ];

    public $belongsToMany = [
        'order' => [
            Order::class,
            'table' => 'lovata_coupons_shopaholic_order_coupon',
            'pivot' => ['code'],
        ],
        'cart'  => [
            Cart::class,
            'table' => 'lovata_coupons_shopaholic_coupon_cart',
        ],
    ];

    /**
     * Get total coupon usages per user ID
     * @param int $iUserID
     * @return int
     */
    public function getUsagePerUser($iUserID = null)
    {
        if (empty($iUserID)) {
            $iUserID = $this->user_id;
        }

        if (empty($iUserID)) {
            return 0;
        }

        $iCount = 0;

        //Get order list
        $obOrderList = $this->order;
        foreach ($obOrderList as $obOrder) {
            if ($obOrder->user_id != $iUserID) {
                continue;
            }

            $iCount++;
        }

        return $iCount;
    }

    /**
     * Get hidden elements
     * @param Coupon $obQuery
     * @return Coupon
     */
    public function scopeHidden($obQuery)
    {
        return $obQuery->where('hidden', true);
    }

    /**
     * Get elements by group ID
     * @param Coupon $obQuery
     * @param string $sData
     * @return Coupon
     */
    public function scopeGetByGroup($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('group_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get not hidden elements
     * @param Coupon $obQuery
     * @return Coupon
     */
    public function scopeNotHidden($obQuery)
    {
        return $obQuery->where('hidden', false);
    }

    /**
     * Get total coupon usages
     * @return int
     */
    protected function getTotalUsageAttribute()
    {
        $iCount = $this->order()->count();

        return $iCount;
    }

    /**
     * Set code attribute value
     * @param string $sValue
     */
    protected function setCodeAttribute($sValue)
    {
        $this->attributes['code'] = trim($sValue);
    }

    /**
     * Set max usage attribute value
     * @param string $sValue
     */
    protected function setMaxUsageAttribute($sValue)
    {
        if ($sValue === '') {
            $sValue = null;
        }

        $this->attributes['max_usage'] = $sValue;
    }
}
