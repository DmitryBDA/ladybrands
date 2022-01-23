<?php namespace Lovata\SearchShopaholic\Updates;

use Seeder;
use Illuminate\Support\Arr;
use Lovata\Shopaholic\Models\Settings;
use Lovata\SearchShopaholic\Classes\Helper\SearchHelper;

/**
 * Class SeederUpdateSearchSettings
 * @package Lovata\SearchShopaholic\Updates
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SeederUpdateSearchSettings extends Seeder
{
    /**
     * Run seeder
     */
    public function run()
    {
        $this->updateSettings('product_search_by');
        $this->updateSettings('brand_search_by');
        $this->updateSettings('category_search_by');
        $this->updateSettings('tag_search_by');
    }

    /**
     * Get "full" value, fill "type" value, forget "full" value
     * @param $sField
     */
    protected function updateSettings($sField)
    {
        $arSettings = Settings::getValue($sField);
        if (empty($arSettings)) {
            return;
        }

        foreach ($arSettings as $iKey => $arData) {
            $arData['type'] = Arr::get($arData, 'full') ? SearchHelper::TYPE_FULL : SearchHelper::TYPE_DEFAULT;
            Arr::forget($arData, 'full');
            $arSettings[$iKey] = $arData;
        }

        Settings::set($sField, $arSettings);
    }
}
