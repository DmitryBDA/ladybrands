<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddImageAlignFontSize extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            $table->string('image_align', 6)->default('center');
            $table->integer('title_size')->default(50);
            $table->integer('subtitle_size')->default(28);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('dizoo_slider_slides', 'image_align')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('image_align');
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'title_size')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('title_size');
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'subtitle_size')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('subtitle_size');
            });
        }
    }
}
