<?php namespace Lovata\PropertiesShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Sortable;

use Kharanenka\Scope\CodeField;
use Kharanenka\Scope\NameField;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Shopaholic\Models\Category;

/**
 * Class PropertySet
 * @package Lovata\PropertiesShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                                              $id
 * @property bool                                                                             $is_global
 * @property string                                                                           $name
 * @property string                                                                           $code
 * @property string                                                                           $description
 * @property int                                                                              $sort_order
 * @property \October\Rain\Argon\Argon                                                        $created_at
 * @property \October\Rain\Argon\Argon                                                        $updated_at
 *
 * @property Category                                                                         $category
 * @method static Category|\October\Rain\Database\Relations\BelongsToMany category()
 *
 * @property \October\Rain\Database\Collection|\Lovata\PropertiesShopaholic\Models\Property[] $product_property
 * @method static \October\Rain\Database\Relations\BelongsToMany|Property product_property()
 *
 * @property \October\Rain\Database\Collection|\Lovata\PropertiesShopaholic\Models\Property[] $offer_property
 * @method static \October\Rain\Database\Relations\BelongsToMany|Property offer_property()
 *
 * @method static $this isGlobal()
 */
class PropertySet extends Model
{
    use NameField;
    use CodeField;
    use Validation;
    use Sortable;
    use TraitCached;

    public $table = 'lovata_properties_shopaholic_set';

    public $dates = ['created_at', 'updated_at'];

    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_properties_shopaholic_set',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    public $slugs = ['code' => 'name'];

    public $belongsToMany = [
        'category' => [
            Category::class,
            'table'    => 'lovata_properties_shopaholic_set_category_link',
            'key'      => 'set_id',
            'otherKey' => 'category_id',
        ],
    ];

    public $fillable = [
        'name',
        'code',
        'description',
        'sort_order',
    ];

    public $cached = [
        'id',
        'is_global',
        'name',
        'code',
        'description',
    ];

    /**
     * Get element with enabled is_global flag
     * @param PropertySet $obQuery
     * @return PropertySet
     */
    public function scopeIsGlobal($obQuery)
    {
        $obQuery->where('is_global', true);

        return $obQuery;
    }
}
