<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Collection;

use Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Classes\Item\PropertySetItem;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertySetCollection;

/**
 * Class PropertySetCollectionTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA PropertySet
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertySetCollectionTest extends CommonTest
{
    /** @var  PropertySet */
    protected $obElement;

    /** @var  Property */
    protected $obProperty;

    protected $arCreateData = [
        'name' => 'main',
        'code' => 'main',
    ];

    protected $arPropertyData = [
        'name'        => 'name',
        'slug'        => 'slug',
        'code'        => 'code',
        'type'        => 'input',
        'description' => 'description',
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

        $sErrorMessage = 'PropertySet collection item data is not correct';

        //Check item collection
        $obCollection = PropertySetCollection::make([$this->obElement->id]);

        /** @var PropertySetItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PropertySetItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check sort collection method
     */
    public function testSortCollectionMethod()
    {
        $this->createTestData(0);
        $this->createTestData(1);
        $this->createTestData(2);
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection
        $obCollection = PropertySetCollection::make([3, 2, 1])->sort();

        self::assertEquals([1, 2, 3], $obCollection->getIDList());
    }

    /**
     * Test getProductPropertyList() method
     */
    public function testGetProductPropertyListMethod()
    {
        $this->createProperty();

        $this->createTestData(0);
        $this->obElement->product_property()->attach($this->obProperty->id, ['groups' => json_encode([1,2])]);

        $this->createTestData(1);
        $this->obElement->product_property()->attach($this->obProperty->id - 1, ['groups' => json_encode([3])]);

        $arPropertyDataList = PropertySetCollection::make([3, 2, 1])->getProductPropertyList();

        self::assertEquals(true, is_array($arPropertyDataList));
        self::assertEquals([$this->obProperty->id, $this->obProperty->id - 1], array_keys($arPropertyDataList));

        self::assertEquals([1,2], $arPropertyDataList[$this->obProperty->id]['groups']);
        self::assertEquals([3], $arPropertyDataList[$this->obProperty->id - 1]['groups']);
    }

    /**
     * Test getOfferPropertyList() method
     */
    public function testGetOfferPropertyListMethod()
    {
        $this->createProperty();

        $this->createTestData(0);
        $this->obElement->offer_property()->attach($this->obProperty->id, ['groups' => json_encode([1,2])]);

        $this->createTestData(1);
        $this->obElement->offer_property()->attach($this->obProperty->id - 1, ['groups' => json_encode([3])]);

        $arPropertyDataList = PropertySetCollection::make([3, 2, 1])->getOfferPropertyList();

        self::assertEquals(true, is_array($arPropertyDataList));
        self::assertEquals([$this->obProperty->id, $this->obProperty->id - 1], array_keys($arPropertyDataList));

        self::assertEquals([1,2], $arPropertyDataList[$this->obProperty->id]['groups']);
        self::assertEquals([3], $arPropertyDataList[$this->obProperty->id - 1]['groups']);
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

        $sErrorMessage = 'Property set collection has not correct "code" method';

        $obPropertySetList = PropertySetCollection::make([$this->obElement->id]);

        $obResultPropertySetList = $obPropertySetList->code(null);

        self::assertInstanceOf(PropertySetCollection::class, $obResultPropertySetList, $sErrorMessage);
        self::assertEquals(true, $obResultPropertySetList->isEmpty(), $sErrorMessage);

        $obResultPropertySetList = $obPropertySetList->code($this->obElement->code);

        /** @var PropertySetItem $obPropertySetItem */
        $obPropertySetItem = $obResultPropertySetList->first();

        self::assertEquals($this->obElement->id, $obPropertySetItem->id, $sErrorMessage);

        $obResultPropertySetList = $obPropertySetList->code([$this->obElement->code, 'test']);

        /** @var PropertySetItem $obPropertySetItem */
        $obPropertySetItem = $obResultPropertySetList->first();

        self::assertEquals($this->obElement->id, $obPropertySetItem->id, $sErrorMessage);

        $obResultPropertySetList = $obPropertySetList->code(['test']);
        self::assertEquals(true, $obResultPropertySetList->isEmpty(), $sErrorMessage);

        $obResultPropertySetList = $obPropertySetList->code('test');
        self::assertEquals(true, $obResultPropertySetList->isEmpty(), $sErrorMessage);
    }

    /**
     * Create group object for test
     * @param int $iIndex
     */
    protected function createTestData($iIndex = 0)
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['code'] = $arCreateData['code'].$iIndex;
        $arCreateData['sort_order'] = $iIndex;

        $this->obElement = PropertySet::create($arCreateData);


    }

    /**
     * Create test properties
     */
    protected function createProperty()
    {
        for ($i = 0; $i <=1; $i++) {

            $arCreateData = $this->arPropertyData;
            $arCreateData['code'] = $arCreateData['code'].$i;
            $arCreateData['slug'] = $arCreateData['slug'].$i;

            $this->obProperty = Property::create($arCreateData);
        }
    }
}
