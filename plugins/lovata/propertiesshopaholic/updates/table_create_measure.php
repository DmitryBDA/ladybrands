<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateMeasure
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreateMeasure extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_properties_shopaholic_measure')) {
            return;
        }
        
        Schema::create('lovata_properties_shopaholic_measure', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_properties_shopaholic_measure');
    }
}