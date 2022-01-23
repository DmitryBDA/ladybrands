<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateDizooSliderSlides2 extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            $table->boolean('active')->default(1);
        });
    }
    
    public function down()
    {
        if (Schema::hasColumn('dizoo_slider_slides', 'active')) {
            Schema::table('dizoo_slider_slides', function ($table) {
                $table->dropColumn('active');

            });
        }
    }
}
