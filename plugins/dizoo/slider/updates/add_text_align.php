<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddTextAlign extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            $table->string('text_align', 6)->default('left');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('dizoo_slider_slides', 'text_align')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('text_align');
            });
        }
    }
}
