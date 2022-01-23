<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponGroupShippingType
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponGroupShippingType extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_group_shipping_type';

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
            $obTable->integer('shipping_type_id')->unsigned();
            $obTable->integer('group_id')->unsigned();

            $obTable->primary(['shipping_type_id', 'group_id'], 'shipping_type_group');
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
