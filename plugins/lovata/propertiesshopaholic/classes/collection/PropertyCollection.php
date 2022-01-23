<?php namespace Lovata\PropertiesShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\Shopaholic\Classes\Collection\OfferCollection;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\PropertiesShopaholic\Classes\Item\PropertyItem;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyListStore;

/**
 * Class PropertyCollection
 * @package Lovata\PropertiesShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see     \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertyCollectionTest
 */
class PropertyCollection extends ElementCollection
{
    const ITEM_CLASS = PropertyItem::class;

    /** @var  array with data for property set relation */
    protected $arPropertySetRelation;

    /** @var  \Lovata\Shopaholic\Classes\Item\ProductItem|\Lovata\Shopaholic\Classes\Item\OfferItem */
    protected $obElementItem;

    /** @var  \Lovata\Shopaholic\Classes\Item\CategoryItem */
    protected $obCategoryItem;

    /** @var ProductCollection */
    protected $obProductList;

    /** @var OfferCollection */
    protected $obOfferList;

    protected $sModelName;

    protected $arGroupIDList = [];

    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        $arResultIDList = PropertyListStore::instance()->sorting->get();

        return $this->applySorting($arResultIDList);
    }

    /**
     * Apply filter by active element list
     * @return $this
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertyCollectionTest::testActiveList()
     */
    public function active()
    {
        $arResultIDList = PropertyListStore::instance()->active->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * Set property set relation data
     * @param array $arPropertyList
     *
     * @return $this
     */
    public function setPropertySetRelation($arPropertyList)
    {
        if (empty($arPropertyList)) {
            return $this->clear();
        }

        $this->arPropertySetRelation = $arPropertyList;
        $this->initPropertyIDListForModel();

        return $this->returnThis();
    }

    /**
     * Set element item
     * @param \Lovata\Shopaholic\Classes\Item\ProductItem|\Lovata\Shopaholic\Classes\Item\OfferItem $obElementItem
     * @return $this
     */
    public function setItem($obElementItem)
    {
        $this->obElementItem = $obElementItem;

        return $this->returnThis();
    }

    /**
     * Set category item
     * @param \Lovata\Shopaholic\Classes\Item\CategoryItem $obCategoryItem
     * @return $this
     */
    public function setCategory($obCategoryItem)
    {
        $this->obCategoryItem = $obCategoryItem;

        return $this->returnThis();
    }

    /**
     * Set model class name
     * @param string $sModelName
     * @return $this
     */
    public function setModel($sModelName)
    {
        $this->sModelName = $sModelName;

        return $this->returnThis();
    }

    /**
     * Get model name
     * @return string
     */
    public function getModelName()
    {
        return $this->sModelName;
    }

    /**
     * Set product collection object
     * @param ProductCollection $obProductList
     * @return $this
     */
    public function setProductList($obProductList)
    {
        if (empty($obProductList) || !$obProductList instanceof ProductCollection) {
            return $this->returnThis();
        }

        $this->obProductList = $obProductList;

        return $this->returnThis();
    }

    /**
     * Set offer collection object
     * @param OfferCollection $obOfferList
     * @return $this
     */
    public function setOfferList($obOfferList)
    {
        if (empty($obOfferList) || !$obOfferList instanceof OfferCollection) {
            return $this->returnThis();
        }

        $this->obOfferList = $obOfferList;

        return $this->returnThis();
    }

    /**
     * Get property array with group ID
     * @param int $iGroupID
     *
     * @return $this
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertyCollectionTest::testGroupCollectionMethod()
     */
    public function group($iGroupID)
    {
        if (empty($iGroupID) || empty($this->arPropertySetRelation)) {
            $obThis = clone $this;
            $obThis->clear();
            return $obThis;
        }

        $arResult = [];
        foreach ($this->arPropertySetRelation as $iPropertyID => $arPropertyData) {
            if (empty($arPropertyData) || !isset($arPropertyData['groups']) || !is_array($arPropertyData['groups'])) {
                continue;
            }

            if (!in_array($iGroupID, $arPropertyData['groups'])) {
                continue;
            }

            /** @var PropertyItem $obPropertyItem */
            $obPropertyItem = $this->find($iPropertyID);
            if (!$obPropertyItem->hasValue()) {
                continue;
            }

            $arResult[] = $iPropertyID;
        }

        $obThis = clone $this;
        $obThis->intersect($arResult);

        return $obThis;
    }

    /**
     * Get group list for property collection
     * @return GroupCollection
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertyCollectionTest::testGetGroupListMethod()
     */
    public function getGroupList()
    {
        $obGroupCollection = GroupCollection::make($this->arGroupIDList)->sort();

        return $obGroupCollection;
    }

    /**
     * Filter collection by code
     * @param string|array $arCodeList
     * @return $this
     */
    public function code($arCodeList)
    {
        $obList = clone $this;
        if (empty($arCodeList)) {
            return $obList->clear();
        }

        if (!is_array($arCodeList)) {
            $arCodeList = [$arCodeList];
        }

        //Get all properties
        $arPropertyList = $this->all();

        $arResultIDList = [];

        /** @var PropertyItem $obPropertyItem */
        foreach ($arPropertyList as $obPropertyItem) {
            if (!in_array($obPropertyItem->code, $arCodeList)) {
                continue;
            }

            $arResultIDList[] = $obPropertyItem->id;
        }

        return $obList->intersect($arResultIDList);
    }

    /**
     * Get property item by code
     * @param string $sCode
     *
     * @return PropertyItem
     * @see \Lovata\PropertiesShopaholic\Tests\Unit\Collection\PropertyCollectionTest::testGetByCodeCollectionMethod()
     */
    public function getByCode($sCode)
    {
        if ($this->isEmpty() || empty($sCode)) {
            return PropertyItem::make(null);
        }

        $arPropertyList = $this->all();

        /** @var PropertyItem $obPropertyItem */
        foreach ($arPropertyList as $obPropertyItem) {
            if ($obPropertyItem->code == $sCode) {
                return $obPropertyItem;
            }
        }

        return PropertyItem::make(null);
    }

    /**
     * Get property ID list
     */
    protected function initPropertyIDListForModel()
    {
        if (empty($this->arPropertySetRelation)) {
            return;
        }

        $arResult = [];
        foreach ($this->arPropertySetRelation as $arPropertyData) {
            if (empty($arPropertyData) || !isset($arPropertyData['id'])) {
                continue;
            }

            $arResult[] = $arPropertyData['id'];
            if (!empty($arPropertyData['groups'])) {
                $this->arGroupIDList = array_merge($this->arGroupIDList, $arPropertyData['groups']);
            }
        }

        $this->arGroupIDList = array_unique($this->arGroupIDList);
        $this->intersect($arResult);
    }

    /**
     * Make element item
     * @param int                                          $iElementID
     * @param \Lovata\PropertiesShopaholic\Models\Property $obElement
     *
     * @return PropertyItem
     */
    protected function makeItem($iElementID, $obElement = null)
    {
        /** @var PropertyItem $obItem */
        $obItem = parent::makeItem($iElementID, $obElement);
        $obItem->setItem($this->obElementItem);
        $obItem->setCategory($this->obCategoryItem);
        $obItem->setModel($this->sModelName);
        $obItem->setProductList($this->obProductList);
        $obItem->setOfferList($this->obOfferList);
        if (isset($this->arPropertySetRelation[$iElementID])) {
            $obItem->setPropertySetRelationData($this->arPropertySetRelation[$iElementID]);
        }

        return $obItem;
    }

    /**
     * Make element item from cache only
     * @param int $iElementID
     *
     * @return PropertyItem
     */
    protected function makeItemOnlyCache($iElementID)
    {
        /** @var PropertyItem $obItem */
        $obItem = parent::makeItemOnlyCache($iElementID);
        if ($obItem->isEmpty()) {
            return $obItem;
        }

        $obItem->setItem($this->obElementItem);
        $obItem->setCategory($this->obCategoryItem);
        $obItem->setModel($this->sModelName);
        $obItem->setProductList($this->obProductList);
        $obItem->setOfferList($this->obOfferList);
        if (isset($this->arPropertySetRelation[$iElementID])) {
            $obItem->setPropertySetRelationData($this->arPropertySetRelation[$iElementID]);
        }

        return $obItem;
    }
}
