<?php namespace Zprimegroup\BaseCode\Classes\Event\Category;

use Lang;
use Lovata\Shopaholic\Models\Measure;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Controllers\Categories;

class ExtendCategoryFieldsHandler extends AbstractBackendFieldHandler
{

    protected function extendFields($obWidget)
    {
        $arAdditionFields = [
          'show_main' => [
            'label' => 'Рекомендованная категория',
            'span' => 'right',
            'default' => '0',
            'type' => 'switch',
          ],

        ];

        $obWidget->addFields($arAdditionFields);
    }

    protected function getModelClass() : string
    {
        return Category::class;
    }

    protected function getControllerClass() : string
    {
        return Categories::class;
    }
}
