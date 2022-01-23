<?php namespace Lovata\CouponsShopaholic\Classes\Event\Category;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Controllers\Categories;

/**
 * Class ExtendCategoryFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Category
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendCategoryFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend backend fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $arAdditionFields = [
            'coupon_group' => [
                'type'    => 'partial',
                'tab'     => 'lovata.couponsshopaholic::lang.menu.coupon_group',
                'path'    => '$/lovata/couponsshopaholic/views/coupon_group.htm',
                'context' => ['update'],
            ],
        ];

        $obWidget->addTabFields($arAdditionFields);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Category::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Categories::class;
    }
}
