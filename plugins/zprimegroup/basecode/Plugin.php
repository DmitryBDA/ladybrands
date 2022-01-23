<?php namespace Zprimegroup\Basecode;

use Event;
use System\Classes\PluginBase;
//Console commands
use Zprimegroup\Basecode\Classes\Console\ResetAdminPassword;

//Events
//Category events
use Zprimegroup\BaseCode\Classes\Event\Category\ExtendCategoryFieldsHandler;
use Zprimegroup\BaseCode\Classes\Event\Category\CategoryListHandler;
use Zprimegroup\BaseCode\Classes\Event\Category\CategoryModelHandler;

use Zprimegroup\BaseCode\Classes\Event\Product\ProductCollectionHandler;

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
            'Zprimegroup\Basecode\Components\SiteSettings' => 'SiteSettings',
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
                'label'       => 'zprimegroup.basecode::lang.menu.settings',
                'description' => '',
                'icon'        => 'icon-cogs',
                'class'       => 'Zprimegroup\Basecode\Models\Settings',
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
        $this->registerConsoleCommand('basecode:reset_admin_password', ResetAdminPassword::class);
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
      //Category
      Event::subscribe(ExtendCategoryFieldsHandler::class);
      Event::subscribe(CategoryListHandler::class);
      Event::subscribe(CategoryModelHandler::class);

      Event::subscribe(ProductCollectionHandler::class);

    }
}
