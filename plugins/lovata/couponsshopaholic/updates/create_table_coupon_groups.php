<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponGroups
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponGroups extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_coupon_groups';

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
            $obTable->increments('id')->unsigned();
            $obTable->boolean('active')->default(0);
            $obTable->string('name');
            $obTable->dateTime('date_begin');
            $obTable->dateTime('date_end')->nullable();
            $obTable->integer('promo_mechanism_id')->unsigned();
            $obTable->integer('promo_block_id')->unsigned()->nullable();
            $obTable->integer('max_usage')->unsigned()->nullable();
            $obTable->integer('max_usage_per_user')->unsigned()->nullable();
            $obTable->timestamps();
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
