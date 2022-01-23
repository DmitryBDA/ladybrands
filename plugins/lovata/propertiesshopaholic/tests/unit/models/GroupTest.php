<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\PropertiesShopaholic\Models\Group;
use Lovata\Toolbox\Traits\Tests\TestModelValidationNameField;

/**
 * Class GroupTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class GroupTest extends PluginTestCase
{
    use TestModelValidationNameField;

    protected $sModelClass;

    /**
     * GroupTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Group::class;
        parent::__construct();
    }

    /**
     * Check model "images" config
     */
    public function testHasValidationCodeField()
    {
        //Create model object
        /** @var Group $obModel */
        $obModel = new Group();

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
