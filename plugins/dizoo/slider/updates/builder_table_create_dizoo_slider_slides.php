<?php namespace Dizoo\Slider\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDizooSliderSlides extends Migration
{
    public function up()
    {
        Schema::create('dizoo_slider_slides', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 25);
            $table->string('subtitle', 40);
            $table->string('description', 210);
            $table->boolean('button_1_active')->default(0);
            $table->string('button_1_text', 15);
            $table->string('button_1_color', 7);
            $table->boolean('button_2_active');
            $table->string('button_2_text', 15);
            $table->integer('sort_order')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('dizoo_slider_slides');
    }
}
