<?php namespace Lovata\PropertiesShopaholic\Classes\Item;

use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\PropertiesShopaholic\Models\PropertySet;

/**
 * Class PropertySetItem
 * @package Lovata\PropertiesShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see     \Lovata\PropertiesShopaholic\Tests\Unit\Item\PropertySetItemTest
 *
 * @property        $id
 * @property bool   $is_global
 * @property string $name
 * @property string $code
 * @property string $description
 * @property array  $product_property_list
 * @property array  $offer_property_list
 */
class PropertySetItem extends ElementItem
{
    const MODEL_CLASS = PropertySet::class;

    /** @var PropertySet */
    protected $obElement = null;

    /**
     * Get element data for cache
     * @return array
     */
    public function getElementData()
    {
        $obProductPropertyList = $this->obElement->product_property;
        $obOfferPropertyList = $this->obElement->offer_property;

        $arResult = [
            'product_property_list' => $this->getPropertyListData($obProductPropertyList),
            'offer_property_list'   => $this->getPropertyListData($obOfferPropertyList),
        ];

        return $arResult;
    }

    /**
     * Get property list data with pivot data
     * @param \October\Rain\Database\Collection|\Lovata\PropertiesShopaholic\Models\Property[] $obPropertyList
     * @return array
     */
    protected function getPropertyListData($obPropertyList)
    {
        if (empty($obPropertyList) || $obPropertyList->isEmpty()) {
            return null;
        }

        //Process property list and pivot data
        $arResult = [];
        foreach ($obPropertyList as $obProperty) {
            /** @var \Lovata\PropertiesShopaholic\Models\PropertyProductLink|\Lovata\PropertiesShopaholic\Models\PropertyOfferLink $obPropertyPivot */
            $obPropertyPivot = $obProperty->pivot;
            $arPropertyData = [
                'id'     => $obProperty->id,
                'groups' => $obPropertyPivot->groups,
            ];

            //Add filter data
            if (PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')) {
                $arPropertyData['in_filter'] = $obPropertyPivot->in_filter;
                $arPropertyData['filter_type'] = $obPropertyPivot->filter_type;
                $arPropertyData['filter_name'] = $obPropertyPivot->filter_name;
            }

            $arResult[$obProperty->id] = $arPropertyData;
        }

        return $arResult;
    }
}
