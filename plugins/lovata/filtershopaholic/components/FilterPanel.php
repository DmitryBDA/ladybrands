<?php namespace Lovata\FilterShopaholic\Components;

use Cms\Classes\ComponentBase;

use Lovata\PropertiesShopaholic\Classes\Collection\PropertySetCollection;
use Lovata\FilterShopaholic\Classes\Collection\FilterPropertyCollection;

/**
 * Class FilterPanel
 * @package Lovata\FilterShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class FilterPanel extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.filtershopaholic::lang.component.filter_name',
            'description'   => 'lovata.filtershopaholic::lang.component.filter_description',
        ];
    }

    /**
     * Get product property list by property set list
     * @param array $arPropertySetList
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection $obProductList
     * @return FilterPropertyCollection
     */
    public function getProductPropertyList($arPropertySetList, $obProductList = null) : FilterPropertyCollection
    {
        //Get property set list
        $obPropertySetList = PropertySetCollection::make()->sort()->code($arPropertySetList);
        //dd($obPropertySetList); // , $arPropertySetList
        return $obPropertySetList->getProductPropertyCollection($obProductList);
    }

    /**
     * Get offer property list by property set list
     * @param array $arPropertySetList
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection $obOfferList
     * @return FilterPropertyCollection
     */
    public function getOfferPropertyList($arPropertySetList, $obProductList = null, $obOfferList = null) : FilterPropertyCollection
    {
        //Get property set list
        $obPropertySetList = PropertySetCollection::make()->sort()->code($arPropertySetList);

        return $obPropertySetList->getOfferPropertyCollection($obProductList, $obOfferList);
    }
}
