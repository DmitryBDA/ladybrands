<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCouponGroupTag
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCouponGroupTag extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_group_tag';

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
            $obTable->integer('tag_id')->unsigned();
            $obTable->integer('group_id')->unsigned();

            $obTable->primary(['tag_id', 'group_id'], 'tag_group');
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
