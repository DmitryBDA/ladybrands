<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdatePropertiesValuesAddLabelField
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableUpdatePropertiesValuesAddLabelField extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_values';
    
    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'label')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->string('label')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'label')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->dropColumn(['label']);
        });
    }
}
