<?php namespace Lovata\DiscountsShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Settings;
use Lovata\DiscountsShopaholic\Classes\Processor\CatalogPriceProcessor;

/**
 * Class ExtendCategoryModel
 * @package Lovata\DiscountsShopaholic\Classes\Event
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
        });
    }

    /**
     * Extend settings fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendSettingsFields($obWidget)
    {
        // Only for the Settings controller
        if (!$obWidget->getController() instanceof \System\Controllers\Settings || $obWidget->isNested) {
            return;
        }

        // Only for the Settings model
        if (!$obWidget->model instanceof Settings) {
            return;
        }

        // Add an extra birthday field
        $obWidget->addTabFields([
            CatalogPriceProcessor::SETTING_QUEUE_ON   => [
                'tab'   => 'lovata.discountsshopaholic::lang.menu.discount',
                'label' => 'lovata.discountsshopaholic::lang.settings.discount_update_price_queue_on',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            CatalogPriceProcessor::SETTING_QUEUE_NAME => [
                'tab'     => 'lovata.discountsshopaholic::lang.menu.discount',
                'label'   => 'lovata.discountsshopaholic::lang.settings.discount_update_price_queue_name',
                'span'    => 'left',
                'type'    => 'text',
                'trigger' => [
                    'action'    => 'show',
                    'condition' => 'checked',
                    'field'     => 'discount_update_price_queue_on',
                ],
            ],
        ]);
    }
}
