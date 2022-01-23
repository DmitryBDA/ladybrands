<?php namespace Lovata\DiscountsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTableDiscounts
 * @package Lovata\DiscountsShopaholic\Updates
 */
class CreateTableDiscounts extends Migration
{
    const TABLE_NAME = 'lovata_discounts_shopaholic_discounts';

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
            $obTable->integer('promo_block_id')->nullable();
            $obTable->string('name');
            $obTable->string('code')->nullable();
            $obTable->string('external_id')->nullable();
            $obTable->dateTime('date_begin');
            $obTable->dateTime('date_end')->nullable();
            $obTable->float('discount_value')->unsigned();
            $obTable->string('discount_type');
            $obTable->text('preview_text')->nullable();
            $obTable->text('description')->nullable();
            $obTable->integer('sort_order')->nullable();
            $obTable->timestamps();

            $obTable->index('name');
            $obTable->index('code');
            $obTable->index('external_id');
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
