<?php namespace Lovata\PropertiesShopaholic\Classes\Import;

use Lang;
use Lovata\Toolbox\Classes\Helper\AbstractImportModelFromXML;

use Lovata\Shopaholic\Models\XmlImportSettings;
use Lovata\Shopaholic\Models\Measure;
use Lovata\PropertiesShopaholic\Models\PropertyValue;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class ImportPropertyModelFromXML
 * @package Lovata\PropertiesShopaholic\Classes\Import
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ImportPropertyModelFromXML extends AbstractImportModelFromXML
{
    const MODEL_CLASS = Property::class;

    /** @var Property */
    protected $obModel;

    /** @var array */
    protected $arValueList;

    /** @var array */
    protected $arSettings = [];

    /**
     * ImportPropertyModelFromCSV constructor.
     */
    public function __construct()
    {
        $this->arExistIDList = Property::whereNotNull('external_id')->lists('external_id', 'id');
        $this->arExistIDList = array_filter($this->arExistIDList);

        $this->prepareImportSettings();

        parent::__construct();
    }

    /**
     * Get import fields
     * @return array
     */
    public function getFields() : array
    {
        $this->arFieldList = [
            'external_id'     => Lang::get('lovata.toolbox::lang.field.external_id'),
            'active'          => Lang::get('lovata.toolbox::lang.field.active'),
            'is_translatable' => Lang::get('lovata.toolbox::lang.field.property_is_translatable'),
            'name'            => Lang::get('lovata.toolbox::lang.field.name'),
            'code'            => Lang::get('lovata.toolbox::lang.field.code'),
            'type'            => Lang::get('lovata.toolbox::lang.field.type'),
            'tab'             => Lang::get('lovata.toolbox::lang.field.property_tab'),
            'value'           => Lang::get('lovata.toolbox::lang.field.property_list_value'),
            'measure'         => Lang::get('lovata.shopaholic::lang.field.measure'),
            'description'     => Lang::get('lovata.toolbox::lang.field.description'),
        ];

        return parent::getFields();
    }

    /**
     * Start import
     * @param $obProgressBar
     * @throws
     */
    public function import($obProgressBar = null)
    {
        parent::import($obProgressBar);

        $this->deactivateElements();
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

        parent::processModelObject();
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
        $this->arValueList = array_get($this->arImportData, 'value');
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

        if (empty($this->arValueList) || !in_array($this->obModel->type, $arTypeList)) {
            return;
        }

        $arValueIDList = [];
        foreach ($this->arValueList as $sValue) {
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

    /**
     * Prepare import settings
     */
    protected function prepareImportSettings()
    {
        $this->arXMLFileList = XmlImportSettings::getValue('file_list');
        $this->sImageFolderPath = XmlImportSettings::getValue('image_folder');
        $this->sImageFolderPath = trim($this->sImageFolderPath, '/');

        $this->bDeactivate = (bool) XmlImportSettings::getValue('property_deactivate');
        $this->arImportSettings = XmlImportSettings::getValue('property');
        $this->sElementListPath = XmlImportSettings::getValue('property_path_to_list');

        $iFileNumber = XmlImportSettings::getValue('property_file_path');
        if ($iFileNumber !== null) {
            $this->sMainFilePath = array_get($this->arXMLFileList, $iFileNumber.'.path');
            $this->sMainFilePath = trim($this->sMainFilePath, '/');
        }
    }
}
