<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Category;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class CategoryModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Category
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Category */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Category::extend(function ($obElement) {
            $this->extendCategoryModel($obElement);
        });

        $obEvent->listen('shopaholic.category.update.sorting', function () {
            $this->clearAllCategories();
        });
    }

    /**
     * Extend model object
     * @param Category $obElement
     */
    protected function extendCategoryModel($obElement)
    {
        $obElement->belongsToMany['discount'] = [
            Discount::class,
            'table'    => 'lovata_discounts_shopaholic_discount_category',
        ];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('active')) {
            return;
        }

        $this->clearProductList($this->obElement);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->discount()->detach();
        if (!$this->obElement->active) {
            return;
        }

        $this->clearProductList($this->obElement);
    }

    /**
     * Clear product cached list by discount ID (Relation between category and discount)
     * @param Category $obCategory
     */
    protected function clearProductList($obCategory)
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

        $this->clearProductList($obCategory->parent);
    }

    /**
     * Clear product cached list by discount ID (Relation between category and discount)
     */
    protected function clearAllCategories()
    {
        //Get discount list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_category')->groupBy('discount_id')->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        foreach ($arDiscountIDList as $iDiscountID) {
            ProductListStore::instance()->discount->clear($iDiscountID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Category::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return CategoryItem::class;
    }
}
