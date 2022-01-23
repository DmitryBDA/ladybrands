<?php namespace Zprimegroup\Basecode\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class addProductIdVojtasvobodaReviewsReviews extends Migration
{

  const TABLE_NAME = 'vojtasvoboda_reviews_reviews';

  public function up()
  {
    if (!Schema::hasTable(self::TABLE_NAME)) {
      return;
    }

    $arNewFieldList = [
      'product_id'
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
      if (in_array('product_id', $arNewFieldList)) {
        $obTable->integer('product_id')->nullable();
      }
    });
  }

  public function down()
  {
    if (!Schema::hasTable(self::TABLE_NAME)) {
      return;
    }

    $arNewFieldList = [
      'product_id'
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
