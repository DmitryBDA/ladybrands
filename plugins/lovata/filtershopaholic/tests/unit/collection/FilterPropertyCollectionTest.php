<?php namespace Lovata\FilterShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\FilterShopaholic\Plugin;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;
use System\Classes\PluginManager;

/**
 * Class FilterPropertyCollectionTest
 * @package Lovata\FilterShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class FilterPropertyCollectionTest extends CommonTest
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
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Filter property collection item data is not correct';

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property;

        /** @var PropertyItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PropertyItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check empty item collection
     */
    public function testEmptyCollectionItem()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData(false);
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Filter property collection item data is not correct';

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property;

        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);
    }

    /**
     * Check "type" collection method
     */
    public function testTypeMethod()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Filter property collection has not correct "type" method';

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property->type(Plugin::TYPE_SELECT);

        /** @var PropertyItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property->type(Plugin::TYPE_BETWEEN);

        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);

        $obPropertyList = $this->obPropertySet->product_property;
        foreach ($obPropertyList as $obProperty) {
            /** @var \Lovata\PropertiesShopaholic\Models\PropertyProductLink $obPivot */
            $obPivot = $obProperty->pivot;
            $obPivot->filter_type = Plugin::TYPE_BETWEEN;
            $obPivot->save();
        }

        $this->obPropertySet->save();
        $this->obCategory->save();

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property->type(Plugin::TYPE_BETWEEN);

        /** @var PropertyItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property->type(Plugin::TYPE_SELECT);

        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);
    }

    /**
     * Check "getFilterType" collection method
     */
    public function testGetFilterTypeMethod()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Filter property collection has not correct "getFilterType" method';

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property;

        self::assertEquals(Plugin::TYPE_SELECT, $obCollection->getFilterType($this->obElement->id), $sErrorMessage);

        $obPropertyList = $this->obPropertySet->product_property;
        foreach ($obPropertyList as $obProperty) {
            /** @var \Lovata\PropertiesShopaholic\Models\PropertyProductLink $obPivot */
            $obPivot = $obProperty->pivot;
            $obPivot->filter_type = Plugin::TYPE_BETWEEN;
            $obPivot->save();
        }

        $this->obPropertySet->save();
        $this->obCategory->save();

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property;

        self::assertEquals(Plugin::TYPE_BETWEEN, $obCollection->getFilterType($this->obElement->id), $sErrorMessage);
    }

    /**
     * Check "getFilterName" collection method
     */
    public function testGetFilterNameMethod()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Filter property collection has not correct "getFilterName" method';

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property;

        self::assertEquals('test', $obCollection->getFilterName($this->obElement->id), $sErrorMessage);
    }

    /**
     * Check "getFilterName" collection method
     */
    public function testGetEmptyFilterNameMethod()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $obPropertyList = $this->obPropertySet->product_property;
        foreach ($obPropertyList as $obProperty) {
            /** @var \Lovata\PropertiesShopaholic\Models\PropertyProductLink $obPivot */
            $obPivot = $obProperty->pivot;
            $obPivot->filter_name = null;
            $obPivot->save();
        }

        $this->obPropertySet->save();
        $this->obCategory->save();

        //Get category item
        $obCategoryItem = CategoryItem::make($this->obCategory->id);

        //Check item collection
        $obCollection = $obCategoryItem->product_filter_property;

        self::assertEquals('name', $obCollection->getFilterName($this->obElement->id));
    }

    /**
     * Create data for test
     * @param bool $bInFilter
     */
    protected function createTestData($bInFilter = true)
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

        $arPivotData = [
            'filter_name' => 'test',
            'filter_type' => Plugin::TYPE_SELECT,
            'in_filter'   => $bInFilter,
        ];

        //Attach property to category
        $this->obPropertySet->product_property()->attach($this->obElement->id, $arPivotData);
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
