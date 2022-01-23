<?php namespace Lovata\FilterShopaholic\Classes\Event;

use DB;
use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\FilterShopaholic\Plugin;
use Lovata\FilterShopaholic\Classes\Store\FilterValueStore;
use Lovata\FilterShopaholic\Classes\Helper\PropertyFilterHelper;
use Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection;

/**
 * Class ProductModelHandler
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler extends ModelHandler
{
    /** @var Product */
    protected $obElement;

    /** @var  PropertyFilterHelper */
    protected $obPropertyFilterHelper;

    /**
     * ProductModelHandler constructor.
     *
     * @param PropertyFilterHelper $obPropertyFilterHelper
     */
    public function __construct(PropertyFilterHelper $obPropertyFilterHelper)
    {
        $this->obPropertyFilterHelper = $obPropertyFilterHelper;
    }

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        ProductCollection::extend(function ($obList) {
            $this->extendProductCollection($obList);
        });
    }

    /**
     * Extend category item
     * @param ProductCollection $obList
     */
    protected function extendProductCollection($obList)
    {
        if (empty($obList) || !$obList instanceof ProductCollection) {
            return;
        }

        $this->addFilterByPrice($obList);
        $this->addFilterByBrandList($obList);
        $this->addFilterByDiscount($obList);
        $this->addFilterByQuantity($obList);
        $this->addFilterByQuantityNull($obList);

        if (PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            $this->addFilterByProperty($obList);
        }
    }

    /**
     * Add method for filter by price
     * @param ProductCollection $obList
     */
    protected function addFilterByPrice($obList)
    {
        if (empty($obList) || !$obList instanceof ProductCollection) {
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
                ->select('lovata_shopaholic_offers.product_id')
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

            /** @var array $arProductIDList */
            $arProductIDList = (array) $obQuery->join('lovata_shopaholic_offers', 'lovata_shopaholic_offers.id', '=', 'lovata_shopaholic_prices.item_id')->lists('product_id');
            if (empty($arProductIDList)) {
                return $obList->clear();
            }

            $arProductIDList = array_unique($arProductIDList);

            return $obList->intersect($arProductIDList);
        });
    }

    /**
     * Add method for filter by brand list
     * @param ProductCollection $obList
     */
    protected function addFilterByBrandList($obList)
    {
        if (empty($obList) || !$obList instanceof ProductCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByBrandList', function ($arBrandIDList) use ($obList) {

            if (empty($arBrandIDList) || !is_array($arBrandIDList)) {
                return $obList->returnThis();
            }

            $arProductIDList = [];
            //Process brand list
            foreach ($arBrandIDList as $iBrandID) {
                if (empty($iBrandID)) {
                    continue;
                }

                //Get product list for brand
                $arTempProductIDList = ProductCollection::make()->brand($iBrandID)->getIDList();
                if (empty($arTempProductIDList)) {
                    continue;
                }

                $arProductIDList = array_merge($arProductIDList, $arTempProductIDList);
            }

            return $obList->intersect($arProductIDList);
        });
    }

    /**
     * Add method for filter by product with discount
     * @param ProductCollection $obList
     */
    protected function addFilterByDiscount($obList)
    {
        if (empty($obList) || !$obList instanceof ProductCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByDiscount', function () use ($obList) {
            $arProductIDList = FilterValueStore::instance()->discount->get();

            return $obList->intersect($arProductIDList);
        });
    }

    /**
     * Add method for filter by product with available quantity
     * @param ProductCollection $obList
     */
    protected function addFilterByQuantity($obList)
    {
        if (empty($obList) || !$obList instanceof ProductCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByQuantity', function () use ($obList) {
            $arProductIDList = FilterValueStore::instance()->quantity->get();

            return $obList->intersect($arProductIDList);
        });
    }

  protected function addFilterByQuantityNull($obList)
  {

    if (empty($obList) || !$obList instanceof ProductCollection) {
      return;
    }

    $obList->addDynamicMethod('filterByQuantityNull', function () use ($obList) {
      $arProductIDList = FilterValueStore::instance()->quantity_null->get();
      return $obList->intersect($arProductIDList);
    });
  }

    /**
     * Add method for filter by product properties
     * @param ProductCollection $obList
     */
    protected function addFilterByProperty($obList)
    {
        if (empty($obList) || !$obList instanceof ProductCollection) {
            return;
        }

        $obList->addDynamicMethod('filterByProperty', function ($arFilterList, $obPropertyList, $obOfferList = null) use ($obList) {

            /** @var \Lovata\PropertiesShopaholic\Classes\Collection\PropertyCollection $obPropertyList */
            if (empty($arFilterList)
                || !is_array($arFilterList)
                || empty($obPropertyList)
                || !$obPropertyList instanceof FilterPropertyCollection
            ) {
                return $obList->returnThis();
            }

            $sModelName = $obPropertyList->getModelName();
            //Process filter list
            $newProductCollection = new ProductCollection;

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
                $arProductIDList = null;
                switch ($sFilterType) {
                    case Plugin::TYPE_CHECKBOX:
                        $arProductIDList = $this->obPropertyFilterHelper
                            ->getResultCheckboxFilter($iPropertyID, $arFilterValue, $sModelName, Product::class, $obList, $obOfferList);
                        break;
                    case Plugin::TYPE_SELECT:
                        $arProductIDList = $this->obPropertyFilterHelper
                            ->getResultSelectFilter($iPropertyID, $arFilterValue, $sModelName, Product::class, $obList, $obOfferList);
                        break;
                    case Plugin::TYPE_SWITCH:
                        $arProductIDList = $this->obPropertyFilterHelper
                            ->getResultSelectFilter($iPropertyID, $arFilterValue, $sModelName, Product::class, $obList, $obOfferList);
                        break;
                    case Plugin::TYPE_BETWEEN:
                        $arProductIDList = $this->obPropertyFilterHelper
                            ->getResultBetweenFilter($iPropertyID, $arFilterValue, $sModelName, Product::class, $obOfferList);
                        break;
                    case Plugin::TYPE_SELECT_BETWEEN:
                        $arProductIDList = $this->obPropertyFilterHelper
                            ->getResultBetweenFilter($iPropertyID, $arFilterValue, $sModelName, Product::class, $obOfferList);
                        break;
                    case Plugin::TYPE_RADIO:
                        $arProductIDList = $this->obPropertyFilterHelper
                            ->getResultSelectFilter($iPropertyID, $arFilterValue, $sModelName, Product::class, $obOfferList);
                        break;
                }

                if ($arProductIDList === null) {
                    continue;
                }

                if (empty($arProductIDList)) {
                    return $obList->clear();
                }

                $copyObList = clone $obList;
                $newProductCollection->merge($copyObList->intersect($arProductIDList)->getIDList());
            }
            return $newProductCollection->returnThis();
        });
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        if (!$this->isFieldChanged('active')) {
            return;
        }

        $this->clearPropertyValueLinkByPropertyID();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        if (!$this->obElement->active) {
            return;
        }

        $this->clearPropertyValueLinkByPropertyID();
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
        $obPropertyValueLinkList = \Lovata\PropertiesShopaholic\Models\PropertyValueLink::with('value')->getByElementType(Product::class)->getByElementID($this->obElement->id)->get();
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
