<?php namespace Lovata\SearchShopaholic\Classes\Event;

use Lang;
use System\Classes\PluginManager;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Controllers\Brands;
use Lovata\Shopaholic\Controllers\Products;
use Lovata\Shopaholic\Controllers\Categories;
use Lovata\SearchShopaholic\Classes\Helper\SearchHelper;

/**
 * Class ExtendCategoryModel
 * @package Lovata\SearchShopaholic\Classes\Event
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
            $this->extendSettingsFields($obWidget);
            $this->extendProductFields($obWidget);
            $this->extendBrandFields($obWidget);
            $this->extendCategoryFields($obWidget);
            $this->extendTagFields($obWidget);
        });
    }

    /**
     * Extend settings fields
     * @param \Backend\Widgets\Form $obWidget
     */
    private function extendSettingsFields($obWidget)
    {
        // Only for the Settings controller
        if (!$obWidget->getController() instanceof \System\Controllers\Settings || $obWidget->isNested || empty($obWidget->context)) {
            return;
        }

        // Only for the Settings model
        if (!$obWidget->model instanceof Settings) {
            return;
        }

        $this->addProductSearchSettings($obWidget);
        $this->addCategorySearchSettings($obWidget);
        $this->addBrandSearchSettings($obWidget);
        $this->addTagSearchSettings($obWidget);
    }

    /**
     * Extend product fields
     * @param \Backend\Widgets\Form $obWidget
     */
    private function extendProductFields($obWidget)
    {
        // Only for the Products controller
        if (!$obWidget->getController() instanceof Products || $obWidget->isNested || empty($obWidget->context)) {
            return;
        }

        // Only for the Product model
        if (!$obWidget->model instanceof Product) {
            return;
        }

        $this->addSearchField($obWidget);
    }

    /**
     * Extend brand fields
     * @param \Backend\Widgets\Form $obWidget
     */
    private function extendBrandFields($obWidget)
    {
        // Only for the Brands controller
        if (!$obWidget->getController() instanceof Brands || $obWidget->isNested || empty($obWidget->context)) {
            return;
        }

        // Only for the Brand model
        if (!$obWidget->model instanceof Brand) {
            return;
        }

        $this->addSearchField($obWidget);
    }

    /**
     * Extend category fields
     * @param \Backend\Widgets\Form $obWidget
     */
    private function extendCategoryFields($obWidget)
    {
        // Only for the Categories controller
        if (!$obWidget->getController() instanceof Categories || $obWidget->isNested || empty($obWidget->context)) {
            return;
        }

        // Only for the Category model
        if (!$obWidget->model instanceof Category) {
            return;
        }

        $this->addSearchField($obWidget);
    }

    /**
     * Extend tag fields
     * @param \Backend\Widgets\Form $obWidget
     */
    private function extendTagFields($obWidget)
    {
        // Only for the Tags controller
        if (!$obWidget->getController() instanceof \Lovata\TagsShopaholic\Controllers\Tags || $obWidget->isNested || empty($obWidget->context)) {
            return;
        }

        // Only for the Tag model
        if (!$obWidget->model instanceof \Lovata\TagsShopaholic\Models\Tag) {
            return;
        }

        $this->addSearchField($obWidget);
    }

    /**
     * Add search settings for Product model
     * @param \Backend\Widgets\Form $obWidget
     */
    private function addProductSearchSettings($obWidget)
    {
        $arLabelData = [
            'model' => Lang::get('lovata.shopaholic::lang.product.name'),
        ];

        $arFieldList = [
            'field' => [
                'label'   => 'lovata.searchshopaholic::lang.field.search_field',
                'span'    => 'full',
                'type'    => 'dropdown',
                'options' => [
                    'name'           => 'lovata.toolbox::lang.field.name',
                    'code'           => 'lovata.shopaholic::lang.field.vendor_code',
                    'preview_text'   => 'lovata.toolbox::lang.field.preview_text',
                    'description'    => 'lovata.toolbox::lang.field.description',
                    'search_synonym' => 'lovata.searchshopaholic::lang.field.search_synonym',
                    'search_content' => 'lovata.searchshopaholic::lang.field.search_content',
                ],
            ],
        ];

        $arFieldList = array_merge($arFieldList, $this->getDefaultConfigArray());

        $obWidget->addTabFields([
            'product_search_by' => [
                'tab'   => 'lovata.searchshopaholic::lang.tab.search_settings',
                'label' => Lang::get('lovata.searchshopaholic::lang.field.search_by', $arLabelData),
                'span'  => 'left',
                'type'  => 'repeater',
                'form'  => [
                    'fields' => $arFieldList,
                ],
            ],
        ]);
    }

    /**
     * Add search settings for Brand model
     * @param \Backend\Widgets\Form $obWidget
     */
    private function addBrandSearchSettings($obWidget)
    {
        $arLabelData = [
            'model' => Lang::get('lovata.shopaholic::lang.brand.name'),
        ];

        $arFieldList = [
            'field' => [
                'label'   => 'lovata.searchshopaholic::lang.field.search_field',
                'span'    => 'full',
                'type'    => 'dropdown',
                'options' => [
                    'name'           => 'lovata.toolbox::lang.field.name',
                    'preview_text'   => 'lovata.toolbox::lang.field.preview_text',
                    'description'    => 'lovata.toolbox::lang.field.description',
                    'search_synonym' => 'lovata.searchshopaholic::lang.field.search_synonym',
                    'search_content' => 'lovata.searchshopaholic::lang.field.search_content',
                ],
            ],
        ];

        $arFieldList = array_merge($arFieldList, $this->getDefaultConfigArray());

        $obWidget->addTabFields([
            'brand_search_by' => [
                'tab'   => 'lovata.searchshopaholic::lang.tab.search_settings',
                'label' => Lang::get('lovata.searchshopaholic::lang.field.search_by', $arLabelData),
                'span'  => 'left',
                'type'  => 'repeater',
                'form'  => [
                    'fields' => $arFieldList,
                ],
            ],
        ]);
    }

    /**
     * Add search settings for Category model
     * @param \Backend\Widgets\Form $obWidget
     */
    private function addCategorySearchSettings($obWidget)
    {
        $arLabelData = [
            'model' => Lang::get('lovata.shopaholic::lang.category.name'),
        ];

        $arFieldList = [
            'field' => [
                'label'   => 'lovata.searchshopaholic::lang.field.search_field',
                'span'    => 'full',
                'type'    => 'dropdown',
                'options' => [
                    'name'           => 'lovata.toolbox::lang.field.name',
                    'preview_text'   => 'lovata.toolbox::lang.field.preview_text',
                    'description'    => 'lovata.toolbox::lang.field.description',
                    'search_synonym' => 'lovata.searchshopaholic::lang.field.search_synonym',
                    'search_content' => 'lovata.searchshopaholic::lang.field.search_content',
                ],
            ],
        ];

        $arFieldList = array_merge($arFieldList, $this->getDefaultConfigArray());

        $obWidget->addTabFields([
            'category_search_by' => [
                'tab'   => 'lovata.searchshopaholic::lang.tab.search_settings',
                'label' => Lang::get('lovata.searchshopaholic::lang.field.search_by', $arLabelData),
                'span'  => 'right',
                'type'  => 'repeater',
                'form'  => [
                    'fields' => $arFieldList,
                ],
            ],
        ]);
    }

    /**
     * Add search settings for Tag model, if "Tags for Shopaholic" plugin is installed
     * @param \Backend\Widgets\Form $obWidget
     */
    private function addTagSearchSettings($obWidget)
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        $arLabelData = [
            'model' => Lang::get('lovata.tagsshopaholic::lang.tag.name'),
        ];

        $arFieldList = [
            'field' => [
                'label'   => 'lovata.searchshopaholic::lang.field.search_field',
                'span'    => 'full',
                'type'    => 'dropdown',
                'options' => [
                    'name'           => 'lovata.toolbox::lang.field.name',
                    'preview_text'   => 'lovata.toolbox::lang.field.preview_text',
                    'description'    => 'lovata.toolbox::lang.field.description',
                    'search_synonym' => 'lovata.searchshopaholic::lang.field.search_synonym',
                    'search_content' => 'lovata.searchshopaholic::lang.field.search_content',
                ],
            ],
        ];

        $arFieldList = array_merge($arFieldList, $this->getDefaultConfigArray());

        $obWidget->addTabFields([
            'tag_search_by' => [
                'tab'   => 'lovata.searchshopaholic::lang.tab.search_settings',
                'label' => Lang::get('lovata.searchshopaholic::lang.field.search_by', $arLabelData),
                'span'  => 'right',
                'type'  => 'repeater',
                'form'  => [
                    'fields' => $arFieldList,
                ],
            ],
        ]);
    }

    /**
     * Get default config for field
     * @return array
     */
    private function getDefaultConfigArray()
    {
        $arResult = [
            'min'         => [
                'label'       => 'lovata.searchshopaholic::lang.field.search_min_length',
                'span'        => 'left',
                'type'        => 'number',
                'placeholder' => 3,
            ],
            'weight'      => [
                'label'       => 'lovata.searchshopaholic::lang.field.search_weight',
                'span'        => 'right',
                'type'        => 'number',
                'placeholder' => 100,
            ],
            'type'        => [
                'label'   => 'lovata.toolbox::lang.field.type',
                'span'    => 'left',
                'type'    => 'dropdown',
                'options' => [
                    SearchHelper::TYPE_DEFAULT   => Lang::get('lovata.searchshopaholic::lang.field.search_type_'.SearchHelper::TYPE_DEFAULT),
                    SearchHelper::TYPE_FULL      => Lang::get('lovata.searchshopaholic::lang.field.search_type_'.SearchHelper::TYPE_FULL),
                    SearchHelper::TYPE_ALL_WORDS => Lang::get('lovata.searchshopaholic::lang.field.search_type_'.SearchHelper::TYPE_ALL_WORDS),
                ],
            ],
            'word_weight' => [
                'label'       => 'lovata.searchshopaholic::lang.field.search_word_weight',
                'span'        => 'right',
                'type'        => 'number',
                'placeholder' => 1,
            ],
        ];

        return $arResult;
    }

    /**
     * Add search_synonym field
     * @param \Backend\Widgets\Form $obWidget
     */
    private function addSearchField($obWidget)
    {
        $obWidget->addTabFields([
            'search_synonym' => [
                'label' => 'lovata.searchshopaholic::lang.field.search_synonym',
                'tab'   => 'lovata.searchshopaholic::lang.tab.search_content',
                'span'  => 'full',
                'type'  => 'textarea',
            ],
        ]);
    }
}