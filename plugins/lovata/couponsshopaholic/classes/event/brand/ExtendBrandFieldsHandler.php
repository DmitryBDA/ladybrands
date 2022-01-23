<?php namespace Lovata\CouponsShopaholic\Classes\Event\Brand;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Controllers\Brands;

/**
 * Class ExtendBrandFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Brand
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendBrandFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
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
        return Brand::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Brands::class;
    }
}
