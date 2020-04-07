<?php

/*
|--------------------------------------------------------------------------
| Scholarship Admin Routes
|--------------------------------------------------------------------------
|
| These are routes for SCHOLARSHIP ADMINS ONLY
| routes are loaded by the RouteServiceProvider within a group which
| contains the "auth.admin" middleware group. Now create something great!
|
*/
Route::get('/', 'AdminScholarshipsController@index');
Route::get('addscholarship_page', 'AdminScholarshipsController@index');

Route::get('/filter/{section}', 'AdminScholarshipsController@getAjaxFilterSections');
Route::get('/scholarships', 'AdminScholarshipsController@scholarships');

Route::post('/ajax/setRecommendationFilter/{tab_name}', 'AjaxController@setAdminRecommendationFilter_scholarshipadmin');
Route::post('/ajax/resetRecommendationFilter/{tab_name}', 'AjaxController@resetAdminRecommendationFilter_scholarshipadmin');
Route::get('/ajax/getNumberOfUsersForFilter', 'AjaxController@getNumberOfUsersForFilter');

Route::get('/{edit}/{scholarship_id}', 'AdminScholarshipsController@index');
Route::get('/{add}', 'AdminScholarshipsController@index');
