<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateProperties
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreateProperties extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_properties_shopaholic_properties')) {
            return;
        }
        
        Schema::create('lovata_properties_shopaholic_properties', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->boolean('active')->default(1);
            $obTable->string('name');
            $obTable->string('slug');
            $obTable->string('code')->nullable();
            $obTable->string('type')->default('input');
            $obTable->integer('measure_id')->nullable();
            $obTable->string('external_id')->nullable();
            $obTable->text('settings')->nullable();
            $obTable->text('description')->nullable();
            $obTable->integer('sort_order')->default(1);
            $obTable->timestamps();

            $obTable->index('name');
            $obTable->index('slug');
            $obTable->index('code');
            $obTable->index('sort_order');
            $obTable->index('external_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_properties_shopaholic_properties');
    }
}