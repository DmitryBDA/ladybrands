<?php namespace Zprimegroup\Basecode\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddGoodNewsArticles extends Migration
{

  const TABLE_NAME = 'lovata_good_news_articles';

  public function up()
  {
    if (!Schema::hasTable(self::TABLE_NAME)) {
      return;
    }

    $arNewFieldList = [
      'meta_title',
      'meta_description',
      'meta_keywords',
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
      if (in_array('meta_title', $arNewFieldList)) {
        $obTable->text('meta_title')->nullable();
      }
      if (in_array('meta_description', $arNewFieldList)) {
        $obTable->text('meta_description')->nullable();
      }
      if (in_array('meta_keywords', $arNewFieldList)) {
        $obTable->text('meta_keywords')->nullable();
      }
    });
  }

  public function down()
  {
    if (!Schema::hasTable(self::TABLE_NAME)) {
      return;
    }

    $arNewFieldList = [
      'meta_title',
      'meta_description',
      'meta_keywords',
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
