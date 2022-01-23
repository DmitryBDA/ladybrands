<?php namespace VojtaSvoboda\Extend\Classes\Event\Review;

use Lang;
use Lovata\Shopaholic\Models\Measure;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use VojtaSvoboda\Reviews\Models\Review;
use VojtaSvoboda\Reviews\Controllers\Reviews;

class ExtendReviewFieldsHandler extends AbstractBackendFieldHandler
{

    protected function extendFields($obWidget)
    {
        $arAdditionFields = [
          'product_id' => [
            'tab'=>'General',
            'label' => 'Название товара',
            'span' => 'left',
            'default' => 'published',
            'type' => 'dropdown',
            'emptyOption' => 'Не выбрано',
          ],

        ];

        $obWidget->addTabFields($arAdditionFields);
    }

    protected function getModelClass() : string
    {
        return Review::class;
    }

    protected function getControllerClass() : string
    {
        return Reviews::class;
    }
}
