<?php

/***************************************************************************************
 ** Auth
 ***************************************************************************************/
Route::get('auth/user', 'Auth\AuthController@user')->name('user');
Route::post('auth/logout', 'Auth\AuthController@logout');
Route::post('auth/refresh', 'Auth\AuthController@refresh');

// Today
Route::get('today/patients', 'TodayPatientController@index');
Route::get('today/procedures', 'TodayProcedureController@index');

// Facilities
Route::get('facilities', 'FacilityController@index');
Route::get('facilities/{facility}', 'FacilityController@show');

// Patients
Route::get('patients', 'PatientController@index');
Route::post('patients', 'PatientController@store');
Route::put('patients/{patient}', 'PatientController@update');

// Scan / Pair
Route::post('procedures/{procedure}/band', 'ProcedureBandController@store');
Route::delete('procedures/{procedure}/band', 'ProcedureBandController@destroy');
Route::post('procedures/{procedure}/kit', 'ProcedureKitController@store');
Route::delete('procedures/{procedure}/kit', 'ProcedureKitController@destroy');

// Recordings
Route::post('procedures/{procedure}/recordings', 'RecordingController@store');

// Procedures
Route::get('procedures', 'ProcedureController@index');
Route::get('procedures/{procedure}', 'ProcedureController@show');
Route::post('procedures', 'ProcedureController@store');
Route::put('procedures/{procedure}', 'ProcedureController@update');

// Providers
Route::get('patients/{patient}/providers', 'PatientProviderController@index');

// Nogos
Route::get('nogos', 'NogoController@index');
Route::get('nogos/{nogo}', 'NogoController@show');
Route::post('nogos', 'NogoController@store');
Route::put('nogos/{nogo}', 'NogoController@update');

// Settings
Route::put('settings', 'SettingsController@update');
Route::put('settings/password', 'SettingsController@password');

// Support
Route::post('support', 'SupportController@store');


// Scripts
Route::get('scripts/decision', 'ScriptController@decision');
Route::get('scripts/timeout', 'ScriptController@timeout');
Route::get('scripts/signout', 'ScriptController@signout');
