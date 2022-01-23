<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Discount;

use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Item\DiscountItem;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;
use Lovata\DiscountsShopaholic\Classes\Store\DiscountListStore;

/**
 * Class DiscountModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Discount
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class DiscountModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var Discount $obElement */
    protected $obElement;

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        ProductListStore::instance()->discount->clear($this->obElement->id);

        $this->checkFieldChanges('promo_block_id', DiscountListStore::instance()->promo_block);
        $this->checkFieldChanges('active', DiscountListStore::instance()->promo_block);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        ProductListStore::instance()->discount->clear($this->obElement->id);

        DiscountListStore::instance()->promo_block->clear($this->obElement->promo_block_id);

        $this->obElement->brand()->detach();
        $this->obElement->category()->detach();
        $this->obElement->offer()->detach();
        $this->obElement->product()->detach();

        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        $this->obElement->tag()->detach();
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Discount::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return DiscountItem::class;
    }
}
