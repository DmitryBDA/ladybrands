<?php namespace Lovata\PropertiesShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * Class TableRemoveCategoryLink
 * @package Lovata\PropertiesShopaholic\Updates
 */
class TableRemoveCategoryLink extends Migration
{
    const TABLE_PRODUCT_LINK = 'lovata_properties_shopaholic_product_link';
    const TABLE_OFFER_LINK = 'lovata_properties_shopaholic_offer_link';

    /**
     * Apply migration
     */
    public function up()
    {
        Schema::dropIfExists(self::TABLE_PRODUCT_LINK);
        Schema::dropIfExists(self::TABLE_OFFER_LINK);
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_PRODUCT_LINK)) {
            Schema::create(self::TABLE_PRODUCT_LINK, function (Blueprint $obTable) {
                $obTable->engine = 'InnoDB';
                $obTable->integer('property_id')->unsigned();
                $obTable->integer('category_id')->unsigned();
                $obTable->primary(['property_id', 'category_id'], 'property_category_link');
                $obTable->text('groups')->nullable();

                if (PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')
                    && !Schema::hasColumns(self::TABLE_PRODUCT_LINK, ['in_filter', 'filter_type', 'filter_name'])
                ) {
                    $obTable->boolean('in_filter')->default(false);
                    $obTable->string('filter_type')->nullable();
                    $obTable->string('filter_name')->nullable();
                }
            });
        }

        if (!Schema::hasTable(self::TABLE_OFFER_LINK)) {
            Schema::create(self::TABLE_OFFER_LINK, function (Blueprint $obTable) {
                $obTable->engine = 'InnoDB';
                $obTable->integer('property_id')->unsigned();
                $obTable->integer('category_id')->unsigned();
                $obTable->primary(['property_id', 'category_id'], 'property_category_link');
                $obTable->text('groups')->nullable();

                if (PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')
                    && !Schema::hasColumns(self::TABLE_OFFER_LINK, ['in_filter', 'filter_type', 'filter_name'])
                ) {
                    $obTable->boolean('in_filter')->default(false);
                    $obTable->string('filter_type')->nullable();
                    $obTable->string('filter_name')->nullable();
                }
            });
        }
    }
}
