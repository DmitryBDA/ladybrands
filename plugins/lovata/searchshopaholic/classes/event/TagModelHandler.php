<?php namespace Lovata\SearchShopaholic\Classes\Event;

use System\Classes\PluginManager;
use Lovata\Shopaholic\Models\Settings;
use Lovata\SearchShopaholic\Classes\Helper\SearchHelper;

/**
 * Class TagModelHandler
 * @package Lovata\SearchShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class TagModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        \Lovata\TagsShopaholic\Models\Tag::extend(function ($obModel) {
            /** @var \Lovata\TagsShopaholic\Models\Tag $obModel */
            $obModel->fillable[] = 'search_synonym';
            $obModel->fillable[] = 'search_content';
        });

        \Lovata\TagsShopaholic\Classes\Collection\TagCollection::extend(function ($obCollection) {
            /** @var \Lovata\TagsShopaholic\Classes\Collection\TagCollection $obCollection */
            $obCollection->addDynamicMethod('search', function ($sSearch) use ($obCollection) {

                /** @var array $arSettings */
                $arSettings = Settings::getValue('tag_search_by');

                /** @var SearchHelper $obSearchHelper */
                $obSearchHelper = app(SearchHelper::class, [\Lovata\TagsShopaholic\Models\Tag::class]);
                $arElementIDList = $obSearchHelper->result($sSearch, $arSettings);

                return $obCollection->applySorting($arElementIDList);
            });
        });
    }
}
