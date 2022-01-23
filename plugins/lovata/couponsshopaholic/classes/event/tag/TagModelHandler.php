<?php namespace Lovata\CouponsShopaholic\Classes\Event\Tag;

use DB;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;
use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\CouponsShopaholic\Models\CouponGroup;

/**
 * Class TagModelHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Tag
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

        CouponGroup::extend(function ($obElement) {
            /** @var Coupon $obElement */
            $obElement->belongsToMany['tag'] = [
                \Lovata\TagsShopaholic\Models\Tag::class,
                'table' => 'lovata_coupons_shopaholic_group_tag',
                'key'   => 'group_id',
            ];
        });
    }

    /**
     * Extend tag model
     * @param \Lovata\TagsShopaholic\Models\Tag $obElement
     */
    protected function extendTagModel($obElement)
    {
        $obElement->belongsToMany['coupon_group'] = [
            CouponGroup::class,
            'table'    => 'lovata_coupons_shopaholic_group_tag',
            'otherKey' => 'group_id',
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
        $this->obElement->coupon_group()->detach();
    }

    /**
     * Clear product cached list by coupon group ID (Relation between tag and coupon group)
     */
    protected function clearProductList()
    {
        //Get coupon group list
        $arCouponGroupIDList = (array) DB::table('lovata_coupons_shopaholic_group_tag')->where('tag_id', $this->obElement->id)->lists('group_id');
        if (empty($arCouponGroupIDList)) {
            return;
        }

        foreach ($arCouponGroupIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
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
