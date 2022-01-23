<?php namespace Dizoo\Slider;

use Dizoo\Slider\Components\Slider;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            Slider::class       => 'slider'
        ];
    }

    public function registerSettings()
    {
        return [
            'polismap-slider' => [
                'label'       => 'Slider',
                'description' => 'Instellingen voor de slider',
                'category'    => SettingsManager::CATEGORY_SYSTEM,
                'icon'        => 'icon-image',
                'class'       => 'Dizoo\Slider\Models\Settings',
                'order'       => 300,
                'keywords'    => 'dizoo slider',
                'permissions' => ['bsbvolmachten.polismapslider.manage-slider']
            ]
        ];
    }
}
