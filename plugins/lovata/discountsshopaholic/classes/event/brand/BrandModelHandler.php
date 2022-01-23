<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Brand;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Classes\Item\BrandItem;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class BrandModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Brand
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class BrandModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Brand */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Brand::extend(function ($obElement) {
            $this->extendBrandModel($obElement);
        });
    }

    /**
     * Extend brand model
     * @param Brand $obElement
     */
    protected function extendBrandModel($obElement)
    {
        $obElement->belongsToMany['discount'] = [
            Discount::class,
            'table'    => 'lovata_discounts_shopaholic_discount_brand',
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

        $this->clearProductList();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->discount()->detach();
    }

    /**
     * Clear product cached list by discount ID (Relation between brand and discount)
     */
    protected function clearProductList()
    {
        //Get discount list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_brand')->where('brand_id', $this->obElement->id)->lists('discount_id');
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
        return Brand::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return BrandItem::class;
    }
}
