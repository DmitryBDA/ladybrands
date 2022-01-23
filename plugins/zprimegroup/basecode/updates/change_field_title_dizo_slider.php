<?php namespace Zprimegroup\Basecode\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class changeFieldTitleDizoSlider extends Migration
{

  public function up()
  {
    Schema::table('dizoo_slider_slides', function (Blueprint $table) {
      $table->string('title', 25)->nullable()->change();
    });
  }

  public function down()
  {
    Schema::table('dizoo_slider_slides', function (Blueprint $table) {
      $table->string('title', 25)->change();
    });
  }
}
