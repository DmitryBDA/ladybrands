<?php namespace Lovata\DiscountsShopaholic\Models;

use Lang;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\NameField;
use Kharanenka\Scope\CodeField;
use Kharanenka\Scope\ExternalIDField;
use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Models\PromoBlock;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Offer;

/**
 * Class Discount
 * @package Lovata\DiscountsShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                                   $id
 * @property int                                                                   $promo_block_id
 * @property bool                                                                  $active
 * @property string                                                                $name
 * @property string                                                                $code
 * @property string                                                                $external_id
 * @property \October\Rain\Argon\Argon                                             $date_begin
 * @property \October\Rain\Argon\Argon                                             $date_end
 * @property float                                                                 $discount_value
 * @property string                                                                $discount_type
 * @property string                                                                $preview_text
 * @property string                                                                $description
 * @property int                                                                   $sort_order
 * @property \October\Rain\Argon\Argon                                             $created_at
 * @property \October\Rain\Argon\Argon                                             $updated_at
 *
 * @property PromoBlock                                                            $promo_block
 * @method static PromoBlock|\October\Rain\Database\Relations\BelongsTo promo_block()
 * @property Product[]|\October\Rain\Database\Collection                           $product
 * @method static Product|\October\Rain\Database\Relations\BelongsToMany product()
 * @property Offer[]|\October\Rain\Database\Collection                             $offer
 * @method static Offer|\October\Rain\Database\Relations\BelongsToMany offer()
 * @property Brand[]|\October\Rain\Database\Collection                             $brand
 * @method static Brand|\October\Rain\Database\Relations\BelongsToMany brand()
 * @property Category[]|\October\Rain\Database\Collection                          $category
 * @method static Category|\October\Rain\Database\Relations\BelongsToMany category()
 * @property \Lovata\TagsShopaholic\Models\Tag[]|\October\Rain\Database\Collection $tag
 * @method static \Lovata\TagsShopaholic\Models\Tag|\October\Rain\Database\Relations\BelongsToMany tag()
 *
 * @method static $this currentActive()
 * @method static $this getByPromoBlock($iPromoBlock)
 */
class Discount extends Model
{
    use Validation;
    use Sortable;
    use CodeField;
    use NameField;
    use ExternalIDField;
    use ActiveField;
    use TraitCached;

    const FIXED_TYPE = 'fixed';
    const PERCENT_TYPE = 'percent';

    public $table = 'lovata_discounts_shopaholic_discounts';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = ['name', 'preview_text', 'description'];

    public $rules = [
        'date_begin'     => 'required',
        'discount_type'  => 'required',
        'discount_value' => 'required',
        'name'           => 'required',
    ];

    public $attributeNames = [
        'name'           => 'lovata.toolbox::lang.field.name',
        'date_begin'     => 'lovata.discountsshopaholic::lang.field.date_begin',
        'discount_type'  => 'lovata.discountsshopaholic::lang.field.discount_type',
        'discount_value' => 'lovata.discountsshopaholic::lang.field.discount_value',
    ];

    public $attachOne = [];
    public $attachMany = [];

    public $fillable = [
        'active',
        'name',
        'code',
        'external_id',
        'date_begin',
        'date_end',
        'discount_value',
        'discount_type',
        'preview_text',
        'description',
        'sort_order',
    ];

    public $cached = [
        'id',
        'active',
        'name',
        'code',
        'date_begin',
        'date_end',
        'discount_value',
        'discount_type',
        'preview_text',
        'description',
        'promo_block_id',
    ];

    public $dates = ['created_at', 'updated_at', 'date_begin', 'date_end'];

    public $belongsToMany = [
        'product'  => [
            Product::class,
            'table' => 'lovata_discounts_shopaholic_discount_product',
        ],
        'offer'    => [
            Offer::class,
            'table' => 'lovata_discounts_shopaholic_discount_offer',
        ],
        'brand'    => [
            Brand::class,
            'table' => 'lovata_discounts_shopaholic_discount_brand',
        ],
        'category' => [
            Category::class,
            'table' => 'lovata_discounts_shopaholic_discount_category',
        ],
    ];

    public $belongsTo = [
        'promo_block'  => [PromoBlock::class],
    ];


    /**
     * Get active elements
     * @param Discount $obQuery
     * @return Discount
     */
    public function scopeCurrentActive($obQuery)
    {
        $sDateNow = Argon::now()->toDateTimeString();

        return $obQuery->where('date_begin', '<=', $sDateNow)
            ->where(function ($obQuery) use ($sDateNow) {
                /** @var Discount $obQuery */
                $obQuery->whereNull('date_end')->orWhere('date_end', '>', $sDateNow);
            });
    }

    /**
     * Get elements by promo block ID
     * @param Discount $obQuery
     * @param string $sData
     * @return Discount
     */
    public function scopeGetByPromoBlock($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('promo_block_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get discount type options
     * @return array
     */
    public function getDiscountTypeOptions() : array
    {
        return [
            self::PERCENT_TYPE => Lang::get('lovata.discountsshopaholic::lang.type.'.self::PERCENT_TYPE),
            self::FIXED_TYPE   => Lang::get('lovata.discountsshopaholic::lang.type.'.self::FIXED_TYPE),
        ];
    }

    /**
     * Set discount value from string to float
     * @param $sValue
     */
    protected function setDiscountValueAttribute($sValue)
    {
        $fValue = PriceHelper::toFloat($sValue);
        if ($this->discount_type == self::PERCENT_TYPE && $fValue > 100) {
            $fValue = 100;
        }

        $this->attributes['discount_value'] = $fValue;
    }
}
