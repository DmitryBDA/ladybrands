<?php namespace Lovata\LabelsShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\LabelsShopaholic\Classes\Item\LabelItem;
use Lovata\LabelsShopaholic\Classes\Collection\LabelCollection;

/**
 * Class LabelCollectionTest
 * @package Lovata\Shopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class LabelCollectionTest extends CommonTest
{
    /** @var  Label */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    protected $arCreateData = [
        'name'        => 'name',
        'code'        => 'code',
        'description' => 'description',
    ];

    protected $arProductData = [
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
    ];

    /**
     * Check item collection
     */
    public function testCollectionItem()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        //Check item collection
        $obCollection = LabelCollection::make([$this->obElement->id]);

        /** @var LabelItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(LabelItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);
    }

    /**
     * Check item collection "active" method
     */
    public function testActiveList()
    {
        LabelCollection::make()->active();

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        //Check item collection after create
        $obCollection = LabelCollection::make()->active();

        /** @var LabelItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(LabelItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->active = false;
        $this->obElement->save();

        //Check item collection, after active = false
        $obCollection = LabelCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->active = true;
        $this->obElement->save();

        //Check item collection, after active = true
        $obCollection = LabelCollection::make()->active();

        /** @var LabelItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(LabelItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = LabelCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Check item collection "product" method
     */
    public function testProductFilter()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        LabelCollection::make()->product($this->obProduct->id);

        //Check item collection after create
        $obCollection = LabelCollection::make()->product($this->obProduct->id);

        /** @var LabelItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(LabelItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->product()->detach();

        //Check item collection, after change product_id field
        $obCollection = LabelCollection::make()->product($this->obProduct->id);
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->product()->add($this->obProduct);

        //Check item collection, after change product_id field
        $obCollection = LabelCollection::make()->product($this->obProduct->id);

        /** @var LabelItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(LabelItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obProduct->label()->detach();

        //Check item collection, after change product_id field
        $obCollection = LabelCollection::make()->product($this->obProduct->id);
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obProduct->label()->add($this->obProduct);

        //Check item collection, after change product_id field
        $obCollection = LabelCollection::make()->product($this->obProduct->id);

        /** @var LabelItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(LabelItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = LabelCollection::make()->product($this->obProduct->id);
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Create Label object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;
        $this->obElement = Label::create($arCreateData);

        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        $this->obElement->product()->attach($this->obProduct);
    }
}
