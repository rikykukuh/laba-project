<?php

use App\Http\Controllers\LogController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::impersonate();

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/', 'App\Http\Controllers\HomeController@index');
    Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');
    Route::get('/config', 'App\Http\Controllers\ConfigController@index')->name('config');
    Route::put('/config/update/{id}', 'App\Http\Controllers\ConfigController@update')->name('config.update');
    Route::post('/config/store/permission_group', 'App\Http\Controllers\ConfigController@storePermissionGroup')->name('config.store.permission_group');
    Route::put('/config/update/permission_group/{id}', 'App\Http\Controllers\ConfigController@updatePermissionGroup')->name('config.update.permission_group');
    Route::post('/config/store/permission', 'App\Http\Controllers\ConfigController@storePermission')->name('config.store.permission');
    Route::put('/config/update/permission/{id}', 'App\Http\Controllers\ConfigController@updatePermission')->name('config.update.permission');
    Route::get('/order-logs', [LogController::class, 'showOrderLogs'])->name('order.logs');

	Route::get('/order/merchant-by-payment', '\App\Http\Controllers\Order\OrderController@getPaymentMerchants')->name('order.merchant_by_payment');
	Route::get('/order-products/merchant-by-payment', '\App\Http\Controllers\OrderProduct\OrderProductController@getPaymentMerchants')->name('order-products.merchant_by_payment');
});

Route::group(['prefix'=>'laporan'], function(){
    Route::get('/penjualan', '\App\Http\Controllers\OrderProduct\OrderProductController@index')->name('laporan.penjualan');
    Route::get('/reparasi', '\App\Http\Controllers\Order\OrderController@index')->name('laporan.reparasi');
});

Route::group(['namespace' => 'App\Http\Controllers\Profile', 'middleware' => 'auth'], function (){
	Route::get('/profile', 'ProfileController@index')->name('profile');
	Route::put('/profile/update/profile/{id}', 'ProfileController@updateProfile')->name('profile.update.profile');
	Route::put('/profile/update/password/{id}', 'ProfileController@updatePassword')->name('profile.update.password');
	Route::put('/profile/update/avatar/{id}', 'ProfileController@updateAvatar')->name('profile.update.avatar');
});

Route::group(['namespace' => 'App\Http\Controllers\Error', 'middleware' => 'auth'], function (){
	Route::get('/unauthorized', 'ErrorController@unauthorized')->name('unauthorized');
});

Route::group(['namespace' => 'App\Http\Controllers\User', 'middleware' => 'auth'], function (){
	//Users
	Route::get('/user', 'UserController@index')->name('user');
	Route::get('/user/create', 'UserController@create')->name('user.create');
	Route::post('/user/store', 'UserController@store')->name('user.store');
	Route::get('/user/edit/{id}', 'UserController@edit')->name('user.edit');
	Route::put('/user/update/{id}', 'UserController@update')->name('user.update');
	Route::get('/user/edit/password/{id}', 'UserController@editPassword')->name('user.edit.password');
	Route::put('/user/update/password/{id}', 'UserController@updatePassword')->name('user.update.password');
	Route::get('/user/show/{id}', 'UserController@show')->name('user.show');
	Route::get('/user/destroy/{id}', 'UserController@destroy')->name('user.destroy');
	// Roles
	Route::get('/role', 'RoleController@index')->name('role');
	Route::get('/role/create', 'RoleController@create')->name('role.create');
	Route::post('/role/store', 'RoleController@store')->name('role.store');
	Route::get('/role/edit/{id}', 'RoleController@edit')->name('role.edit');
	Route::put('/role/update/{id}', 'RoleController@update')->name('role.update');
	Route::get('/role/show/{id}', 'RoleController@show')->name('role.show');
	Route::get('/role/destroy/{id}', 'RoleController@destroy')->name('role.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('order-products', \App\Http\Controllers\OrderProduct\OrderProductController::class);
    Route::get('/order-products/print/{id}', 'App\Http\Controllers\OrderProduct\OrderProductController@orderPrint')->name('order-products.print');
    Route::resource('orders', \App\Http\Controllers\Order\OrderController::class);
    Route::get('/orders/print/{id}', 'App\Http\Controllers\Order\OrderController@orderPrint')->name('orders.print');
    Route::put('/orders/status/{id}', 'App\Http\Controllers\Order\OrderController@setStatus')->name('orders.status');
    Route::delete('/orders/item/photo/', 'App\Http\Controllers\Order\OrderController@destroyItemPhoto')->name('orders.item-photo');
    Route::resource('customers', \App\Http\Controllers\Customer\CustomerController::class);
    Route::resource('cities', \App\Http\Controllers\City\CityController::class);
    Route::resource('products', \App\Http\Controllers\Product\ProductController::class);
    Route::resource('payments', \App\Http\Controllers\Payment\PaymentController::class);
    Route::resource('payment-methods', \App\Http\Controllers\PaymentMethod\PaymentMethodController::class);
    Route::resource('payment-merchants', \App\Http\Controllers\PaymentMerchant\PaymentMerchantController::class);
    Route::resource('sites', \App\Http\Controllers\Site\SiteController::class);
    Route::get('/search-customers', 'App\Http\Controllers\Customer\CustomerController@searchCustomers')->name('customer.search');
    Route::get('/search-products', 'App\Http\Controllers\Product\ProductController@searchProducts')->name('products.search');
});


// Route::group(['namespace' => 'App\Http\Controllers\Order'], function(){
// 	//orders
// 	Route::get('/orders', 'OrderController@index')->name('orders.index');
// 	Route::get('/orders/create', 'OrderController@create')->name('orders.create');
// 	Route::post('/orders/store', 'OrderController@store')->name('orders.store');
// 	Route::get('/orders/edit/{id}', 'OrderController@edit')->name('orders.edit');
// 	Route::put('/orders/update/{id}', 'OrderController@d')->name('orders.update');
// 	Route::get('/orders/show/{id}', 'OrderController@show')->name('orders.show');
// 	Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
// });
