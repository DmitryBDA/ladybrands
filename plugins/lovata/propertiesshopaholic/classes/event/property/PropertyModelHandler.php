<?php namespace Lovata\PropertiesShopaholic\Classes\Event\Property;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyListStore;

/**
 * Class PropertyModelHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event\Property
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PropertyModelHandler extends ModelHandler
{
    /** @var Property */
    protected $obElement;

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Property::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return PropertyItem::class;
    }

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);
        $obEvent->listen('shopaholic.property.update.sorting', function () {
            $this->clearSortingList();
        });
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        parent::afterCreate();
        $this->clearSortingList();
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('active', PropertyListStore::instance()->active);

        $arTypeList = [
            Property::TYPE_SELECT,
            Property::TYPE_CHECKBOX,
            Property::TYPE_RADIO,
            Property::TYPE_BALLOON,
            Property::TYPE_TAG_LIST,
        ];

        if ($this->isFieldChanged('type') && !in_array($this->obElement->type, $arTypeList)) {
            $this->obElement->property_value()->detach();
        }
    }

    /**
     * After delete event handler
     */
    public function afterDelete()
    {
        $this->clearSortingList();
        parent::afterDelete();

        if ($this->obElement->active) {
            PropertyListStore::instance()->active->clear();
        }

        $this->deletePropertyValueLink();

        $this->obElement->property_value()->detach();
    }

    /**
     * Clear sorting list
     */
    protected function clearSortingList()
    {
        PropertyListStore::instance()->sorting->clear();
    }

    /**
     * Delete all property value links after property was removed
     */
    protected function deletePropertyValueLink()
    {
        $obPropertyValueLinkList = PropertyValueLink::getByProperty($this->obElement->id)->get();
        if ($obPropertyValueLinkList->isEmpty()) {
            return;
        }

        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {
            $obPropertyValueLink->delete();
        }
    }
}
