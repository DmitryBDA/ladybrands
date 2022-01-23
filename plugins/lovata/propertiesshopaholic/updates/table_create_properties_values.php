<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePropertiesValues
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreatePropertiesValues extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_values';
    
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->text('value')->nullable();
            $obTable->text('slug')->nullable();
            $obTable->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}