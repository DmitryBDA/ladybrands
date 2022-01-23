<?php namespace Lovata\LabelsShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\LabelsShopaholic\Classes\Item\LabelItem;

/**
 * Class LabelItemTest
 * @package Lovata\LabelsShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class LabelItemTest extends CommonTest
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
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }


        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = LabelItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField);
        }

        //Check product list
        $obProductList = $obItem->product;

        self::assertInstanceOf(ProductCollection::class, $obProductList);
        self::assertEquals([$this->obProduct->id], $obProductList->getIDList());
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

        $obItem = LabelItem::make($this->obElement->id);
        self::assertEquals('name', $obItem->name);

        //Check cache update
        $this->obElement->name = 'test';
        $this->obElement->save();

        $obItem = LabelItem::make($this->obElement->id);
        self::assertEquals('test', $obItem->name);
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

        $obItem = LabelItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty());

        //Remove element
        $this->obElement->delete();

        $obItem = LabelItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty());
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
