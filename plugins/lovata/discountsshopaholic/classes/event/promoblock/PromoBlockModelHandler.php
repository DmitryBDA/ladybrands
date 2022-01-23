<?php namespace Lovata\DiscountsShopaholic\Classes\Event\PromoBlock;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\PromoBlock;
use Lovata\Shopaholic\Classes\Item\PromoBlockItem;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Store\DiscountListStore;
use Lovata\DiscountsShopaholic\Classes\Collection\DiscountCollection;

/**
 * Class PromoBlockModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\PromoBlock
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PromoBlockModelHandler extends ModelHandler
{
    /** @var PromoBlock */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        PromoBlock::extend(function ($obElement) {
            $this->extendPromoBlockModel($obElement);
        });

        $obEvent->listen(PromoBlock::EVENT_GET_PRODUCT_LIST, function ($iPromoBlockID) {
            return $this->getProductIDList($iPromoBlockID);
        });
    }

    /**
     * Extend brand model
     * @param PromoBlock $obElement
     */
    protected function extendPromoBlockModel($obElement)
    {
        $obElement->hasMany['discount'] = [Discount::class];
    }

    /**
     * Get product ID list by relation between promo block and discounts
     * @param int $iPromoBlockID
     * @return array
     */
    protected function getProductIDList($iPromoBlockID) : array
    {
        $obDiscountList = DiscountCollection::make()->promoBlock($iPromoBlockID);
        if ($obDiscountList->isEmpty()) {
            return [];
        }

        $arResult = [];
        /** @var \Lovata\DiscountsShopaholic\Classes\Item\DiscountItem $obDiscountItem */
        foreach ($obDiscountList as $obDiscountItem) {
            $arProductIDList = $obDiscountItem->product->getIDList();
            if (empty($arProductIDList)) {
                continue;
            }

            $arResult = array_merge($arResult, $arProductIDList);
        }

        $arResult = array_unique($arResult);

        return $arResult;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        DiscountListStore::instance()->promo_block->clear($this->obElement->id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PromoBlock::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return PromoBlockItem::class;
    }
}
