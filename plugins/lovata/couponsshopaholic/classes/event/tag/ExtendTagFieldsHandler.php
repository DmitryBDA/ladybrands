<?php namespace Lovata\CouponsShopaholic\Classes\Event\Tag;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use System\Classes\PluginManager;

/**
 * Class ExtendTagFieldsHandler
 * @package Lovata\CouponsShopaholic\Classes\Event\Tag
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendTagFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        parent::subscribe($obEvent);
    }

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
        return \Lovata\TagsShopaholic\Models\Tag::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return \Lovata\TagsShopaholic\Controllers\Tags::class;
    }
}
