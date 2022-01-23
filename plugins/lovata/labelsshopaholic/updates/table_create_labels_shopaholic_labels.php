<?php namespace Lovata\LabelsShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateLabelsShopaholicLabelsTable
 * @package Lovata\LabelsShopaholic\Updates
 */
class TableCreateLabelsShopaholicLabels extends Migration
{
    const TABLE_NAME = 'lovata_labels_shopaholic_labels';

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
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->boolean('active')->default(true);
            $obTable->string('name');
            $obTable->string('slug')->unique();
            $obTable->string('code');
            $obTable->string('external_id')->nullable();
            $obTable->text('description')->nullable();
            $obTable->integer('sort_order')->nullable()->default(0);
            $obTable->timestamps();

            $obTable->index('name');
            $obTable->index('code');
            $obTable->index('external_id');
            $obTable->index('sort_order');
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
