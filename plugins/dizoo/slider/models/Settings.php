<?php namespace Dizoo\Slider\Models;

use Model;

class Settings extends Model
{

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'dizoo_slider_settings';

    public $settingsFields = 'fields.yaml';
}
