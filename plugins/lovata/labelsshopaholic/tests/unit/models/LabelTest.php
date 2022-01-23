<?php namespace Lovata\LabelsShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\LabelsShopaholic\Models\Label;
use Lovata\Shopaholic\Models\Product;
use Lovata\Toolbox\Traits\Tests\TestModelValidationNameField;

/**
 * Class LabelTest
 * @package Lovata\Shopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class LabelTest extends PluginTestCase
{
    use TestModelValidationNameField;

    protected $sModelClass;

    /**
     * LabelTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Label::class;
        parent::__construct();
    }

    /**
     * Check model "product" relation config
     */
    public function testHasProductRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "product" relation config';

        /** @var Label $obModel */
        $obModel = new Label();
        self::assertNotEmpty($obModel->belongsToMany, $sErrorMessage);
        self::assertArrayHasKey('product', $obModel->belongsToMany, $sErrorMessage);
        self::assertEquals(Product::class, array_shift($obModel->belongsToMany['product']), $sErrorMessage);
    }

    /**
     * Check model "images" config
     */
    public function testHasImage()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct image config';

        /** @var Label $obModel */
        $obModel = new $this->sModelClass();
        self::assertNotEmpty($obModel->attachOne, $sErrorMessage);
        self::assertArrayHasKey('image', $obModel->attachOne, $sErrorMessage);
        self::assertEquals('System\Models\File', $obModel->attachOne['image'], $sErrorMessage);
    }

    /**
     * Check model "slug" config
     */
    public function testHasValidationSlugField()
    {
        //Create model object
        /** @var Label $obModel */
        $obModel = new $this->sModelClass();

        //Get validation rules array and check it
        $arValidationRules = $obModel->rules;
        self::assertNotEmpty($arValidationRules, $this->sModelClass.' model has empty validation rules array');

        //Check rules for "code" field
        self::assertArrayHasKey('code', $arValidationRules, $this->sModelClass.' model not has validation rules for field "code"');
        self::assertNotEmpty($arValidationRules['code'], $this->sModelClass.' model not has validation rules for field "code"');

        $arValidationCondition = explode('|', $arValidationRules['code']);
        self::assertContains('required', $arValidationCondition,$this->sModelClass.' model not has validation rule "required" for field "code"');
        self::assertContains('unique:'.$obModel->table, $arValidationCondition,$this->sModelClass.' model not has validation rule "unique" for field "code"');
    }
}