<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePropertiesValue
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreatePropertiesValue extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_properties_shopaholic_value')) {
            return;
        }
        
        Schema::create('lovata_properties_shopaholic_value', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->integer('property_id')->unsigned();
            $obTable->integer('product_id')->unsigned();
            $obTable->integer('category_id')->nullable();
            $obTable->string('model');
            $obTable->boolean('active');
            $obTable->text('value')->nullable();
            $obTable->text('slug')->nullable();
            $obTable->timestamps();

            $obTable->index('property_id');
            $obTable->index('product_id');
            $obTable->index('category_id');
            $obTable->index('model');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_properties_shopaholic_value');
    }
}