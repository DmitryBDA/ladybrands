<?php namespace Zprimegroup\Basecode\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddShopaholicProducts2 extends Migration
{

  const TABLE_NAME = 'lovata_shopaholic_products';

    public function up()
    {
      if (!Schema::hasTable(self::TABLE_NAME)) {
        return;
      }

      $arNewFieldList = [
        'description_1',
        'description_2',
        'description_3',
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
        if (in_array('description_1', $arNewFieldList)) {
          $obTable->text('description_1')->nullable();
        }
        if (in_array('description_2', $arNewFieldList)) {
          $obTable->text('description_2')->nullable();
        }
        if (in_array('description_3', $arNewFieldList)) {
          $obTable->text('description_3')->nullable();
        }
      });
    }

    public function down()
    {
      if (!Schema::hasTable(self::TABLE_NAME)) {
          return;
      }

      $arNewFieldList = [
        'description_1',
        'description_2',
        'description_3',
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
