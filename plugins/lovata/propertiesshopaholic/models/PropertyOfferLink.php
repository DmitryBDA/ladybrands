<?php namespace Lovata\PropertiesShopaholic\Models;

use Lang;
use October\Rain\Database\Pivot;

/**
 * Class PropertyOfferLink
 * @package Lovata\PropertiesShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property integer $property_id
 * @property integer $category_id
 * @property array $groups
 *
 * "Filter for Shopaholic" columns
 * @property bool $in_filter
 * @property string $filter_type
 * @property string $filter_name
 *
 * @see \Lovata\FilterShopaholic\Plugin::boot() - getter for 'filter_type' (category relation)
 */
class PropertyOfferLink extends Pivot
{
    public $table = 'lovata_properties_shopaholic_set_offer_link';

    public $jsonable = ['groups'];

    /**
     * Get properties groups array for dropdown or checkbox list
     * @return array
     */
    public static function getGroupsOptions()
    {
        $arGroupList = (array) Group::orderBy('name', 'ASC')->lists('name', 'id');

        return $arGroupList;
    }

    /**
     * Get filter type
     * @return string
     */
    public function getType()
    {
        return $this->attributes['filter_type'];
    }

    /**
     * @return array
     */
    public function getFilterTypeOptions()
    {
        return [
            \Lovata\FilterShopaholic\Plugin::TYPE_SWITCH         => Lang::get('lovata.filtershopaholic::lang.type.' . \Lovata\FilterShopaholic\Plugin::TYPE_SWITCH),
            \Lovata\FilterShopaholic\Plugin::TYPE_SELECT         => Lang::get('lovata.filtershopaholic::lang.type.' . \Lovata\FilterShopaholic\Plugin::TYPE_SELECT),
            \Lovata\FilterShopaholic\Plugin::TYPE_CHECKBOX       => Lang::get('lovata.filtershopaholic::lang.type.' . \Lovata\FilterShopaholic\Plugin::TYPE_CHECKBOX),
            \Lovata\FilterShopaholic\Plugin::TYPE_BETWEEN        => Lang::get('lovata.filtershopaholic::lang.type.' . \Lovata\FilterShopaholic\Plugin::TYPE_BETWEEN),
            \Lovata\FilterShopaholic\Plugin::TYPE_SELECT_BETWEEN => Lang::get('lovata.filtershopaholic::lang.type.' . \Lovata\FilterShopaholic\Plugin::TYPE_SELECT_BETWEEN),
            \Lovata\FilterShopaholic\Plugin::TYPE_RADIO          => Lang::get('lovata.filtershopaholic::lang.type.' . \Lovata\FilterShopaholic\Plugin::TYPE_RADIO),
        ];
    }
}
