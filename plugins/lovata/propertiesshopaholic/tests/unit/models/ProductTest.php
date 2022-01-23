<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Models\CommonProperty;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class ProductTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductTest extends CommonTest
{
    protected $sModelClass;

    /** @var Product */
    protected $obProduct;

    /** @var Property */
    protected $obFirstProperty;

    /** @var Property */
    protected $obSecondProperty;

    /**
     * CategoryTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Category::class;
        parent::__construct();
    }

    /**
     * Check model "property_set" relation config
     */
    public function testHasPropertySetRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "property_set" relation config';

        /** @var Product $obModel */
        $obModel = new Product();
        self::assertNotEmpty($obModel->morphMany, $sErrorMessage);
        self::assertArrayHasKey('property_value', $obModel->morphMany, $sErrorMessage);
        self::assertEquals(PropertyValueLink::class, $obModel->morphMany['property_value'][0], $sErrorMessage);
    }

    /**
     * Test "property" attribute in model
     */
    public function testPropertyValueAttribute()
    {
        $this->createProductObject();
        $this->createPropertyObject();

        $arPropertyValueList = [
            $this->obFirstProperty->id  => 'test',
            $this->obSecondProperty->id => [
                'test',
                'test1',
            ],
        ];

        $this->obProduct->property = $arPropertyValueList;
        $this->obProduct->save();

        $this->obProduct = Product::find($this->obProduct->id);

        $arPropertyValue = $this->obProduct->property;

        self::assertEquals($arPropertyValueList, $arPropertyValue);

        $arValueList = PropertyValue::lists('value');

        self::assertEquals(['test', 'test1'], $arValueList);

        $arPropertyValueList = [
            $this->obFirstProperty->id  => 'test',
            $this->obSecondProperty->id => [
                'test',
            ],
        ];

        $this->obProduct->property = $arPropertyValueList;
        $this->obProduct->save();

        $this->obProduct = Product::find($this->obProduct->id);

        $arPropertyValue = $this->obProduct->property;

        self::assertEquals($arPropertyValueList, $arPropertyValue);

        $arValueList = PropertyValue::lists('value');

        self::assertEquals(['test'], $arValueList);

        $this->obProduct->property = [];
        $this->obProduct->save();

        $this->obProduct = Product::find($this->obProduct->id);

        $arPropertyValue = $this->obProduct->property;

        self::assertEquals([], $arPropertyValue);
    }

    /**
     * Create product object
     */
    protected function createProductObject()
    {
        $arElementData = [
            'name' => 'name',
            'slug' => 'slug',
        ];

        $this->obProduct = Product::create($arElementData);
    }

    /**
     * Create property objects
     */
    protected function createPropertyObject()
    {
        $arElementData = [
            'name' => 'name',
            'code' => 'code',
            'type' => CommonProperty::TYPE_INPUT,
        ];

        $this->obFirstProperty = Property::create($arElementData);

        $arElementData['type'] = CommonProperty::TYPE_CHECKBOX;
        $this->obSecondProperty = Property::create($arElementData);
    }
}
