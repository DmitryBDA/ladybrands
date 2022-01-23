<?php namespace Lovata\LabelsShopaholic\Classes\Item;

use Cms\Classes\Page as CmsPage;

use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\Toolbox\Classes\Helper\PageHelper;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

/**
 * Class LabelItem
 * @package Lovata\LabelsShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                             $id
 * @property string                                                          $name
 * @property string                                                          $slug
 * @property string                                                          $code
 *
 * @property string                                                          $description
 * @property \System\Models\File                                             $image
 * @property ProductCollection|\Lovata\Shopaholic\Classes\Item\ProductItem[] $product
 */
class LabelItem extends ElementItem
{
    const MODEL_CLASS = Label::class;

    protected function getProductAttribute()
    {
        $obCollection = ProductCollection::make()->label($this->id);

        return $obCollection;
    }

    /**
     * Returns URL of a label page.
     *
     * @param string $sPageCode
     *
     * @return string
     */
    public function getPageUrl($sPageCode = 'label')
    {
        //Get URL params
        $arParamList = $this->getPageParamList($sPageCode);

        //Generate page URL
        $sURL = CmsPage::url($sPageCode, $arParamList);

        return $sURL;
    }

    /**
     * Get URL param list by page code
     * @param string $sPageCode
     * @return array
     */
    public function getPageParamList($sPageCode) : array
    {
        $arPageParamList = [];

        //Get URL params for page
        $arParamList = PageHelper::instance()->getUrlParamList($sPageCode, 'LabelPage');
        if (!empty($arParamList)) {
            $sPageParam = array_shift($arParamList);
            $arPageParamList[$sPageParam] = $this->slug;
        }

        return $arPageParamList;
    }
}
