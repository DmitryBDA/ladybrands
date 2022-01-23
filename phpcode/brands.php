<?php
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

$obBrand = $this->BrandPage->get();
$obProductList = $this->ProductList->make()->brand($obBrand->id)->filterByQuantity()->sort($this->ProductList->getSorting())->active();
$obProductList2 = $this->ProductList->make()->brand($obBrand->id)->filterByQuantityNull()->sort($this->ProductList->getSorting())->active();

$obProductList = $obProductList->merge($obProductList2->getIdList());

$obOfferMinPriceTotal = $obProductList->getOfferMinPrice();
$obOfferMaxPriceTotal = $obProductList->getOfferMaxPrice();

$fPriceFrom = '';
$fPriceTo = '';
$arAppliedPriceList = input('price');
if($arAppliedPriceList){
  $arAppliedPriceList = explode('|', $arAppliedPriceList);
  $fPriceFrom = $arAppliedPriceList[0];
  $fPriceTo = $arAppliedPriceList[1];
}

$arAppliedBrandList = input('brand');
if($arAppliedBrandList){
  $arAppliedBrandList = explode('|', $arAppliedBrandList);
  $obProductList = $obProductList->filterByBrands($arAppliedBrandList);
  $this['arAppliedBrandList'] = $obProductList->getArrIdBrandsBySlug($arAppliedBrandList);
}

$obProductList = $obProductList->filterByPrice($fPriceFrom, $fPriceTo);

$obOfferMinPrice = $obProductList->getOfferMinPrice();
$obOfferMaxPrice = $obProductList->getOfferMaxPrice();

$iPage = $this->Pagination->getPageFromRequest();
$countPerPage = $this->Pagination->getCountPerPage();
$iCount = $obProductList->count();
$iMaxPage = $this->Pagination->getMaxPage($iCount);



if($countPerPage < $iCount and $iPage < $iMaxPage) {
  $isLoadMore = true;
} else {
  $isLoadMore = false;
}

$arPaginationList = $this->Pagination->get($iPage,$iCount,$countPerPage);

if($iMaxPage == 1 ){
  $arPaginationList = '';
}
$arProductList = $obProductList->page($iPage, $countPerPage);

$arBreadcrumbs[] = [
  'name' => $obBrand->name,
  'url' => $obBrand->getPageUrl('brands-routing'),
  'active' => true,
];

$arBreadcrumbs[] = [
  'name' => 'Бренды',
  'url' => \Cms\Classes\Page::url('brands'),
  'active' => false,
];

$arBreadcrumbs[] = [
  'name' => 'Главная',
  'url' => \Cms\Classes\Page::url('index'),
  'active' => false,
];

$arBreadcrumbs = array_reverse($arBreadcrumbs);

