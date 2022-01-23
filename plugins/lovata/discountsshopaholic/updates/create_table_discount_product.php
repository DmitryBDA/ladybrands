<?php namespace Lovata\DiscountsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableDiscountProduct
 * @package Lovata\DiscountsShopaholic\Updates
 */
class CreateTableDiscountProduct extends Migration
{
    const TABLE_NAME = 'lovata_discounts_shopaholic_discount_product';

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
            $obTable->integer('discount_id')->unsigned();

            $obTable->primary(['product_id', 'discount_id'], 'product_discount');
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
