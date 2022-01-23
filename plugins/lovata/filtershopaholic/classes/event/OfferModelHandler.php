<?php namespace Lovata\FilterShopaholic\Classes\Event;

use DB;
use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\Shopaholic\Classes\Collection\OfferCollection;

use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

use Lovata\FilterShopaholic\Plugin;
use Lovata\FilterShopaholic\Classes\Store\FilterValueStore;
use Lovata\FilterShopaholic\Classes\Helper\PropertyFilterHelper;
use Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection;

/**
 * Class OfferModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferModelHandler extends ModelHandler
{
    protected $bWithRestore = true;

    /** @var  Offer */
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

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);
        OfferCollection::extend(function ($obList) {
            $this->extendOfferCollection($obList);
        });
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        $bChangeActive = $this->isFieldChanged('active');
        $bChangeQuantity = $this->isFieldChanged('quantity');

        //Check active value and clear filter cache
        if ($bChangeActive && $this->obElement->old_price_value > 0) {
            FilterValueStore::instance()->discount->clear();
            FilterValueStore::instance()->offer_discount->clear();
        }

        //Check quantity value and clear filter cache
        if ($bChangeActive || $bChangeQuantity) {
            FilterValueStore::instance()->quantity->clear();
            FilterValueStore::instance()->offer_quantity->clear();
        }

        if (PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            $this->checkProductField();

            if ($bChangeActive) {
                $this->clearPropertyValueLinkByPropertyID();
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

        //Check old price value and clear filter cache
        if ($this->obElement->old_price_value > 0) {
            FilterValueStore::instance()->discount->clear();
            FilterValueStore::instance()->offer_discount->clear();
        }

        //Check quantity value and clear filter cache
        if ($this->obElement->quantity > 0) {
            FilterValueStore::instance()->quantity->clear();
            FilterValueStore::instance()->offer_quantity->clear();
        }

        $this->clearPropertyValueLinkByPropertyID();
    }

    /**
     * After restore event handler
     */
    protected function afterRestore()
    {
        if (!$this->obElement->active) {
            return;
        }

        //Check old price value and clear filter cache
        if ($this->obElement->old_price_value > 0) {
            FilterValueStore::instance()->discount->clear();
            FilterValueStore::instance()->offer_discount->clear();
        }

        //Check quantity value and clear filter cache
        if ($this->obElement->quantity > 0) {
            FilterValueStore::instance()->quantity->clear();
            FilterValueStore::instance()->offer_quantity->clear();
        }

        $this->clearPropertyValueLinkByPropertyID();
    }

    /**
     * Check product_id field
     */
    protected function checkProductField()
    {
        if (!$this->isFieldChanged('product_id')) {
            return;
        }

        //Get property ID list
        $arPropertyIDList = (array) PropertyValueLink::getByElementType(Offer::class)->getByElementID($this->obElement->id)->lists('property_id');
        if (empty($arPropertyIDList)) {
            return;
        }

        foreach ($arPropertyIDList as $iPropertyID) {
            FilterValueStore::instance()->property->clear($iPropertyID);
        }
    }

    /**
     * Extend category item
     * @param OfferCollection $obList
     */
    protected function extendOfferCollection($obList)
    {
        if (empty($obList) || !$obList instanceof OfferCollection) {
            return;
        }

        $this->addFilterByPrice($obList);
        $this->addFilterByDiscount($obList);
        $this->addFilterByQuantity($obList);

        if (PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            $this->addFilterByProperty($obList);
        }
    }

    /**
     * Add method for filter by price
     * @param OfferCollection $obList
     */
    protected function addFilterByPrice($obList)
    {
        if (empty($obList) || !$obList instanceof OfferCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByPrice', function ($fStartPrice, $fStopPrice, $iPriceTypeID = null) use ($obList) {

            $fStartPrice = (float) $fStartPrice;
            $fStopPrice = (float) $fStopPrice;

            if (empty($fStartPrice) && empty($fStopPrice)) {
                return $obList->returnThis();
            }

            /** @var Offer $obQuery */
            $obQuery = DB::table('lovata_shopaholic_prices')
                ->select('lovata_shopaholic_offers.id')
                ->where('lovata_shopaholic_offers.active', true)
                ->whereNull('lovata_shopaholic_offers.deleted_at')
                ->where('lovata_shopaholic_prices.item_type', Offer::class);

            if (empty($iPriceTypeID)) {
                $obQuery->whereNull('lovata_shopaholic_prices.price_type_id');
            } else {
                $obQuery->where('lovata_shopaholic_prices.price_type_id', $iPriceTypeID);
            }

            if (!empty($fStartPrice)) {
                $obQuery->where('lovata_shopaholic_prices.price', '>=', $fStartPrice);
            }

            if (!empty($fStopPrice) && $fStopPrice >= $fStartPrice) {
                $obQuery->where('lovata_shopaholic_prices.price', '<=', $fStopPrice);
            }

            /** @var array $arOfferIDList */
            $arOfferIDList = (array) $obQuery->join('lovata_shopaholic_offers', 'lovata_shopaholic_offers.id', '=', 'lovata_shopaholic_prices.item_id')->lists('id');
            if (empty($arOfferIDList)) {
                return $obList->clear();
            }

            return $obList->intersect($arOfferIDList);
        });
    }

    /**
     * Add method for filter by product with discount
     * @param OfferCollection $obList
     */
    protected function addFilterByDiscount($obList)
    {
        if (empty($obList) || !$obList instanceof OfferCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByDiscount', function () use ($obList) {
            $arProductIDList = FilterValueStore::instance()->offer_discount->get();

            return $obList->intersect($arProductIDList);
        });
    }

    /**
     * Add method for filter by product with available quantity
     * @param OfferCollection $obList
     */
    protected function addFilterByQuantity($obList)
    {
        if (empty($obList) || !$obList instanceof OfferCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByQuantity', function () use ($obList) {
            $arProductIDList = FilterValueStore::instance()->offer_quantity->get();

            return $obList->intersect($arProductIDList);
        });
    }

    /**
     * Add method for filter by offer properties
     * @param OfferCollection $obList
     */
    protected function addFilterByProperty($obList)
    {
        if (empty($obList) || !$obList instanceof OfferCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByProperty', function ($arFilterList, $obPropertyList) use ($obList) {

            /** @var \Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection $obPropertyList */
            if (empty($arFilterList)
                || !is_array($arFilterList)
                || empty($obPropertyList)
                || !$obPropertyList instanceof FilterPropertyCollection
            ) {
                return $obList->returnThis();
            }

            //Process filter list
            foreach ($arFilterList as $iPropertyID => $arFilterValue) {

                if (empty($arFilterValue)) {
                    continue;
                }
                /** @var \Lovata\PropertiesShopaholic\Classes\Item\PropertyItem $obPropertyItem */
                $obPropertyItem = $obPropertyList->find($iPropertyID);
                if ($obPropertyItem->isEmpty()) {
                    continue;
                }

                //Get filter type for property
                $sFilterType = $obPropertyItem->filter_type;
                if (empty($sFilterType)) {
                    continue;
                }

                //Apply filter
                $arOfferIDList = null;
                switch ($sFilterType) {
                    case Plugin::TYPE_CHECKBOX:
                        $arOfferIDList = $this->obPropertyFilterHelper
                            ->getResultCheckboxFilter($iPropertyID, $arFilterValue, Offer::class, Offer::class);
                        break;
                    case Plugin::TYPE_SELECT:
                        $arOfferIDList = $this->obPropertyFilterHelper
                            ->getResultSelectFilter($iPropertyID, $arFilterValue, Offer::class, Offer::class);
                        break;
                    case Plugin::TYPE_SWITCH:
                        $arOfferIDList = $this->obPropertyFilterHelper
                            ->getResultSelectFilter($iPropertyID, $arFilterValue, Offer::class, Offer::class);
                        break;
                    case Plugin::TYPE_BETWEEN:
                        $arOfferIDList = $this->obPropertyFilterHelper
                            ->getResultBetweenFilter($iPropertyID, $arFilterValue, Offer::class, Offer::class);
                        break;
                    case Plugin::TYPE_SELECT_BETWEEN:
                        $arOfferIDList = $this->obPropertyFilterHelper
                            ->getResultBetweenFilter($iPropertyID, $arFilterValue, Offer::class, Offer::class);
                        break;
                    case Plugin::TYPE_RADIO:
                        $arOfferIDList = $this->obPropertyFilterHelper
                            ->getResultSelectFilter($iPropertyID, $arFilterValue, Offer::class, Offer::class);
                        break;
                }

                if ($arOfferIDList === null) {
                    continue;
                }

                if (empty($arOfferIDList)) {
                    return $obList->clear();
                }

                $obList->intersect($arOfferIDList);
            }

            return $obList->returnThis();
        });
    }

    /**
     * Clear property value links, cached by property ID
     */
    protected function clearPropertyValueLinkByPropertyID()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        //Get property value link list
        $obPropertyValueLinkList = \Lovata\PropertiesShopaholic\Models\PropertyValueLink::with('value')->getByElementType(Offer::class)->getByElementID($this->obElement->id)->get();
        if ($obPropertyValueLinkList->isEmpty()) {
            return;
        }

        /** @var \Lovata\PropertiesShopaholic\Models\PropertyValueLink $obPropertyValueLink */
        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {

            $obValue = $obPropertyValueLink->value;
            if (empty($obValue)) {
                continue;
            }

            FilterValueStore::instance()->property->clear($obValue->slug);
        }
    }
}
