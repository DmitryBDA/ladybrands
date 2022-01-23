<?php namespace Lovata\PropertiesShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLinkListStore;

/**
 * Class PropertyValueItem
 * @package Lovata\PropertiesShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see     \Lovata\PropertiesShopaholic\Tests\Unit\Item\PropertyValueItemTest
 *
 * @property        $id
 * @property string $value
 * @property string $slug
 */
class PropertyValueItem extends ElementItem
{
    const MODEL_CLASS = PropertyValue::class;

    /** @var PropertyValue */
    protected $obElement = null;

    protected $iPropertyID;
    protected $sModelName;

    /**
     * Set model class name
     * @param string $sModelName
     */
    public function setModel($sModelName)
    {
        $this->sModelName = $sModelName;
    }

    /**
     * Set property ID
     * @param int $iPropertyID
     */
    public function setPropertyID($iPropertyID)
    {
        $this->iPropertyID = $iPropertyID;
    }

    /**
     * Check, value is disabled
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection $obProductList
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection   $obOfferList
     * @return bool
     */
    public function isDisabled($obProductList, $obOfferList = null)
    {
        if (empty($obProductList) || !$obProductList instanceof ProductCollection || $obProductList->isEmpty()) {
            return true;
        }

        if (empty($this->sModelName) || empty($this->iPropertyID)) {
            return true;
        }

        $arFilterProductIDList = PropertyValueLinkListStore::instance()->property->getProductListByValueID($this->iPropertyID, $this->id, $this->sModelName, $obOfferList);

        $bResult = empty($arFilterProductIDList) || empty(array_intersect($arFilterProductIDList, $obProductList->getIDList()));

        return $bResult;
    }
}
