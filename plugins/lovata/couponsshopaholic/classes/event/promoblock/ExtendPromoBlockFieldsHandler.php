<?php namespace Lovata\CouponsShopaholic\Classes\Event\PromoBlock;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\PromoBlock;
use Lovata\Shopaholic\Controllers\PromoBlocks;

/**
 * Class ExtendPromoBlockFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\PromoBlock
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendPromoBlockFieldsHandler extends AbstractBackendFieldHandler
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
        return PromoBlock::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return PromoBlocks::class;
    }
}
