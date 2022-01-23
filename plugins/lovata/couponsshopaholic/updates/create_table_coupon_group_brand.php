<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponGroupBrand
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponGroupBrand extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_group_brand';

    /**
     * Apply migration
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->integer('brand_id')->unsigned();
            $obTable->integer('group_id')->unsigned();

            $obTable->primary(['brand_id', 'group_id'], 'brand_group');
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
