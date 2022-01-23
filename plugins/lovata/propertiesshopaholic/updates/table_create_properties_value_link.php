<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePropertiesValueLink
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreatePropertiesValueLink extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_value_link';

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
            $obTable->integer('value_id')->unsigned();
            $obTable->integer('property_id')->unsigned();
            $obTable->integer('element_id')->unsigned();
            $obTable->string('element_type');
            $obTable->integer('product_id')->unsigned();
            $obTable->timestamps();

            $obTable->index('property_id');
            $obTable->index('element_id');
            $obTable->index('element_type');
            $obTable->index('product_id');
            $obTable->index('value_id');
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