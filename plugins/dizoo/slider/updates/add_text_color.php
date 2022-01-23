<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class addTextColor extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            $table->string('subtitle_color', 7)->default('#FFFFFF');
            $table->string('title_color', 7)->default('#FFFFFF');
            $table->string('description_color', 7)->default('#FFFFFF');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('dizoo_slider_slides', 'subtitle_color')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('subtitle_color');
                $table->dropColumn('title_color');
                $table->dropColumn('description_color');
            });
        }
    }
}