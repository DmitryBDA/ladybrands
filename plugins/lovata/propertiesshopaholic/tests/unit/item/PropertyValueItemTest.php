<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem;

/**
 * Class PropertyValueItemTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA PropertyValue
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyValueItemTest extends CommonTest
{
    /** @var  PropertyValue */
    protected $obElement;

    /** @var PropertyValueLink */
    protected $obPropertyValueLink;

    /** @var  Product */
    protected $obProduct;

    protected $arCreateData = [
        'value' => 'test value',
    ];

    protected $arProductData = [
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
    ];

    /**
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'PropertyValue item data is not correct';

        $arCreatedData = [
            'value' => 'test value',
            'slug'  => 'testvalue',
        ];

        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = PropertyValueItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField, $sErrorMessage);
        }
    }

    /**
     * Check update cache item data, after update model data
     */
    public function testItemClearCache()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'PropertyValue item data is not correct, after model update';

        $obItem = PropertyValueItem::make($this->obElement->id);
        self::assertEquals('test value', $obItem->value, $sErrorMessage);

        //Check cache update
        $this->obElement->value = 'test value test';
        $this->obElement->save();

        $obItem = PropertyValueItem::make($this->obElement->id);
        self::assertEquals('test value test', $obItem->value, $sErrorMessage);
        self::assertEquals('testvaluetest', $obItem->slug, $sErrorMessage);
    }

    /**
     * Check update cache item data, after remove element
     */
    public function testRemoveElement()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'PropertyValue item data is not correct, after model remove';

        $obItem = PropertyValueItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = PropertyValueItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Check isDisabled method
     */
    public function testIsDisabledMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $obItem = PropertyValueItem::make($this->obElement->id);
        $obItem->setModel(Product::class);
        $obItem->setPropertyID(1);

        self::assertEquals(true, $obItem->isDisabled(null));
        self::assertEquals(true, $obItem->isDisabled(ProductCollection::make([2])));
        self::assertEquals(false, $obItem->isDisabled(ProductCollection::make([1])));
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;

        $this->obElement = PropertyValue::create($arCreateData);

        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        $this->obPropertyValueLink = PropertyValueLink::create([
            'property_id'  => 1,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obProduct->id,
            'element_type' => Product::class,
            'value_id'     => $this->obElement->id,
        ]);
    }
}