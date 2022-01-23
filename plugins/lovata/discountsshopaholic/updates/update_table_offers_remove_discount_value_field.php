<?php namespace Lovata\DiscountsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class UpdateTableOffersRemoveDiscountValueField
 * @package Lovata\DiscountsShopaholic\Updates
 */
class UpdateTableOffersRemoveDiscountValueField extends Migration
{
    const TABLE_NAME = 'lovata_shopaholic_offers';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'discount_price')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->dropColumn(['discount_price']);
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'discount_price')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->decimal('discount_price', 15, 2)->nullable();
        });
    }
}
