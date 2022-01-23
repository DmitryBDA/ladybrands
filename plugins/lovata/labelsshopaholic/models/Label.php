<?php namespace Lovata\LabelsShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\SlugField;
use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\CodeField;
use Kharanenka\Scope\NameField;
use Kharanenka\Scope\ExternalIDField;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Shopaholic\Models\Product;

/**
 * Class Label
 * @package Lovata\LabelsShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                             $id
 * @property bool                                        $active
 * @property string                                      $name
 * @property string                                      $slug
 * @property string                                      $code
 * @property string                                      $external_id
 * @property string                                      $description
 * @property int                                         $sort_order
 *
 * @property \October\Rain\Argon\Argon                   $created_at
 * @property \October\Rain\Argon\Argon                   $updated_at
 *
 * @property \October\Rain\Database\Collection|Product[] $product
 * @method static \October\Rain\Database\Relations\BelongsToMany|Product product()
 *
 * @property \System\Models\File                         $image
 */
class Label extends Model
{
    use Validation;
    use TraitCached;
    use Sortable;
    use Sluggable;
    use ExternalIDField;
    use CodeField;
    use NameField;
    use ActiveField;
    use SlugField;

    public $table = 'lovata_labels_shopaholic_labels';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = ['name', 'description'];

    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:lovata_labels_shopaholic_labels',
        'code' => 'required|unique:lovata_labels_shopaholic_labels',
    ];

    public $slugs = ['slug' => 'name'];
    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'slug' => 'lovata.toolbox::lang.field.slug',
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    public $fillable = [
        'active',
        'name',
        'slug',
        'code',
        'external_id',
        'description',
        'sort_order',
        'image',
    ];

    public $cached = [
        'id',
        'name',
        'slug',
        'code',
        'description',
        'image',
    ];

    public $dates = ['created_at', 'updated_at',];
    public $attachOne = ['image' => 'System\Models\File'];

    public $belongsToMany = [
        'product' => [Product::class, 'table' => 'lovata_labels_shopaholic_product_label'],
    ];

    /**
     * Before validate event handler
     */
    public function beforeValidate()
    {
        if (empty($this->slug)) {
            $this->slugAttributes();
        }
    }
}
