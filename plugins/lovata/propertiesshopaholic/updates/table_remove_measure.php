<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableRemoveMeasure
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableRemoveMeasure extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_measure';

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

        Schema::create(self::TABLE_NAME, function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }
}