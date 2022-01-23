<?php namespace Dizoo\Slider\Models;

use Model;

/**
 * Model
 */
class Slides extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * Softly implement the TranslatableModel behavior.
     */
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'title',
        'subtitle',
        'description',
        'button_1_text',
        'button_2_text',
        'button_1_url',
        'button_2_url'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'dizoo_slider_slides';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required|max:25',
        'subtitle' => 'max:40',
        'description' => 'max:255',
        'button_1_text' => 'max:15',
        'button_2_text' => 'max:15',
        'image' => 'required',
//        'title_size' => 'required|integer',
//        'subtitle_size' => 'required|integer',
        'width' => 'required|integer',
        'height' => 'required|integer'
    ];

    public function beforeSave()
    {
        if (!$this->title_color) $this->title_color = '#ffffff';
        if (!$this->subtitle_color) $this->subtitle_color = '#ffffff';
        if (!$this->description_color) $this->description_color = '#ffffff';
    }
}
