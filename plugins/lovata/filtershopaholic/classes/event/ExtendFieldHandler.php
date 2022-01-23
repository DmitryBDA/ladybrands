<?php namespace Lovata\FilterShopaholic\Classes\Event;

use Lang;
use System\Classes\PluginManager;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyOfferLink;
use Lovata\PropertiesShopaholic\Models\PropertyProductLink;
use Lovata\PropertiesShopaholic\Controllers\PropertySets;

/**
 * Class ExtendCategoryModel
 * @package Lovata\FilterShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        $obEvent->listen('backend.list.extendColumns', function($obWidget) {
            $this->extendCategoryRelationFields($obWidget);
        });
    }

    /**
     * Extend category -> property fields
     * @param \Backend\Widgets\Lists $obWidget
     */
    protected function extendCategoryRelationFields($obWidget)
    {
        if(!$obWidget->getController() instanceof PropertySets || !preg_match('%ViewList$%', $obWidget->alias)) {
            return;
        }

        if(!$obWidget->model instanceof Property) {
            return;
        }

        $obWidget->addColumns([
            'pivot[in_filter]' => [
                'type'      => 'switch',
                'label'     => 'lovata.filtershopaholic::lang.field.in_filter',
                'sortable'  => false,
            ],
            'pivot[filter_type]' => [
                'type'      => 'text',
                'label'     => 'lovata.filtershopaholic::lang.field.filter_type',
                'sortable'  => false,
            ],
            'pivot[filter_name]' => [
                'type'      => 'text',
                'label'     => 'lovata.filtershopaholic::lang.field.filter_name',
                'sortable'  => false,
            ],
        ]);

        PropertyProductLink::extend(function($obElement) {

            $obElement->bindEvent('model.getAttribute', function($attribute, $value) {
                if ($attribute == 'filter_type' && !empty($value)) {
                    return Lang::get('lovata.filtershopaholic::lang.type.'.$value);
                }
            });
        });

        PropertyOfferLink::extend(function($obElement) {

            $obElement->bindEvent('model.getAttribute', function($attribute, $value) {
                if ($attribute == 'filter_type' && !empty($value)) {
                    return Lang::get('lovata.filtershopaholic::lang.type.'.$value);
                }
            });
        });
    }
}