<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * Class TableCreatePropertiesSetLink
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreatePropertiesSetLink extends Migration
{
    const TABLE_CATEGORY_LINK = 'lovata_properties_shopaholic_set_category_link';
    const TABLE_PRODUCT_LINK = 'lovata_properties_shopaholic_set_product_link';
    const TABLE_OFFER_LINK = 'lovata_properties_shopaholic_set_offer_link';

    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable(self::TABLE_CATEGORY_LINK)) {
            Schema::create(self::TABLE_CATEGORY_LINK, function(Blueprint $obTable)
            {
                $obTable->engine = 'InnoDB';
                $obTable->integer('category_id')->unsigned();
                $obTable->integer('set_id')->unsigned();
                $obTable->primary(['category_id', 'set_id'], 'category_set_link');
            });
        }

        if(!Schema::hasTable(self::TABLE_PRODUCT_LINK)) {
            Schema::create(self::TABLE_PRODUCT_LINK, function(Blueprint $obTable)
            {
                $obTable->engine = 'InnoDB';
                $obTable->integer('property_id')->unsigned();
                $obTable->integer('set_id')->unsigned();
                $obTable->primary(['property_id', 'set_id'], 'property_set_link');
                $obTable->text('groups')->nullable();

                if(PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')) {
                    $obTable->boolean('in_filter')->default(false);
                    $obTable->string('filter_type')->nullable();
                    $obTable->string('filter_name')->nullable();
                }
            });
        }

        if(!Schema::hasTable(self::TABLE_OFFER_LINK)) {
            Schema::create(self::TABLE_OFFER_LINK, function(Blueprint $obTable)
            {
                $obTable->engine = 'InnoDB';
                $obTable->integer('property_id')->unsigned();
                $obTable->integer('set_id')->unsigned();
                $obTable->primary(['property_id', 'set_id'], 'property_set_link');
                $obTable->text('groups')->nullable();

                if(PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')) {
                    $obTable->boolean('in_filter')->default(false);
                    $obTable->string('filter_type')->nullable();
                    $obTable->string('filter_name')->nullable();
                }
            });
        }
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_CATEGORY_LINK);
        Schema::dropIfExists(self::TABLE_PRODUCT_LINK);
        Schema::dropIfExists(self::TABLE_OFFER_LINK);
    }
}