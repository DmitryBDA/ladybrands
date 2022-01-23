<?php namespace Lovata\DiscountsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableDiscountTag
 * @package Lovata\DiscountsShopaholic\Updates
 */
class CreateTableDiscountTag extends Migration
{
    const TABLE_NAME = 'lovata_discounts_shopaholic_discount_tag';

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
            $obTable->integer('discount_id')->unsigned();

            $obTable->primary(['tag_id', 'discount_id'], 'tag_discount');
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
