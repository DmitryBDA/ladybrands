<?php namespace Lovata\PropertiesShopaholic\Classes\Helper;

use DB;
use Input;
use System\Classes\PluginManager;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

/**
 * Class CommonPropertyHelper
 * @package Lovata\PropertiesShopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CommonPropertyHelper
{
    /** @var string */
    protected $sModelClass;

    /** @var int */
    protected $iProductID;

    /** @var \Lovata\Shopaholic\Models\Offer|\Lovata\Shopaholic\Models\Product */
    protected $obElement;

    /** @var \October\Rain\Database\Collection|\Lovata\PropertiesShopaholic\Models\PropertyValueLink[] */
    protected $obValueLinkList;

    /** @var array */
    protected $arValueList;

    /** @var array */
    protected $arProcessedLinkList = [];

    /**
     * ProductPropertyHelper constructor.
     * @param Product|Offer $obElement
     */
    public function __construct($obElement)
    {
        if (empty($obElement) || (!$obElement instanceof Product && !$obElement instanceof Offer)) {
            return;
        }

        $this->obElement = $obElement;

        $this->sModelClass = get_class($this->obElement);

        $this->iProductID = $obElement instanceof Product ? $this->obElement->id : $this->obElement->product_id;

        //Get property values
        $this->obValueLinkList = PropertyValueLink::with('value', 'property')->getByElementType($this->sModelClass)
            ->getByElementID($this->obElement->id)
            ->get();
    }

    /**
     * Set properties (backend)
     * @param array $arValueList
     * @@throws \Exception
     */
    public function setPropertyAttribute($arValueList)
    {
        $this->arValueList = $arValueList;
        $this->processValueList();
        $this->removeOldValue();
    }

    /**
     * Get property values (backend)
     * @return array
     * @throws \Exception
     */
    public function getPropertyAttribute() : array
    {
        $arResult = [];
        if (empty($this->obElement) || $this->obValueLinkList->isEmpty()) {
            return $arResult;
        }

        foreach ($this->obValueLinkList as $obValueLink) {

            //Get property object
            $obProperty = $obValueLink->property;
            $obValue = $obValueLink->value;
            if (empty($obValue) || empty($obProperty)) {
                $obValueLink->delete();
                continue;
            }

            if ($obProperty->type == Property::TYPE_CHECKBOX) {
                if (!isset($arResult[$obProperty->id])) {
                    $arResult[$obProperty->id] = [];
                }

                $arResult[$obProperty->id][] = $obValue->value;
            } else {
                $arResult[$obProperty->id] = $obValue->value;
            }
        }

        return $arResult;
    }

    /**
     * Process array with property values. Save/create/update/remove property values.
     */
    protected function processValueList()
    {
        if (empty($this->arValueList) || !is_array($this->arValueList)) {
            return;
        }

        //Process property value list
        foreach ($this->arValueList as $iPropertyID => $sValue) {

            //Get property object
            $obProperty = Property::find($iPropertyID);

            //Check value
            if (empty($obProperty) || !PropertyValue::hasValue($sValue) || ($obProperty->type == Property::TYPE_CHECKBOX && empty($sValue))) {
                continue;
            }

            //If value is array, then we need to process value list
            if (is_array($sValue)) {
                foreach ($sValue as $sSingleValue) {
                    if (!PropertyValue::hasValue($sSingleValue)) {
                        continue;
                    }

                    $this->processPropertyValue($iPropertyID, $sSingleValue);
                }
            } else {
                $this->processPropertyValue($iPropertyID, $sValue);
            }
        }
    }

    /**
     * Remove old property value links
     * @throws \Exception
     */
    protected function removeOldValue()
    {
        if ($this->obValueLinkList->isEmpty()) {
            return;
        }

        foreach ($this->obValueLinkList as $obPropertyValueLink) {
            if (in_array($obPropertyValueLink->id, $this->arProcessedLinkList)) {
                continue;
            }

            $obPropertyValueLink->delete();
        }
    }

    /**
     * @param int    $iPropertyID
     * @param string $sValue
     */
    protected function processPropertyValue($iPropertyID, $sValue)
    {
        $obProperty = Property::find($iPropertyID);
        if (empty($obProperty)) {
            return;
        }

        //Get property value object
        $obPropertyValue = $this->getValueObject($sValue, $obProperty);
        if (empty($obPropertyValue)) {
            return;
        }

        //Find property value link object
        $obPropertyValueLink = $this->findPropertyLink($iPropertyID);
        if (empty($obPropertyValueLink)) {
            $obPropertyValueLink = PropertyValueLink::create([
                'value_id'     => $obPropertyValue->id,
                'property_id'  => $iPropertyID,
                'product_id'   => $this->iProductID,
                'element_id'   => $this->obElement->id,
                'element_type' => $this->sModelClass,
            ]);

        } elseif ($obPropertyValueLink->value_id != $obPropertyValue->id) {
            $obPropertyValueLink->value_id = $obPropertyValue->id;
            $obPropertyValueLink->save();
        }

        $this->arProcessedLinkList[] = $obPropertyValueLink->id;
    }

    /**
     * Get property value object by slug value
     * @param string   $sValue
     * @param Property $obProperty
     * @return PropertyValue
     */
    protected function getValueObject($sValue, $obProperty)
    {
        $sSlug = PropertyValue::getSlugValue($sValue);
        if (!PropertyValue::hasValue($sSlug)) {
            return null;
        }

        $obPropertyValue = PropertyValue::getBySlug($sSlug)->first();
        try {
            if (empty($obPropertyValue)) {
                $obPropertyValue = PropertyValue::create(['value' => $sValue]);
            } else {
                $obPropertyValue->value = $sValue;
                $obPropertyValue->save();
            }
        } catch (\Exception $obException) {
            $obPropertyValue = PropertyValue::getBySlug($sSlug)->first();
        }

        if (!PluginManager::instance()->hasPlugin('RainLab.Translate') || empty(\RainLab\Translate\Models\Locale::listEnabled())) {
            return $obPropertyValue;
        }

        $arActiveLangList = \RainLab\Translate\Models\Locale::listEnabled();
        foreach (array_keys($arActiveLangList) as $sLangCode) {
            //Get lang value from request
            $sLangValue = Input::get('RLTranslate.'.$sLangCode.'.property.'.$obProperty->id);
            if (!in_array($obProperty->type, [Property::TYPE_INPUT, Property::TYPE_TEXT_AREA, Property::TYPE_RICH_EDITOR]) && empty($sLangValue)) {
                continue;
            }

            if (!empty($sLangValue)) {
                $obPropertyValue->setAttributeTranslated('value', $sLangValue, $sLangCode);
            } else {
                DB::table('rainlab_translate_attributes')
                    ->where('locale', $sLangCode)
                    ->where('model_id', $obPropertyValue->id)
                    ->where('model_type', PropertyValue::class)
                    ->delete();
            }
        }

        $obPropertyValue->save();

        return $obPropertyValue;
    }

    /**
     * Find property link by property ID
     * @param int $iPropertyID
     * @return null|PropertyValueLink
     */
    protected function findPropertyLink($iPropertyID)
    {
        if ($this->obValueLinkList->isEmpty()) {
            return null;
        }

        foreach ($this->obValueLinkList as $obPropertyValueLink) {
            if ($obPropertyValueLink->property_id != $iPropertyID || in_array($obPropertyValueLink->id, $this->arProcessedLinkList)) {
                continue;
            }

            return $obPropertyValueLink;
        }

        return null;
    }
}