<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Item;

use October\Rain\Argon\Argon;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Item\DiscountItem;

/**
 * Class DiscountsItemTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class DiscountItemTest extends CommonTest
{
    /** @var  Discount */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    protected $arProductData = [
        'name' => 'name',
        'slug' => 'slug',
    ];

    protected $arDiscountData = [
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
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $arCreatedData = $this->arDiscountData;
        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = DiscountItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField);
        }

        $obProductList = $obItem->product;

        self::assertInstanceOf(ProductCollection::class, $obProductList);
        self::assertEquals(1, $obProductList->count());

        /** @var \Lovata\Shopaholic\Classes\Item\ProductItem $obProductItem */
        $obProductItem = $obProductList->first();
        self::assertEquals($this->obProduct->id, $obProductItem->id);
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

        $obItem = DiscountItem::make($this->obElement->id);
        self::assertEquals('name', $obItem->name);

        //Check cache update
        $this->obElement->name = 'test';
        $this->obElement->save();

        $obItem = DiscountItem::make($this->obElement->id);
        self::assertEquals('test', $obItem->name);
    }

    /**
     * Check item data, after delete model
     */
    public function testDeleteElement()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $obItem = DiscountItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty());

        //Check active flag in item data
        $this->obElement->delete();

        $obItem = DiscountItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty());
    }

    /**
     * Create test data
     */
    protected function createTestData()
    {
        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        //Create discount data
        $this->arDiscountData['date_begin'] = Argon::now()->subMonth();
        $this->arDiscountData['date_end'] = Argon::now()->addMonth();
        $this->arDiscountData['discount_type'] = Discount::PERCENT_TYPE;
        $arCreateData = $this->arDiscountData;
        $this->obElement = Discount::create($arCreateData);

        $this->obElement->product()->attach($this->obProduct->id);
        $this->obElement->save();

        $this->arDiscountData['created_at'] = $this->obElement->created_at;
        $this->arDiscountData['updated_at'] = $this->obElement->updated_at;
    }
}
