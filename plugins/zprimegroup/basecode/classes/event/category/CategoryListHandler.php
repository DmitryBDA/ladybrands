<?php namespace Zprimegroup\BaseCode\Classes\Event\Category;

use Lovata\Shopaholic\Components\CategoryList;
use Lovata\Shopaholic\Models\Category;

class CategoryListHandler
{

  public function subscribe()
  {
    CategoryList::extend(function ($obCategoryList){
      /** @var CategoryList $obCategoryList */
      $obCategoryList->addDynamicMethod('getCategoriesShowMain', function () use ($obCategoryList){
        return $obCategoryList->make(Category::where("show_main", 1)->get()->pluck("id")->toArray());
      });
    });
  }
}
