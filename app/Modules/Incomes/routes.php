<?php

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'incomes', 'namespace' => 'FI\Modules\Incomes\Controllers'], function () {
    Route::get('/', ['uses' => 'IncomeController@index', 'as' => 'incomes.index']);
    Route::get('create', ['uses' => 'IncomeCreateController@create', 'as' => 'incomes.create']);
    Route::post('create', ['uses' => 'IncomeCreateController@store', 'as' => 'incomes.store']);
    Route::get('{id}/edit', ['uses' => 'IncomeEditController@edit', 'as' => 'incomes.edit']);
    Route::post('{id}/edit', ['uses' => 'IncomeEditController@update', 'as' => 'incomes.update']);
    Route::get('{id}/delete', ['uses' => 'IncomeController@delete', 'as' => 'incomes.delete']);

    Route::group(['prefix' => 'bill'], function () {
        Route::post('create', ['uses' => 'IncomeBillController@create', 'as' => 'incomeBill.create']);
        Route::post('store', ['uses' => 'IncomeBillController@store', 'as' => 'incomeBill.store']);
    });

    Route::get('lookup/category', ['uses' => 'IncomeLookupController@lookupCategory', 'as' => 'incomes.lookupCategory']);
    Route::get('lookup/vendor', ['uses' => 'IncomeLookupController@lookupVendor', 'as' => 'incomes.lookupVendor']);

    Route::post('bulk/delete', ['uses' => 'IncomeController@bulkDelete', 'as' => 'incomes.bulk.delete']);
});