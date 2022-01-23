<?php namespace Lovata\CouponsShopaholic\Classes\Event\Category;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\Shopaholic\Models\Category;
use Lovata\CouponsShopaholic\Classes\Store\ProductListStore;

/**
 * Class CategoryRelationHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Category
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     * @param array $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearProductList($arAttachedIDList);
    }

    /**
     * After detach event handler
     * @param \Model $obModel
     * @param array $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearProductList($arAttachedIDList);
    }

    /**
     * Clear cached product list
     * @param array $arAttachedIDList
     */
    protected function clearProductList($arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }

        foreach ($arAttachedIDList as $iCouponGroupID) {
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID);
            ProductListStore::instance()->coupon_group->clear($iCouponGroupID, true);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() :string
    {
        return Category::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'coupon_group';
    }
}
