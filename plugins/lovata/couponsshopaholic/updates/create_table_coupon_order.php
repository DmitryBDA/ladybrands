<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponOrder
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponOrder extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_order_coupon';

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
            $obTable->integer('order_id')->unsigned();
            $obTable->integer('coupon_id')->unsigned();
            $obTable->string('code');

            $obTable->primary(['order_id', 'coupon_id'], 'order_coupon');
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
