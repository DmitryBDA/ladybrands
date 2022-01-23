<?php namespace Lovata\DiscountsShopaholic\Classes\Event\Offer;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Controllers\Offers;

/**
 * Class ExtendOfferFieldsHandler
 * @package Lovata\DiscountsShopaholic\Classes\Event\Offer
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOfferFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $arAdditionFields = [
            'discount' => [
                'type'    => 'partial',
                'tab'     => 'lovata.discountsshopaholic::lang.menu.discount',
                'path'    => '$/lovata/discountsshopaholic/views/discount.htm',
                'context' => ['update'],
            ],
        ];

        $obWidget->addTabFields($arAdditionFields);

        if (empty($obWidget->model->discount_id)) {
            return;
        }

        $arAdditionFields = [
            'active_discount' => [
                'tab'      => 'lovata.shopaholic::lang.tab.price',
                'label'    => 'lovata.discountsshopaholic::lang.field.active_discount',
                'type'     => 'relation',
                'disabled' => true,
                'span'     => 'right',
            ],
            'discount_value'  => [
                'tab'      => 'lovata.shopaholic::lang.tab.price',
                'label'    => 'lovata.discountsshopaholic::lang.field.discount_value',
                'type'     => 'text',
                'disabled' => true,
                'span'     => 'left',
            ],
            'discount_type'  => [
                'tab'      => 'lovata.shopaholic::lang.tab.price',
                'label'    => 'lovata.discountsshopaholic::lang.field.discount_type',
                'type'     => 'text',
                'disabled' => true,
                'span'     => 'right',
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
        return Offer::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Offers::class;
    }
}
