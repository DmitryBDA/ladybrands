<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class PropertyValueLinkTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA PropertyValueLink
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyValueLinkTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * PropertyValueLinkTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = PropertyValueLink::class;
        parent::__construct();
    }

    /**
     * Check model validation "rules" array
     */
    public function testValidationRulesArray()
    {
        /** @var PropertyValueLink $obModel */
        $obModel = new PropertyValueLink();
        self::assertNotEmpty($obModel->rules);
        self::assertArrayHasKey('value_id', $obModel->rules);
        self::assertEquals('required', $obModel->rules['value_id']);

        self::assertArrayHasKey('property_id', $obModel->rules);
        self::assertEquals('required', $obModel->rules['property_id']);

        self::assertArrayHasKey('product_id', $obModel->rules);
        self::assertEquals('required', $obModel->rules['product_id']);

        self::assertArrayHasKey('element_id', $obModel->rules);
        self::assertEquals('required', $obModel->rules['element_id']);

        self::assertArrayHasKey('element_type', $obModel->rules);
        self::assertEquals('required', $obModel->rules['element_type']);
    }

    /**
     * Check model "property" relation config
     */
    public function testHasPropertyRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "property" relation config';

        /** @var PropertyValueLink $obModel */
        $obModel = new PropertyValueLink();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('property', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Property::class, $obModel->belongsTo['property'][0], $sErrorMessage);
    }

    /**
     * Check model "value" relation config
     */
    public function testHasValueRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "value" relation config';

        /** @var PropertyValueLink $obModel */
        $obModel = new PropertyValueLink();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('value', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(PropertyValue::class, $obModel->belongsTo['value'][0], $sErrorMessage);
    }
}
