<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponGroupProduct
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponGroupProduct extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_group_product';

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
            $obTable->integer('product_id')->unsigned();
            $obTable->integer('group_id')->unsigned();

            $obTable->primary(['product_id', 'group_id'], 'product_group');
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
