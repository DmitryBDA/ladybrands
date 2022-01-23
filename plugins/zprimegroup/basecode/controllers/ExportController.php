<?php namespace Zprimegroup\Basecode\Controllers;

use Backend\Classes\Controller;
use App;
use Illuminate\Support\Str;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\Shopaholic\Models\Brand;
use Model;
use Flash;
use Illuminate\Support\Facades\DB;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Price;

use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertyOfferLink;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;

use Lovata\Shopaholic\Components\CategoryList;



use Illuminate\Database\Eloquent\Model as ModelBase;
use Vdlp\Redirect\Models\Redirect;


class ExportController extends Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function exportBrands()
  {

    $brands = DB::select('select * from catalogBrands ORDER BY id');

    $testTable = [];
    $testTable2 = [];


    /* foreach ($categories as $category) {
       $testTable[$category->id]['parent_old'] = $category->parentId;
       $testTable[$category->id]['new_id'] = '';

     }*/

    $i = 0;
    $errorArr = [];
    foreach ($brands as $brand) {
      if($brand->brand == ''){
        continue;
      }
      $data = [];
      $data = [
        'id' => $brand->id,
        'active' => 1,
        'code' => $brand->country,
        'name' => $brand->brand,
        'slug' => Str::slug($brand->brand),
        'description' => $brand->text,
      ];

      $test = (new Brand)->create($data);
     /* $id_new_cat = $test->id;
      $slug_new_cat = $test->slug;

      $testTable[$category->id]['new_id'] = $test->id;*/

      $i++;


    }

    dd('success');
  }

  public function exportImagesBrands()
  {

    set_time_limit(0);

    $categories = DB::select('select * from catalogBrands');

    function glob_tree_search($path, $pattern, $_base_path = null)
    {
      if (is_null($_base_path)) {
        $_base_path = '';
      } else {
        $_base_path .= basename($path) . '/';
      }

      $out = array();
      foreach (glob($path . '/' . $pattern, GLOB_BRACE) as $file) {
        $out[] = $_base_path . basename($file);
      }

      foreach (glob($path . '/*', GLOB_ONLYDIR) as $file) {
        $out = array_merge($out, glob_tree_search($file, $pattern, $_base_path));
      }

      return $out;
    }

    $arr = [];
    foreach ($categories as $category) {
      $path = storage_path() . "/upload/catalog/brands";
      $files = glob_tree_search($path, $category->photo);
      if (!empty($files) and $files[0] != 'brands') {
        $arr[$category->id]['path'] = $files[0];
      }
    }


    foreach ($arr as $key => $elem) {

      if($elem['path'] == 'thumb'){
        continue;
      }
      /* if(strpos($elem['path'], '7widkpyj0fwlik33.jpg') == true){
         dd('yes');
       }*/

      $obBrand = DB::table('lovata_shopaholic_brands')->where('id', $key)->first();

      if (!empty($obBrand)) {
        $newParentId = $obBrand->id;

        $file1 = new \System\Models\File;
        $file1->data = storage_path() . "/upload/catalog/brands/" . $elem['path'];
        $file1->is_public = true;
        $file1->attachment_id = $newParentId;
        $file1->field = 'preview_image';
        $file1->attachment_type = 'Lovata\Shopaholic\Models\Brand';
        $file1->save();
      }


    }

    dd('success');

  }

  public function exportCategories()
  {

    $categories = DB::select('select * from catalogCategories ORDER BY parentId');

    $testTable = [];
    $testTable2 = [];


   /* foreach ($categories as $category) {
      $testTable[$category->id]['parent_old'] = $category->parentId;
      $testTable[$category->id]['new_id'] = '';

    }*/

    $i = 0;
    $errorArr = [];
    foreach ($categories as $category) {
      $data = [];
      $data = [
        'id' => $category->id,
        'active' => 1,
//        'id_temp' => $category->id,
        'parent_id' => $category->parentId != 0 ? $category->parentId: null,
        'name' => $category->title,
        'slug' => Str::slug($category->title),
        'description' => $category->text,
        'seo_title' => $category->mtitle ?? '',
        'seo_desc' => $category->md ?? '',
        'seo_keywords' => $category->mk ?? '',

      ];

      $test = (new Category)->create($data);
      $id_new_cat = $test->id;
      $slug_new_cat = $test->slug;

      $testTable[$category->id]['new_id'] = $test->id;

      $i++;


    }

    dd('success');
  }

  public function exportCategoriesVL()
  {

    $categories = Category::all();

    foreach ($categories as $category) {
      $oldId = $category->id_temp;
      $parentIdOld = $category->parent_uuid;

      if ($parentIdOld != 0) {
        $id_new_cat = $category->id;
        $slug_new_cat = $category->slug;

        $obCategory = DB::table('lovata_shopaholic_categories')->where('id_temp', $parentIdOld)->first();

        if (!empty($obCategory)) {
          $newParentId = $obCategory->id;
        } else {
          $newParentId = 0;
        }

        $category->updateOrCreate(
          ['id' => $id_new_cat, 'slug' => $slug_new_cat],
          ['parent_id' => $newParentId]
        );

      }
    }

  }


  public function exportProducts()
  {

    $propertiesDefault = [
      1 =>[
        'name' => 'Объем',
        'code' => 'obem',
        'type' => 'single_checkbox',
      ]
    ];

    $isObSet = PropertySet::where('name', '=', 'Главные')->first();

    if (!isset($isObSet)) {
      $propertySet = new PropertySet;

      $propertySet->name = 'Главные';
      $propertySet->code = Str::slug('Главные');
      $propertySet->is_global = 1;
      $propertySet->save();
    }


    foreach ($propertiesDefault as $item){
      $isObProperty = Property::where('name', '=', $item['name'])->first();

      if (!isset($isObProperty)) {
        $obProperty = new Property;

        $obProperty->active = 1;
        $obProperty->type = $item['type'];
        $obProperty->name = $item['name'];
        $obProperty->slug = Str::slug($item['name']);
        $obProperty->code = $item['code'];
        $obProperty->settings = "{\"is_translatable\":\"0\",\"tab\":\"\",\"datepicker\":\"date\",\"mediafinder\":\"file\"}";
        $obProperty->save();

        DB::update("INSERT INTO `lovata_properties_shopaholic_set_product_link`(`property_id`, `set_id`) VALUES ($obProperty->id,$propertySet->id)");
      }

    }

    $add = 0;
    $new = 0;

    $testProduct = '';
    set_time_limit(0);

    $products = DB::select("select * from catalogItems WHERE public = '1'");

    $firstPeriod = 0;

    foreach ($products as $product) {

      //$isProduct = Product::where('name', '=', trim($product->title))->first();

     // if(isset($isProduct)){
      //  continue;
      //} else {

        //$parentCategory = $product->categoryId;
        //$obCategory = DB::table('lovata_shopaholic_categories')->where('id', $parentCategory)->first();

      /*  if (!empty($obCategory)) {
          $categoryIdinNewDB = $obCategory->id;
        }

        $obCategory = Category::find($categoryIdinNewDB);

        $setCat = $obCategory->id;*/

      /*  $obCategoryParent = $obCategory;
        while (!empty($obCategoryParent)) {
          $setCat = $obCategoryParent->id;
          $obCategoryParent = $obCategoryParent->parent;
        }*/


       /* $additionalDescription = DB::select("select text from catalogAdditionalpages WHERE itemId = $product->id");

        if(!empty($additionalDescription)){
          $additionalDescription = $additionalDescription[0]->text;
        } else {
          $additionalDescription = '';
        }*/

        $data = [
          'id' => $product->id,
          'active' => 1,
          'name' => trim($product->title),
          'brand_id' => $product->brand,
          'category_id' => $product->categoryId,
          'description' => $product->text1 . $product->text2 . $product->text3 . $product->text4 ,
          'preview_text' => strip_tags($product->announcement) ,
        ];


        $obProduct = Product::create($data);
        //$obProduct = Product::where('id', 1)->first();

        $oldIdProd = $product->id;
      /*  $obOffers = DB::table('catalogAdditionals')
          ->select('catalogAdditionals.id',
            'catalogAdditionalParametersValues.value as value_param',
            'catalogItemsParametersTitles.title as title_param',
            'catalogAdditionals.public',
            'catalogAdditionals.title',
            'catalogAdditionals.count',
            'catalogAdditionals.cost')
          ->join('catalogAdditionalParametersValues', 'catalogAdditionalParametersValues.parentId', '=', 'catalogAdditionals.id')
          ->join('catalogItemsParametersTitles', function($join) use ($oldIdProd)
          {
            $join->on('catalogAdditionalParametersValues.index','=', 'catalogItemsParametersTitles.index')
              ->where('catalogItemsParametersTitles.parentId', '=', $oldIdProd);
          })
          ->where('catalogAdditionals.itemId', '=', $oldIdProd)
          ->orderBy('catalogAdditionals.id', 'ASC')
          ->get();


        $property = DB::table('catalogItemsParametersTitles')
          ->select('catalogItems.title',
            'catalogAdditionals.title as name_offer',
            'catalogAdditionalParametersValues.value as value_param',
            'catalogItemsParametersTitles.title AS name_param')
          ->join('catalogItems', 'catalogItems.id', '=', 'catalogItemsParametersTitles.parentId')
          ->join('catalogAdditionals', 'catalogItems.id', '=', 'catalogAdditionals.itemId')
          ->join('catalogAdditionalParametersValues', function($join)
          {
            $join->on('catalogAdditionals.id', '=', 'catalogAdditionalParametersValues.parentId');
            $join->on('catalogAdditionalParametersValues.index','=', 'catalogItemsParametersTitles.index');
          })
          ->where('catalogItems.id', '=', $product->id)
          ->get();*/

        $ObProperty = Property::where('name', '=', 'Объем')->first();
//        $valueProperty
        $findme = 'мл';
        $mystring = $product->volume;
        $pos = strpos($product->volume, $findme);

        if ($pos === false) {
          $valueProperty = '';
        } else {
          /*$valueProperty = trim(str_replace($findme, '', $mystring));

          $isObPropertyValue = PropertyValue::where('value', '=', $valueProperty)->first();

          if (!isset($isObPropertyValue)) {
            $obPropertyValue = new PropertyValue;
            $obPropertyValue->value = $valueProperty;
            $obPropertyValue->slug = Str::slug($valueProperty);
            $obPropertyValue->save();
            $arrVulue[] = $obPropertyValue->id;
          } else {
            $obPropertyValue = $isObPropertyValue;
            $arrVulue[] = $obPropertyValue->id;
          }*/


        }

        if($product->cost){

          $data = [];
          $data = [
            'active' => 1,
            'product_id' => $obProduct->id,
            'name' => $product->title,
            'quantity' => $product->presence ? 1 : 0 ,
            'price' => $product->cost,
          ];

          $obOfferNew = (new Offer)->create($data);
        }
/*
        if ($property->isNotEmpty()) {

          $dataSootvet = [

          ];

          foreach ($property as $elem) {


            $namePropery = $elem->name_param;
            $valueProperty = $elem->value_param;

            if($valueProperty == '-'){
              $valueProperty = '';
            }
            $typeProperty = '';
            $filterType = '';
            if (is_float($valueProperty)) {

              $typeProperty = 'number';
              $filterType = 'between';
            } elseif (is_int($valueProperty)) {

              $typeProperty = 'number';
              $filterType = 'between';
            } elseif (is_string($valueProperty)) {

              $typeProperty = 'input';
              $filterType = 'switch';
            }

            $isObProperty = Property::where('name', '=', $namePropery)->first();

            if (!isset($isObProperty)) {
              $obProperty = new Property;
              $obProperty->active = 1;
              $obProperty->type = $typeProperty;
              $obProperty->name = $namePropery;
              $obProperty->slug = Str::slug($namePropery);
              $obProperty->settings = "{\"is_translatable\":\"0\",\"tab\":\"\",\"datepicker\":\"date\",\"mediafinder\":\"file\"}";
              $obProperty->save();
            } else {
              $obProperty = $isObProperty;
            }

            if(!empty($valueProperty))
            {
              $isObPropertyValue = PropertyValue::where('value', '=', $valueProperty)->first();

              if (!isset($isObPropertyValue)) {
                $obPropertyValue = new PropertyValue;
                $obPropertyValue->value = $valueProperty;
                $obPropertyValue->slug = Str::slug($valueProperty);
                $obPropertyValue->save();
                $arrVulue[] = $obPropertyValue->id;
              } else {
                $obPropertyValue = $isObPropertyValue;
                $arrVulue[] = $obPropertyValue->id;
              }
            } else {
              $arrVulue[] = 999;
            }

            $result = DB::table("lovata_properties_shopaholic_set_offer_link")
              ->select('*')
              ->where('property_id', '=', $obProperty->id)
              ->where('set_id', '=', $setCat)
              ->get()
              ->first();
            if (empty($result)) {
              \DB::table("lovata_properties_shopaholic_set_offer_link")
                ->insert(
                  [
                    "property_id" => $obProperty->id,//$propSet->id,
                    "set_id" => $setCat,
                    "groups" => "",
                    "in_filter" => 1,
                    "filter_type" => $filterType
                  ]
                );
            }


          }

        }

        $prop = 0;
        $arrVulue = [];
        $arrOffersOld = [];
        if ($obOffers->isNotEmpty()) {

          foreach ($obOffers as $obOffer) {


            if (!in_array($obOffer->id, $arrOffersOld)) {
              $arrOffersOld[] = $obOffer->id;

              $data = [];

              $data = [
                'active' => $obOffer->public,
                'product_id' => $obProduct->id,
                'name' => $obOffer->title,
                'quantity' => $obOffer->count,

              ];

              $obOfferNew = (new Offer)->create($data);

              $obPrice = Price::where('item_id', $obOfferNew->id)->first();

              $obPrice->update(
                ['price' => $obOffer->cost,]
              );

            }

            $idPropertyValue = PropertyValue::where('value', '=', $obOffer->value_param)->first();
            $idProperty = Property::where('name', '=', $obOffer->title_param)->first();
            if ($property->isNotEmpty()) {

              if(!empty($obOffer->value_param) and $obOffer->value_param != '-'){
                PropertyValueLink::updateOrCreate(
                  [
                    'value_id' => $idPropertyValue->id,
                    'property_id' => $idProperty->id,
                    'product_id' => $obProduct->id,
                    'element_id' => $obOfferNew->id,
                    'element_type' => "Lovata\Shopaholic\Models\Offer",
                  ]
                );
              }
            }

            $prop++;

          }
        } else {

          if($product->cost){

            $data = [];
            $data = [
              'active' => 1,
              'product_id' => $obProduct->id,
              'name' => $product->title,
              'quantity' => $product->count,

            ];

            $obOfferNew = (new Offer)->create($data);

            $obPrice = Price::where('item_id', $obOfferNew->id)->first();

            $obPrice->update(
              ['price' => $product->cost,]
            );

          }
        }
*/

        $firstPeriod++;
      }
    //}

    dd('success');
  }

  public function exportImagesCategories()
  {

    set_time_limit(0);

    $categories = DB::select('select * from catalogCategories');

    function glob_tree_search($path, $pattern, $_base_path = null)
    {
      if (is_null($_base_path)) {
        $_base_path = '';
      } else {
        $_base_path .= basename($path) . '/';
      }

      $out = array();
      foreach (glob($path . '/' . $pattern, GLOB_BRACE) as $file) {
        $out[] = $_base_path . basename($file);
      }

      foreach (glob($path . '/*', GLOB_ONLYDIR) as $file) {
        $out = array_merge($out, glob_tree_search($file, $pattern, $_base_path));
      }

      return $out;
    }

    $arr = [];
    foreach ($categories as $category) {
      $path = storage_path() . "/upload/catalog/categories/thumb";
      $files = glob_tree_search($path, $category->photo);
      if (!empty($files) and $files[0] != 'category') {
        $arr[$category->id]['path'] = $files[0];
      }
    }


    foreach ($arr as $key => $elem) {

      if($elem['path'] == 'thumb'){
        continue;
      }
      /* if(strpos($elem['path'], '7widkpyj0fwlik33.jpg') == true){
         dd('yes');
       }*/

      $obCategory = DB::table('lovata_shopaholic_categories')->where('id', $key)->first();

      if (!empty($obCategory)) {
        $newParentId = $obCategory->id;

        $file1 = new \System\Models\File;
        $file1->data = storage_path() . "/upload/catalog/categories/thumb/" . $elem['path'];
        $file1->is_public = true;
        $file1->attachment_id = $newParentId;
        $file1->field = 'preview_image';
        $file1->attachment_type = 'Lovata\Shopaholic\Models\Category';
        $file1->save();
      }


    }

    dd('success');

  }

  public function exportImagesProducts()
  {


    set_time_limit(0);

    $products = DB::select("select * from catalogItems WHERE public = '1'");


    function glob_tree_search($path, $pattern, $_base_path = null)
    {
      if (is_null($_base_path)) {
        $_base_path = '';
      } else {
        $_base_path .= basename($path) . '/';
      }

      $out = array();
      foreach (glob($path . '/' . $pattern, GLOB_BRACE) as $file) {
        $out[] = $_base_path . basename($file);
      }

      foreach (glob($path . '/*', GLOB_ONLYDIR) as $file) {
        $out = array_merge($out, glob_tree_search($file, $pattern, $_base_path));
      }

      return $out;
    }

    $arr = [];

    foreach ($products as $product) {


      $path = storage_path() . "/upload/catalog/items";

      $files = glob_tree_search($path, $product->photo);
      if (!empty($files) and $files[0] != 'items') {

        foreach ($files as $file) {

          if (strpos($file, 'big') === 0) {
            $arr[$product->id]['path'] = $file;
            break;
          }

        }

      }
    }


    $i = 0;
    foreach ($arr as $key => $elem) {


      $obProduct = DB::table('lovata_shopaholic_products')->where('id', $key)->first();


      if (!empty($obProduct)) {

        $newParentId = $obProduct->id;

        /*$idOldProd = $elem['id_prod'];

        $galleryImages = DB::select("select * from catalogItemsPhotos WHERE itemId = $idOldProd");

        foreach ($galleryImages as $galleryImage) {
          $path = storage_path() . "/1cExchange/catalog/item";

          if (!empty($galleryImage->path)) {

            $filesGallery = glob_tree_search($path, $galleryImage->path);

            if (!empty($filesGallery) and $filesGallery[0] != 'imagesProduct') {

              $file2 = new \System\Models\File;
              $file2->data = storage_path() . "/1cExchange/catalog/item/" . $filesGallery[0];
              $file2->is_public = true;
              $file2->attachment_id = $newParentId;
              $file2->field = 'images';
              $file2->attachment_type = 'Lovata\Shopaholic\Models\Product';
              $file2->save();

            }


          }

        }*/

        $file1 = new \System\Models\File;
        $file1->data = storage_path() . "/upload/catalog/items/" . $elem['path'];
        $file1->is_public = true;
        $file1->attachment_id = $newParentId;
        $file1->field = 'preview_image';
        $file1->attachment_type = 'Lovata\Shopaholic\Models\Product';
        $file1->save();
      }


      $i++;
    }


    dd('success');
  }

  public function createSetProperty()
  {
    $categories = DB::select('select * from lovata_shopaholic_categories');

    foreach ($categories as $category) {

      $propertySet = new PropertySet;
      $propertySet->name = $category->name;
      $propertySet->code = Str::slug($category->name);
      $propertySet->save();

      DB::update("INSERT INTO `lovata_properties_shopaholic_set_category_link`(`category_id`, `set_id`) VALUES ($category->id,$propertySet->id)");

    }

    dd('success');
  }

  public function exportRedirects()
  {



    $products = DB::select("select * from catalogItems WHERE public = '1'");


    foreach ($products as $product) {
      $obProductItem = ProductCollection::make([$product->id])->first();
      $fullUrl = $obProductItem->getPageUrl('catalog-routing');
      $fullUrl = parse_url($fullUrl, PHP_URL_PATH);

      Redirect::create([
        'from_url' => $product->url,
        'from_scheme' => 'auto',
        'to_url' => $fullUrl,
        'to_scheme' => 'auto',
        'match_type' => 'exact',
        'target_type' => 'path_or_url',
        'status_code' => '301',
        'is_enabled' => 1,
        'category_id' => 1,
      ]);

    }

    dd('success');
  }

}
