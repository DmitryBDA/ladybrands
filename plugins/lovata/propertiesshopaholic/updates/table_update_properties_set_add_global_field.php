<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdatePropertiesSetAddGlobalField
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableUpdatePropertiesSetAddGlobalField extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_set';
    
    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'is_global')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->boolean('is_global')->default(0);
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'is_global')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->dropColumn(['is_global']);
        });
    }
}
