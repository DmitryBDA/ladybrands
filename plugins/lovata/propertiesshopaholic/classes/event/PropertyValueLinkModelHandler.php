<?php namespace Lovata\PropertiesShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLinkListStore;

/**
 * Class PropertyValueLinkModelHandler
 * @package Lovata\PropertiesShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA PropertyValueLink
 */
class PropertyValueLinkModelHandler extends ModelHandler
{
    /** @var PropertyValueLink */
    protected $obElement;
    
    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PropertyValueLink::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return null;
    }

    /**
     * After save event handler
     * @throws \Exception
     */
    protected function afterSave()
    {
        $this->checkFieldChanges('property_id', PropertyValueLinkListStore::instance()->property);

        if ($this->isFieldChanged('value_id')) {
            $this->removeValueObject($this->obElement->getOriginal('value_id'));

            PropertyValueLinkListStore::instance()->property->clear($this->obElement->property_id);
        }

        if ($this->isFieldChanged('value_id') || $this->isFieldChanged('property_id')) {
            $this->clearCacheByCategory();
        }
    }

    /**
     * After delete event handler
     * @throws \Exception
     */
    protected function afterDelete()
    {
        PropertyValueLinkListStore::instance()->property->clear($this->obElement->property_id);

        $this->removeValueObject($this->obElement->value_id);

        $this->clearCacheByCategory();
    }

    /**
     * Remove property value object, if value has not relations
     * @param int $iValueID
     * @throws \Exception
     */
    protected function removeValueObject($iValueID)
    {
        if (empty($iValueID)) {
            return;
        }

        //Get property value links with value_id = $this->value_id
        $obPropertyValueLinkList = PropertyValueLink::getByValue($iValueID)->get();
        foreach ($obPropertyValueLinkList as $obPropertyValueLink) {
            if ($obPropertyValueLink->id != $this->obElement->id) {
                return;
            }
        }

        $obValue = PropertyValue::find($iValueID);
        if (empty($obValue) || $obValue->property->isNotEmpty()) {
            return;
        }

        $obValue->delete();
    }

    /**
     * Clear cache property value list by category ID
     */
    protected function clearCacheByCategory()
    {
        //Get link element
        $obRelatedModel = $this->obElement->element;
        if (empty($obRelatedModel)) {
            return;
        }

        $arPropertyIDList = [$this->obElement->property_id, $this->obElement->getOriginal('property_id')];
        $arPropertyIDList = array_unique($arPropertyIDList);

        if ($obRelatedModel instanceof Product) {
            PropertyValueLinkListStore::instance()->category->clearByProduct($obRelatedModel, $arPropertyIDList);
        } elseif($obRelatedModel instanceof Offer) {
            PropertyValueLinkListStore::instance()->category->clearByOffer($obRelatedModel, $obRelatedModel->product_id, $arPropertyIDList);
        }
    }
}
