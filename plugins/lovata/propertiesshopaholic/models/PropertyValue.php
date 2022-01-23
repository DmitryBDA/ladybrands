<?php namespace Lovata\PropertiesShopaholic\Models;

use Str;
use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\SlugField;
use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Shopaholic\Models\Settings;

/**
 * Class PropertyValue
 * @package Lovata\PropertiesShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                           $id
 * @property string                    $value
 * @property string                    $slug
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @method static $this getByValue(string $sValue)
 * @method static $this likeByValue(string $sValue)
 * @property \October\Rain\Database\Collection|Property[] $property
 *
 * @method static \October\Rain\Database\Relations\BelongsToMany|Property property()
 */
class PropertyValue extends Model
{
    use SlugField;
    use Validation;
    use TraitCached;

    public $table = 'lovata_properties_shopaholic_values';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = ['value'];

    public $rules = [
        'value' => 'required',
        'slug'  => 'required|unique:lovata_properties_shopaholic_values',
    ];

    public $fillable = [
        'value',
        'slug',
    ];

    public $cached = [
        'id',
        'value',
        'slug',
    ];

    public $belongsToMany = [
        'property' => [
            Property::class,
            'table'    => 'lovata_properties_shopaholic_variant_link',
            'otherKey' => 'property_id',
            'key'      => 'value_id',
        ],
    ];

    /**
     * Before validate method
     */
    public function beforeValidate()
    {
        $this->slug = self::getSlugValue($this->value);
    }

    /**
     * Get element by value
     * @param PropertyValue $obQuery
     * @param string        $sData
     * @return PropertyValue
     */
    public function scopeGetByValue($obQuery, $sData)
    {
        if (self::hasValue($sData)) {
            $obQuery->where('value', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element like value
     * @param PropertyValue $obQuery
     * @param string        $sData
     * @return PropertyValue
     */
    public function scopeLikeByValue($obQuery, $sData)
    {
        if (self::hasValue($sData)) {
            $obQuery->where('value', 'like', '%'.$sData.'%');
        }

        return $obQuery;
    }

    /**
     * Get element by slug value
     * @param PropertyValue $obQuery
     * @param string        $sData
     * @return PropertyValue
     */
    public function scopeGetBySlug($obQuery, $sData)
    {
        if (self::hasValue($sData)) {
            $obQuery->where('slug', $sData);
        }

        return $obQuery;
    }

    /**
     * Check, value is not empty
     * @param string $sValue
     * @return bool
     */
    public static function hasValue($sValue)
    {
        $bResult = !empty($sValue) || $sValue === 0 || $sValue === '0';

        return $bResult;
    }

    /**
     * Get slug string from value string
     * @param string $sValue
     * @return string
     */
    public static function getSlugValue($sValue)
    {
        $bUseUrlencode = (bool) Settings::getValue('property_value_with_urlencode');
        $bNotUseStrSlug = (bool) Settings::getValue('property_value_without_str_slug');

        if ($bUseUrlencode) {
            $sValue = urlencode($sValue);
        }

        $sSlug = $sValue;
        if (!$bNotUseStrSlug) {
            $sSlug = str_replace([',', '.'], 'x', $sValue);
            $sSlug = Str::slug($sSlug, '');
        }

        $sSlug = (string) $sSlug;

        return $sSlug;
    }
}