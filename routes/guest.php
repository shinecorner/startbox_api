<?php

Route::get('/', function() {
    return 'ok';
});

Route::post('auth/login', 'Auth\AuthController@login');
Route::get('auth/email-is-unique', 'Auth\AuthController@emailIsUnique');
Route::get('auth/password-is-valid', 'Auth\AuthController@passwordIsValid');
Route::get('auth/password/validate-token', 'Auth\ResetPasswordController@validateToken');
Route::post('auth/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::post('auth/password/reset', 'Auth\ResetPasswordController@reset');
