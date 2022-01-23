<?php

$obSlideList = $this->slider->getSlides();
$obLabelList = $this->LabelList->make()->sort()->active();

$obProductListWithLabels = [];
$index = 0;
foreach($obLabelList as $item){
  $obList = $this->ProductList->make()->label($item->id);

  $iPage = $this->Pagination->getPageFromRequest();
  $countPerPage = $this->Pagination->getCountPerPage();
  $iCount = $obList->count();
  $iMaxPage = $this->Pagination->getMaxPage($iCount);

  if($countPerPage < $iCount and $iPage < $iMaxPage) {
    $isLoadMore = true;
  } else {
    $isLoadMore = false;
  }

  $arProductList = $obList->page($iPage, $countPerPage);

  if($obList->isNotEmpty())
  {
    $obProductListWithLabels[$index]['label'] = $item;
    $obProductListWithLabels[$index]['arProductList'] = $arProductList;
    $obProductListWithLabels[$index]['isLoadMore'] = $isLoadMore;
    $obProductListWithLabels[$index]['iPage'] = $iPage;
  }
  $index++;
}

$imagesInsta = $this->SiteSettings->get('image_instagram');
