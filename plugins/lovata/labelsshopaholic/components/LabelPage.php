<?php namespace Lovata\LabelsShopaholic\Components;

use Lovata\Toolbox\Classes\Component\ElementPage;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\LabelsShopaholic\Classes\Item\LabelItem;

/**
 * Class LabelPage
 * @package Lovata\LabelsShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LabelPage extends ElementPage
{
    /** @var Label */
    protected $obElement;

    /** @var LabelItem */
    protected $obElementItem;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.labelsshopaholic::lang.component.label_page_name',
            'description' => 'lovata.labelsshopaholic::lang.component.label_page_description',
        ];
    }

    /**
     * Get element object
     * @param string $sElementSlug
     * @return Label
     */
    protected function getElementObject($sElementSlug)
    {
        if (empty($sElementSlug)) {
            return null;
        }

        if ($this->isSlugTranslatable()) {
            $obElement = Label::active()->transWhere('slug', $sElementSlug)->first();
            if (!$this->checkTransSlug($obElement, $sElementSlug)) {
                $obElement = null;
            }
        } else {
            $obElement = Label::active()->getBySlug($sElementSlug)->first();
        }

        return $obElement;
    }

    /**
     * Make new element item
     * @param int   $iElementID
     * @param Label $obElement
     * @return LabelItem
     */
    protected function makeItem($iElementID, $obElement)
    {
        return LabelItem::make($iElementID, $obElement);
    }
}
