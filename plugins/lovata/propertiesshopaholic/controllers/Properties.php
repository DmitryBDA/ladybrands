<?php namespace Lovata\PropertiesShopaholic\Controllers;

use Lang;
use Flash;
use Event;
use Backend\Classes\Controller;
use BackendMenu;
use Backend\Classes\BackendController;
use System\Classes\SettingsManager;

use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Classes\Import\ImportPropertyModelFromXML;

/**
 * Class Properties
 * @package Lovata\PropertiesShopaholic\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Properties extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ReorderController',
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.ImportExportController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    /**
     * Properties constructor.
     */
    public function __construct()
    {
        if (BackendController::$action == 'import') {
            Property::extend(function ($obModel) {
                $obModel->rules['external_id'] = 'required';
            });
        }

        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Lovata.PropertiesShopaholic', 'shopaholic-menu-property');
    }

    /**
     * Ajax handler onReorder event
     */
    public function onReorder()
    {
        $obResult = parent::onReorder();
        Event::fire('shopaholic.property.update.sorting');

        return $obResult;
    }

    /**
     * Start import from XML
     */
    public function onImportFromXML()
    {
        $obImport = new ImportPropertyModelFromXML();
        $obImport->import();

        $arReportData = [
            'created' => $obImport->getCreatedCount(),
            'updated' => $obImport->getUpdatedCount(),
            'skipped' => $obImport->getSkippedCount(),
            'processed' => $obImport->getProcessedCount(),
        ];

        Flash::info(Lang::get('lovata.toolbox::lang.message.import_from_xml_report', $arReportData));

        return $this->listRefresh();
    }
}