<?php namespace Lovata\PropertiesShopaholic\Updates;

use DB;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Seeder;
use Lovata\PropertiesShopaholic\Models\PropertyValue;

/**
 * Class SeederTransferPropertyValue
 * @package Lovata\Toolbox\Updates
 */
class SeederTransferPropertyValue extends Seeder
{
    const TABLE_OLD_VALUE = 'lovata_properties_shopaholic_value';

    /**
     * Run seeder
     */
    public function run()
    {
        //Get value list
        $obPropertyValueList = DB::table(self::TABLE_OLD_VALUE)->get();
        if ($obPropertyValueList->isEmpty()) {
            return;
        }

        foreach ($obPropertyValueList as $obPropertyValue) {
            $bResult = PropertyValue::hasValue($obPropertyValue->value);

            if ($bResult) {
                $this->transferFromOldToNew($obPropertyValue);
            }
        }
    }

    /**
     * @param $obOldValue
     */
    protected function transferFromOldToNew($obOldValue)
    {
        //Get value object
        $obNewValue = $this->getValueObjectBySlug($obOldValue->value, $obOldValue->slug);
        $iProductID = $this->getProductID($obOldValue);
        if (empty($obNewValue) || empty($iProductID)) {
            return;
        }

        try {
            PropertyValueLink::create([
                'value_id'     => $obNewValue->id,
                'property_id'  => $obOldValue->property_id,
                'product_id'   => $iProductID,
                'element_id'   => $obOldValue->product_id,
                'element_type' => $obOldValue->model,
            ]);
        } catch (\Exception $obException) {
            return;
        }
    }

    /**
     * Get property value object by slug
     * @param string $sValue
     * @param string $sSlug
     * @return PropertyValue
     */
    protected function getValueObjectBySlug($sValue, $sSlug)
    {
        $obValue = PropertyValue::getBySlug($sSlug)->first();
        if (!empty($obValue)) {
            return $obValue;
        }

        try {
            $obValue = PropertyValue::create([
                'value' => $sValue,
                'slug'  => $sSlug,
            ]);
        } catch (\Exception $obException) {
            return null;
        }

        return $obValue;
    }

    /**
     * Get product ID from old value object
     * @param $obOldValue
     * @return int
     */
    protected function getProductID($obOldValue)
    {
        if ($obOldValue->model == Product::class) {
            return $obOldValue->product_id;
        }

        $iOfferID = $obOldValue->product_id;

        //Get offer object
        $obOffer = Offer::withTrashed()->find($iOfferID);
        if (empty($obOffer)) {
            return null;
        }

        return $obOffer->product_id;
    }
}
