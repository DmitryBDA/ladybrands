<?php namespace Lovata\CouponsShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\CouponsShopaholic\Models\Coupon;

/**
 * Class CouponItem
 * @package Lovata\CouponsShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int             $id
 * @property int             $group_id
 * @property string          $code
 * @property CouponGroupItem $group
 */
class CouponItem extends ElementItem
{
    const MODEL_CLASS = Coupon::class;

    public $arRelationList = [
        'group' => [
            'class' => CouponGroupItem::class,
            'field' => 'group_id',
        ],
    ];

    /** @var Coupon */
    protected $obElement = null;
}
