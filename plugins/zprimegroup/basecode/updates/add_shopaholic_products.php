<?php namespace Zprimegroup\Basecode\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddShopaholicProducts extends Migration
{

  const TABLE_NAME = 'lovata_shopaholic_products';

    public function up()
    {
      if (!Schema::hasTable(self::TABLE_NAME)) {
        return;
      }

      $arNewFieldList = [
        'seo_title',
        'seo_description',
        'seo_keywords',
        'review_counter'
      ];

      foreach ($arNewFieldList as $iKey => $sFieldName) {
        if (Schema::hasColumn(self::TABLE_NAME, $sFieldName)) {
          unset($arNewFieldList[$iKey]);
        }
      }

      if (empty($arNewFieldList)) {
        return;
      }

      Schema::table(self::TABLE_NAME, function (Blueprint $obTable) use ($arNewFieldList) {
        if (in_array('seo_title', $arNewFieldList)) {
          $obTable->text('seo_title')->nullable();
        }
        if (in_array('seo_description', $arNewFieldList)) {
          $obTable->text('seo_description')->nullable();
        }
        if (in_array('seo_keywords', $arNewFieldList)) {
          $obTable->text('seo_keywords')->nullable();
        }
        if (in_array('review_counter', $arNewFieldList)) {
          $obTable->text('review_counter')->nullable();
        }
      });
    }

    public function down()
    {
      if (!Schema::hasTable(self::TABLE_NAME)) {
          return;
      }

      $arNewFieldList = [
        'seo_title',
        'seo_description',
        'seo_keywords',
        'review_counter'
      ];

      foreach ($arNewFieldList as $iKey => $sFieldName) {
        if (!Schema::hasColumn(self::TABLE_NAME, $sFieldName)) {
          unset($arNewFieldList[$iKey]);
        }
      }

      if (empty($arNewFieldList)) {
        return;
      }

      Schema::table(self::TABLE_NAME, function (Blueprint $obTable) use ($arNewFieldList) {
        $obTable->dropColumn($arNewFieldList);
      });
    }
}
