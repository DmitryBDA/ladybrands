<?php namespace Lovata\CouponsShopaholic\Classes\Event\CouponGroup;

use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Models\PromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater\PositionTotalPriceGreaterDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater\PositionTotalPriceGreaterDiscountTotalPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountMinPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountPositionTotalPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountTotalPrice;

use Lovata\CouponsShopaholic\Models\CouponGroup;
use Lovata\CouponsShopaholic\Controllers\CouponGroups;

/**
 * Class ExtendCouponGroupFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\CouponGroup
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendCouponGroupFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        //Get promo-mechanism list and add triggers
        $obPromoMechanismList = PromoMechanism::all();
        if ($obPromoMechanismList->isEmpty()) {
            return;
        }

        $arPositionMechanismList = [];
        $arShippingTypeMechanismList = [];
        $arMechanismClassList = [
            WithoutConditionDiscountMinPrice::class,
            WithoutConditionDiscountPositionTotalPrice::class,
            WithoutConditionDiscountShippingPrice::class,
            WithoutConditionDiscountTotalPrice::class,
            PositionTotalPriceGreaterDiscountShippingPrice::class,
            PositionTotalPriceGreaterDiscountTotalPrice::class,
        ];

        foreach ($obPromoMechanismList as $obPromoMechanism) {
            $sMechanismClass = $obPromoMechanism->type;
            if (empty($sMechanismClass) || !class_exists($sMechanismClass)) {
                $arPositionMechanismList[] = $obPromoMechanism->id;
                continue;
            }

            if ($sMechanismClass::getType() == AbstractPromoMechanism::TYPE_SHIPPING) {
                $arShippingTypeMechanismList[] = $obPromoMechanism->id;
            }

            if (in_array($sMechanismClass, $arMechanismClassList)) {
                $arPositionMechanismList[] = $obPromoMechanism->id;
            }
        }

        $arFieldList = $this->getFieldList($arPositionMechanismList, $arShippingTypeMechanismList);

        $obWidget->addTabFields($arFieldList);
    }

    /**
     * Get field list
     * @param array $arPositionMechanismList
     * @param array $arShippingTypeMechanismList
     * @return array
     */
    protected function getFieldList($arPositionMechanismList, $arShippingTypeMechanismList)
    {
        $arFieldList = [
            'product'       => [
                'span'    => 'full',
                'context' => ['update', 'preview'],
                'type'    => 'partial',
                'path'    => '~/plugins/lovata/couponsshopaholic/controllers/coupongroups/_product.htm',
                'tab'     => 'lovata.shopaholic::lang.menu.product',
            ],
            'offer'         => [
                'span'    => 'full',
                'context' => ['update', 'preview'],
                'type'    => 'partial',
                'path'    => '~/plugins/lovata/couponsshopaholic/controllers/coupongroups/_offer.htm',
                'tab'     => 'lovata.shopaholic::lang.tab.offer',
            ],
            'brand'         => [
                'span'    => 'full',
                'context' => ['update', 'preview'],
                'type'    => 'partial',
                'path'    => '~/plugins/lovata/couponsshopaholic/controllers/coupongroups/_brand.htm',
                'tab'     => 'lovata.shopaholic::lang.menu.brands',
            ],
            'category'      => [
                'span'    => 'full',
                'context' => ['update', 'preview'],
                'type'    => 'partial',
                'path'    => '~/plugins/lovata/couponsshopaholic/controllers/coupongroups/_category.htm',
                'tab'     => 'lovata.shopaholic::lang.menu.categories',
            ],
            'shipping_type' => [
                'span'    => 'full',
                'context' => ['update', 'preview'],
                'type'    => 'partial',
                'path'    => '~/plugins/lovata/couponsshopaholic/controllers/coupongroups/_shipping_type.htm',
                'tab'     => 'lovata.ordersshopaholic::lang.menu.shipping_types',
            ],
        ];

        $arTriggerConfig = [];
        if (!empty($arPositionMechanismList)) {
            $arTriggerConfig = [
                'action'    => 'hide',
                'field'     => 'mechanism',
                'condition' => $this->prepareConditionString($arPositionMechanismList),
            ];

            $arFieldList['product']['trigger'] = $arTriggerConfig;
            $arFieldList['offer']['trigger'] = $arTriggerConfig;
            $arFieldList['brand']['trigger'] = $arTriggerConfig;
            $arFieldList['category']['trigger'] = $arTriggerConfig;
        }

        if (!empty($arShippingTypeMechanismList)) {
            $arFieldList['shipping_type']['trigger'] = [
                'action'    => 'show',
                'field'     => 'mechanism',
                'condition' => $this->prepareConditionString($arShippingTypeMechanismList),
            ];
        }

        if (PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            $arFieldList['tag'] = [
                'span'    => 'full',
                'context' => ['update', 'preview'],
                'type'    => 'partial',
                'path'    => '~/plugins/lovata/couponsshopaholic/controllers/coupongroups/_tag.htm',
                'tab'     => 'lovata.tagsshopaholic::lang.menu.tags',
            ];

            if (!empty($arTriggerConfig)) {
                $arFieldList['tag']['trigger'] = $arTriggerConfig;
            }
        }

        return $arFieldList;
    }

    /**
     * Prepare condition string
     * @param array $arValueList
     * @return string|null
     */
    protected function prepareConditionString($arValueList)
    {
        if (empty($arValueList)) {
            return null;
        }

        $sResult = '';
        foreach ($arValueList as $sValue) {
            if (!empty($sResult)) {
                $sResult .= ' || ';
            }
            $sResult .= 'value['.$sValue.']';
        }

        return $sResult;
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return CouponGroup::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return CouponGroups::class;
    }
}
