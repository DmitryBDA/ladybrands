<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Collection;

use October\Rain\Argon\Argon;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class ProductCollectionTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductCollectionTest extends CommonTest
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

    /** @var  Product */
    protected $obProduct;

    protected $arProductData = [
        'active'         => true,
        'name'           => 'name',
        'slug'           => 'slug',
    ];

    /** @var  Category */
    protected $obCategory;

    protected $arCategoryData = [
        'active'         => true,
        'name'           => 'name',
        'slug'           => 'slug',
    ];

    /** @var  Brand */
    protected $obBrand;

    protected $arBrandData = [
        'active'         => true,
        'name'           => 'name',
        'slug'           => 'slug',
    ];

    /** @var  Offer */
    protected $obOffer;

    protected $arOfferData = [
        'active'         => true,
        'name'           => 'name',
    ];

    /**
     * Check product collection "discount" method
     */
    public function testCollectionMethod()
    {
        $this->createTestData();

        //Check collection method
        $obCollection = ProductCollection::make([$this->obProduct->id])->discount($this->obElement->id);

        self::assertInstanceOf(ProductCollection::class, $obCollection);
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Check product collection "discount" method with relation by product
     */
    public function testRelationWithProduct()
    {
        $this->createTestData();

        //Test empty relations
        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Attach product to discount
        $this->obElement->product()->attach($this->obProduct->id);
        $this->obElement->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Detach product from discount
        $this->obElement->product()->detach($this->obProduct->id);
        $this->obElement->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);

        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Check product collection "discount" method with relation by brand
     */
    public function testRelationWithBrand()
    {
        $this->createTestData();

        //Test empty relations
        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Attach brand to discount
        $this->obElement->brand()->attach($this->obBrand->id);
        $this->obElement->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Set active == false in brand object
        $this->obBrand->active = false;
        $this->obBrand->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Set active == true in brand object
        $this->obBrand->active = true;
        $this->obBrand->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Change brand_id in product object
        $this->obProduct->brand_id = $this->obBrand->id + 1;
        $this->obProduct->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Revert brand_id in product object
        $this->obProduct->brand_id = $this->obBrand->id;
        $this->obProduct->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Attach brand from discount
        $this->obElement->brand()->detach($this->obBrand->id);
        $this->obElement->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Check product collection "discount" method with relation by category
     */
    public function testRelationWithCategory()
    {
        $this->createTestData();

        //Test empty relations
        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Attach category to discount
        $this->obElement->category()->attach($this->obCategory->id);
        $this->obElement->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Set active == false in category object
        $this->obCategory->active = false;
        $this->obCategory->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Set active == true in category object
        $this->obCategory->active = true;
        $this->obCategory->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Change category_id in product object
        $this->obProduct->category_id = $this->obCategory->id + 1;
        $this->obProduct->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());

        //Revert category_id in product object
        $this->obProduct->category_id = $this->obCategory->id;
        $this->obProduct->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals([$this->obProduct->id], $obCollection->getIDList());

        //Attach category from discount
        $this->obElement->category()->detach($this->obCategory->id);
        $this->obElement->save();

        $obCollection = ProductCollection::make()->discount($this->obElement->id);
        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Create  object for test
     */
    protected function createTestData()
    {
        $this->arCreateData['date_begin'] = Argon::now()->subMonth();
        $this->arCreateData['date_end'] = Argon::now()->addMonth();
        $this->arCreateData['discount_type'] = Discount::PERCENT_TYPE;

        //Create discount data
        $arCreateData = $this->arCreateData;
        $this->obElement = Discount::create($arCreateData);

        //Create brand object
        $arCreateData = $this->arBrandData;
        $this->obBrand = Brand::create($arCreateData);

        //Create category object
        $arCreateData = $this->arCategoryData;
        $this->obCategory = Category::create($arCreateData);

        //Create product object
        $arCreateData = $this->arProductData;
        $arCreateData['brand_id'] = $this->obBrand->id;
        $arCreateData['category_id'] = $this->obCategory->id;
        $this->obProduct = Product::create($arCreateData);

        //Create offer object
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);
    }
}
