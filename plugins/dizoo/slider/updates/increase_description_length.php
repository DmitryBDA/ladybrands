<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class increaseDescriptionLength extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            $table->string('description', 255)->nullable()->change();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('dizoo_slider_slides', 'description')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->string('description', 210)->nullable()->change();
            });
        }
    }
}
