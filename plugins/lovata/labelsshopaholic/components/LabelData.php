<?php namespace Lovata\LabelsShopaholic\Components;

use Lovata\Toolbox\Classes\Component\ElementData;

use Lovata\LabelsShopaholic\Classes\Item\LabelItem;

/**
 * Class LabelData
 * @package Lovata\LabelsShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LabelData extends ElementData
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.labelsshopaholic::lang.component.label_data_name',
            'description'   => 'lovata.labelsshopaholic::lang.component.label_data_description',
        ];
    }

    /**
     * Make new element item
     * @param int $iElementID
     * @return LabelItem
     */
    protected function makeItem($iElementID)
    {
        return LabelItem::make($iElementID);
    }
}
