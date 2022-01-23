<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePropertiesSet
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreatePropertiesSet extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_set';

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
            $obTable->string('name');
            $obTable->string('code')->unique();
            $obTable->text('description')->nullable();
            $obTable->integer('sort_order')->default(0);
            $obTable->timestamps();

            $obTable->index('sort_order');
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
