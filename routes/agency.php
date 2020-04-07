<?php
// routes/admin.php

Route::get('/', 'AgencyController@index');
Route::get('/filter', 'AgencyController@getFilterIndex');
Route::get('/reporting', 'AgencyController@reportingIndex');
Route::get('/video-tutorial', 'AgencyController@videoTutorialIndex');

Route::post('/inquiries/setRecruit/{userid}/{recruit_bool}/{is_remove?}', 'AgencyController@setRecruit');

// Deprecated routes, pending deletion.
	// Route::get('/inquiries', 'AgencyController@inquiriesIndex');
	// Route::get('/recommendations', 'AgencyController@recommendationIndex');
	// Route::get('/pending', 'AgencyController@pendingIndex');
	// Route::get('/approved', 'AgencyController@approvedIndex');
	// Route::get('/rejected', 'AgencyController@rejectedIndex');

Route::post('/ajax/setRecommendationFilter/{tab_name}', 'AjaxController@setAdminRecommendationFilter');
Route::post('/ajax/resetRecommendationFilter/{tab_name}', 'AjaxController@resetAdminRecommendationFilter');
Route::get('/ajax/getNumberOfUsersForFilter', 'AjaxController@getNumberOfUsersForFilter');

// Send urgent message
Route::post('/ajax/sendAdminUrgentMatterMsg', 'AjaxController@sendAgencyUrgentMatterMsg');

Route::get('/inquiries/', 'AgencyController@leadsIndex'); // Leads is default
Route::get('/inquiries/leads/{ajax?}', 'AgencyController@leadsIndex');

Route::post('/inquiries/updateSearchResults/{currentPage?}', 'AgencyController@updateSearchResults');

Route::get('/inquiries/opportunities/{ajax?}', 'AgencyController@opportunitiesIndex');
Route::get('/inquiries/applications/{ajax?}', 'AgencyController@applicationsIndex');
Route::get('/inquiries/removed/{ajax?}', 'AgencyController@removedIndex');

Route::post('/inquiries/removeTranscriptAttachment', 'AjaxController@removeTranscriptAttachment');
Route::post('/inquiries/getMatchedCollegesForThisUser', 'AgencyController@getMatchedCollegesForThisUser');

// Agency Messaging Routes
// Route::get('/messages/{userid?}/{type?}', 'AgencyController@getUsrTopics');
Route::get('/messages/{userid?}/{type?}', 'AgencyController@messagingIndex');
Route::get('/ajax/messages/getInitialThreadList/{user_id?}/{type?}', 'CollegeMessageController@getInitialThreadList');
Route::get('/ajax/messages/getUserNewTopics/{userid?}/{type?}', 'CollegeMessageController@getThreadListHeartBeat');
Route::get('/ajax/messages/getNewMsgs/{threadid}/{lastMsgId?}', 'Controller@getUserMessages');
Route::get('/ajax/messages/setMsgRead/{threadid}', 'Controller@setMsgRead');

Route::post('/inquiries/changeStudentAgencyBucket', 'AgencyController@changeStudentAgencyBucket');
Route::post('/inquiries/undoStudentAgencyBucketChange', 'AgencyController@undoStudentAgencyBucketChange');

Route::post('/setRecommendationRecruit/{userid}/{recruit_bool}', 'AgencyController@setRecommendationRecruit');
Route::post('/setNote', 'AgencyController@setRecruitmentNote');
//ajax filtering routes
Route::get('/filter/{section}', 'AgencyController@getAjaxFilterSections');
Route::get('/settings/{load?}', 'AgencyController@getAgencySettingsIndex');
Route::get('/ajax/getSettingsSection/{tab}', 'AgencyController@getSettingsSection');
Route::post('/ajax/saveProfileInfo', 'AgencyController@saveProfileInfo');
Route::post('/ajax/removeAgentsSpecializedSchool/{collegeId}', 'AgencyController@removeAgentsSpecializedSchool');
Route::post('/ajax/removeCustomAgentService', 'AgencyController@removeCustomAgentService');
Route::get('/ajax/messages/getUserNewTopics/{userid?}/{type?}', 'AgencyController@getThreadListHeartBeat');
Route::get('/ajax/messages/getNewMsgs/{threadid}/{lastMsgId?}', 'Controller@getUserMessages');
Route::get('/ajax/messages/setMsgRead/{threadid}', 'Controller@setMsgRead');
Route::get('/ajax/messages/getInitialThreadList/{user_id?}/{type?}', 'CollegeMessageController@getInitialThreadListAgency');

Route::post('/ajax/notFirstTimeAgentAnymore', 'AgencyController@notFirstTimeAgentAnymore');

Route::get('/ajax/getDashboardReportingOne/{is_overall?}', 'AgencyController@getDashboardReportingOne');
Route::get('/ajax/getDashboardReportingTwo/{number_of_months?}', 'AgencyController@getDashboardReportingTwo');
Route::get('/ajax/getDashboardBoxesNumbers', 'AgencyController@getDashboardBoxesNumbers');

//export students route
Route::post('/exportApprovedStudentsFile/{admintype}', 'AjaxController@exportApprovedStudentsFile');

Route::post('/ajax/setAgencyViewingYourProfile', 'AjaxController@setAgencyViewingYourProfile');

//Advanced Student Search - Disabled until further notice
// Route::get('/studentsearch', 'AdvancedStudentSearchController@index');
// Route::post('/studentsearch/setRecruit', 'AdvancedStudentSearchController@setRecruit');
// Route::post('/studentsearch/updateSearchResults', 'AdvancedStudentSearchController@update');
// Route::post('/studentsearch/loadmore', 'AdvancedStudentSearchController@loadMore');
// Route::post('/studentsearch/loadProfileData', 'AdvancedStudentSearchController@loadProfileData');

//ajax results for manage stundets
// Deprecated routes, pending deletion.
	// Route::get('/ajax/inquiries/{is_ajax?}', 'AgencyController@inquirieIndex');
	// Route::get('/ajax/pending/{is_ajax?}', 'AgencyController@pendingIndex');
	// Route::get('/ajax/approved/{is_ajax?}', 'AgencyController@approvedIndex');
	// Route::get('/ajax/rejected/{is_ajax?}', 'AgencyController@rejectedIndex');
	// Route::get('/ajax/recommendations/{is_ajax?}', 'AgencyController@recommendationIndex');
Route::get('/ajax/removed/{is_ajax?}', 'AgencyController@removedIndex');


//load profile data for agencies
Route::post('/inquiries/loadProfileData', 'AgencyController@loadProfileData');

Route::post('/inquiries/setRestore/{userid}/', 'AgencyController@setRestore');

Route::post('/inquiries/savePlexussUserInfo', 'AgencyController@savePlexussUserInfo');

//save applied student
Route::post('/ajax/applied', 'AjaxController@saveAppliedStudent');
Route::post('/ajax/appliedReminder', 'AjaxController@appliedReminder');
Route::post('/ajax/appliedRemindMeLater', 'AjaxController@appliedRemindMeLater');

//group messaging
Route::get('/groupmsg', 'GroupMessagingController@index');
Route::post('/setGroupMsg', 'GroupMessagingController@setGroupMsg');
Route::post('/saveCampaign', 'GroupMessagingController@saveCampaign');

Route::get('/agencyApproval/{token}', 'AjaxController@agencyApproval');
Route::get('/agencyUserInquiry/{token}', 'AjaxController@agencyUserInquiry');

Route::get('/generateAgencyLeads', 'AgencyController@generateAgencyLeads');
Route::get('/getProfile', 'AjaxController@getAgencyProfile');