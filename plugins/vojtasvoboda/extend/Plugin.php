<?php namespace VojtaSvoboda\Extend;

use Event;
use System\Classes\PluginBase;

use VojtaSvoboda\Extend\Classes\Event\Review\ReviewModelHandler;
use VojtaSvoboda\Extend\Classes\Event\Review\ReviewComponentsHandler;
use VojtaSvoboda\Extend\Classes\Event\Review\ExtendReviewFieldsHandler;

/**
 * Class Plugin
 */
class Plugin extends PluginBase
{
    /**
     * Register plugin components
     *
     * @return array
     */
    public function registerComponents()
    {
        return [

        ];
    }

    /**
     * Register settings
     * @return array
     */
    public function registerSettings()
    {
        return [
          'config'    => [
            'label'       => '',
            'description' => '',
            'icon'        => '',
            'class'       => '',
            'permissions' => ['zprimegroup-basecode'],
            'order'       => 100,
          ],
        ];
    }

    /**
     * Plugin boot method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    public function register()
    {

    }

    /**
     * @return array
     */
    public function registerMailTemplates()
    {
        return [];
    }

    /**
     * Add listener
     */
    protected function addEventListener()
    {
      Event::subscribe(ReviewModelHandler::class);
      Event::subscribe(ReviewComponentsHandler::class);
      Event::subscribe(ExtendReviewFieldsHandler::class);
    }
}
