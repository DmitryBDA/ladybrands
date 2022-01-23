<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\PropertiesShopaholic\Models\Group;
use Lovata\PropertiesShopaholic\Classes\Item\GroupItem;
use Lovata\PropertiesShopaholic\Classes\Collection\GroupCollection;

/**
 * Class GroupCollectionTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class GroupCollectionTest extends CommonTest
{
    /** @var  Group */
    protected $obElement;

    protected $arCreateData = [
        'name'        => 'name',
        'code'        => 'code',
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

        $sErrorMessage = 'Group collection item data is not correct';

        //Check item collection
        $obCollection = GroupCollection::make([$this->obElement->id]);

        /** @var GroupItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(GroupItem::class, $obItem, $sErrorMessage);
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

        $sErrorMessage = 'Group collection has not correct "sort" method';

        //Check item collection
        $obCollection = GroupCollection::make([3, 2, 1])->sort();

        self::assertEquals([1, 2, 3], $obCollection->getIDList(), $sErrorMessage);
    }

    /**
     * Check hasCode collection method
     */
    public function testHasCodeCollectionMethod()
    {
        $this->createTestData(0);
        $this->createTestData(1);
        $this->createTestData(2);
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Group collection has not correct "hasCode" method';

        //Check item collection
        $obCollection = GroupCollection::make([1, 2, 3]);

        self::assertEquals(true, $obCollection->hasCode('code1'), $sErrorMessage);
        self::assertEquals(false, $obCollection->hasCode('code10'), $sErrorMessage);
    }

    /**
     * Check getByCode collection method
     */
    public function testGetByCodeCollectionMethod()
    {
        $this->createTestData(0);
        $this->createTestData(1);
        $this->createTestData(2);
        if (empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Group collection has not correct "getByCode" method';

        //Check item collection
        $obCollection = GroupCollection::make([1, 2, 3]);

        /** @var GroupItem $obItem */
        $obItem = $obCollection->getByCode('code1');
        self::assertInstanceOf(GroupItem::class, $obItem, $sErrorMessage);
        self::assertEquals(2, $obItem->id, $sErrorMessage);

        /** @var GroupItem $obItem */
        $obItem = $obCollection->getByCode('code10');
        self::assertInstanceOf(GroupItem::class, $obItem, $sErrorMessage);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
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

        $this->obElement = Group::create($arCreateData);
    }
}
