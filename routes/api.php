<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Authenticate routes
Route::prefix('jupiter')->group(function () {
	Route::post('/', 'API\TabletController@connection')->name('connection');
    Route::post('/login', 'API\TabletController@login')->name('login');
    Route::post('/register_work_location', 'API\TabletController@registerWorkLocation')->name('register_work_location');
    Route::post('/choose_work_address', 'API\TabletController@chooseWorkAddress')->name('choose_work_address');
    Route::post('/check', 'API\TabletController@check')->name('check');
    Route::post('/time_stamping', 'API\TabletController@timeStamping')->name('time_stamping');
    Route::post('/time_table', 'API\TabletController@timeTable')->name('time_table');
    Route::post('/offline_data', 'API\TabletController@offlineData')->name('offline_data');
    Route::post('/check_card', 'API\TabletController@checkCard')->name('check_card');
    Route::post('/get_employee_name', 'API\TabletController@getEmployeeName')->name('get_employee_name');
    Route::post('/register_card', 'API\TabletController@registerCard')->name('register_card');
    Route::get('/download_installer', 'API\TabletController@downloadInstaller')->name('download_installer');
});
