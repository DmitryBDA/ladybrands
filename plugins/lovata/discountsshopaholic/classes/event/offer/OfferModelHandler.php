<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Offer;

use DB;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Item\OfferItem;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Item\DiscountItem;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class OfferModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Offer */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        Offer::extend(function ($obElement) {
            $this->extendModel($obElement);
        });

        OfferItem::extend(function ($obOfferItem) {
            $this->extendItem($obOfferItem);
        });
    }

    /**
     * Extend model object
     * @param Offer $obElement
     */
    protected function extendModel($obElement)
    {
        $obElement->addCachedField([
            'discount_id',
            'discount_value',
            'discount_type',
        ]);

        $obElement->belongsToMany['discount'] = [
            Discount::class,
            'table'    => 'lovata_discounts_shopaholic_discount_offer',
        ];

        $obElement->belongsTo['active_discount'] = [
            Discount::class,
            'key' => 'discount_id',
        ];
    }

    /**
     * Extend item object
     * @param OfferItem $obOfferItem
     */
    protected function extendItem($obOfferItem)
    {
        $obOfferItem->arRelationList['discount'] = [
            'class' => DiscountItem::class,
            'field' => 'discount_id',
        ];
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('active') && !$this->isFieldChanged('product_id')) {
            return;
        }

        $this->clearCachedList();
        $this->clearCachedListByProduct($this->obElement->product_id);
        if ($this->isFieldChanged('product_id')) {
            $this->clearCachedListByProduct($this->obElement->getOriginal('product_id'));
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
        $this->clearCachedListByProduct($this->obElement->product_id);
    }

    /**
     * Clear product cached list by discount ID (Relation between offer and discount)
     */
    protected function clearCachedList()
    {
        //Get coupon group list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_offer')->where('offer_id', $this->obElement->id)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        foreach ($arDiscountIDList as $iDiscountID) {
            ProductListStore::instance()->discount->clear($iDiscountID);
        }
    }

    /**
     * Clear product cached list by discount ID (Relation between product and discount)
     * @param int $iProductID
     */
    protected function clearCachedListByProduct($iProductID)
    {
        if (!Settings::getValue('check_offer_active') || empty($iProductID)) {
            return;
        }

        //Get coupon group list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_product')->where('product_id', $iProductID)->lists('discount_id');
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
        return Offer::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return OfferItem::class;
    }
}
