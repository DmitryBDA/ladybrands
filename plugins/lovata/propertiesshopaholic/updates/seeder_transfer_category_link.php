<?php namespace Lovata\PropertiesShopaholic\Updates;

use DB;
use Schema;
use Seeder;
use System\Classes\PluginManager;

use Lovata\Shopaholic\Models\Category;
use Lovata\PropertiesShopaholic\Models\PropertySet;

/**
 * Class SeederTransferCategoryLink
 * @package Lovata\Toolbox\Updates
 */
class SeederTransferCategoryLink extends Seeder
{
    const TABLE_OLD_PRODUCT_LINK = 'lovata_properties_shopaholic_product_link';
    const TABLE_NEW_PRODUCT_LINK = 'lovata_properties_shopaholic_set_product_link';

    const TABLE_OLD_OFFER_LINK = 'lovata_properties_shopaholic_offer_link';
    const TABLE_NEW_OFFER_LINK = 'lovata_properties_shopaholic_set_offer_link';

    /**
     * Run seeder
     */
    public function run()
    {
        if (Schema::hasTable(self::TABLE_OLD_PRODUCT_LINK) && Schema::hasTable(self::TABLE_NEW_PRODUCT_LINK)) {
            $this->transferFromCategoryToSet(self::TABLE_OLD_PRODUCT_LINK, self::TABLE_NEW_PRODUCT_LINK);
        }

        if (Schema::hasTable(self::TABLE_OLD_OFFER_LINK) && Schema::hasTable(self::TABLE_NEW_OFFER_LINK)) {
            $this->transferFromCategoryToSet(self::TABLE_OLD_OFFER_LINK, self::TABLE_NEW_OFFER_LINK);
        }
    }

    /**
     * Transfer data from category link to set link
     * @param string $sCategoryTable
     * @param string $sSetTable
     */
    protected function transferFromCategoryToSet($sCategoryTable, $sSetTable)
    {
        //Get category link list
        $obOCategoryLinkList = DB::table($sCategoryTable)->get();
        if ($obOCategoryLinkList->isEmpty()) {
            return;
        }

        $arInsetRowList = [];
        foreach ($obOCategoryLinkList as $iKey => $obCategoryLink) {

            //Get set id
            $iPropertySetID = $this->getSetID($obCategoryLink->category_id);
            if (empty($iPropertySetID)) {
                continue;
            }

            $arRowData = [
                'property_id' => $obCategoryLink->property_id,
                'set_id'      => $iPropertySetID,
                'groups'      => $obCategoryLink->groups,
            ];

            if (PluginManager::instance()->hasPlugin('Lovata.FilterShopaholic')) {
                $arRowData['in_filter'] = $obCategoryLink->in_filter;
                $arRowData['filter_type'] = $obCategoryLink->filter_type;
                $arRowData['filter_name'] = $obCategoryLink->filter_name;
            }

            $arInsetRowList[] = $arRowData;
        }

        try {
            DB::table($sSetTable)->insert($arInsetRowList);
        } catch (\Exception $obException) {
            return;
        }

    }

    /**
     * Get set ID by category ID
     * @param int $iCategoryID
     * @return int|null
     */
    protected function getSetID($iCategoryID)
    {
        if (empty($iCategoryID)) {
            return null;
        }

        //Get property set by category ID
        $obLink = DB::table('lovata_properties_shopaholic_set_category_link')->where('category_id', $iCategoryID)->first();
        if (!empty($obLink)) {
            return $obLink->set_id;
        }

        //Get category object
        $obCategory = Category::find($iCategoryID);
        if (empty($obCategory)) {
            return null;
        }

        try {
            $obPropertySet = PropertySet::create([
                'name' => $obCategory->name,
                'code' => $obCategory->slug,
            ]);

            $obPropertySet->category()->attach($iCategoryID);
        } catch (\Exception $obException) {
            return null;
        }

        return $obPropertySet->id;
    }
}
