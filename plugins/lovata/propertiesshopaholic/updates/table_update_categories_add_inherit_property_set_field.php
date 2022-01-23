<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateCategoriesAddInheritPropertySetField
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableUpdateCategoriesAddInheritPropertySetField extends Migration
{
    const TABLE_NAME = 'lovata_shopaholic_categories';
    
    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'inherit_property_set')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->boolean('inherit_property_set')->default(0);
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'inherit_property_set')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->dropColumn(['inherit_property_set']);
        });
    }
}
