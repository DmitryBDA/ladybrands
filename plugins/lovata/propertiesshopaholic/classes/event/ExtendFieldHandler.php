<?php namespace Lovata\PropertiesShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Controllers\Offers;
use Lovata\Shopaholic\Controllers\Products;
use Lovata\Shopaholic\Controllers\Categories;
use Lovata\PropertiesShopaholic\Models\Property;

use System\Controllers\Settings as SettingsController;
use Lovata\Shopaholic\Models\Settings as SettingsModel;
use Lovata\Shopaholic\Models\XmlImportSettings;
use Lovata\PropertiesShopaholic\Classes\Import\ImportPropertyModelFromXML;

/**
 * Class ExtendCategoryModel
 * @package Lovata\PropertiesShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) {
            $this->extendCategoryFields($obWidget);
            $this->extendProductFields($obWidget);
            $this->extendOfferFields($obWidget);
            $this->extendSettingsFields($obWidget);
            $this->extendImportSettingsFields($obWidget);
        }, 10000);
    }

    /**
     * Extend fields for product model
     * @param \Backend\Widgets\Form $obWidget
     */
    public function extendProductFields($obWidget)
    {
        if (!$obWidget->getController() instanceof Products || $obWidget->isNested) {
            return;
        }

        // Only for the Product model
        if (!$obWidget->model instanceof Product || $obWidget->context != 'update') {
            return;
        }

        /** @var Category $obCategory */
        $obCategory = $obWidget->model->category;
        if (empty($obCategory)) {
            return;
        }

        //Get product property list
        $obPropertyList = $obCategory->product_property;
        $this->addPropertyFields($obWidget, $obPropertyList);
    }

    /**
     * Extend "Offer" model fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendOfferFields($obWidget)
    {
        if (!$obWidget->getController() instanceof Offers || $obWidget->isNested) {
            return;
        }

        // Only for the Product model
        if (!$obWidget->model instanceof Offer || $obWidget->context != 'update') {
            return;
        }

        //Get product object
        $obProduct = $obWidget->model->product;
        if (empty($obProduct)) {
            return;
        }

        /** @var Category $obCategory */
        $obCategory = $obProduct->category;
        if (empty($obCategory)) {
            return;
        }

        //Get product property list
        $obPropertyList = $obCategory->offer_property;
        $this->addPropertyFields($obWidget, $obPropertyList);
    }

    /**
     * @param \Backend\Widgets\Form                        $obWidget
     * @param \October\Rain\Database\Collection|Property[] $obPropertyList
     */
    protected function addPropertyFields($obWidget, $obPropertyList)
    {
        if ($obPropertyList->isEmpty()) {
            return;
        }

        //Get widget data for properties
        $arAdditionPropertyData = [];
        /** @var Property $obProperty */
        foreach ($obPropertyList as $obProperty) {

            //Check active property
            if (!$obProperty->active) {
                continue;
            }

            $arPropertyData = $obProperty->getWidgetData();
            if (!empty($arPropertyData)) {
                $arAdditionPropertyData[Property::NAME.'['.$obProperty->id.']'] = $arPropertyData;
            }
        }

        // Add fields
        if (empty($arAdditionPropertyData)) {
            return;
        }

        $obWidget->addTabFields($arAdditionPropertyData);
    }

    /**
     * Extend fields for offer model
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendCategoryFields($obWidget)
    {
        if (!$obWidget->getController() instanceof Categories || $obWidget->isNested) {
            return;
        }

        // Only for the Category model
        if (!$obWidget->model instanceof Category || $obWidget->context != 'update') {
            return;
        }

        // Add fields
        $obWidget->addTabFields([
            'inherit_property_set' => [
                'tab'     => 'lovata.propertiesshopaholic::lang.menu.property_set',
                'label'   => 'lovata.propertiesshopaholic::lang.field.inherit_property_set',
                'type'    => 'checkbox',
                'context' => ['update'],
            ],
            'property_set'         => [
                'tab'      => 'lovata.propertiesshopaholic::lang.menu.property_set',
                'type'     => 'relation',
                'nameFrom' => 'name',
                'context'  => ['update'],
            ],
        ]);
    }

    /**
     * Extend settings fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendSettingsFields($obWidget)
    {
        if (!$obWidget->getController() instanceof SettingsController || $obWidget->isNested) {
            return;
        }

        if (!$obWidget->model instanceof SettingsModel) {
            return;
        }

        $arFieldList = [
            'property_value_with_urlencode'    => [
                'label' => 'lovata.propertiesshopaholic::lang.field.property_value_with_urlencode',
                'tab'   => 'lovata.propertiesshopaholic::lang.field.properties',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'property_value_without_str_slug'  => [
                'label' => 'lovata.propertiesshopaholic::lang.field.property_value_without_str_slug',
                'tab'   => 'lovata.propertiesshopaholic::lang.field.properties',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'property_inheriting_property_set' => [
                'label' => 'lovata.propertiesshopaholic::lang.field.property_inheriting_property_set',
                'tab'   => 'lovata.propertiesshopaholic::lang.field.properties',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'hide_property_import_from_csv' => [
                'label' => 'lovata.propertiesshopaholic::lang.field.hide_property_import_from_csv',
                'tab'   => 'lovata.shopaholic::lang.tab.import_setting',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'hide_property_import_from_xml' => [
                'label' => 'lovata.propertiesshopaholic::lang.field.hide_property_import_from_xml',
                'tab'   => 'lovata.shopaholic::lang.tab.import_setting',
                'span'  => 'right',
                'type'  => 'checkbox',
            ],
        ];

        $obWidget->addTabFields($arFieldList);
    }

    /**
     * Extend settings fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendImportSettingsFields($obWidget)
    {
        if (!$obWidget->getController() instanceof SettingsController || $obWidget->isNested) {
            return;
        }

        if (!$obWidget->model instanceof XmlImportSettings) {
            return;
        }

        $obParse = new ImportPropertyModelFromXML();

        $arFieldList = [
            'property_file_path'     => [
                'label'       => 'lovata.toolbox::lang.field.import_from_file',
                'tab'         => 'lovata.toolbox::lang.tab.properties',
                'span'        => 'full',
                'type'        => 'dropdown',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'options'     => 'getFileList',
                'dependsOn'   => 'file_list',
            ],
            'property_path_to_list'  => [
                'label'       => 'lovata.toolbox::lang.field.import_path_to_list',
                'placeholder' => 'lovata.toolbox::lang.field.import_path_to_list_example',
                'tab'         => 'lovata.toolbox::lang.tab.properties',
                'span'        => 'full',
                'type'        => 'text',
            ],
            'property_deactivate'    => [
                'label'   => 'lovata.toolbox::lang.field.import_deactivate',
                'comment' => 'lovata.toolbox::lang.field.import_deactivate_description',
                'tab'     => 'lovata.toolbox::lang.tab.properties',
                'span'    => 'full',
                'type'    => 'checkbox',
            ],
            'property'               => [
                'label'   => 'lovata.toolbox::lang.field.import_field_list',
                'tab'     => 'lovata.toolbox::lang.tab.properties',
                'span'    => 'full',
                'type'    => 'repeater',
                'form'    => [
                    'fields' => [
                        'field'         => [
                            'label'   => 'lovata.toolbox::lang.field.field',
                            'span'    => 'full',
                            'type'    => 'dropdown',
                            'options' => $obParse->getFields(),
                        ],
                        'path_to_field' => [
                            'label'       => 'lovata.toolbox::lang.field.import_path_to_field',
                            'placeholder' => 'lovata.toolbox::lang.field.import_path_to_field_example',
                            'span'        => 'full',
                            'type'        => 'text',
                        ],
                    ],
                ],
            ],
            'product_property_list_path'  => [
                'label'       => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_list',
                'placeholder' => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_list_example',
                'tab'         => 'lovata.shopaholic::lang.menu.product',
                'span'        => 'full',
                'type'        => 'text',
            ],
            'product_property_id_path'  => [
                'label'       => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_id',
                'placeholder' => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_id_example',
                'tab'         => 'lovata.shopaholic::lang.menu.product',
                'span'        => 'full',
                'type'        => 'text',
            ],
            'product_property_value_path'  => [
                'label'       => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_value',
                'placeholder' => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_value_example',
                'tab'         => 'lovata.shopaholic::lang.menu.product',
                'span'        => 'full',
                'type'        => 'text',
            ],
            'offer_property_list_path'  => [
                'label'       => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_list',
                'placeholder' => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_list_example',
                'tab'         => 'lovata.shopaholic::lang.field.offer',
                'span'        => 'full',
                'type'        => 'text',
            ],
            'offer_property_id_path'  => [
                'label'       => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_id',
                'placeholder' => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_id_example',
                'tab'         => 'lovata.shopaholic::lang.field.offer',
                'span'        => 'full',
                'type'        => 'text',
            ],
            'offer_property_value_path'  => [
                'label'       => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_value',
                'placeholder' => 'lovata.propertiesshopaholic::lang.field.import_path_to_property_value_example',
                'tab'         => 'lovata.shopaholic::lang.field.offer',
                'span'        => 'full',
                'type'        => 'text',
            ],
        ];

        $obWidget->addTabFields($arFieldList);
    }
}
