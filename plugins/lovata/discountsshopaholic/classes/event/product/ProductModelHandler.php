<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Product;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\ProductItem;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class ProductModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Product */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Product::extend(function ($obElement) {
            $this->extendModel($obElement);
        });
    }

    /**
     * Extend model object
     * @param Product $obElement
     */
    protected function extendModel($obElement)
    {
        $obElement->belongsToMany['discount'] = [
            Discount::class,
            'table'    => 'lovata_discounts_shopaholic_discount_product',
        ];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if ($this->isFieldChanged('active')) {
            $this->clearCachedList();
        }
        
        if ($this->isFieldChanged('brand_id')) {
            $this->clearProductListByBrandID($this->obElement->brand_id);
            $this->clearProductListByBrandID($this->obElement->getOriginal('brand_id'));
        }

        if ($this->isFieldChanged('category_id')) {
            $this->clearProductListByCategory($this->obElement->category);

            $iOldCategoryID = $this->obElement->getOriginal('category_id');
            //Get old category object
            if (!empty($iOldCategoryID)) {
                $obOldCategory = Category::find($iOldCategoryID);
                $this->clearProductListByCategory($obOldCategory);
            }
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        if (!$this->obElement->active) {
            return;
        }

        $this->clearCachedList();

        $this->clearProductListByCategory($this->obElement->category);
        $this->clearProductListByBrandID($this->obElement->brand_id);
    }

    /**
     * Clear product cached list by discount ID (Relation between product and discount)
     */
    protected function clearCachedList()
    {
        //Get discount list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_product')->where('product_id', $this->obElement->id)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        foreach ($arDiscountIDList as $iDiscountID) {
            ProductListStore::instance()->discount->clear($iDiscountID);
        }
    }
    
    /**
     * Clear product cached list by discount ID (Relation between brand and discount)
     * @param int $iBrandID
     */
    protected function clearProductListByBrandID($iBrandID)
    {
        if (empty($iBrandID)) {
            return;
        }

        //Get discount list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_brand')->where('brand_id', $iBrandID)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        foreach ($arDiscountIDList as $iDiscountID) {
            ProductListStore::instance()->discount->clear($iDiscountID);
        }
    }

    /**
     * Clear product cached list by discount ID (Relation between category and discount)
     * @param \Lovata\Shopaholic\Models\Category $obCategory
     */
    protected function clearProductListByCategory($obCategory)
    {
        if (empty($obCategory)) {
            return;
        }

        //Get discount list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_category')->where('category_id', $obCategory->id)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        foreach ($arDiscountIDList as $iDiscountID) {
            ProductListStore::instance()->discount->clear($iDiscountID);
        }

        $this->clearProductListByCategory($obCategory->parent);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Product::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ProductItem::class;
    }
}
