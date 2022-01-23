<?php

$arBreadcrumbs[] = [
  'name' => 'Корзина',
  'url' => \Cms\Classes\Page::url('cart'),
  'active' => true,
];

$arBreadcrumbs[] = [
  'name' => 'Главная',
  'url' => \Cms\Classes\Page::url('index'),
  'active' => false,
];

$arBreadcrumbs = array_reverse($arBreadcrumbs);

$obCartPositionList = $this->Cart->get();


$obShippingTypeList = $this->ShippingTypeList->make()->sort()->active();

$obPaymentMethodList = $this->PaymentMethodList->make()->sort()->active();
