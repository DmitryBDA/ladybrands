<?php namespace Lovata\LabelsShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\LabelsShopaholic\Classes\Item\LabelItem;
use Lovata\LabelsShopaholic\Classes\Store\LabelListStore;

/**
 * Class LabelCollection
 * @package Lovata\LabelsShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class LabelCollection extends ElementCollection
{
    const ITEM_CLASS = LabelItem::class;

    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        //Get sorting list
        $arResultIDList = LabelListStore::instance()->sorting->get();

        return $this->applySorting($arResultIDList);
    }

    /**
     * Apply filter by active field
     * @return $this
     */
    public function active()
    {
        $arResultIDList = LabelListStore::instance()->active->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * @param string $sProductID
     * @return $this
     */
    public function product($sProductID)
    {
        $arResultIDList = LabelListStore::instance()->product->get($sProductID);

        return $this->intersect($arResultIDList);
    }

    /**
     * Get property item by code
     * @param string $sCode
     *
     * @return LabelItem
     */
    public function getByCode($sCode)
    {
        if ($this->isEmpty() || empty($sCode)) {
            return LabelItem::make(null);
        }

        $arLabelList = $this->all();

        /** @var LabelItem $obLabelItem */
        foreach ($arLabelList as $obLabelItem) {
            if ($obLabelItem->code == $sCode) {
                return $obLabelItem;
            }
        }

        return LabelItem::make(null);
    }
}
