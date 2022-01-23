<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\PropertySetItem;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertySetCollection;

/**
 * Class CategoryItemTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CategoryItemTest extends CommonTest
{
    /** @var  Property */
    protected $obElement;

    /** @var  Category */
    protected $obCategory;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

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

    protected $arOfferData = [
        'active'       => true,
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'price'        => '10,50',
        'old_price'    => '11,50',
        'quantity'     => 5,
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
     * Check property set collection in category item
     */
    public function testPropertySetField()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Category item data has not correct property set field';

        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        $obPropertySetList = $obCategoryItem->property_set;

        /** @var PropertySetItem $obPropertySetItem */
        $obPropertySetItem = $obPropertySetList->first();

        self::assertInstanceOf(PropertySetCollection::class, $obPropertySetList, $sErrorMessage);
        self::assertEquals($this->obPropertySet->id, $obPropertySetItem->id, $sErrorMessage);
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

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        //Create property
        $this->obElement = Property::create($arCreateData);

        $this->obPropertySet = PropertySet::create($this->arPropertySetData);

        //Attach property set to category
        $this->obCategory->property_set()->attach($this->obPropertySet->id);
        $this->obCategory->save();

        $this->obPropertySet->product_property()->attach($this->obElement->id);
        $this->obPropertySet->offer_property()->attach($this->obElement->id);

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

        $obPropertyValue = PropertyValue::create([
            'value' => Offer::class,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obOffer->id,
            'element_type' => Offer::class,
            'value_id'        => $obPropertyValue->id,
        ]);
    }
}