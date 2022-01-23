<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * Class TableCreateCategoryLink
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableCreateCategoryLink extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable('lovata_properties_shopaholic_product_link')) {
            Schema::create('lovata_properties_shopaholic_product_link', function(Blueprint $obTable)
            {
                $obTable->engine = 'InnoDB';
                $obTable->integer('property_id')->unsigned();
                $obTable->integer('category_id')->unsigned();
                $obTable->primary(['property_id', 'category_id'], 'property_category_link');
                $obTable->text('groups')->nullable();

                if(PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')
                    && !Schema::hasColumns('lovata_properties_shopaholic_product_link', ['in_filter', 'filter_type', 'filter_name'])
                ) {
                    $obTable->boolean('in_filter')->default(false);
                    $obTable->string('filter_type')->nullable();
                    $obTable->string('filter_name')->nullable();
                }
            });
        }
        
        if(!Schema::hasTable('lovata_properties_shopaholic_offer_link')) {
            Schema::create('lovata_properties_shopaholic_offer_link', function(Blueprint $obTable)
            {
                $obTable->engine = 'InnoDB';
                $obTable->integer('property_id')->unsigned();
                $obTable->integer('category_id')->unsigned();
                $obTable->primary(['property_id', 'category_id'], 'property_category_link');
                $obTable->text('groups')->nullable();

                if(PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')
                    && !Schema::hasColumns('lovata_properties_shopaholic_offer_link', ['in_filter', 'filter_type', 'filter_name'])
                ) {
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
        Schema::dropIfExists('lovata_properties_shopaholic_product_link');
        Schema::dropIfExists('lovata_properties_shopaholic_offer_link');
    }
}