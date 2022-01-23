<?php namespace Zprimegroup\Basecode\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddFieldUsers extends Migration
{

  const TABLE_NAME = 'users';

  public function up()
  {
    if (!Schema::hasTable(self::TABLE_NAME)) {
      return;
    }

    $arNewFieldList = [
      'phone'
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
      if (in_array('phone', $arNewFieldList)) {
        $obTable->string('phone');
      }
    });
  }

  public function down()
  {
    if (!Schema::hasTable(self::TABLE_NAME)) {
      return;
    }

    $arNewFieldList = [
      'phone'
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
