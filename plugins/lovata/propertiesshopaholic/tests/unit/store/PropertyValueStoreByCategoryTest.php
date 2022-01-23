<?php namespace Lovata\PropertiesShopaholic\Tests\Unit\Store;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Settings;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLink\ListByCategoryStore;

/**
 * Class PropertyValueStoreByCategoryTest
 * @package Lovata\PropertiesShopaholic\Tests\Unit\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertyValueStoreByCategoryTest extends CommonTest
{
    /** @var  Property */
    protected $obProductProperty;

    /** @var  Property */
    protected $obOfferProperty;

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
     * Check value id list, if product 'active' field was changed
     */
    public function testProductActiveField()
    {
        $this->createTestData();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $this->obProduct->active = false;
        $this->obProduct->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([], $arValueIDList);

        $this->obProduct->active = true;
        $this->obProduct->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $this->obProduct->delete();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([], $arValueIDList);
    }

    /**
     * Check value id list, if product 'category_id' field was changed
     */
    public function testProductCategoryIDField()
    {
        $this->createTestData();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id + 1, Product::class);
        self::assertEquals([], $arValueIDList);

        $this->obProduct->category_id = $this->obCategory->id + 1;
        $this->obProduct->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id + 1, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([], $arValueIDList);

        $this->obProduct->category_id = $this->obCategory->id;
        $this->obProduct->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id + 1, Product::class);
        self::assertEquals([], $arValueIDList);
    }

    /**
     * Check value id list, if offer 'active' field was changed
     */
    public function testOfferActiveFieldNotEnabled()
    {
        $this->createTestData();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);

        $this->obOffer->active = false;
        $this->obOffer->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([], $arValueIDList);

        $this->obOffer->active = true;
        $this->obOffer->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);
    }

    /**
     * Check value id list, if offer 'active' field was changed
     */
    public function testOfferActiveField()
    {
        $this->createTestData();

        Settings::set('check_offer_active', true);

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);

        $this->obOffer->active = false;
        $this->obOffer->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([], $arValueIDList);

        $this->obOffer->active = true;
        $this->obOffer->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);

        $this->obOffer->delete();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([], $arValueIDList);
    }

    /**
     * Check value id list, if offer 'product_id' field was changed
     */
    public function testOfferProductIDField()
    {
        $this->createTestData();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);

        $this->obOffer->product_id = $this->obProduct->id + 1;
        $this->obOffer->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([], $arValueIDList);

        $this->obOffer->product_id = $this->obProduct->id;
        $this->obOffer->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obOfferProperty->id, $this->obCategory->id, Offer::class);
        self::assertEquals([$this->obOfferPropertyValue->id], $arValueIDList);
    }

    /**
     * Check value id list, if property value link fields was changed
     */
    public function testPropertyValieLinkFielda()
    {
        $this->createTestData();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $this->obProductValueLink->value_id = $this->obProductPropertyValue->id + 1;
        $this->obProductValueLink->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id + 1], $arValueIDList);

        $this->obProductValueLink->value_id = $this->obProductPropertyValue->id;
        $this->obProductValueLink->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $this->obProductValueLink->property_id = $this->obProductProperty->id + 1;
        $this->obProductValueLink->save();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id, $this->obCategory->id, Product::class);
        self::assertEquals([], $arValueIDList);

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id + 1, $this->obCategory->id, Product::class);
        self::assertEquals([$this->obProductPropertyValue->id], $arValueIDList);

        $this->obProductValueLink->delete();

        $arValueIDList = ListByCategoryStore::instance()->getValueByCategory($this->obProductProperty->id + 1, $this->obCategory->id, Product::class);
        self::assertEquals([], $arValueIDList);
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
        $this->obOfferProperty = Property::create($arCreateData);

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
            'property_id'  => $this->obOfferProperty->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obOffer->id,
            'element_type' => Offer::class,
            'value_id'     => $this->obOfferPropertyValue->id,
        ]);
    }
}
