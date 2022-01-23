<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\ProductItem;

use Lovata\PropertiesShopaholic\Models\Group;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\GroupItem;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;
use Lovata\PropertiesShopaholic\Classes\Item\PropertySetItem;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection;

/**
 * Class PropertyCollectionTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyCollectionTest extends CommonTest
{
    /** @var  Property */
    protected $obElement;

    /** @var  Category */
    protected $obCategory;

    /** @var  Group */
    protected $obGroup;

    /** @var  PropertySet */
    protected $obPropertySet;

    /** @var  Product */
    protected $obProduct;

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

    protected $arGroupData = [
        'name'        => 'name',
        'code'        => 'code',
        'description' => 'description',
    ];

    protected $arPropertySetData = [
        'name' => 'main',
        'code' => 'main',
    ];

    /**
     * Check item collection
     */
    public function testCollectionItem()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property collection item data is not correct';

        //Check item collection
        $obCollection = PropertyCollection::make([$this->obElement->id]);

        /** @var PropertyItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PropertyItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check item collection "active" method
     */
    public function testActiveList()
    {
        PropertyCollection::make()->active();

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property collection "active" method is not correct';

        //Check item collection after create
        $obCollection = PropertyCollection::make()->active();

        /** @var PropertyItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PropertyItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->active = false;
        $this->obElement->save();

        //Check item collection, after active = false
        $obCollection = PropertyCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);

        $this->obElement->active = true;
        $this->obElement->save();

        //Check item collection, after active = true
        $obCollection = PropertyCollection::make()->active();

        /** @var PropertyItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PropertyItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = PropertyCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);
    }

    /**
     * Check group collection method
     */
    public function testGroupCollectionMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property collection has not correct "group" method';

        $obPropertyList = ProductItem::make($this->obProduct->id)->property;

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obPropertyList->first();

        self::assertInstanceOf(PropertyCollection::class, $obPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obGroupPropertyList = $obPropertyList->group($this->obGroup->id);

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obGroupPropertyList->first();

        self::assertInstanceOf(PropertyCollection::class, $obGroupPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obGroupPropertyList = $obPropertyList->group($this->obGroup->id + 1);

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obGroupPropertyList->first();

        self::assertInstanceOf(PropertyCollection::class, $obGroupPropertyList, $sErrorMessage);
        self::assertEquals(true, $obGroupPropertyList->isEmpty(), $sErrorMessage);
    }

    /**
     * Check code collection method
     */
    public function testCodeCollectionMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property collection has not correct "code" method';

        $obPropertyList = PropertyCollection::make([$this->obElement->id]);

        $obResultPropertyList = $obPropertyList->code(null);

        self::assertInstanceOf(PropertyCollection::class, $obResultPropertyList, $sErrorMessage);
        self::assertEquals(true, $obResultPropertyList->isEmpty(), $sErrorMessage);

        $obResultPropertyList = $obPropertyList->code($this->obElement->code);

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obResultPropertyList->first();

        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obResultPropertyList = $obPropertyList->code([$this->obElement->code, 'test']);

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obResultPropertyList->first();

        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obResultPropertyList = $obPropertyList->code(['test']);
        self::assertEquals(true, $obResultPropertyList->isEmpty(), $sErrorMessage);

        $obResultPropertyList = $obPropertyList->code('test');
        self::assertEquals(true, $obResultPropertyList->isEmpty(), $sErrorMessage);
    }

    /**
     * Check getByCode collection method
     */
    public function testGetByCodeCollectionMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property collection has not correct "getByCode" method';

        $obPropertyList = PropertyCollection::make([$this->obElement->id]);

        $obPropertyItem = $obPropertyList->getByCode(null);

        self::assertInstanceOf(PropertyItem::class, $obPropertyItem, $sErrorMessage);
        self::assertEquals(true, $obPropertyItem->isEmpty(), $sErrorMessage);

        $obPropertyItem = $obPropertyList->getByCode($this->obElement->code);

        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obPropertyItem = $obPropertyList->getByCode('test');

        self::assertEquals(true, $obPropertyItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Check getGroupList collection method
     */
    public function testGetGroupListMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property collection has not correct "getGroupList" method';

        $obPropertySetItem = PropertySetItem::make($this->obPropertySet->id);

        $obPropertyList = PropertyCollection::make([$this->obElement->id])->setPropertySetRelation($obPropertySetItem->product_property_list);

        $obGroupList = $obPropertyList->getGroupList();

        /** @var GroupItem $obGroupItem */
        $obGroupItem = $obGroupList->first();

        self::assertInstanceOf(GroupItem::class, $obGroupItem, $sErrorMessage);
        self::assertEquals($this->obGroup->id, $obGroupItem->id, $sErrorMessage);
        self::assertEquals(1, $obGroupList->count(), $sErrorMessage);
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

        //Create group data
        $this->obGroup = Group::create($this->arGroupData);

        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        //Create property
        $this->obElement = Property::create($arCreateData);

        $this->obPropertySet = PropertySet::create($this->arPropertySetData);

        //Attach property set to category
        $this->obCategory->property_set()->attach($this->obPropertySet->id);
        $this->obCategory->save();

        //Attach property to property set
        $this->obPropertySet->product_property()->attach($this->obElement->id, ['groups' => json_encode([$this->obGroup->id])]);
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
