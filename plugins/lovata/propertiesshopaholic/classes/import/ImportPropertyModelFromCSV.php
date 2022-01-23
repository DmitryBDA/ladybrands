<?php namespace Lovata\PropertiesShopaholic\Classes\Import;

use Lovata\Toolbox\Classes\Helper\AbstractImportModelFromCSV;

use Lovata\Shopaholic\Models\Measure;
use Lovata\PropertiesShopaholic\Models\PropertyValue;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class ImportPropertyModelFromCSV
 * @package Lovata\PropertiesShopaholic\Classes\Import
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ImportPropertyModelFromCSV extends AbstractImportModelFromCSV
{
    const MODEL_CLASS = Property::class;

    /** @var Property */
    protected $obModel;

    /** @var string */
    protected $sValueList;

    /** @var array */
    protected $arSettings = [];

    /**
     * ImportPropertyModelFromCSV constructor.
     */
    public function __construct()
    {
        $this->arExistIDList = Property::whereNotNull('external_id')->lists('external_id', 'id');
        $this->arExistIDList = array_filter($this->arExistIDList);
    }

    /**
     * Prepare array of import data
     */
    protected function prepareImportData()
    {
        $this->arImportData['settings'] = [];

        $this->setActiveField();
        $this->setMeasureField();
        $this->initSettingsField();
        $this->initValueField();

        parent::prepareImportData();
    }

    /**
     * Process model object after creation/updating
     */
    protected function processModelObject()
    {
        $this->updateSettingsField();
        $this->attachPropertyValues();
    }

    /**
     * Init settings filed value
     */
    protected function initSettingsField()
    {
        $this->arSettings['is_translatable'] = $this->processBooleanValue(array_get($this->arImportData, 'is_translatable'));
        $this->arSettings['tab'] = array_get($this->arImportData, 'tab');
        array_forget($this->arImportData, 'is_translatable');
        array_forget($this->arImportData, 'tab');
    }

    /**
     * Update settings filed value
     */
    protected function updateSettingsField()
    {
        $arSettings = (array) $this->obModel->settings;
        foreach ($this->arSettings as $sKey => $sValue) {
            $arSettings[$sKey] = $sValue;
        }

        $this->obModel->settings = $arSettings;
        $this->obModel->save();
    }

    /**
     * Set measure filed value
     */
    protected function setMeasureField()
    {
        $sMeasure = trim(array_get($this->arImportData, 'measure'));
        array_forget($this->arImportData, 'measure');
        if (empty($sMeasure)) {
            $this->arImportData['measure_id'] = null;
            return;
        }

        $obMeasure = Measure::getByName($sMeasure)->first();
        if (empty($obMeasure)) {
            $obMeasure = Measure::create([
                'name' => $sMeasure,
            ]);
        }

        $this->arImportData['measure_id'] = $obMeasure->id;
    }

    /**
     * Init value list string
     */
    protected function initValueField()
    {
        $this->sValueList = trim(array_get($this->arImportData, 'value'));
        array_forget($this->arImportData, 'value');
    }

    /**
     * Attach property values to property object
     */
    protected function attachPropertyValues()
    {
        $arTypeList = [
            Property::TYPE_SELECT,
            Property::TYPE_CHECKBOX,
            Property::TYPE_RADIO,
            Property::TYPE_BALLOON,
            Property::TYPE_TAG_LIST,
        ];

        if (empty($this->sValueList) || !in_array($this->obModel->type, $arTypeList)) {
            return;
        }

        $arValueIDList = [];
        $arValueList = explode('|', $this->sValueList);
        foreach ($arValueList as $sValue) {
            $obValue = $this->findPropertyValueObject($sValue);
            if (!empty($obValue)) {
                $arValueIDList[] = $obValue->id;
            }
        }

        $this->obModel->property_value()->sync($arValueIDList);
    }

    /**
     * Find value object
     * @param $sValue
     * @return null|PropertyValue
     */
    protected function findPropertyValueObject($sValue)
    {
        $sValue = trim($sValue);
        if (empty($sValue)) {
            return null;
        }

        $sSlug = PropertyValue::getSlugValue($sValue);
        if (empty($sSlug)) {
            return null;
        }

        $obPropertyValue = PropertyValue::getBySlug($sSlug)->first();
        if (empty($obPropertyValue)) {
            try {
                $obPropertyValue = PropertyValue::create([
                    'value' => $sValue,
                ]);
            } catch (\Exception $obException) {
                return null;
            }
        }

        return $obPropertyValue;
    }
}
