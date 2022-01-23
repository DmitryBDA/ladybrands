<?php namespace Lovata\LabelsShopaholic\Components;

use Cms\Classes\ComponentBase;
use Lovata\LabelsShopaholic\Classes\Collection\LabelCollection;

/**
 * Class LabelList
 * @package Lovata\LabelsShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LabelList extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.labelsshopaholic::lang.component.label_list_name',
            'description'   => 'lovata.labelsshopaholic::lang.component.label_list_description',
        ];
    }

    /**
     * Make element collection
     * @param array $arElementIDList
     *
     * @return LabelCollection
     */
    public function make($arElementIDList = null)
    {
        return LabelCollection::make($arElementIDList);
    }

    /**
     * Method for ajax request with empty response
     * @return bool
     */
    public function onAjaxRequest()
    {
        return true;
    }
}
