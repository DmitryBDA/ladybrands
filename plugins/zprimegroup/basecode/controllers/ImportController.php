<?php namespace October\Demo\Controllers;

use Backend\Classes\Controller;
use App;
use Model;
use Flash;

use Vdomah\Excel\Classes\ImportProducts;
use Vdomah\Excel\Classes\ImportCategories;

use Vdomah\Excel\Classes\Excel;

class ImportController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function importProducts()
    {
        Excel::import(new ImportProducts,request()->file('myfile'));
        header('Location: https://mankor.ru/backend/lovata/shopaholic/products');
    }

    public function importCategories()
    {
        Excel::import(new ImportCategories,request()->file('myfile'));
        header('Location: https://mankor.ru/backend/lovata/shopaholic/categories');
    }

}