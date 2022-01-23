<?php
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

$obProductList = $this->ProductList->make()->filterByQuantity()->sort($this->ProductList->getSorting())->active();
$obProductList2 = $this->ProductList->make()->filterByQuantityNull()->sort($this->ProductList->getSorting())->active();

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
  $arBrandsIds = $obProductList->getArrBrandsId($arAppliedBrandList);
  $obProductList->filterByBrandList($arBrandsIds);
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

$obCategoryList = $this->CategoryList->make()->tree();

$arBreadcrumbs[] = [
  'name' => 'Каталог',
  'url' => \Cms\Classes\Page::url('catalog'),
  'active' => false,
];

$arBreadcrumbs[] = [
  'name' => 'Главная',
  'url' => \Cms\Classes\Page::url('index'),
  'active' => false,
];

$arBreadcrumbs = array_reverse($arBreadcrumbs);

$obBrandList = $this->BrandList->make()->active()->sort();
