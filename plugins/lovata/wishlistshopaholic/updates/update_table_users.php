<?php namespace Lovata\WishListShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class UpdateTableUsers
 * @package Lovata\WishListShopaholic\Updates
 */
class UpdateTableUsers extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if (Schema::hasTable('lovata_buddies_users') && !Schema::hasColumn('lovata_buddies_users', 'product_wish_list')) {

            Schema::table('lovata_buddies_users', function (Blueprint $obTable) {
                $obTable->text('product_wish_list')->nullable();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'product_wish_list')) {

            Schema::table('users', function (Blueprint $obTable) {
                $obTable->text('product_wish_list')->nullable();
            });
        }
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (Schema::hasTable('lovata_buddies_users') && Schema::hasColumn('lovata_buddies_users', 'product_wish_list')) {
            Schema::table('lovata_buddies_users', function (Blueprint $obTable) {
                $obTable->dropColumn(['product_wish_list']);
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'product_wish_list')) {
            Schema::table('users', function (Blueprint $obTable) {
                $obTable->dropColumn(['product_wish_list']);
            });
        }
    }
}
