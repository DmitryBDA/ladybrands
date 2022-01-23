<?php namespace Lovata\LabelsShopaholic\Classes\Event\Product;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

use Lovata\LabelsShopaholic\Models\Label;
use Lovata\LabelsShopaholic\Classes\Store\ProductListStore;
use Lovata\LabelsShopaholic\Classes\Collection\LabelCollection;

/**
 * Class ProductModelHandler
 * @package Lovata\LabelsShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $this->extendItem();
        $this->extendModel();
        $this->extendCollection();
    }

    /**
     * Extend product item
     */
    protected function extendItem()
    {
        ProductItem::extend(function ($obProductItem) {
            /** @var ProductItem $obProductItem */
            $obProductItem->addDynamicMethod('getLabelAttribute', function ($obProductItem) {
                /** @var ProductItem $obProductItem */
                $obLabelCollection = LabelCollection::make()->product($obProductItem->id);

                return $obLabelCollection;
            });
        });
    }

    /**
     * Extend product madel
     */
    protected function extendModel()
    {
        Product::extend(function ($obModel) {
            /** @var Product $obModel */
            $obModel->belongsToMany['label'] = [
                Label::class,
                'table' => 'lovata_labels_shopaholic_product_label',
            ];
        });
    }

    /**
     * Extend product collection
     */
    protected function extendCollection()
    {
        ProductCollection::extend(function ($obCollection) {
            /** @var ProductCollection $obCollection */
            $obCollection->addDynamicMethod('label', function ($iLabelID) use ($obCollection) {
                $arProductIDList = ProductListStore::instance()->label->get($iLabelID);

                return $obCollection->intersect($arProductIDList);
            });
        });
    }
}
