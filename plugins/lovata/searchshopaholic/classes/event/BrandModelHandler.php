<?php namespace Lovata\SearchShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Collection\BrandCollection;
use Lovata\SearchShopaholic\Classes\Helper\SearchHelper;

/**
 * Class BrandModelHandler
 * @package Lovata\SearchShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class BrandModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Brand::extend(function ($obModel) {
            /** @var Brand $obModel */
            $obModel->fillable[] = 'search_synonym';
            $obModel->fillable[] = 'search_content';
        });

        BrandCollection::extend(function ($obCollection) {
            /** @var BrandCollection $obCollection */
            $obCollection->addDynamicMethod('search', function ($sSearch) use ($obCollection) {

                /** @var array $arSettings */
                $arSettings = Settings::getValue('brand_search_by');

                /** @var SearchHelper $obSearchHelper */
                $obSearchHelper = app(SearchHelper::class, [Brand::class]);
                $arElementIDList = $obSearchHelper->result($sSearch, $arSettings);

                return $obCollection->applySorting($arElementIDList);
            });
        });
    }
}
