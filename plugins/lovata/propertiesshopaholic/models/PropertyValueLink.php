<?php namespace Lovata\PropertiesShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Validation;

use Lovata\Shopaholic\Models\Product;

/**
 * Class PropertyValueLink
 * @package Lovata\PropertiesShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                           $id
 * @property int                       $value_id
 * @property int                       $property_id
 * @property int                       $product_id
 * @property int                       $element_id
 * @property string                    $element_type
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property PropertyValue             $value
 * @property Property                  $property
 * @property Product                   $product
 * @property Product|\Lovata\Shopaholic\Models\Offer $element
 *
 * @method static \October\Rain\Database\Relations\BelongsTo|PropertyValue value()
 * @method static \October\Rain\Database\Relations\BelongsTo|Property property()
 * @method static \October\Rain\Database\Relations\BelongsTo|Product product()
 *
 * @method static $this getByValue(int $iValueID)
 * @method static $this getByProperty(int $iPropertyID)
 * @method static $this getByProduct(int $iProductID)
 * @method static $this getByElementID(int $iElementID)
 * @method static $this getByElementType(string $sElementType)
 */
class PropertyValueLink extends Model
{
    use Validation;

    public $table = 'lovata_properties_shopaholic_value_link';

    public $belongsTo = [
        'value'    => [PropertyValue::class],
        'property' => [Property::class],
        'product'  => [Product::class],
    ];

    public $rules = [
        'value_id'     => 'required',
        'property_id'  => 'required',
        'product_id'   => 'required',
        'element_id'   => 'required',
        'element_type' => 'required',
    ];

    public $fillable = [
        'value_id',
        'property_id',
        'product_id',
        'element_id',
        'element_type',
    ];

    public $morphTo = [
        'element' => [],
    ];

    /**
     * Get element by value ID
     * @param PropertyValue $obQuery
     * @param int           $iElementID
     * @return PropertyValue
     */
    public function scopeGetByValue($obQuery, $iElementID)
    {
        if (!empty($iElementID)) {
            $obQuery->where('value_id', $iElementID);
        }

        return $obQuery;
    }

    /**
     * Get property by property ID
     * @param PropertyValue $obQuery
     * @param int           $iElementID
     * @return PropertyValue
     */
    public function scopeGetByProperty($obQuery, $iElementID)
    {
        if (!empty($iElementID)) {
            $obQuery->where('property_id', $iElementID);
        }

        return $obQuery;
    }

    /**
     * Get property by product ID
     * @param PropertyValue $obQuery
     * @param int           $iElementID
     * @return PropertyValue
     */
    public function scopeGetByProduct($obQuery, $iElementID)
    {
        if (!empty($iElementID)) {
            $obQuery->where('product_id', $iElementID);
        }

        return $obQuery;
    }

    /**
     * Get element by item ID
     * @param PropertyValueLink $obQuery
     * @param string       $sData
     * @return PropertyValueLink
     */
    public function scopeGetByElementID($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('element_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element by item type
     * @param PropertyValueLink $obQuery
     * @param string       $sData
     * @return PropertyValueLink
     */
    public function scopeGetByElementType($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('element_type', $sData);
        }

        return $obQuery;
    }
}
