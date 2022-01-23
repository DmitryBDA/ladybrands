<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Collection;

use October\Rain\Argon\Argon;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Item\DiscountItem;
use Lovata\DiscountsShopaholic\Classes\Store\DiscountListStore;
use Lovata\DiscountsShopaholic\Classes\Collection\DiscountCollection;

/**
 * Class DiscountCollectionTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class DiscountCollectionTest extends CommonTest
{
    /** @var  Discount */
    protected $obElement;

    protected $arCreateData = [
        'active'         => true,
        'hidden'         => true,
        'name'           => 'name',
        'slug'           => 'slug',
        'code'           => 'code',
        'external_id'    => 'external_id',
        'discount_value' => 10,
        'preview_text'   => 'preview_text',
        'description'    => 'description',
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

        //Check item collection
        $obCollection = DiscountCollection::make([$this->obElement->id]);

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(DiscountItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);
    }

    /**
     * Check item collection "active" method
     */
    public function testActiveList()
    {
        DiscountCollection::make()->active();

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = DiscountCollection::make()->active();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->active = false;
        $this->obElement->save();

        //Check item collection, after active = false
        $obCollection = DiscountCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->active = true;
        $this->obElement->save();

        //Check item collection, after active = true
        $obCollection = DiscountCollection::make()->active();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(DiscountItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = DiscountCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Check item collection "active" method with date begin
     */
    public function testActiveListWithDateBegin()
    {
        DiscountCollection::make()->active();

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = DiscountCollection::make()->active();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->date_begin = Argon::now()->addDay();
        $this->obElement->save();

        $obCollection = DiscountCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->date_begin = Argon::now()->subDay();
        $this->obElement->save();

        $obCollection = DiscountCollection::make()->active();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(DiscountItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);
    }

    /**
     * Check item collection "active" method with date end
     */
    public function testActiveListWithDateEnd()
    {
        DiscountCollection::make()->active();

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = DiscountCollection::make()->active();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->date_end = Argon::now()->subDay();
        $this->obElement->save();

        $obCollection = DiscountCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->date_end = Argon::now()->addDay();
        $this->obElement->save();

        $obCollection = DiscountCollection::make()->active();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(DiscountItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);
    }

    /**
     * Check item collection "hidden" method
     */
    public function testHiddenList()
    {
        DiscountCollection::make()->hidden();

        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = DiscountCollection::make()->hidden();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $obCollection = DiscountCollection::make()->notHidden();
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->hidden = false;
        $this->obElement->save();

        //Check item collection, after hidden = false
        $obCollection = DiscountCollection::make()->hidden();
        self::assertEquals(true, $obCollection->isEmpty());


        $obCollection = DiscountCollection::make()->notHidden();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->hidden = true;
        $this->obElement->save();

        //Check item collection, after hidden = true
        $obCollection = DiscountCollection::make()->hidden();

        /** @var DiscountItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(DiscountItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $obCollection = DiscountCollection::make()->notHidden();
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = DiscountCollection::make()->hidden();
        self::assertEquals(true, $obCollection->isEmpty());

        $obCollection = DiscountCollection::make()->notHidden();
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Check item collection "sort" method by date begin
     */
    public function testSortLisByDateBegin()
    {
        DiscountCollection::make()->active();

        $this->createTestData(1);
        $this->createTestData(2);
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_BEGIN_ASC);
        self::assertEquals([$this->obElement->id, $this->obElement->id - 1], $obCollection->getIDList());

        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_BEGIN_DESC);
        self::assertEquals([$this->obElement->id - 1, $this->obElement->id], $obCollection->getIDList());

        $this->obElement->date_begin = Argon::now()->addDay();
        $this->obElement->save();

        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_BEGIN_ASC);
        self::assertEquals([$this->obElement->id - 1, $this->obElement->id], $obCollection->getIDList());

        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_BEGIN_DESC);
        self::assertEquals([$this->obElement->id, $this->obElement->id - 1], $obCollection->getIDList());
    }

    /**
     * Check item collection "sort" method by date end
     */
    public function testSortLisByDateEnd()
    {
        DiscountCollection::make()->active();

        $this->createTestData(1);
        $this->createTestData(2);
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_END_ASC);
        self::assertEquals([$this->obElement->id -1, $this->obElement->id], $obCollection->getIDList());

        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_END_DESC);
        self::assertEquals([$this->obElement->id, $this->obElement->id -1], $obCollection->getIDList());

        $this->obElement->date_end = Argon::now()->addDay();
        $this->obElement->save();

        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_END_ASC);
        self::assertEquals([$this->obElement->id, $this->obElement->id -1], $obCollection->getIDList());

        $obCollection = DiscountCollection::make()->sort(DiscountListStore::SORT_DATE_END_DESC);
        self::assertEquals([$this->obElement->id - 1, $this->obElement->id], $obCollection->getIDList());
    }

    /**
     * Create  object for test
     * @param int $iCount
     */
    protected function createTestData($iCount = 1)
    {
        $this->arCreateData['date_begin'] = Argon::now()->subMonth($iCount);
        $this->arCreateData['date_end'] = Argon::now()->addMonth($iCount);
        $this->arCreateData['slug'] = 'slug'.$iCount;
        $this->arCreateData['discount_type'] = Discount::PERCENT_TYPE;

        //Create discount data
        $arCreateData = $this->arCreateData;
        $this->obElement = Discount::create($arCreateData);
    }
}
