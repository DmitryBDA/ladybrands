<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateGroups
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreateGroups extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_properties_shopaholic_groups')) {
            return;
        }
        
        Schema::create('lovata_properties_shopaholic_groups', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->string('name');
            $obTable->string('code');
            $obTable->text('description')->nullable();
            $obTable->integer('sort_order')->default(0);
            $obTable->timestamps();

            $obTable->index('code');
            $obTable->index('sort_order');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_properties_shopaholic_groups');
    }
}