<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyValueItem;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyValueCollection;

/**
 * Class PropertyValueCollectionTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyValueCollectionTest extends CommonTest
{
    /**
     * Check item collection
     */
    public function testCollectionItem()
    {
        $this->createTestData();

        $sErrorMessage = 'Property value collection item data is not correct';

        //Check item collection
        $obCollection = PropertyValueCollection::make([1]);

        /** @var PropertyValueItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PropertyValueItem::class, $obItem, $sErrorMessage);
        self::assertEquals(1, $obItem->id, $sErrorMessage);
        self::assertEquals(Product::class, $obItem->value, $sErrorMessage);
    }

    /**
     * Check item collection "getValueString" method
     */
    public function testGetValueStringMethod()
    {
        $this->createTestData();

        $sErrorMessage = '"getValueString" method is not correct in PropertyValueCollection ';

        //Check item collection after create
        $obCollection = PropertyValueCollection::make([1,2,3,10]);

        $sExpectedValue = Product::class.', '.Offer::class.', '.Category::class;
        $sValue = $obCollection->getValueString();

        self::assertEquals($sExpectedValue, $sValue, $sErrorMessage);

        $sExpectedValue = Product::class.'-'.Offer::class.'-'.Category::class;
        $sValue = $obCollection->getValueString('-');

        self::assertEquals($sExpectedValue, $sValue, $sErrorMessage);
    }

    /**
     * Check item collection "sort" method
     */
    public function testSortMethod()
    {
        $this->createTestData();

        $sErrorMessage = '"sort" method is not correct in PropertyValueCollection ';

        //Check item collection after create
        $obCollection = PropertyValueCollection::make([1,2,3,4,5])->sort();

        $arValueList = [
            Category::class,
            Offer::class,
            Product::class,
            "0,15",
            1,
            "1,25",
        ];

        /** @var PropertyValueItem $obValue */
        foreach ($obCollection as $obValue) {

            $sExpectedValue = array_shift($arValueList);
            self::assertEquals($sExpectedValue, $obValue->value, $sErrorMessage);
        }
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        PropertyValue::create([
            'value' => Product::class,
        ]);

        PropertyValue::create([
            'value' => Offer::class,
        ]);

        //Create new property value
        PropertyValue::create([
            'value' => Category::class,
        ]);

        //Create new property value
        PropertyValue::create([
            'value' => 1,
        ]);

        //Create new property value
        PropertyValue::create([
            'value' => "0,15",
        ]);

        //Create new property value
        PropertyValue::create([
            'value' => "1,25",
        ]);
    }
}
