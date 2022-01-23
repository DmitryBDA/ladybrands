<?php namespace Lovata\CouponsShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Classes\Item\PromoBlockItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\CouponsShopaholic\Models\CouponGroup;

/**
 * Class CouponGroupItem
 * @package Lovata\CouponsShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                             $id
 * @property int                                                             $name
 * @property \October\Rain\Argon\Argon                                       $date_begin
 * @property \October\Rain\Argon\Argon                                       $date_end
 * @property int                                                             $promo_block_id
 * @property int                                                             $discount_value
 * @property string                                                          $discount_type
 * @property PromoBlockItem                                                  $promo_block
 * @property ProductCollection|\Lovata\Shopaholic\Classes\Item\ProductItem[] $product
 */
class CouponGroupItem extends ElementItem
{
    const MODEL_CLASS = CouponGroup::class;

    public static $arQueryWith = [
        'mechanism'
    ];

    public $arRelationList = [
        'promo_block' => [
            'class' => PromoBlockItem::class,
            'field' => 'promo_block_id',
        ],
    ];

    /** @var CouponGroup */
    protected $obElement = null;

    /**
     * Get product collection attribute
     * @return ProductCollection
     */
    protected function getProductAttribute() : ProductCollection
    {
        $obProductList = ProductCollection::make()->couponGroup($this->id);

        return $obProductList;
    }

    /**
     * Get element data
     * @return array
     */
    protected function getElementData()
    {
        $arResult = [];
        $obPromoMechanism = $this->obElement->mechanism;
        if (!empty($obPromoMechanism)) {
            $arResult['discount_value'] = $obPromoMechanism->discount_value;
            $arResult['discount_type'] = $obPromoMechanism->discount_type;
        }

        return $arResult;
    }
}
