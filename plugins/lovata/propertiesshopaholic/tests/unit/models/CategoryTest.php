<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Models;

use Lovata\PropertiesShopaholic\Classes\Event\CategoryModelHandler;
use October\Rain\Database\Collection;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Category;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;

/**
 * Class CategoryTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CategoryTest extends CommonTest
{
    protected $sModelClass;

    /** @var Category */
    protected $obCategory;

    /** @var PropertySet */
    protected $obFirstPropertySet;

    /** @var PropertySet */
    protected $obSecondPropertySet;

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

        /** @var Category $obModel */
        $obModel = new Category();
        self::assertNotEmpty($obModel->belongsToMany, $sErrorMessage);
        self::assertArrayHasKey('property_set', $obModel->belongsToMany, $sErrorMessage);
        self::assertEquals(PropertySet::class, $obModel->belongsToMany['property_set'][0], $sErrorMessage);
    }

    /**
     * Test product property attribute
     */
    public function testEmptyProductPropertyAttribute()
    {
        $this->createCategoryObject();
        $this->createPropertySetObject();

        $obPropertyList = $this->obCategory->product_property;

        self::assertInstanceOf(Collection::class, $obPropertyList);
        self::assertEquals(true, $obPropertyList->isEmpty());
    }

    /**
     * Test product property attribute
     */
    public function testProductPropertyAttribute()
    {
        $this->createCategoryObject();
        $this->createPropertySetObject();

        $this->obFirstPropertySet->product_property()->add($this->obFirstProperty);
        $this->obSecondPropertySet->product_property()->add($this->obSecondProperty);

        $this->obCategory->property_set()->add($this->obFirstPropertySet);
        $this->obCategory->property_set()->add($this->obSecondPropertySet);

        $obPropertyList = $this->obCategory->product_property;
        self::assertInstanceOf(Collection::class, $obPropertyList);
        self::assertEquals([$this->obFirstProperty->id, $this->obSecondProperty->id], $obPropertyList->lists('id'));
    }

    /**
     * Test offer property attribute
     */
    public function testEmptyOfferPropertyAttribute()
    {
        $this->createCategoryObject();
        $this->createPropertySetObject();

        $obPropertyList = $this->obCategory->offer_property;

        self::assertInstanceOf(Collection::class, $obPropertyList);
        self::assertEquals(true, $obPropertyList->isEmpty());
    }

    /**
     * Test offer property attribute
     */
    public function testOfferPropertyAttribute()
    {
        $this->createCategoryObject();
        $this->createPropertySetObject();

        $this->obFirstPropertySet->offer_property()->attach($this->obFirstProperty->id);
        $this->obSecondPropertySet->offer_property()->attach($this->obSecondProperty->id);

        $this->obCategory->property_set()->attach($this->obFirstPropertySet->id);
        $this->obCategory->property_set()->attach($this->obSecondPropertySet->id);

        $obPropertyList = $this->obCategory->offer_property;
        self::assertInstanceOf(Collection::class, $obPropertyList);
        self::assertEquals([$this->obFirstProperty->id, $this->obSecondProperty->id], $obPropertyList->lists('id'));
    }

    /**
     * Create category object
     */
    protected function createCategoryObject()
    {
        $arElementData = [
            'active' => true,
            'name'   => 'name',
            'slug'   => 'slug',
        ];

        $this->obCategory = Category::create($arElementData);
    }

    /**
     * Create property set and properties objects
     */
    protected function createPropertySetObject()
    {
        $arElementData = [
            'name' => 'name',
            'code' => 'code',
        ];

        $this->obFirstProperty = Property::create($arElementData);
        $this->obSecondProperty = Property::create($arElementData);

        $this->obFirstPropertySet = PropertySet::create($arElementData);

        $arElementData['code'] = $arElementData['code'].'1';
        $this->obSecondPropertySet = PropertySet::create($arElementData);
    }
}
