<?php

Auth::routes();

Route::group(['middleware'=>['auth']],function(){
    Route::get('/', 'HomeController@index')->name('home');
});

Route::prefix('/admin')->group(function(){

    Route::get('/','AdminController@index')->name('admin');
    Route::post('/logout','AuthAdmin\AdminLoginController@logout')->name('admin.logout');
    Route::get('/login','AuthAdmin\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login/attempt','AuthAdmin\AdminLoginController@login')->name('admin.login.attempt');
    Route::get('/forgot-password','AuthAdmin\PasswordResetController@index')->name('admin.forgot-password');
    Route::post('/send-reset-link','AuthAdmin\PasswordResetController@sendResetEmail')->name('admin.forgot-password.reset-link');
    Route::get('/reset/pasword/verify/{email}/{token}','AuthAdmin\PasswordResetController@verify')->name('admin.reset.password.verify');
    Route::post('/reset/pasword/update','AuthAdmin\PasswordResetController@updatePassword')->name('admin.reset.password.update');

    Route::group(['middleware'=>['auth:admin']],function(){
        Route::get('/change-password','AdminController@changePassword')->name('admin.profile.change');
    });
});
