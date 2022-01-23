<?php namespace Zprimegroup\BaseCode\Classes\Event\Product;

use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Models\Brand;

class ProductCollectionHandler
{

  public function subscribe()
  {
    ProductCollection::extend(function ($obProductList){
      /** @var ProductCollection $obProductList */
      $obProductList->addDynamicMethod('getArrBrandsId', function ($arrSlugs) use ($obProductList){

        $arrBrandIds = [];

        foreach ($arrSlugs as $item){
          $obBrand = Brand::getBySlug($item)->get()->first();
          if($obBrand){
            $arrBrandIds[] = $obBrand->id;
          }
        }

        return $arrBrandIds;
      });
    });

    ProductCollection::extend(function ($obProductList){
      /** @var ProductCollection $obProductList */
      $obProductList->addDynamicMethod('getArrIdBrandsBySlug', function ($arrSlugs) use ($obProductList){

        $arrBrandIds = [];

        foreach ($arrSlugs as $item){
          $obBrand = Brand::getBySlug($item)->get()->first();
          if($obBrand){
            $arrBrandIds[] = $obBrand->id;
          }
        }

        return $arrBrandIds;
      });
    });
  }
}
