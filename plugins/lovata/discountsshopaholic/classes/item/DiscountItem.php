<?php namespace Lovata\DiscountsShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Classes\Item\PromoBlockItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class DiscountItem
 * @package Lovata\DiscountsShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                             $id
 * @property string                                                          $name
 * @property string                                                          $code
 * @property float                                                           $discount_value
 * @property string                                                          $discount_type
 * @property \October\Rain\Argon\Argon                                       $date_begin
 * @property \October\Rain\Argon\Argon                                       $date_end
 * @property int                                                             $promo_block_id
 * @property string                                                          $preview_text
 * @property string                                                          $description
 * @property PromoBlockItem                                                  $promo_block
 * @property ProductCollection|\Lovata\Shopaholic\Classes\Item\ProductItem[] $product
 */
class DiscountItem extends ElementItem
{
    const MODEL_CLASS = Discount::class;

    public $arRelationList = [
        'promo_block' => [
            'class' => PromoBlockItem::class,
            'field' => 'promo_block_id',
        ],
    ];

    /** @var Discount */
    protected $obElement = null;

    /**
     * Get product collection attribute
     * @return ProductCollection
     */
    protected function getProductAttribute() : ProductCollection
    {
        $obProductList = ProductCollection::make()->discount($this->id);

        return $obProductList;
    }
}
