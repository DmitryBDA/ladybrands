<?php namespace Lovata\PropertiesShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Sortable;

use Kharanenka\Scope\NameField;
use Kharanenka\Scope\CodeField;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

/**
 * Class Group
 * @package Lovata\PropertiesShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $description
 * @property int $sort_order
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 */
class Group extends Model
{
    use Sluggable;
    use NameField;
    use CodeField;
    use Validation;
    use Sortable;
    use TraitCached;

    public $table = 'lovata_properties_shopaholic_groups';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = ['name','description'];
    
    public $dates = ['created_at', 'updated_at'];

    /** @var array Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_properties_shopaholic_groups',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    public $slugs = ['code' => 'name'];

    public $fillable = [
        'name',
        'code',
        'description',
        'sort_order',
    ];

    public $cached = [
        'id',
        'name',
        'code',
        'description',
    ];
}
