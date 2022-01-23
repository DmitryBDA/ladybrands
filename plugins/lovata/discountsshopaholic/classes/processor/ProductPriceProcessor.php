<?php namespace Lovata\DiscountsShopaholic\Classes\Processor;

use DB;
use System\Classes\PluginManager;

use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class ProductPriceProcessor
 * @package Lovata\DiscountsShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductPriceProcessor
{
    /** @var Product */
    protected $obProduct;

    /** @var \October\Rain\Database\Collection|\Lovata\Shopaholic\Models\Offer[]  */
    protected $obOfferList;

    /** @var \Lovata\DiscountsShopaholic\Models\Discount */
    protected $obPercentProductDiscount;

    /** @var \Lovata\DiscountsShopaholic\Models\Discount */
    protected $obFixedProductDiscount;

    /** @var \Lovata\DiscountsShopaholic\Models\Discount */
    protected $obPercentOfferDiscount;

    /** @var \Lovata\DiscountsShopaholic\Models\Discount */
    protected $obFixedOfferDiscount;

    /** @var \October\Rain\Database\Collection|\Lovata\DiscountsShopaholic\Models\Discount */
    protected $obActiveDiscountList;

    /**
     * ProductPriceProcessor constructor.
     * @param Product $obProduct
     */
    public function __construct($obProduct)
    {
        if (empty($obProduct) || !$obProduct instanceof Product) {
            return;
        }

        CurrencyHelper::instance()->disableActiveCurrency();

        $this->obProduct = $obProduct;
        $this->obOfferList = $obProduct->offer;
        if ($this->obOfferList->isEmpty()) {
            return;
        }

        $this->obActiveDiscountList = Discount::active()->currentActive()->get();
    }

    /**
     * Update offers prices for product
     */
    public function run()
    {
        if (empty($this->obProduct) || $this->obOfferList->isEmpty()) {
            return;
        }

        $this->initProductDiscount();
        $this->initBrandDiscount();
        $this->initCategoryDiscount($this->obProduct->category_id);
        $this->initTagDiscount();

        foreach ($this->obOfferList as $obOffer) {
            $obOffer->setActiveCurrency(null);
            $this->updateOfferPrice($obOffer);
        }
    }

    /**
     * Find active discounts by relation with product
     */
    protected function initProductDiscount()
    {
        if (empty($this->obActiveDiscountList) || $this->obActiveDiscountList->isEmpty()) {
            return;
        }

        //Get discount Id list by relation with product
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_product')->where('product_id', $this->obProduct->id)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        //Process discount Id list and find active discounts
        foreach($arDiscountIDList as $iDiscountID) {
            $obDiscount = $this->obActiveDiscountList->find($iDiscountID);
            if (empty($obDiscount)) {
                continue;
            }

            $this->addProductDiscount($obDiscount);
        }
    }

    /**
     * Find active discounts by relation with brand
     */
    protected function initBrandDiscount()
    {
        if (empty($this->obActiveDiscountList) || $this->obActiveDiscountList->isEmpty() || empty($this->obProduct->brand_id)) {
            return;
        }

        //Get discount Id list by relation with brand
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_brand')->where('brand_id', $this->obProduct->brand_id)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        //Process discount ID list and find active discounts
        foreach($arDiscountIDList as $iDiscountID) {
            $obDiscount = $this->obActiveDiscountList->find($iDiscountID);
            if (empty($obDiscount)) {
                continue;
            }

            $this->addProductDiscount($obDiscount);
        }
    }

    /**
     * Find active discounts by relation with category
     * @param int $iCategoryID
     */
    protected function initCategoryDiscount($iCategoryID)
    {
        if (empty($this->obActiveDiscountList) || $this->obActiveDiscountList->isEmpty() || empty($iCategoryID)) {
            return;
        }

        //Get discount Id list by relation with brand
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_category')->where('category_id', $iCategoryID)->lists('discount_id');
        if (!empty($arDiscountIDList)) {
            //Process discount ID list and find active discounts
            foreach($arDiscountIDList as $iDiscountID) {
                $obDiscount = $this->obActiveDiscountList->find($iDiscountID);
                if (empty($obDiscount)) {
                    continue;
                }

                $this->addProductDiscount($obDiscount);
            }
        }

        //Get category object and check discount for parent category
        $obCategory = Category::find($iCategoryID);
        if (empty($obCategory) || empty($obCategory->parent_id)) {
            return;
        }

        $this->initCategoryDiscount($obCategory->parent_id);
    }

    /**
     * Find active discounts by relation with tags
     */
    protected function initTagDiscount()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        if (empty($this->obActiveDiscountList) || $this->obActiveDiscountList->isEmpty()) {
            return;
        }

        //Get tag ID list
        $arTagIDList = (array) DB::table('lovata_tagsshopaholic_tag_product')->where('product_id', $this->obProduct->id)->lists('tag_id');
        if (empty($arTagIDList)) {
            return;
        }

        //Get discount Id list by relation with brand
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_tag')->whereIn('tag_id', $arTagIDList)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        //Process discount ID list and find active discounts
        foreach($arDiscountIDList as $iDiscountID) {
            $obDiscount = $this->obActiveDiscountList->find($iDiscountID);
            if (empty($obDiscount)) {
                continue;
            }

            $this->addProductDiscount($obDiscount);
        }
    }

    /**
     * Update offer price
     * @param \Lovata\Shopaholic\Models\Offer $obOffer
     */
    protected function updateOfferPrice($obOffer)
    {
        if (empty($obOffer)) {
            return;
        }

        $this->initOfferDiscount($obOffer->id);
        $this->updatePriceValue($obOffer);

        $obOffer->save();
    }

    /**
     * Clear old price value
     * @param \Lovata\Shopaholic\Models\Offer $obOffer
     */
    protected function clearPriceValue(&$obOffer)
    {
        $obOffer->discount_id = null;
        $obOffer->discount_value = 0;
        $obOffer->discount_type = null;

        if ($obOffer->old_price_value == 0) {
            return;
        }

        $obOffer->price = $obOffer->old_price_value;
        $obOffer->old_price = 0;
        $obOffer->save();
    }

    /**
     * Update old price value
     * @param \Lovata\Shopaholic\Models\Offer $obOffer
     */
    protected function updatePriceValue(&$obOffer)
    {
        $this->clearPriceValue($obOffer);
        if (empty($this->obFixedOfferDiscount) && empty($this->obPercentOfferDiscount)) {
            return;
        }

        //calculate discount value
        $fFixedDiscount = !empty($this->obFixedOfferDiscount) ? PriceHelper::round($this->obFixedOfferDiscount->discount_value) : 0;
        $fPercentDiscount = !empty($this->obPercentOfferDiscount) ? PriceHelper::round($obOffer->price_value * ($this->obPercentOfferDiscount->discount_value / 100)) : 0;

        //Apply discount
        if ($fFixedDiscount > 0 && $fFixedDiscount > $fPercentDiscount && $fFixedDiscount < $obOffer->price_value) {
            $obOffer->discount_id = $this->obFixedOfferDiscount->id;
            //$obOffer->discount_price = $fFixedDiscount;
            $obOffer->discount_value = $this->obFixedOfferDiscount->discount_value;
            $obOffer->discount_type = Discount::FIXED_TYPE;
            $obOffer->old_price = $obOffer->price_value;
            $obOffer->price = PriceHelper::round($obOffer->price_value - $fFixedDiscount);
        } elseif($fPercentDiscount > 0 && $fPercentDiscount < $obOffer->price_value) {
            $obOffer->discount_id = $this->obPercentOfferDiscount->id;
            //$obOffer->discount_price = $fPercentDiscount;
            $obOffer->discount_value = $this->obPercentOfferDiscount->discount_value;
            $obOffer->discount_type = Discount::PERCENT_TYPE;
            $obOffer->old_price = $obOffer->price_value;
            $obOffer->price = PriceHelper::round($obOffer->price_value - $fPercentDiscount);
        }
    }

    /**
     * Find active discounts by relation with offer
     * @param int $iOfferID
     */
    protected function initOfferDiscount($iOfferID)
    {
        $this->obFixedOfferDiscount = $this->obFixedProductDiscount;
        $this->obPercentOfferDiscount = $this->obPercentProductDiscount;

        //Get discount Id list by relation with offer
        $arDiscountIDList = (array) DB::table('lovata_discounts_shopaholic_discount_offer')->where('offer_id', $iOfferID)->lists('discount_id');
        if (empty($arDiscountIDList)) {
            return;
        }

        //Process discount Id list and find active discounts
        foreach($arDiscountIDList as $iDiscountID) {
            $obDiscount = $this->obActiveDiscountList->find($iDiscountID);
            if (empty($obDiscount)) {
                continue;
            }

            if ($obDiscount->discount_type == Discount::FIXED_TYPE && (empty($this->obFixedOfferDiscount) || $this->obFixedOfferDiscount->discount_value < $obDiscount->discount_value)) {
                $this->obFixedOfferDiscount = $obDiscount;
            } elseif($obDiscount->discount_type == Discount::PERCENT_TYPE && (empty($this->obPercentOfferDiscount) || $this->obPercentOfferDiscount->discount_value < $obDiscount->discount_value)) {
                $this->obPercentOfferDiscount = $obDiscount;
            }
        }
    }

    /**
     * Add product discount
     * save discount with maximum value
     * @param \Lovata\DiscountsShopaholic\Models\Discount $obDiscount
     */
    protected function addProductDiscount($obDiscount)
    {
        if (empty($obDiscount)) {
            return;
        }

        if ($obDiscount->discount_type == Discount::FIXED_TYPE && (empty($this->obFixedProductDiscount) || $this->obFixedProductDiscount->discount_value < $obDiscount->discount_value)) {
            $this->obFixedProductDiscount = $obDiscount;
        } elseif($obDiscount->discount_type == Discount::PERCENT_TYPE && (empty($this->obPercentProductDiscount) || $this->obPercentProductDiscount->discount_value < $obDiscount->discount_value)) {
            $this->obPercentProductDiscount = $obDiscount;
        }
    }
}
