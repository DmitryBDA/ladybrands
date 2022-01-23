<?php

Route::get('export-categories', 'Zprimegroup\Basecode\Controllers\ExportController@exportCategories');
Route::get('export-imageCategories', 'Zprimegroup\Basecode\Controllers\ExportController@exportImagesCategories');
Route::get('export-Brands', 'Zprimegroup\Basecode\Controllers\ExportController@exportBrands');
Route::get('export-imageBrands', 'Zprimegroup\Basecode\Controllers\ExportController@exportImagesBrands');
Route::get('export-imageProducts', 'Zprimegroup\Basecode\Controllers\ExportController@exportImagesProducts');
Route::get('export-products', 'Zprimegroup\Basecode\Controllers\ExportController@exportProducts');
Route::get('export-redirects', 'Zprimegroup\Basecode\Controllers\ExportController@exportRedirects');
