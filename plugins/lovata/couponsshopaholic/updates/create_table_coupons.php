<?php namespace Lovata\CouponsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableCoupons
 * @package Lovata\CouponsShopaholic\Updates
 */
class CreateTableCoupons extends Migration
{
    const TABLE_NAME = 'lovata_coupons_shopaholic_coupons';

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
            $obTable->integer('group_id')->unsigned();
            $obTable->string('code')->unique();
            $obTable->boolean('hidden')->default(0);
            $obTable->integer('user_id')->unsigned()->nullable();
            $obTable->integer('max_usage')->unsigned()->nullable();
            $obTable->timestamps();

            $obTable->index('group_id');
            $obTable->index('user_id');
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
