<?php namespace Lovata\PropertiesShopaholic\Models;

use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;

use Kharanenka\Scope\ExternalIDField;
use Lovata\Toolbox\Models\CommonProperty;
use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Shopaholic\Models\Measure;
use Lovata\PropertiesShopaholic\Classes\Import\ImportPropertyModelFromCSV;

/**
 * Class Property
 * @package Lovata\PropertiesShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                   $measure_id
 * @property string                                                $external_id
 *
 * Relations
 * @property Measure                                               $measure
 * @property \October\Rain\Database\Collection|PropertyValueLink[] $property_value_link
 * @property \October\Rain\Database\Collection|PropertyValue[] $property_value
 *
 * @method static \October\Rain\Database\Relations\HasMany|PropertyValueLink property_value_link()
 * @method static \October\Rain\Database\Relations\BelongsToMany|PropertyValue property_value()
 */
class Property extends CommonProperty
{
    use Sluggable;
    use Sortable;
    use ExternalIDField;
    use TraitCached;

    const SLUG_SEPARATOR = '';

    public $table = 'lovata_properties_shopaholic_properties';

    public $rules = [
        'name' => 'required',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
    ];

    protected $slugs = [];

    public $belongsTo = [
        'measure' => [Measure::class, 'order' => 'name asc'],
    ];

    public $hasMany = [
        'property_value_link' => [PropertyValueLink::class, 'key' => 'property_id'],
    ];

    public $belongsToMany = [
        'property_value' => [
            PropertyValue::class,
            'table'    => 'lovata_properties_shopaholic_variant_link',
            'key'      => 'property_id',
            'otherKey' => 'value_id',
        ],
    ];

    public $fillable = [
        'active',
        'name',
        'slug',
        'code',
        'type',
        'settings',
        'description',
        'sort_order',
        'measure_id',
        'external_id',
    ];

    public $cached = [
        'id',
        'name',
        'slug',
        'code',
        'type',
        'description',
        'measure_id',
        'settings',
    ];

    /**
     * Before save method
     */
    public function beforeSave()
    {
        $this->slug = $this->setSluggedValue('slug', 'name');
    }

    /**
     * Before delete method handler
     * @throws \Exception
     */
    public function beforeDelete()
    {
        PropertyValueLink::getByProperty($this->id)->delete();
    }

    /**
     * Get property variants from settings
     * @return array
     */
    public function getPropertyVariants()
    {
        $arValueList = (array) $this->property_value->lists('label', 'value');
        foreach ($arValueList as $sValue => &$sLabel) {
            if (empty($sLabel)) {
                $sLabel = (string) $sValue;
            }
        }

        natsort($arValueList);

        return $arValueList;
    }

    /**
     * Import item list from CSV file
     * @param array $arElementList
     * @param null  $sSessionKey
     * @throws \Throwable
     */
    public function importData($arElementList, $sSessionKey = null)
    {
        if (empty($arElementList)) {
            return;
        }

        $obImport = new ImportPropertyModelFromCSV();
        $obImport->setDeactivateFlag();

        foreach ($arElementList as $iKey => $arImportData) {
            $obImport->import($arImportData);
            $sResultMethod = $obImport->getResultMethod();
            if (in_array($sResultMethod, ['logUpdated', 'logCreated'])) {
                $this->$sResultMethod();
            } else {
                $sErrorMessage = $obImport->getResultError();
                $this->$sResultMethod($iKey, $sErrorMessage);
            }
        }

        $obImport->deactivateElements();
    }
}
