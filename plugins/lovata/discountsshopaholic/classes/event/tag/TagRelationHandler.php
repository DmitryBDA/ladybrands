<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Tag;

use System\Classes\PluginManager;
use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\DiscountsShopaholic\Classes\Store\ProductListStore;

/**
 * Class TagRelationHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Tag
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class TagRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * Add listeners
     */
    public function subscribe()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        parent::subscribe();
    }

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

        foreach ($arAttachedIDList as $iDiscountID) {
            ProductListStore::instance()->discount->clear($iDiscountID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() :string
    {
        return \Lovata\TagsShopaholic\Models\Tag::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'discount';
    }
}
