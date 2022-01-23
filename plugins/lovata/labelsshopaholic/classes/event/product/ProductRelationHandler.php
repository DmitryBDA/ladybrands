<?php namespace Lovata\LabelsShopaholic\Classes\Event\Product;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\Shopaholic\Models\Product;

use Lovata\LabelsShopaholic\Classes\Store\LabelListStore;
use Lovata\LabelsShopaholic\Classes\Store\ProductListStore;

/**
 * Class ProductRelationHandler
 * @package Lovata\LabelsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param Product $obModel
     * @param array    $arAttachedIDList
     * @param array    $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearCachedLabelList($obModel);
        $this->clearCachedProductList($arAttachedIDList);
    }

    /**
     * After detach event handler
     * @param Product $obModel
     * @param array    $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearCachedLabelList($obModel);
        $this->clearCachedProductList($arAttachedIDList);
    }

    /**
     * Clear cached list
     * @param array $arAttachedIDList
     */
    protected function clearCachedProductList($arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }

        foreach ($arAttachedIDList as $iLabelID) {
            ProductListStore::instance()->label->clear($iLabelID);
        }
    }

    /**
     * Clear cached list
     * @param Product $obModel
     */
    protected function clearCachedLabelList($obModel)
    {
        LabelListStore::instance()->product->clear($obModel->id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Product::class;
    }

    /**
     * Get relation name
     * @return array
     */
    protected function getRelationName()
    {
        return ['label'];
    }
}
