<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponGroupCategory
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponGroupCategory extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_group_category';

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
            $obTable->integer('category_id')->unsigned();
            $obTable->integer('group_id')->unsigned();

            $obTable->primary(['category_id', 'group_id'], 'category_group');
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
