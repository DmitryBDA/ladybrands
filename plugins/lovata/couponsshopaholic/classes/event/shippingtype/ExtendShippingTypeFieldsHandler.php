<?php namespace Lovata\CouponsShopaholic\Classes\Event\ShippingType;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Controllers\ShippingTypes;

/**
 * Class ExtendShippingTypeFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendShippingTypeFieldsHandler extends AbstractBackendFieldHandler
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
        return ShippingType::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return ShippingTypes::class;
    }
}
