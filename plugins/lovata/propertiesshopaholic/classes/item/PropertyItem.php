<?php namespace Lovata\PropertiesShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Classes\Item\MeasureItem;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Classes\Store\PropertyValueLinkListStore;
use Lovata\PropertiesShopaholic\Classes\Collection\PropertyValueCollection;

/**
 * Class PropertyItem
 * @package Lovata\PropertiesShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see     \Lovata\PropertiesShopaholic\Tests\Unit\Item\PropertyItemTest
 *
 * @property                                             $id
 * @property string                                      $name
 * @property string                                      $slug
 * @property string                                      $code
 * @property string                                      $type
 * @property string                                      $description
 *
 * @property int                                         $measure_id
 * @property \Lovata\Shopaholic\Classes\Item\MeasureItem $measure
 *
 * @property PropertyValueCollection                     $property_value
 *
 * Filter for Shopaholic
 * @property bool                                        $in_filter
 * @property string                                      $filter_type
 * @property string                                      $filter_name
 */
class PropertyItem extends ElementItem
{
    const MODEL_CLASS = Property::class;

    /** @var Property */
    protected $obElement = null;

    /** @var  \Lovata\Shopaholic\Classes\Item\ProductItem|\Lovata\Shopaholic\Classes\Item\OfferItem */
    protected $obElementItem;

    /** @var  \Lovata\Shopaholic\Classes\Item\CategoryItem */
    protected $obCategoryItem;

    /** @var \Lovata\Shopaholic\Classes\Collection\ProductCollection */
    protected $obProductList;

    /** @var \Lovata\Shopaholic\Classes\Collection\OfferCollection */
    protected $obOfferList;

    protected $sModelName;

    public $arRelationList = [
        'measure' => [
            'class' => MeasureItem::class,
            'field' => 'measure_id',
        ],
    ];

    /**
     * Get property value
     *
     * @return PropertyValueCollection
     */
    public function getPropertyValueAttribute()
    {
        $obValueList = $this->getAttribute('property_value');
        if (!empty($obValueList) && $obValueList instanceof PropertyValueCollection) {
            return $obValueList;
        }

        $obValueList = PropertyValueCollection::make();
        if (empty($this->sModelName)) {
            return $obValueList;
        }

        if (!empty($this->obElementItem) && isset($this->obElementItem->property_value_array[$this->id])) {
            $arValueIDList = $this->obElementItem->property_value_array[$this->id];
        } else if (!empty($this->obElementItem)) {
            $arValueIDList = [];
        } else if (empty($this->obElementItem) && !empty($this->obCategoryItem)) {
            $arValueIDList = PropertyValueLinkListStore::instance()->category->getValueByCategory($this->id, $this->obCategoryItem->id, $this->sModelName);
        } else {
            $arValueIDList = PropertyValueLinkListStore::instance()->property->getValueByProductList($this->id, $this->sModelName, $this->obProductList, $this->obOfferList);
        }

        $obValueList = PropertyValueCollection::make()->intersect($arValueIDList);
        $obValueList->setModel($this->sModelName)->setPropertyID($this->id);

        $this->setAttribute('property_value', $obValueList);

        return $obValueList;
    }

    /**
     * Property has not empty value, checking
     * @return bool
     */
    public function hasValue()
    {
        return $this->property_value->isNotEmpty();
    }

    /**
     * Set element item
     * @param \Lovata\Shopaholic\Classes\Item\ProductItem|\Lovata\Shopaholic\Classes\Item\OfferItem $obElementItem
     */
    public function setItem($obElementItem)
    {
        $this->obElementItem = $obElementItem;
    }

    /**
     * Set category item
     * @param \Lovata\Shopaholic\Classes\Item\CategoryItem $obCategoryItem
     */
    public function setCategory($obCategoryItem)
    {
        $this->obCategoryItem = $obCategoryItem;
    }

    /**
     * Set model class name
     * @param string $sModelName
     */
    public function setModel($sModelName)
    {
        $this->sModelName = $sModelName;
    }

    /**
     * Set product collection object
     * @param \Lovata\Shopaholic\Classes\Collection\ProductCollection $obProductList
     */
    public function setProductList($obProductList)
    {
        $this->obProductList = $obProductList;
    }

    /**
     * Set offer collection object
     * @param \Lovata\Shopaholic\Classes\Collection\OfferCollection $obOfferList
     */
    public function setOfferList($obOfferList)
    {
        $this->obOfferList = $obOfferList;
    }

    /**
     * set property relation data
     * @param array $arPropertySetRelation
     */
    public function setPropertySetRelationData($arPropertySetRelation)
    {
        if (empty($arPropertySetRelation) || !is_array($arPropertySetRelation)) {
            return;
        }

        foreach ($arPropertySetRelation as $sKey => $sValue) {
            if (array_key_exists($sKey, $this->arModelData)) {
                continue;
            }

            $this->setAttribute($sKey, $sValue);
        }
    }
}
