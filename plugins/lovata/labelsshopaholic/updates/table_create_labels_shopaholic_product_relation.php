<?php namespace Lovata\LabelsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateLabelsShopaholicLabelsTable
 * @package Lovata\LabelsShopaholic\Updates
 */
class TableCreateLabelsShopaholicProductRelation extends Migration
{
    const TABLE_NAME = 'lovata_labels_shopaholic_product_label';

    /**
     * Apply migration
     */
    public function up()
    {
        if (Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::create(self::TABLE_NAME, function (Blueprint $obTable)
        {
            $obTable->integer('label_id');
            $obTable->integer('product_id');

            $obTable->primary(['label_id', 'product_id'], 'product_label');
            $obTable->index('label_id');
            $obTable->index('product_id');
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
