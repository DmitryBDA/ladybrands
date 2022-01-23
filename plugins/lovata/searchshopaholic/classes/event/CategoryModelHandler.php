<?php namespace Lovata\SearchShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Collection\CategoryCollection;
use Lovata\SearchShopaholic\Classes\Helper\SearchHelper;

/**
 * Class CategoryModelHandler
 * @package Lovata\SearchShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CategoryModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Category::extend(function ($obModel) {
            /** @var Category $obModel */
            $obModel->fillable[] = 'search_synonym';
            $obModel->fillable[] = 'search_content';
        });

        CategoryCollection::extend(function ($obCollection) {
            /** @var CategoryCollection $obCollection */
            $obCollection->addDynamicMethod('search', function ($sSearch) use ($obCollection) {

                /** @var array $arSettings */
                $arSettings = Settings::getValue('category_search_by');

                /** @var SearchHelper $obSearchHelper */
                $obSearchHelper = app(SearchHelper::class, [Category::class]);
                $arElementIDList = $obSearchHelper->result($sSearch, $arSettings);

                return $obCollection->applySorting($arElementIDList);
            });
        });
    }
}
