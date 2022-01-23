<?php
use Lovata\Shopaholic\Classes\Collection\ProductCollection;

$obProductItem = $this->ProductPage->get();
$obCategoryItem = $this->CategoryPage->get();
$obMainCategoryItem = $this->MainCategoryPage->get();

if (!empty($this->param('slug')) && empty($obProductItem) && empty($obCategoryItem)) {
  return $this->ProductPage->getErrorResponse();
}
$obActiveCategoryItem = !empty($obCategoryItem) ? $obCategoryItem : $obMainCategoryItem;
$arBreadcrumbs = [];

if (!empty($obProductItem)) {
  $arBreadcrumbs[] = [
    'name' => $obProductItem->name,
    'url' => $obProductItem->getPageUrl('catalog-routing'),
    'active' => true,
  ];

}

// функция транслита
function translit($s) {
  $s = (string) $s; // преобразуем в строковое значение
  // $s = strip_tags($s); // убираем HTML-теги
  $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
  $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
  $s = trim($s); // убираем пробелы в начале и конце строки
  $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
  $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
  // $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
  $s = str_replace(" ", "", $s); // заменяем пробелы знаком минус
  $s = str_replace("/", "", $s);
  $s = str_replace("-", "", $s);
  $s = str_replace(".", "x", $s);
  $s = str_replace(",", "x", $s);
  $s = str_replace("(", "", $s);
  $s = str_replace(")", "", $s);
  // $s = str_replace("%", "", $s);
  return $s; // возвращаем результат
}


if (!empty($obActiveCategoryItem)) {
  $obCurrentCategory = $obActiveCategoryItem;
  $parentCategory = '';
  while($obCurrentCategory->isNotEmpty()) {
    $parentCategory = $obCurrentCategory;
    $arBreadcrumbs[] = [
      'name' => $obCurrentCategory->name,
      'url' => $obCurrentCategory->getPageUrl('catalog-routing', ['slug']),
      'active' => $this->ProductPage->get() ? false : $obCurrentCategory->id == $obActiveCategoryItem->id,
    ];
    $obCurrentCategory = $obCurrentCategory->parent;
  }
}

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

$obCategoryList = $this->CategoryList->make()->tree();
$reviewsProd = 0;
if($obProductItem)
{
  $reviewsProd = $this->reviews->getAllReviewsByProductId($obProductItem->id);
  $totalRating = 0;
  foreach ($reviewsProd as $review){
    $totalRating += $review->rating;
  }

  $countReviews = count($reviewsProd);
  if($countReviews){
    $rating = round($totalRating / $countReviews, 1);
  }

  $reviewsProd = $reviewsProd->take(5);

} else if($obActiveCategoryItem) {
  if($obActiveCategoryItem->children->isNotEmpty())
  {

  } else {
    //$products = $this->ProductList->make()->sort($this->ProductList->getSorting())->category($obActiveCategoryItem->id)->active();

    $obProductList = $this->ProductList->make()->sort($this->ProductList->getSorting())->category($obActiveCategoryItem->id)->active()->filterByQuantity();
    $obProductList2 = $this->ProductList->make()->sort($this->ProductList->getSorting())->category($obActiveCategoryItem->id)->active()->filterByQuantityNull();
    $obProductList = $obProductList->merge($obProductList2->getIdList());

    $obNotFilteredProductList = $obProductList->copy();

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

    // формируем массив из примененных фильтров
    $arAppliedPropertyList = input('property');
    if($arAppliedPropertyList){
      foreach ($arAppliedPropertyList as $key => $value) {
        $arAppliedPropertyList[$key] = explode('|', translit($value));
      }
      $this['arAppliedPropertyList'] = $arAppliedPropertyList;
    }

    $obProductList = $obProductList->filterByPrice($fPriceFrom, $fPriceTo);


    $obProductPropertyList = $obActiveCategoryItem->product_filter_property->setCategory(null)->setProductList($obNotFilteredProductList);
    $obProductList = $obProductList->filterByProperty($arAppliedPropertyList, $obProductPropertyList);

    $obOfferMinPrice = $obProductList->getOfferMinPrice();
    $obOfferMaxPrice = $obProductList->getOfferMaxPrice();

    $obBrandList = $this->BrandList->make()->active()->sort()->category($obActiveCategoryItem->id);


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

    if($iMaxPage == 1){
      $arPaginationList = '';
    }
    $arProductList = $obProductList->page($iPage, $countPerPage);

  }

}

