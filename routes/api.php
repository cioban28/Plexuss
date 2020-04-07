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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// College
Route::get('/getDiscoverColleges/{offset?}/{user_id?}', 'ApiController@getDiscoverColleges');
Route::get('/getQuadArticles/{user_id?}/{offset?}', 'ApiController@getQuadArticles');
Route::get('/getSingleCollegeInfo/{section}/{college_id}/{user_id?}', 'ApiController@getSingleCollegeInfo');
Route::get('/loadMoreItems/{category}/{skip}/{user_id?}', 'ApiController@loadMoreItems');

// Auth
Route::post('/login', 'ApiController@login');
Route::post('/loginWithFb', 'ApiController@loginWithFb');
Route::post('/setup', 'ApiController@setup');
Route::post('/signup', 'ApiController@signup');
Route::get('/getProfile/{user_id?}', 'ApiController@getProfile');
Route::post('/auth/password/reset', 'PasswordController@resetAuthenticated');
Route::get('/getStartedCollegeRecommendations', 'ApiController@getStartedCollegeRecommendations');
Route::post('/saveGetStartedCollegeRecommendations', 'ApiController@saveGetStartedCollegeRecommendations');

// Colleges Tab routes (mobile)
Route::get('/myTrash/{user_id?}', 'ApiController@myTrash');
Route::get('/myApplications/{user_id?}', 'ApiController@myApplications');
Route::get('/collegesViewedMe/{user_id?}', 'ApiController@collegesViewedMe');
Route::get('/collegesSeekingMe/{user_id?}', 'ApiController@collegesSeekingMe');
Route::get('/myRecommendations/{user_id?}', 'ApiController@myRecommendations');
Route::get('/myFavoriteColleges/{user_id?}', 'ApiController@myFavoriteColleges');

// College Hub
Route::get('/getFindCollegesInitialData', 'ApiController@getFindCollegesInitialData');

// College Majors
Route::get('/college-majors/{slug?}/{major?}', 'ApiController@getMajorAndDepartmentData');

// Portal Routes
Route::post('/addToTrash', 'ApiController@addToTrash');
Route::post('/addToFavorites', 'ApiController@addToFavorites');

// All majors
Route::get('/allMajors', 'ApiController@allMajors');

//Routes for scholarships
Route::get('/scholarships', 'ApiController@scholarshipIndex');

// Routes for Ranking
Route::get('/ranking/categories', 'ApiController@rankingCategories');
Route::get('/ranking/listing', 'ApiController@rankingListing');

// messages
Route::get('/myMessageThreads/{user_id}/{org_branch_id?}/{loadMore?}', 'ApiController@myMessageThreads');
Route::get('/threadConvo/{user_id}/{thread_id}/{org_id?}/{latest_msg_id?}/{first_msg_id?}', 'ApiController@threadConvo');
Route::post('/sendMessage', 'ApiController@sendMessage');
Route::get('/messageRead/{user_id}/{thread_id}', 'ApiController@messageRead');
Route::get('/addtlPrevConvo/{thread_id}', 'ApiController@addtlPrevConvo');
Route::get('/addtlMessageThreads/{user_id}/{user_type?}', 'ApiController@addtlMessageThreads');

// oneapp
Route::post('/saveApplication', 'ApiController@saveApplication');
Route::get('/prioritySchools/{user_id}','ApiController@prioritySchools');

// utility routes
Route::get('/getLocationData', 'ApiController@getLocationData');
Route::get('/getNrccuaSchools/{user_id}', 'ApiController@getNrccuaSchools');
Route::get('/allCountries','ApiController@allCountries');
Route::get('/allReligions','ApiController@allReligions');
Route::get('/attendedSchools/{user_id}','ApiController@attendedSchools');
Route::get('/searchSchools/{user_id}/{search_val?}','ApiController@searchSchools');
Route::get('/allLanguages','ApiController@allLanguages');
Route::get('/courseSubjects','ApiController@courseSubjects');
Route::get('/subjectClasses/{subject_id}','ApiController@subjectClasses');
Route::get('/gradingScales/{country_id}','ApiController@gradingScales');
Route::get('/convertToUnitedStatesGPA/{grade_scale_id}/{gpa_applicant_value}/{conversion_type}','ApiController@convertToUnitedStatesGPA');
Route::get('/allStates','ApiController@allStates');
Route::get('/findSchool/{edu_level}/{school}','ApiController@findSchool');
Route::post('/saveContactsList', 'ApiController@saveContactsList');
Route::post('/validatePhone', 'ApiController@validatePhone');
Route::post('/savePhone', 'ApiController@savePhone');
Route::post('/checkCode', 'ApiController@checkCode');
Route::post('/sendCode','ApiController@sendCode');

