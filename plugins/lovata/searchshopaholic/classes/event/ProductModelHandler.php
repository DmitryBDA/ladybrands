<?php namespace Lovata\SearchShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\SearchShopaholic\Classes\Helper\SearchHelper;

/**
 * Class ProductModelHandler
 * @package Lovata\SearchShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Product::extend(function ($obModel) {
            /** @var Product $obModel */
            $obModel->fillable[] = 'search_synonym';
            $obModel->fillable[] = 'search_content';
        });
        
        ProductCollection::extend(function ($obCollection) {
            /** @var ProductCollection $obCollection */
            $obCollection->addDynamicMethod('search', function ($sSearch) use ($obCollection) {

                /** @var array $arSettings */
                $arSettings = Settings::getValue('product_search_by');

                /** @var SearchHelper $obSearchHelper */
                $obSearchHelper = app(SearchHelper::class, [Product::class]);
                $arElementIDList = $obSearchHelper->result($sSearch, $arSettings);

                return $obCollection->applySorting($arElementIDList);
            });
        });
    }
}
