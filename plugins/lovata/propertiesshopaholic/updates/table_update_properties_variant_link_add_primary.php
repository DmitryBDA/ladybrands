<?php namespace Lovata\PropertiesShopaholic\Updates;

use DB;
use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

use Lovata\PropertiesShopaholic\Models\Property;

/**
 * Class TableUpdatePropertiesVariantLinkAddPrimary
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableUpdatePropertiesVariantLinkAddPrimary extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_variant_link';

    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        $arElementList = DB::table(self::TABLE_NAME)->get();
        if ($arElementList->isEmpty()) {
            return;
        }

        $arProcessedList = [];
        foreach ($arElementList as $obElement) {
            $arProcessedList[$obElement->property_id][] = $obElement->value_id;
        }

        DB::table(self::TABLE_NAME)->truncate();

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->primary(['property_id', 'value_id'], 'property_value');
        });

        //Get property list
        $obPropertyList = Property::get();
        /** @var Property $obProperty */
        foreach ($obPropertyList as $obProperty) {
            if (!isset($arProcessedList[$obProperty->id])) {
                continue;
            }

            $arValueIDList = array_unique($arProcessedList[$obProperty->id]);
            $obProperty->property_value()->sync($arValueIDList);
        }
    }

    /**
     * Rollback migration
     */
    public function down() {}
}