<?php namespace Lovata\LabelsShopaholic\Classes\Event\Label;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\LabelsShopaholic\Classes\Item\LabelItem;
use Lovata\LabelsShopaholic\Classes\Store\LabelListStore;

/**
 * Class LabelModelHandler
 * @package Lovata\LabelsShopaholic\Classes\Event\Label
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LabelModelHandler extends ModelHandler
{
    /** @var Label */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        $obEvent->listen('shopaholic.labels.update.sorting', function () {
            LabelListStore::instance()->sorting->clear();
        });
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        parent::afterCreate();

        LabelListStore::instance()->sorting->clear();
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('active', LabelListStore::instance()->active);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        LabelListStore::instance()->sorting->clear();
        $this->clearCacheNotEmptyValue('active', LabelListStore::instance()->active);

        $this->obElement->product()->detach();
    }

    /**
     * @return string
     */
    protected function getItemClass()
    {
        return LabelItem::class;
    }

    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Label::class;
    }
}