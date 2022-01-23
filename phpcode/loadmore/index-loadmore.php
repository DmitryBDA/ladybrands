<?php

$typeLabel = post('mode');

$numPage = post('page') + 1;

$obLabel = $this->LabelList->make()->sort()->active()->getByCode($typeLabel);
$obProductList = $this->ProductList->make()->label($obLabel->id);

$iPage = 1;
$countPerPage = $this->Pagination->getCountPerPage() * $numPage;

$iCount = $obProductList->count();
$iMaxPage = $this->Pagination->getMaxPage($iCount);


if($countPerPage < $iCount and $iPage < $iMaxPage) {
  $isLoadMore = true;
} else {
  $isLoadMore = false;
}

$arProductList = $obProductList->page($iPage, $countPerPage);

