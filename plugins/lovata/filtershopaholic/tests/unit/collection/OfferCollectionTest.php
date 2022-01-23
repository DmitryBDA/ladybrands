<?php namespace Lovata\FilterShopaholic\Tests\Unit\Collection;

use System\Classes\PluginManager;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\Shopaholic\Classes\Item\CategoryItem;
use Lovata\Shopaholic\Classes\Collection\OfferCollection;

use Lovata\FilterShopaholic\Plugin;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class OfferCollection
 * @package Lovata\FilterShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OfferCollectionTest extends CommonTest
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

    /** @var PropertyValue */
    protected $obValue;

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
     * Check "filterByPrice" collection method
     */
    public function testFilterByPriceMethod()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Offer collection has not correct "filterByPrice" method';

        //Get offer collection
        $obOfferList = OfferCollection::make()->active();

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();

        self::assertInstanceOf(OfferItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter from 5
        $obOfferList = OfferCollection::make()->active()->filterByPrice(5, null);

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter from 5 to 12
        $obOfferList = OfferCollection::make()->active()->filterByPrice(5, 12);

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter from 0 to 0
        $obOfferList = OfferCollection::make()->active()->filterByPrice(0, 0);

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter from 10.5 to 10.5
        $obOfferList = OfferCollection::make()->active()->filterByPrice(10.5, 10.5);

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter from 10.5 to 5
        $obOfferList = OfferCollection::make()->active()->filterByPrice(10.5, 5);

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter to 12
        $obOfferList = OfferCollection::make()->active()->filterByPrice(null, 12);

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter from 12
        $obOfferList = OfferCollection::make()->active()->filterByPrice(12, null);

        self::assertEquals(true, $obOfferList->isEmpty(), $sErrorMessage);

        //Test filter from 12 to 15
        $obOfferList = OfferCollection::make()->active()->filterByPrice(12, 15);

        self::assertEquals(true, $obOfferList->isEmpty(), $sErrorMessage);

        //Test filter to 5
        $obOfferList = OfferCollection::make()->active()->filterByPrice(null, 5);

        self::assertEquals(true, $obOfferList->isEmpty(), $sErrorMessage);
    }

    /**
     * Check "filterByProperty" collection method
     */
    public function testFilterByPropertyMethodSelectType()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Offer collection has not correct "filterByProperty" method (select type)';

        //Get property list
        $obPropertyList = CategoryItem::make($this->obCategory->id)->offer_filter_property;

        //Get offer collection
        $obOfferList = OfferCollection::make()->active();

        /** @var OfferItem $obItem */
        $obItem = $obOfferList->first();

        self::assertInstanceOf(OfferItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obOffer->id, $obItem->id, $sErrorMessage);

        //Test filter value = 5
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => 5], $obPropertyList);

        self::assertEquals(2, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 12
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => 12], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 10
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => 10], $obPropertyList);

        self::assertEquals(true, $obOfferList->isEmpty(), $sErrorMessage);

        $obValue = PropertyValue::getByValue(5)->first();

        //Change property value
        $obPropertyValue = PropertyValueLink::getByElementType(Offer::class)->getByElementID($this->obOffer->id + 1)->getByValue($obValue->id)->first();
        $obPropertyValue->value_id = $this->obValue->id;
        $obPropertyValue->save();

        //Test filter value = 10
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => 10], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 5
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => 5], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);
    }

    /**
     * Check "filterByProperty" collection method
     */
    public function testFilterByPropertyMethodCheckboxType()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData(Plugin::TYPE_CHECKBOX);
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Offer collection has not correct "filterByProperty" method (checkbox type)';

        //Get property list
        $obPropertyList = CategoryItem::make($this->obCategory->id)->offer_filter_property;

        //Test filter value = 5
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [5]], $obPropertyList);

        self::assertEquals(2, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 12
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [12]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 10,12
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [10,12]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 10,15
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [10,15]], $obPropertyList);

        self::assertEquals(true, $obOfferList->isEmpty(), $sErrorMessage);

        $obValue = PropertyValue::getByValue(5)->first();

        //Change property value
        $obPropertyValue = PropertyValueLink::getByElementType(Offer::class)->getByElementID($this->obOffer->id + 1)->getByValue($obValue->id)->first();
        $obPropertyValue->value_id = $this->obValue->id;
        $obPropertyValue->save();

        //Test filter value = 10
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [10,12]], $obPropertyList);

        self::assertEquals(2, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 5
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [10,15]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);
    }

    /**
     * Check "filterByProperty" collection method
     */
    public function testFilterByPropertyMethodBetweenType()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData(Plugin::TYPE_BETWEEN);
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Offer collection has not correct "filterByProperty" method (between type)';

        //Get property list
        $obPropertyList = CategoryItem::make($this->obCategory->id)->offer_filter_property;

        //Test filter value = 5
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [5]], $obPropertyList);

        self::assertEquals(2, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 12
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [12]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 10,12
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [10,12]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 12,12
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [12,12]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 14,15
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [14,15]], $obPropertyList);

        self::assertEquals(true, $obOfferList->isEmpty(), $sErrorMessage);

        //Test filter value = 1,2
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [1,2]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        //Test filter value = -1,1
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [-1,1]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);

        $obValue = PropertyValue::getByValue(5)->first();

        //Change property value
        $obPropertyValue = PropertyValueLink::getByElementType(Offer::class)->getByElementID($this->obOffer->id + 1)->getByValue($obValue->id)->first();
        $obPropertyValue->value_id = $this->obValue->id;
        $obPropertyValue->save();

        //Test filter value = 10
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [10,12]], $obPropertyList);

        self::assertEquals(2, $obOfferList->count(), $sErrorMessage);

        //Test filter value = 5
        $obOfferList = OfferCollection::make()->active()->filterByProperty([1 => [12,15]], $obPropertyList);

        self::assertEquals(1, $obOfferList->count(), $sErrorMessage);
    }

    /**
     * Create data for test
     * @param string $sPropertyType
     */
    protected function createTestData($sPropertyType = Plugin::TYPE_SELECT)
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
        Offer::create($arCreateData);

        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

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
            'filter_type' => $sPropertyType,
            'in_filter' => true,
        ];

        //Attach property to property set
        $this->obPropertySet->product_property()->attach($this->obElement->id, $arPivotData);
        $this->obPropertySet->offer_property()->attach($this->obElement->id, $arPivotData);
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

        $obPropertyValue = PropertyValue::create([
            'value' => 5,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obOffer->product_id,
            'element_id'   => $this->obOffer->id,
            'element_type' => Offer::class,
            'value_id'     => $obPropertyValue->id,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obOffer->product_id,
            'element_id'   => $this->obOffer->id + 1,
            'element_type' => Offer::class,
            'value_id'     => $obPropertyValue->id,
        ]);

        $obPropertyValue = PropertyValue::create([
            'value' => 12,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obOffer->product_id,
            'element_id'   => $this->obOffer->id,
            'element_type' => Offer::class,
            'value_id'     => $obPropertyValue->id,
        ]);

        $obPropertyValue = PropertyValue::create([
            'value' => ' 1,2',
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obOffer->product_id,
            'element_id'   => $this->obOffer->id + 1,
            'element_type' => Offer::class,
            'value_id'     => $obPropertyValue->id,
        ]);

        $obPropertyValue = PropertyValue::create([
            'value' => 0,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obOffer->product_id,
            'element_id'   => $this->obOffer->id + 1,
            'element_type' => Offer::class,
            'value_id'     => $obPropertyValue->id,
        ]);

        $this->obValue = PropertyValue::create([
            'value' => 10,
        ]);
    }
}
