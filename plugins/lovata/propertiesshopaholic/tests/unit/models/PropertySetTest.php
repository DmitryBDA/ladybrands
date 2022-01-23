<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;
use Lovata\Toolbox\Traits\Tests\TestModelValidationNameField;

use Lovata\Shopaholic\Models\Category;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;

/**
 * Class PropertySetTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertySetTest extends CommonTest
{
    use TestModelValidationNameField;

    protected $sModelClass;

    /**
     * CategoryTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = PropertySet::class;
        parent::__construct();
    }

    /**
     * Check model "code" config
     */
    public function testHasValidationCodeField()
    {
        //Create model object
        /** @var PropertySet $obModel */
        $obModel = new PropertySet();

        //Get validation rules array and check it
        $arValidationRules = $obModel->rules;
        self::assertNotEmpty($arValidationRules, $this->sModelClass.' model has empty validation rules array');

        //Check rules for "slug" field
        self::assertArrayHasKey('code', $arValidationRules, $this->sModelClass.' model not has validation rules for field "code"');
        self::assertNotEmpty($arValidationRules['code'], $this->sModelClass.' model not has validation rules for field "code"');

        $arValidationCondition = explode('|', $arValidationRules['code']);
        self::assertContains('required', $arValidationCondition,$this->sModelClass.' model not has validation rule "required" for field "code"');
        self::assertContains('unique:'.$obModel->table, $arValidationCondition,$this->sModelClass.' model not has validation rule "unique" for field "code"');
    }

    /**
     * Check model "product_property" relation config
     */
    public function testHasProductPropertyRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "product_property" relation config';

        /** @var PropertySet $obModel */
        $obModel = new PropertySet();
        self::assertNotEmpty($obModel->belongsToMany, $sErrorMessage);
        self::assertArrayHasKey('product_property', $obModel->belongsToMany, $sErrorMessage);
        self::assertEquals(Property::class, $obModel->belongsToMany['product_property'][0], $sErrorMessage);
    }

    /**
     * Check model "offer_property" relation config
     */
    public function testHasOfferPropertyRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "offer_property" relation config';

        /** @var PropertySet $obModel */
        $obModel = new PropertySet();
        self::assertNotEmpty($obModel->belongsToMany, $sErrorMessage);
        self::assertArrayHasKey('offer_property', $obModel->belongsToMany, $sErrorMessage);
        self::assertEquals(Property::class, $obModel->belongsToMany['offer_property'][0], $sErrorMessage);
    }

    /**
     * Check model "offer_property" relation config
     */
    public function testHasCategoryRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "category" relation config';

        /** @var PropertySet $obModel */
        $obModel = new PropertySet();
        self::assertNotEmpty($obModel->belongsToMany, $sErrorMessage);
        self::assertArrayHasKey('category', $obModel->belongsToMany, $sErrorMessage);
        self::assertEquals(Category::class, $obModel->belongsToMany['category'][0], $sErrorMessage);
    }
}
