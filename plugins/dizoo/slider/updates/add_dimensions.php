<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class add_dimensions extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            if (!Schema::hasColumn('dizoo_slider_slides', 'width')) $table->integer('width')->default(1920);
            if (!Schema::hasColumn('dizoo_slider_slides', 'height')) $table->integer('height')->default(900);
            if (Schema::hasColumn('dizoo_slider_slides', 'image_align')) $table->dropColumn('image_align');
        });
    }

    public function down()
    {
            Schema::table('dizoo_slider_slides', function($table)
            {
                if (Schema::hasColumn('dizoo_slider_slides', 'width')) $table->dropColumn('width');
                if (Schema::hasColumn('dizoo_slider_slides', 'height')) $table->dropColumn('height');
                if (Schema::hasColumn('dizoo_slider_slides', 'image_align')) $table->string('image_align', 6)->default('center');
            });
    }
}