Route::post('/saveUpload','ApiController@saveUpload');
Route::post('/deleteUpload','ApiController@deleteUpload');

// Search for colleges and news.
Route::get('/searchForCollegesApplyToColleges/{user_id}/{term}', 'ApiController@searchForCollegesApplyToColleges');
Route::get('/searchForCollegesDiscoverColleges/{user_id}/{term}', 'ApiController@searchForCollegesDiscoverColleges');
Route::get('/searchForTheQuad/{user_id}/{term}', 'ApiController@searchForTheQuad');
Route::post('/getPremiumArticles', 'ApiController@getPremiumArticles');


// News page
Route::get('/news/{pageNumber?}','ApiController@newsIndex');
Route::get('/news/article/{articleName?}/{articleType?}','ApiController@viewNewsArticle');
Route::get('/news/catalog/{categoryName?}/{pageNumber?}','ApiController@newsIndex');
Route::get('/news/subcategory/{categoryName?}/{pageNumber?}','ApiController@newsIndex');

// Search for colleges based on different critera's
Route::get('/search/{is_api}', 'ApiController@searchForColleges');
Route::get('/mainSearchMethod', 'ApiController@mainSearchMethod');

// Mobile Tokens
Route::post('/saveMobileDeviceToken', 'ApiController@saveMobileDeviceToken');
Route::get('/getMobileDeviceTokenForThisUser', 'ApiController@getMobileDeviceTokenForThisUser');

// Users
Route::post('/uploadProfilePic', 'ApiController@uploadProfilePic');

// Setting
Route::post('/updateUsersPushNotification', 'ApiController@updateUsersPushNotification');
Route::post('/deleteAccount', 'ApiController@deleteAccount');

Route::post('/sendReferralInvitesByQueueMobile', 'InviteController@sendReferralInvitesByQueueMobile');

// Search API
Route::get('/detailedSearchForColleges/{take}/{offset}', 'SearchController@searchForApi');
Route::post('/getMajorsFromDepartmentCat', 'SearchController@getMajorFiltersFromCatAjax');

// GET Public Profile API
Route::post('/getProfileClaimToFame', 'ApiController@getProfileClaimToFame');
Route::post('/getSkillsAndEndorsements', 'ApiController@getSkillsAndEndorsements');
Route::post('/getProjectsAndPublications', 'ApiController@getProjectsAndPublications');
Route::post('/getLikedColleges', 'ApiController@getLikedColleges');

Route::post('/profile/searchFor/{name}','GetStartedController@searchFor');
Route::post('/getStudentData', 'ApiController@getStudentData');

// SAVE Public Profile API
Route::post('/generalSaveMeTab', 'ApiController@generalSaveMeTab');
Route::post('/saveProfileClaimToFameSection', 'ApiController@saveProfileClaimToFameSection');
Route::post('/saveProfileSkillsSection', 'ApiController@saveProfileSkillsSection');
Route::post('/saveProfileLikedCollegesSection', 'ApiController@saveProfileLikedCollegesSection');

Route::post('/insertPublicProfilePublication', 'ApiController@insertPublicProfilePublication');
Route::post('/removePublicProfilePublication', 'ApiController@removePublicProfilePublication');

Route::post('/searchCollegesWithLogos', 'ApiController@searchCollegesWithLogos');

Route::post('/getAllScholarshipsNotSubmitted', 'ApiController@getAllScholarshipsNotSubmitted');

Route::post('/getUserSelectedScholarships', 'ApiController@getUserSelectedScholarships');

Route::post('/getUserScholarshipsStatus', 'ApiController@getUserScholarshipsStatus');

// Premium
Route::post('/setUserPremium', 'ApiController@setUserPremium');
