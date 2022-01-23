<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Store;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink\ListByPropertyStore;

/**
 * Class PropertyValueStoreByPropertyTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyValueStoreByPropertyTest extends CommonTest
{
    /** @var  Property */
    protected $obProductProperty;

    /** @var  Category */
    protected $obCategory;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    /** @var  PropertyValue */
    protected $obProductPropertyValue;

    /** @var  PropertyValue */
    protected $obOfferPropertyValue;

    /** @var  PropertyValueLink */
    protected $obProductValueLink;

    /** @var  PropertyValueLink */
    protected $obOfferValueLink;

    protected $arCreateData = [
        'name'        => 'name',
        'slug'        => 'slug',
        'code'        => 'code',
        'type'        => 'input',
        'description' => 'description',
    ];

    protected $arProductData = [
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
    ];

    protected $arOfferData = [
        'active'       => true,
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'price'        => '10,50',
        'old_price'    => '11,50',
        'quantity'     => 5,
    ];

    protected $arCategoryData = [
        'active'       => true,
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'nest_depth'   => 0,
        'parent_id'    => 0,
    ];

    /**
     * Check result array of methods
     */
    public function testResultArray()
    {
        $this->createTestData();

        $arResult = [
            'product'       => [$this->obProductPropertyValue->id => [$this->obProduct->id]],
            'offer'         => [$this->obOfferPropertyValue->id => [$this->obOffer->id]],
            'product_offer' => [$this->obOfferPropertyValue->id => [$this->obProduct->id]],
        ];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getValueByProductList($this->obProductProperty->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getValueByProductList($this->obProductProperty->id, Product::class, ProductCollection::make([$this->obProduct->id]));
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getValueByProductList($this->obProductProperty->id, Offer::class, ProductCollection::make([$this->obProduct->id]));
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getValueByProductList($this->obProductProperty->id, Product::class, ProductCollection::make([$this->obProduct->id + 1]));
        self::assertEquals([], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getValueByProductList($this->obProductProperty->id, Offer::class, ProductCollection::make([$this->obProduct->id + 1]));
        self::assertEquals([], $arValueIDList);


        $arValueIDList = ListByPropertyStore::instance()->getProductListByValueID($this->obProductProperty->id, $this->obProductPropertyValue->id, Product::class);
        self::assertEquals([$this->obProduct->id], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getProductListByValueID($this->obProductProperty->id, $this->obOfferPropertyValue->id, Offer::class);
        self::assertEquals([$this->obProduct->id], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getProductListByValueID($this->obProductProperty->id, $this->obProductPropertyValue->id + 1, Product::class);
        self::assertEquals([], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->getProductListByValueID($this->obProductProperty->id, $this->obOfferPropertyValue->id +1, Offer::class);
        self::assertEquals([], $arValueIDList);
    }

    /**
     * Check id list, if 'property_id' field was changed
     */
    public function testPropertyIDField()
    {
        $this->createTestData();

        $arResult = [
            'product'       => [$this->obProductPropertyValue->id => [$this->obProduct->id]],
            'offer'         => [$this->obOfferPropertyValue->id => [$this->obOffer->id]],
            'product_offer' => [$this->obOfferPropertyValue->id => [$this->obProduct->id]],
        ];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);

        $this->obProductValueLink->property_id = $this->obProductProperty->id + 1;
        $this->obProductValueLink->save();

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals(['product' => [], 'offer' => $arResult['offer'], 'product_offer' => $arResult['product_offer']], $arValueIDList);

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id +1);
        self::assertEquals(['product' => $arResult['product'], 'offer' => [], 'product_offer' => []], $arValueIDList);

        $this->obProductValueLink->delete();

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id +1);
        self::assertEquals([], $arValueIDList);
    }

    /**
     * Check id list, if 'value_id' field was changed
     */
    public function testValueIDField()
    {
        $this->createTestData();

        $arResult = [
            'product'       => [$this->obProductPropertyValue->id => [$this->obProduct->id]],
            'offer'         => [$this->obOfferPropertyValue->id => [$this->obOffer->id]],
            'product_offer' => [$this->obOfferPropertyValue->id => [$this->obProduct->id]],
        ];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);

        $this->obProductValueLink->value_id = $this->obProductPropertyValue->id + 1;
        $this->obProductValueLink->save();

        $arResult['product'] = [$this->obProductPropertyValue->id + 1 => [$this->obProduct->id]];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);
    }

    /**
     * Check id list, if offer 'product_id' field was changed
     */
    public function testProductIDField()
    {
        $this->createTestData();

        $arResult = [
            'product'       => [$this->obProductPropertyValue->id => [$this->obProduct->id]],
            'offer'         => [$this->obOfferPropertyValue->id => [$this->obOffer->id]],
            'product_offer' => [$this->obOfferPropertyValue->id => [$this->obProduct->id]],
        ];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);

        $this->obOffer->product_id = $this->obProduct->id + 1;
        $this->obOffer->save();

        $arResult['product_offer'] = [];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);

        $this->obOffer->product_id = $this->obProduct->id;
        $this->obOffer->save();

        $arResult['product_offer'] = [$this->obOfferPropertyValue->id => [$this->obProduct->id]];

        $arValueIDList = ListByPropertyStore::instance()->get($this->obProductProperty->id);
        self::assertEquals($arResult, $arValueIDList);
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        //Create category data
        $this->obCategory = Category::create($this->arCategoryData);

        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $arCreateData['category_id'] = $this->obCategory->id;
        $this->obProduct = Product::create($arCreateData);

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        //Create property
        $this->obProductProperty = Property::create($arCreateData);

        $this->obProductPropertyValue = PropertyValue::create([
            'value' => Product::class,
        ]);

        $this->obProductValueLink = PropertyValueLink::create([
            'property_id'  => $this->obProductProperty->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obProduct->id,
            'element_type' => Product::class,
            'value_id'     => $this->obProductPropertyValue->id,
        ]);

        $this->obOfferPropertyValue = PropertyValue::create([
            'value' => Offer::class,
        ]);

        $this->obOfferValueLink = PropertyValueLink::create([
            'property_id'  => $this->obProductProperty->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obOffer->id,
            'element_type' => Offer::class,
            'value_id'     => $this->obOfferPropertyValue->id,
        ]);
    }
}
