<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePropertiesVariantLink
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreatePropertiesVariantLink extends Migration
{
    const TABLE_NAME = 'lovata_properties_shopaholic_variant_link';

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
            $obTable->integer('value_id')->unsigned();
            $obTable->integer('property_id')->unsigned();

            $obTable->index('property_id');
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