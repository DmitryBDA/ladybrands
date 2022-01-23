<?php namespace Zprimegroup\BaseCode\Classes\Event\Category;


use Lovata\Shopaholic\Models\Category;

class CategoryModelHandler
{

  public function subscribe()
  {
    Category::extend(function ($obCategory){
      /** @var Category $obCategory */
      $obCategory->addCachedField(['show_main']);
    });
  }
}
