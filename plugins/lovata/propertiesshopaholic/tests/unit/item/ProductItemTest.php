<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\ProductItem;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyValueCollection;

/**
 * Class ProductItemTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductItemTest extends CommonTest
{
    /** @var  Property */
    protected $obElement;

    /** @var  Category */
    protected $obCategory;

    /** @var  Product */
    protected $obProduct;

    /** @var  PropertySet */
    protected $obPropertySet;

    protected $arCreateData = [
        'name'        => 'name',
        'slug'        => 'slug',
        'code'        => 'code',
        'type'        => 'input',
        'description' => 'description',
    ];

    protected $arProductData = [
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
    ];

    protected $arCategoryData = [
        'active'       => true,
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'nest_depth'   => 0,
        'parent_id'    => 0,
    ];

    protected $arPropertySetData = [
        'name' => 'main',
        'code' => 'main',
    ];

    /**
     * Check product item property field
     */
    public function testPropertyField()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Product item data has not correct property field';

        $obProductItem = ProductItem::make($this->obProduct->id);

        $obPropertyList = $obProductItem->property;

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obPropertyList->first();

        self::assertInstanceOf(PropertyCollection::class, $obPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obValueList = $obPropertyItem->property_value;
        self::assertInstanceOf(PropertyValueCollection::class, $obValueList, $sErrorMessage);
        self::assertEquals(Product::class, $obValueList->getValueString(), $sErrorMessage);
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        //Create category data
        $this->obCategory = Category::create($this->arCategoryData);

        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $arCreateData['category_id'] = $this->obCategory->id;
        $this->obProduct = Product::create($arCreateData);

        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        //Create property
        $this->obElement = Property::create($arCreateData);

        $this->obPropertySet = PropertySet::create($this->arPropertySetData);

        //Attach property set to category
        $this->obCategory->property_set()->attach($this->obPropertySet->id);
        $this->obCategory->save();

        //Attach property to category
        $this->obPropertySet->product_property()->attach($this->obElement->id);
        $this->obPropertySet->save();

        $obPropertyValue = PropertyValue::create([
            'value' => Product::class,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obProduct->id,
            'element_type' => Product::class,
            'value_id'     => $obPropertyValue->id,
        ]);
    }
}