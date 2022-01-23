<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponCart
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponCart extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_coupon_cart';

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
            $obTable->integer('coupon_id')->unsigned();
            $obTable->integer('cart_id')->unsigned();

            $obTable->primary(['coupon_id', 'cart_id'], 'coupon_cart');
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
