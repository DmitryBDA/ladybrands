<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\Shopaholic\Models\Measure;
use Lovata\Shopaholic\Classes\Item\MeasureItem;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;

/**
 * Class PropertyItemTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyItemTest extends CommonTest
{
    /** @var  Property */
    protected $obElement;

    /** @var  Measure */
    protected $obMeasure;

    protected $arCreateData = [
        'name'        => 'name',
        'slug'        => 'slug',
        'code'        => 'code',
        'type'        => 'input',
        'description' => 'description',
    ];

    /**
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property item data is not correct';

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;
        $arCreateData['measure_id'] = $this->obMeasure->id;

        //Check item fields
        $obItem = PropertyItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField, $sErrorMessage);
        }

        $obMeasureItem = $obItem->measure;

        self::assertInstanceOf(MeasureItem::class, $obMeasureItem, $sErrorMessage);
        self::assertEquals($this->obMeasure->id, $obMeasureItem->id, $sErrorMessage);
    }

    /**
     * Check update cache item data, after update model data
     */
    public function testItemClearCache()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property item data is not correct, after model update';

        $obItem = PropertyItem::make($this->obElement->id);
        self::assertEquals('name', $obItem->name, $sErrorMessage);

        //Check cache update
        $this->obElement->name = 'test';
        $this->obElement->save();

        $obItem = PropertyItem::make($this->obElement->id);
        self::assertEquals('test', $obItem->name, $sErrorMessage);
    }

    /**
     * Check update cache item data, after remove element
     */
    public function testRemoveElement()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Property item data is not correct, after model remove';

        $obItem = PropertyItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = PropertyItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        //Crete measure
        $this->obMeasure = Measure::create(['name' => 'name']);

        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['measure_id'] = $this->obMeasure->id;

        //Create property
        $this->obElement = Property::create($arCreateData);
    }
}