<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableRemovePropertiesValue
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableRemovePropertiesValue extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_value';

    /**
     * Apply migration
     */
    public function up()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
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
}
