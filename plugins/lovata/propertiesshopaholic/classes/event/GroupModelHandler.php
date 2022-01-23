<?php namespace Lovata\PropertiesShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\Group;
use Lovata\PropertiesShopaholic\Classes\Item\GroupItem;
use Lovata\PropertiesShopaholic\Classes\Store\GroupListStore;

/**
 * Class GroupModelHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class GroupModelHandler extends ModelHandler
{
    /** @var Group */
    protected $obElement;
    
    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Group::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return GroupItem::class;
    }

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);
        $obEvent->listen('shopaholic.property.group.update.sorting', function () {
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
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->clearSortingList();
        parent::afterDelete();
    }

    /**
     * Clear sorting list
     */
    protected function clearSortingList()
    {
        GroupListStore::instance()->sorting->clear();
    }
}
