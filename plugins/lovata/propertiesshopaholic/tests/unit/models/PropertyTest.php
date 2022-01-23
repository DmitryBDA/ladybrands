<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\Shopaholic\Models\Measure;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\Toolbox\Traits\Tests\TestModelValidationNameField;

/**
 * Class PropertyTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyTest extends PluginTestCase
{
    use TestModelValidationNameField;

    protected $sModelClass;

    /**
     * PropertyTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Property::class;
        parent::__construct();
    }

    /**
     * Check model "measure" relation config
     */
    public function testHasMeasureRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "measure" relation config';

        /** @var Property $obModel */
        $obModel = new Property();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('measure', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Measure::class, $obModel->belongsTo['measure'][0], $sErrorMessage);
    }

    /**
     * Check model "property_value_link" relation config
     */
    public function testHasPropertyValueLinkRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "property_value_link" relation config';

        /** @var Property $obModel */
        $obModel = new Property();
        self::assertNotEmpty($obModel->hasMany, $sErrorMessage);
        self::assertArrayHasKey('property_value_link', $obModel->hasMany, $sErrorMessage);
        self::assertEquals(PropertyValueLink::class, $obModel->hasMany['property_value_link'][0], $sErrorMessage);
    }
}
