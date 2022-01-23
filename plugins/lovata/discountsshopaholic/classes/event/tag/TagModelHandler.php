<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Tag;

use DB;
use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class TagModelHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Tag
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class TagModelHandler extends ModelHandler
{
    protected $iPriority = 900;

    /** @var  \Lovata\TagsShopaholic\Models\Tag */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        parent::subscribe($obEvent);

        \Lovata\TagsShopaholic\Models\Tag::extend(function ($obElement) {
            $this->extendTagModel($obElement);
        });

        Discount::extend(function ($obElement) {
            /** @var Discount $obElement */
            $obElement->belongsToMany['tag'] = [
                \Lovata\TagsShopaholic\Models\Tag::class,
                'table' => 'lovata_discounts_shopaholic_discount_tag',
            ];
        });
    }

    /**
     * Extend tag model
     * @param \Lovata\TagsShopaholic\Models\Tag $obElement
     */
    protected function extendTagModel($obElement)
    {
        $obElement->belongsToMany['discount'] = [
            Discount::class,
            'table'    => 'lovata_discounts_shopaholic_discount_tag',
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
     * Clear product cached list by discount ID (Relation between tag and discount)
     */
    protected function clearProductList()
    {
        //Get discount list
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_tag')->where('tag_id', $this->obElement->id)->lists('discount_id');
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
        return \Lovata\TagsShopaholic\Models\Tag::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return \Lovata\TagsShopaholic\Classes\Item\TagItem::class;
    }
}
