<?php
namespace Dizoo\Slider\components;

use Cms\Classes\ComponentBase;
use Dizoo\Slider\Models\Settings;
use Dizoo\Slider\Models\Slides as Slides;

class Slider extends ComponentBase {

    public function componentDetails()
    {
        return [
            'name' => 'Slider',
            'description' => 'Displays the slider on the page.'
        ];
    }

    public function onRun()
    {
        $slides = $this->getSlides();
        if($slides->count()) {
            $this->page['slides'] = $slides;
            if ($this->property('bootstrap')) {
                $this->addCss('/plugins/dizoo/slider/assets/css/bootstrap.min.css');
            }
            if($this->property('css')) {
                $this->addCss('/plugins/dizoo/slider/assets/css/owl.carousel.min.css');
                $this->addCss('/plugins/dizoo/slider/assets/css/owl.theme.min.css');
                $this->addCss('/plugins/dizoo/slider/assets/css/custom-slider.css');
            }
            if ($this->property('scripting')) {
                $this->addJs('/plugins/dizoo/slider/assets/js/owl.carousel.min.js');
                $this->addJs('/plugins/dizoo/slider/assets/js/start-slider.js');
            }
            if ($this->property('custom_code')) {
                $Settings = Settings::instance();
                $this->page['topcode'] = $Settings->top;
                $this->page['slidecode'] = $this->buildCodeSlides($this->page['slides'], $Settings->slide);
                $this->page['bottomcode'] = $Settings->bottom;
            }
        } else {
            $this->page['slides'] = false;

        }

    }

    public function getSlides()
    {
        return Slides::where('active', true)->orderBy('sort_order', 'ASC')->get();
    }

    private function buildCodeSlides($slides, $html)
    {
        $slideCode = '';
        foreach ($slides as $slide) {
            $slideHTML =  $html;
            $slideHTML = str_replace("{{ align }}", ' style="text-align:'.$slide->text_align.' !important;" ', $slideHTML);
            $slideHTML = str_replace("{{ first_line }}", $slide->subtitle, $slideHTML);
            $slideHTML = str_replace("{{ first_line_color }}", ' style="color:'.$slide->subtitle_color.' !important;" ', $slideHTML);
            $slideHTML = str_replace("{{ second_line }}", $slide->title, $slideHTML);
            $slideHTML = str_replace("{{ second_line_color }}", ' style="color:'.$slide->title_color.' !important;" ', $slideHTML);
            $slideHTML = str_replace("{{ third_line }}", $slide->description, $slideHTML);
            $slideHTML = str_replace("{{ third_line_color }}", ' style="color:'.$slide->description_color.' !important;" ', $slideHTML);
            $slideHTML = str_replace("{{ image }}", $slide->image->path, $slideHTML);
            if ($slide->button_1_active === 0) {
                $slideHTML = $this->replace_between($slideHTML, '{% button %}', '{% endbutton %}', '');
            } else {
                $slideHTML = str_replace("{{ button_url }}", $slide->button_1_url, $slideHTML);
                $slideHTML = str_replace("{{ button_color }}", ' style="background-color:'.$slide->button_1_color.';" ', $slideHTML);
                $slideHTML = str_replace("{{ button_text }}", $slide->button_1_text, $slideHTML);
            }
            if (!$slide->button_2_active === 0) {
                $slideHTML = $this->replace_between($slideHTML, '{% button2 %}', '{% endbutton2 %}', '');
            } else {
                $slideHTML = str_replace("{{ button2_url }}", $slide->button_2_url, $slideHTML);
                $slideHTML = str_replace("{{ button2_text }}", $slide->button_2_text, $slideHTML);
            }
            $slideHTML = str_replace(array('{% button %}', '{% button2 %}', '{% endbutton %}', '{% endbutton2 %}'), '', $slideHTML);
            $slideCode .= $slideHTML;
        }

        return $slideCode;
    }

    private function replace_between($str, $needle_start, $needle_end, $replacement)
    {
        $pos = strpos($str, $needle_start);
        $start = $pos === false ? 0 : $pos + strlen($needle_start);

        $pos = strpos($str, $needle_end, $start);
        $end = $pos === false ? strlen($str) : $pos;

        return substr_replace($str, $replacement, $start, $end - $start);
    }
    
    public function defineProperties()
    {
        return [
            'autoplay' => [
                 'title'             => 'Autoplay',
                 'description'       => 'Slide automatically',
                 'default'           => true,
                 'type'              => 'checkbox'
            ],
            'loop' => [
                 'title'             => 'Loop',
                 'description'       => 'Loop slides',
                 'default'           => true,
                 'type'              => 'checkbox'
            ],
            'bootstrap' => [
                'title'             => 'Include Bootstrap',
                'description'       => 'If your theme already uses bootstrap (v3.3.7+) uncheck this box',
                'default'           => true,
                'type'              => 'checkbox'
            ],
            'scripting' => [
                'title'             => 'Include Scripting',
                'description'       => 'If you want to use your own scripting uncheck this',
                'default'           => true,
                'type'              => 'checkbox'
            ],
            'css' => [
                'title'             => 'Include CSS',
                'description'       => 'If you want to use custom CSS uncheck this',
                'default'           => true,
                'type'              => 'checkbox'
            ],
            'custom_code' => [
                'title'             => 'Custom code',
                'description'       => 'If you want to use the custom code in settings then check this',
                'default'           => false,
                'type'              => 'checkbox'
            ]
        ];
    }
}