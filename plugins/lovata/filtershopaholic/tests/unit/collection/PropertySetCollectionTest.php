<?php namespace Lovata\FilterShopaholic\Tests\Unit\Collection;

use Lovata\PropertiesShopaholic\Classes\Collection\PropertySetCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use System\Classes\PluginManager;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\FilterShopaholic\Plugin;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyValueCollection;
use Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection;

/**
 * Class PropertySetCollectionTest
 * @package Lovata\FilterShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Property
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PropertySetCollectionTest extends CommonTest
{
    /** @var  Property */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    /** @var  PropertySet */
    protected $obPropertySet;

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

    protected $arPropertySetData = [
        'name' => 'main',
        'code' => 'main',
    ];

    /**
     * Check product filter property collection in category item
     */
    public function testFilterProductPropertyField()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Category item data has not correct product filter property field';

        $obProductList = ProductCollection::make([$this->obProduct->id]);
        $obPropertySetList = PropertySetCollection::make([$this->obPropertySet->id]);

        $obPropertyList = $obPropertySetList->getProductPropertyCollection($obProductList);

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obPropertyList->first();

        self::assertInstanceOf(FilterPropertyCollection::class, $obPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obValueList = $obPropertyItem->property_value;
        self::assertInstanceOf(PropertyValueCollection::class, $obValueList, $sErrorMessage);
        self::assertEquals(Product::class, $obValueList->getValueString(), $sErrorMessage);

        $obPropertyList = $obPropertySetList->getProductPropertyCollection();

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obPropertyList->first();

        self::assertInstanceOf(FilterPropertyCollection::class, $obPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);
    }

    /**
     * Check offer filter property collection in category item
     */
    public function testFilterOfferPropertyField()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Category item data has not correct offer filter property field';


        $obProductList = ProductCollection::make([$this->obProduct->id]);
        $obPropertySetList = PropertySetCollection::make([$this->obPropertySet->id]);

        $obPropertyList = $obPropertySetList->getOfferPropertyCollection($obProductList);

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obPropertyList->first();

        self::assertInstanceOf(FilterPropertyCollection::class, $obPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);

        $obValueList = $obPropertyItem->property_value;
        self::assertInstanceOf(PropertyValueCollection::class, $obValueList, $sErrorMessage);
        self::assertEquals(Offer::class, $obValueList->getValueString(), $sErrorMessage);

        $obPropertyList = $obPropertySetList->getOfferPropertyCollection();

        /** @var PropertyItem $obPropertyItem */
        $obPropertyItem = $obPropertyList->first();

        self::assertInstanceOf(FilterPropertyCollection::class, $obPropertyList, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obPropertyItem->id, $sErrorMessage);
    }

    /**
     * Create data for test
     */
    protected function createTestData()
    {
        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        //Create property
        $this->obElement = Property::create($arCreateData);

        $this->obPropertySet = PropertySet::create($this->arPropertySetData);

        $arPivotData = [
            'filter_name' => 'test',
            'filter_type' => Plugin::TYPE_SELECT,
            'in_filter' => true,
        ];

        //Attach property to category
        $this->obPropertySet->product_property()->attach($this->obElement->id, $arPivotData);
        $this->obPropertySet->offer_property()->attach($this->obElement->id, $arPivotData);
        $this->obPropertySet->save();

        $obPropertyValue = PropertyValue::create([
            'value' => Product::class,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obProduct->id,
            'element_type' => Product::class,
            'value_id'     => $obPropertyValue->id,
        ]);

        $obPropertyValue = PropertyValue::create([
            'value' => Offer::class,
        ]);

        PropertyValueLink::create([
            'property_id'  => $this->obElement->id,
            'product_id'   => $this->obProduct->id,
            'element_id'   => $this->obOffer->id,
            'element_type' => Offer::class,
            'value_id'        => $obPropertyValue->id,
        ]);
    }
}
