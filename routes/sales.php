<?php

Route::get('/sales', 'SalesController@index');
Route::get('/sales/pixelTrackingTest', 'SalesController@getPixelTrackingIndex');
Route::get('/sales/agency-reporting', 'SalesController@getAgencyReporting');
Route::get('/sales/clients', 'SalesController@getClientReporting');
Route::get('/sales/messages/{org_branch_id?}/{user_id?}/', 'SalesController@getMessages');
Route::post('/sales/setPlexussNote', 'SalesController@setPlexussNote');
Route::get('/sales/getThreadMsgs/{thread_id?}', 'SalesController@getThreadMsgs');
Route::get('/sales/billing', 'SalesController@getBilling');
Route::get('/sales/pickACollege', 'SalesController@getPickACollege');
Route::get('/sales/scholarships', 'SalesController@getSalesScholarships');
Route::get('/sales/loginas/{user_id}', 'SalesController@loginas');
Route::post('sales/setTrigger', 'SalesController@setTrigger');
Route::post('sales/setComparison', 'SalesController@setComparison');
Route::get('forgetSalesCache', 'SalesController@forgetSalesCache');

// export routes
Route::get('/showExportCache', 'SalesController@showExportCache');
Route::get('/forgetExportCache', 'SalesController@forgetExportCache');

// pick a college routes
Route::get('/getPrioritySchools', 'SalesController@getPrioritySchools');
Route::get('/getContractTypes', 'SalesController@getContractTypes');
Route::post('/saveDataToPrioritySchools', 'SalesController@saveDataToPrioritySchools');
Route::post('/saveEditsToPrioritySchools', 'SalesController@saveEditsToPrioritySchools');
Route::post('/saveGoalDates', 'SalesController@saveGoalDates');
Route::get('/getDateForPickACollege', 'SalesController@getDateForPickACollege');
Route::get('/sales/application-order', 'SalesController@getApplicationOrder');
Route::get('/getApplicationCollege', 'SalesController@getApplicationCollege');

// publisher routes
Route::get('/publisher', 'PublisherController@index');
Route::post('/publisher/post/{db?}', 'PublisherController@postArticle');
Route::get('/ajax/getAuthors', 'PublisherController@getAuthors');
Route::get('/ajax/getAllArticles', 'PublisherController@getAllArticles');
Route::post('/ajax/autoSaveArticle/{id?}', 'PublisherController@autoSaveArticle');
Route::post('/publisher/postEvent/{db?}','PublisherController@postEvent');
Route::post('/publisher/postImage/{db?}','PublisherController@postImage');
Route::post('/publisher/removeEvent', 'PublisherController@removeEvent');
Route::get('/ajax/getAllEvents', 'PublisherController@getAllEvents');
Route::post('/publisher/updateEvent/{db?}', 'PublisherController@updateEvent');

// Agency get dashboard data
Route::post('/ajax/getAgencyReportingData', 'SalesController@getAgencyReportingData');
Route::get('/sales/activateAgency/{agency_profile_info_id}', 'AgencyController@activateAgency');
Route::post('/sales/agency/setPlexussNote', 'SalesController@setAgencyPlexussNote');

//scholarship routes
Route::get('/ajax/getAllScholarships', 'ScholarshipsController@getAllScholarships');
Route::post('/ajax/addScholarshipSales', 'ScholarshipsController@addScholarshipSales');
Route::post('/ajax/editScholarshipSales', 'ScholarshipsController@editScholarshipSales');
Route::post('/ajax/deleteScholarshipSales', 'ScholarshipsController@deleteScholarshipSales');
Route::get('/ajax/searchScholarships/', 'ScholarshipsController@searchScholarships');
Route::get('/ajax/getAllProviders', 'ScholarshipsController@getAllProviders');

