<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateDizooSliderSlides extends Migration
{
    public function up()
    {
        Schema::table('dizoo_slider_slides', function($table)
        {
            $table->string('button_1_url', 255)->nullable();
            $table->string('button_2_url', 255)->nullable();
            $table->string('subtitle', 40)->nullable()->change();
            $table->string('description', 210)->nullable()->change();
            $table->boolean('button_1_active')->nullable()->change();
            $table->string('button_1_text', 15)->nullable()->change();
            $table->string('button_1_color', 7)->nullable()->default('#ffffff')->change();
            $table->boolean('button_2_active')->default(0)->change();
            $table->string('button_2_text', 15)->nullable()->change();
        });
    }
    
    public function down()
    {

        if (Schema::hasColumn('dizoo_slider_slides', 'button_2_url')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('button_2_url');
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'button_1_url')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->dropColumn('button_1_url');
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'subtitle')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->string('subtitle', 40)->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'description')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->string('description', 210)->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'button_1_active')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->boolean('button_1_active')->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'button_1_text')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->string('button_1_text', 15)->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'button_1_color')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->string('button_1_color', 7)->nullable(false)->default(null)->change();
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'button_2_active')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->boolean('button_2_active')->default(null)->change();
            });
        }

        if (Schema::hasColumn('dizoo_slider_slides', 'button_2_text')) {
            Schema::table('dizoo_slider_slides', function($table)
            {
                $table->string('button_2_text', 15)->nullable(false)->change();
            });
        }
    }
}
