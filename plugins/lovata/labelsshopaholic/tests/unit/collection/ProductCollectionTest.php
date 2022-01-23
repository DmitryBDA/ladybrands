<?php namespace Lovata\LabelsShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\LabelsShopaholic\Models\Label;

/**
 * Class ProductCollectionTest
 * @package Lovata\Shopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductCollectionTest extends CommonTest
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
     * Check item collection "product" method
     */
    public function testProductFilter()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        ProductCollection::make()->label($this->obElement->id);

        //Check item collection after create
        $obCollection = ProductCollection::make()->label($this->obElement->id);

        /** @var ProductItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(ProductItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->product()->detach();

        //Check item collection, after change product_id field
        $obCollection = ProductCollection::make()->label($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obElement->product()->add($this->obProduct);

        //Check item collection, after change product_id field
        $obCollection = ProductCollection::make()->label($this->obElement->id);

        /** @var ProductItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(ProductItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obProduct->label()->detach();

        //Check item collection, after change product_id field
        $obCollection = ProductCollection::make()->label($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        $this->obProduct->label()->add($this->obProduct);

        //Check item collection, after change product_id field
        $obCollection = ProductCollection::make()->label($this->obElement->id);

        /** @var ProductItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(ProductItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = ProductCollection::make()->label($this->obElement->id);
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