<?php namespace Lovata\FilterShopaholic\Updates;

use Schema;
use System\Classes\PluginManager;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdatePropertiesShopaholicSetLink
 * @package Lovata\FilterShopaholic\Updates
 */
class TableUpdatePropertiesShopaholicSetLink extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        if(Schema::hasTable('lovata_properties_shopaholic_set_product_link')
            && !Schema::hasColumn('lovata_properties_shopaholic_set_product_link', 'in_filter')
        ) {
            Schema::table('lovata_properties_shopaholic_set_product_link', function(Blueprint $table)
            {
                $table->boolean('in_filter')->default(false);
                $table->string('filter_type')->nullable();
                $table->string('filter_name')->nullable();
            });
        }

        if(Schema::hasTable('lovata_properties_shopaholic_set_offer_link')
            && !Schema::hasColumn('lovata_properties_shopaholic_set_offer_link', 'in_filter')
        ) {
            Schema::table('lovata_properties_shopaholic_set_offer_link', function(Blueprint $table)
            {
                $table->boolean('in_filter')->default(false);
                $table->string('filter_type')->nullable();
                $table->string('filter_name')->nullable();
            });
        }
    }
    
    /**
     * Rollback migration
     */
    public function down()
    {
        if(!PluginManager::instance()->hasPlugin('Lovata.PropertiesShopaholic')) {
            return;
        }

        if(Schema::hasTable('lovata_properties_shopaholic_set_product_link')
            && Schema::hasColumn('lovata_properties_shopaholic_set_product_link', 'in_filter')
        ) {
            Schema::table('lovata_properties_shopaholic_set_product_link', function(Blueprint $table)
            {
                $table->dropColumn(['in_filter', 'filter_type', 'filter_name']);
            });
        }

        if(Schema::hasTable('lovata_properties_shopaholic_set_offer_link')
            && Schema::hasColumn('lovata_properties_shopaholic_set_offer_link', 'in_filter')
        ) {
            Schema::table('lovata_properties_shopaholic_set_offer_link', function(Blueprint $table)
            {
                $table->dropColumn(['in_filter', 'filter_type', 'filter_name']);
            });
        }
    }
}