Route::post('/ajax/getScholarshipFilter', 'SalesController@getScholarshipFilter');
Route::post('/ajax/addScholarshipCms', 'ScholarshipsController@addScholarshipCms');
Route::post('/ajax/getScholarshipFilter', 'SalesController@getScholarshipFilter');


// Pixel tracking testing routes
Route::post('/sales/ajax/removePixelTestAdClicks', 'SalesController@removePixelTestAdClicks');
Route::post('/sales/ajax/checkPixelTracked', 'SalesController@checkPixelTracked');
Route::get('/sales/ajax/getPixelTrackedTestingLogs', 'SalesController@getPixelTrackedTestingLogs');

//Reporting Email Templates
Route::get('/sales/email-reporting','AdminController@emailReporting');
Route::get('/sales/emailTemplate/{start}/{end}','AdminController@emailTemplateAjax');
Route::get('/sales/templateHtml/{templateId}','AdminController@templateHtml');
Route::get('/sales/sendMail/{template}/{emailList}','AdminController@sendMail');

Route::any('/sales/category_modify/{category?}/{ajax?}', 'AdminController@modifyEmailTemplateCategory');


// User Reporting
Route::get('/sales/getUserStats', 'SalesController@getUserStats');
Route::get('/sales/getUserInviteStats', 'SalesController@getUserInviteStats');
// Route::get('/sales', 'SalesController@tracking');
Route::get('/sales/tracking', 'SalesController@tracking');
Route::get('/sales/site-performance', 'SalesController@site_performance');

Route::get('/sales/social-newsfeed', 'SalesController@socialNewsfeedAllPosts');
Route::get('/sales/social-newsfeed/new', 'SalesController@socialNewsfeedAllPosts');
Route::get('/sales/social-newsfeed/edit', 'SalesController@socialNewsfeedAllPosts');
Route::get('/sales/social-newsfeed/plexuss-only', 'SalesController@socialNewsfeedPlexussOnlyPosts');
Route::get('/sales/getStats', 'SalesController@getStats' );
Route::get('/sales/exportUserStats/{start}/{end}/{user}', 'SalesController@exportUserStats' );

//  NewsFeed backend post methods
Route::post('/sales/saveNewsfeedPost', 'SalesController@saveNewsfeedPost');
Route::post('/sales/editNewsfeedPost', 'SalesController@editNewsfeedPost');
Route::post('/sales/duplicateNewsfeedPost', 'SalesController@duplicateNewsfeedPost');
Route::get('/sales/getPosts', 'SalesController@getPosts');

Route::post('/sales/ajax/setRecommendationFilter/{tab_name}', 'SalesController@setAdminRecommendationFilter');
Route::post('/sales/ajax/resetRecommendationFilter/{tab_name}', 'AjaxController@resetAdminRecommendationFilter');

Route::get('/sales/device-os-reporting', 'SalesController@device_os_reporting');
Route::get('/sales/overview-tracking', 'SalesController@student_tracking');
Route::get('/sales/getStats', 'SalesController@getStats' );
Route::get('/sales/exportUserStats/{start}/{end}/{user}', 'SalesController@exportUserStats' );

// Site perfomance
Route::get('/sales/getSitePerfomanceReport', 		 'TrackingPageController@getSitePerfomanceReport');
Route::get('/sales/getSitePerfomanceReportDetailed', 'TrackingPageController@getSitePerfomanceReportDetailed');
Route::get('/sales/getSitePerfomanceByPlatform', 	 'TrackingPageController@getSitePerfomanceByPlatform');
Route::get('/sales/getSitePerfomanceByBrowser', 	 'TrackingPageController@getSitePerfomanceByBrowser');
Route::get('/sales/getSitePerfomanceByDevice', 		 'TrackingPageController@getSitePerfomanceByDevice');
Route::get('/sales/getSitePerfomanceReportByFilter', 'TrackingPageController@getSitePerfomanceReportByFilter');

// Overview Report
Route::get('/sales/getOverviewReport', 'TrackingPageController@getOverviewReport');

