<?php namespace Lovata\FilterShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Offer;

use Lovata\Shopaholic\Models\Price;
use Lovata\FilterShopaholic\Classes\Store\FilterValueStore;
use Lovata\FilterShopaholic\Classes\Helper\PropertyFilterHelper;


/**
 * Class PriceModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PriceModelHandler
{
    protected $iPriority = 900;

    /** @var  Price */
    protected $obElement;

    /** @var  PropertyFilterHelper */
    protected $obPropertyFilterHelper;

    /**
     * OfferModelHandler constructor.
     *
     * @param PropertyFilterHelper $obPropertyFilterHelper
     */
    public function __construct(PropertyFilterHelper $obPropertyFilterHelper)
    {
        $this->obPropertyFilterHelper = $obPropertyFilterHelper;
    }

    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sModelClass = $this->getModelClass();
        $sModelClass::extend(function ($obElement) {

            /** @var \Model $obElement */
            $obElement->bindEvent('model.afterSave', function () use ($obElement) {
                $this->obElement = $obElement;
                $this->afterSave();
            }, $this->iPriority);

            /** @var \Model $obElement */
            $obElement->bindEvent('model.afterDelete', function () use ($obElement) {
                $this->obElement = $obElement;
                $this->afterDelete();
            }, $this->iPriority);
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Price::class;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        $bChangeOldPrice = $this->obElement->old_price_value != $this->obElement->getOriginal('old_price');

        //Check old price value and clear filter cache
        if($bChangeOldPrice && $this->obElement->item_type == Offer::class) {
            $obOffer = $this->obElement->item;
            if (!empty($obOffer) && $obOffer->active) {
                FilterValueStore::instance()->discount->clear();
                FilterValueStore::instance()->offer_discount->clear();
            }
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        if ($this->obElement->item_type == Offer::class) {
            $obOffer = $this->obElement->item;
            if (!empty($obOffer) && $obOffer->active) {
                FilterValueStore::instance()->discount->clear();
                FilterValueStore::instance()->offer_discount->clear();
            }
        }
    }
}
