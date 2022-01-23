<?php namespace Lovata\LabelsShopaholic\Classes\Event\Label;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\LabelsShopaholic\Classes\Store\LabelListStore;
use Lovata\LabelsShopaholic\Classes\Store\ProductListStore;

/**
 * Class LabelRelationHandler
 * @package Lovata\LabelsShopaholic\Classes\Event\Label
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LabelRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param Label $obModel
     * @param array    $arAttachedIDList
     * @param array    $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearCachedLabelList($arAttachedIDList);
        $this->clearCachedProductList($obModel);
    }

    /**
     * After detach event handler
     * @param Label $obModel
     * @param array    $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearCachedLabelList($arAttachedIDList);
        $this->clearCachedProductList($obModel);
    }

    /**
     * Clear cached list
     * @param Label $obModel
     */
    protected function clearCachedProductList($obModel)
    {
        ProductListStore::instance()->label->clear($obModel->id);
    }

    /**
     * Clear cached list
     * @param array $arAttachedIDList
     */
    protected function clearCachedLabelList($arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }

        foreach ($arAttachedIDList as $iProductID) {
            LabelListStore::instance()->product->clear($iProductID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Label::class;
    }

    /**
     * Get relation name
     * @return array
     */
    protected function getRelationName()
    {
        return ['product'];
    }
}
