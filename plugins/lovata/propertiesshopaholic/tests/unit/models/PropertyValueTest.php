<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;
use Lovata\Toolbox\Traits\Tests\TestModelValidationSlugField;

use Lovata\PropertiesShopaholic\Models\PropertyValue;

/**
 * Class PropertyValueTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyValueTest extends CommonTest
{
    use TestModelValidationSlugField;

    protected $sModelClass;

    /**
     * CategoryTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = PropertyValue::class;
        parent::__construct();
    }
    
    /**
     * Check model "images" config
     */
    public function testHasValidationValueField()
    {
        //Create model object
        /** @var \Model $obModel */
        $obModel = new $this->sModelClass();

        //Get validation rules array and check it
        $arValidationRules = $obModel->rules;
        self::assertNotEmpty($arValidationRules, $this->sModelClass.' model has empty validation rules array');

        //Check rules for "value" field
        self::assertArrayHasKey('value', $arValidationRules, $this->sModelClass.' model not has validation rules for field "value"');
        self::assertNotEmpty($arValidationRules['value'], $this->sModelClass.' model not has validation rules for field "value"');

        $arValidationCondition = explode('|', $arValidationRules['value']);
        self::assertContains('required', $arValidationCondition,$this->sModelClass.' model not has validation rule "required" for field "value"');
    }
}
