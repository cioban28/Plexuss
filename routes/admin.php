<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These are routes for COLLEGE ADMINS ONLY
| routes are loaded by the RouteServiceProvider within a group which
| contains the "auth.admin" middleware group. Now create something great!
|
*/

Route::get('/', 'AdminController@index');
Route::get('/reporting', 'AdminController@index');
Route::get('/profile', 'AdminController@index');
Route::get('/portals', 'AdminController@index');
Route::get('/users', 'AdminController@index');
Route::get('/setup', 'AdminController@index');
Route::get('/setup/step1', 'AdminController@index');
Route::get('/setup/step2', 'AdminController@index');
Route::get('/setup/step3', 'AdminController@index');
Route::get('/dashboard', 'AdminController@index');
Route::get('/messages/{user_id?}/{type?}', 'AdminController@index');
Route::get('/tools', 'AdminController@index');
Route::get('/tools/rankings', 'AdminController@index');
Route::get('/tools/logo', 'AdminController@index');
Route::get('/tools/rep', 'AdminController@index');
Route::get('/tools/international', 'AdminController@index');
Route::get('/tools/international/program', 'AdminController@index');
Route::get('/tools/international/header', 'AdminController@index');
Route::get('/tools/international/testimonials', 'AdminController@index');
Route::get('/tools/international/admission', 'AdminController@index');
Route::get('/tools/international/scholarship', 'AdminController@index');
Route::get('/tools/international/notes', 'AdminController@index');
Route::get('/tools/international/grades', 'AdminController@index');
Route::get('/tools/international/requirements', 'AdminController@index');
Route::get('/tools/international/majors', 'AdminController@index');
Route::get('/tools/international/alumni', 'AdminController@index');
Route::get('/tools/overview', 'AdminController@index');
Route::get('/tools/overview/header', 'AdminController@index');
Route::get('/tools/overview/content', 'AdminController@index');
Route::get('/tools/application', 'AdminController@index');
Route::get('/tools/application/program', 'AdminController@index');
Route::get('/tools/application/family', 'AdminController@index');
Route::get('/tools/application/awards', 'AdminController@index');
Route::get('/tools/application/clubs', 'AdminController@index');
Route::get('/tools/application/uploads', 'AdminController@index');
Route::get('/tools/application/courses', 'AdminController@index');
Route::get('/tools/application/essay', 'AdminController@index');
Route::get('/tools/application/additional', 'AdminController@index');
Route::get('/tools/application/custom', 'AdminController@index');
Route::get('/tools/application/mandatory', 'AdminController@index');
Route::get('/tools/cost', 'AdminController@index');
Route::get('/tools/cost/program', 'AdminController@index');
Route::get('/tools/cost/tuition', 'AdminController@index');
Route::get('/premium-plan-request', 'AdminController@index');

Route::post('/tools/application/saveCollegeApplicationAllowedSection', 'AjaxController@saveCollegeApplicationAllowedSection');
Route::get('/tools/application/getCollegeApplicaitonAllowedSection', 'AjaxController@getCollegeApplicaitonAllowedSection');

/*Route::get('/tools/scholarshipcms/list', 'AdminController@index');
Route::get('/tools/scholarshipcms/add', 'AdminController@index');
Route::get('/tools/scholarshipcms/edit/{schol_id}', 'AdminController@edit_schol');*/

Route::get('/tools/scholarshipcms/', 'AdminController@scholarshipcms');
Route::get('/tools/scholarshipcms/{scholarship_id}', 'AdminController@scholarshipcms');
Route::post('/tools/scholarshipcms/addScholarshipcms', 'AdminController@addScholarshipcms');
Route::post('/tools/scholarshipcms/delScholarshipAdmin', 'AdminController@delScholarshipAdmin');

Route::get('/tools/scholarshipcms/filter/{section}', 'AdminScholarshipsController@getAjaxFilterSections');
Route::post('/tools/scholarshipcms/ajax/setRecommendationFilter/{tab_name}', 'AjaxController@setAdminRecommendationFilter_scholarshipadmin');
Route::post('/tools/scholarshipcms/ajax/resetRecommendationFilter/{tab_name}', 'AjaxController@resetAdminRecommendationFilter_scholarshipadmin');
Route::get('/tools/scholarshipcms/ajax/getNumberOfUsersForFilter', 'AjaxController@getNumberOfUsersForFilter');


// College international tool tab get method
Route::post('/tools/international/save', 'AjaxController@internationalToolSave');
Route::get('/tools/getCollegeInternationalTab', 'AdminController@getCollegeInternationalTab');
Route::post('/tools/international/removeVideoTestimonial', 'AjaxController@removeVideoTestimonial');
Route::post('/tools/international/removeInternationalAlumni', 'AjaxController@removeInternationalAlumni');
Route::post('/tools/international/removeInternationalRequirment', 'AjaxController@removeInternationalRequirment');
Route::post('/tools/international/removeInternationalAttachment', 'AjaxController@removeInternationalAttachment');

Route::get('/tools/international/getInternationalMajorDegreeTab', 'AjaxController@getInternationalMajorDegreeTab');
Route::get('/tools/international/getPortalsForInternationalTab', 'AjaxController@getPortalsForInternationalTab');
Route::post('/tools/international/importMajorsFromTargetting', 'AjaxController@importMajorsFromTargetting');

// College international tuition and costs
Route::get('/tools/getInternatioanlTuitionCosts', 'AdminController@getInternatioanlTuitionCosts');
Route::post('/tools/internationalTuitionCostSave', 'AjaxController@internationalTuitionCostSave');

// Overview Content
Route::get('/tools/overview/getOverviewToolsTab', 'AdminController@getOverviewToolsTab');
Route::post('/tools/overview/uploadOverviewImages', 'AjaxController@uploadOverviewImages');
Route::post('/tools/overview/removeOverviewImageVideo', 'AjaxController@removeOverviewImageVideo');
Route::post('/tools/overview/uploadOverviewVideo', 'AjaxController@uploadOverviewVideo');
Route::post('/tools/overview/saveOverviewContent', 'AjaxController@saveOverviewContent');
Route::post('/tools/overview/saveApplicationLink', 'AjaxController@saveApplicationLink');
Route::get('/tools/overview/getApplicationLink', 'AjaxController@getApplicationLink');

Route::get('/inquiries', 'AdminController@inquirieIndex');
Route::get('/inquiries/pending', 'AdminController@pendingIndex');
Route::get('/inquiries/approved', 'AdminController@approvedIndex');
Route::get('/inquiries/removed', 'AdminController@removedIndex');
Route::get('/inquiries/rejected', 'AdminController@rejectedIndex');
Route::get('/inquiries/recommendations', 'AdminController@recommendationIndex');
Route::get('/inquiries/prescreened', 'AdminController@prescreenedIndex');
Route::get('/inquiries/verifiedHs', 'AdminController@verifiedHsIndex');
Route::get('/inquiries/verifiedApp', 'AdminController@verifiedAppIndex');
Route::get('/inquiries/converted', 'AdminController@convertedIndex');

// Add to converted 
Route::post('/inquiries/moveStudentToConverted', 'AdminController@moveStudentToConverted');
// Saving Verified Handshake and application
Route::post('/inquiries/verifiedHs/saveVerifiedHandShake', 'AdminController@saveVerifiedHandShake');
Route::post('/inquiries/verifiedApp/saveVerifiedApplication', 'AdminController@saveVerifiedApplication');

Route::post('/inquiries/verifiedHs/undoVerifiedHandShake', 'AdminController@undoVerifiedHandShake');
Route::post('/inquiries/verifiedApp/undoVerifiedApplication', 'AdminController@undoVerifiedApplication');

Route::post('/inquiries/savePlexussUserVerificationStatus', 'AdminController@savePlexussUserVerificationStatus');
Route::post('/inquiries/undoPlexussUserVerificationStatus', 'AdminController@undoPlexussUserVerificationStatus');

Route::post('/inquiries/prescreened/savePrescreenedUser', 'AdminController@savePrescreenedUser');

Route::post('/inquiries/loadProfileData', 'AdminController@loadProfileData');

Route::post('/inquiries/savePlexussUserInfo', 'AdminController@savePlexussUserInfo');

Route::post('/inquiries/addUserAppliedCollege', 'AdminController@addUserAppliedCollege');

Route::post('/inquiries/removeUserAppliedCollege', 'AdminController@removeUserAppliedCollege');

Route::post('/inquiries/updateUserApplicationState', 'AdminController@updateUserApplicationState');

// remove attachments
Route::post('/inquiries/removeTranscriptAttachment', 'AjaxController@removeTranscriptAttachment');

// College Application Acceptance Status routes
Route::post('/ajax/saveCollegeAcceptanceStatus', 'AjaxController@saveCollegeAcceptanceStatus');

//routes for Contact (from user profile contactPane)
Route::get('/inquiries/getMessages', 'ChatMessageController@contactMessageIndex');
Route::get('/inquiries/getMsgTemplates', 'ChatMessageController@getMsgTemplates');
Route::get('/inquiries/getCall', 'TwilioController@makeCall');

//route for FAQs
Route::get('/help', 'AdminController@index');

Route::post('/inquiries/setRecruit/{user_id}/{recruit_bool}/{is_remove?}', 'AjaxController@setRecruit');
Route::post('/inquiries/setRecruitForInquiriesOrConverted/{recruitment_id}/{recruit_bool}/{is_remove?}', 'AjaxController@setRecruitForInquiriesOrConverted');

Route::post('/inquiries/setRecommendationRecruit/{userid}/{recruit_bool}', 'AjaxController@setRecommendationRecruit');
Route::post('/inquiries/setRestore/{userid}/', 'AjaxController@setRestore');
Route::post('/inquiries/setRestoreToInquiries/{userid}/', 'AjaxController@setRestoreToInquiries');
Route::post('/inquiries/reasonsWhyRemovingStudent/{userid}', 'AjaxController@reasonsWhyRemovingStudent');

Route::post('/inquiries/setAddToH', 'AjaxController@setAddToH');

Route::post('/inquiries/setNote', 'AjaxController@setRecruitmentNote');
Route::post('/inquiries/saveNote', 'AjaxController@saveRecruitmentNote');
Route::post('/inquiries/updateSearchResults/{currentPage?}', 'AdminController@updateSearchResults');

Route::any('/inquiries/removePrescreenedUser/{rid}', 'AdminController@removePrescreenedUser');

Route::post('/inquiries/getMatchedCollegesForThisUser', 'AdminController@getMatchedCollegesForThisUser');

//Admin Advanced Filtering Routes
Route::get('/filter', 'AdminController@getFilterIndex');
//ajax filtering routes
Route::get('/filter/{section}', 'AdminController@getAjaxFilterSections');
//ajax textmsg expire reminder
Route::post('/ajax/textmsgRemindMeLater', 'AjaxController@textmsgRemindMeLater');

//CMS - Add Ranking for your school
Route::get('/content', 'AdminController@getContentManagement');
Route::get('/ajax/getSavedRankingPins', 'AjaxController@getSavedRankingPins');
Route::post('/ajax/saveRankingPin', 'AjaxController@saveRankingPin');
Route::post('/ajax/removeRankingPin', 'AjaxController@removeRankingPin');
Route::post('/ajax/saveLogo', 'AjaxController@saveLogo');
Route::get('/ajax/getSchoolData', 'AjaxController@getSchoolData');
Route::get('/ajax/getRepData', 'AjaxController@getRepData');
Route::post('/ajax/saveRepData', 'AjaxController@saveRepData');
Route::post('/ajax/saveRepPic', 'AjaxController@saveRepPic');

//Advanced Student Search
Route::get('/studentsearch', 'AdvancedStudentSearchController@index');
Route::post('/studentsearch/setRecruit', 'AdvancedStudentSearchController@setRecruit');
Route::post('/studentsearch/updateSearchResults', 'AdvancedStudentSearchController@update');
Route::post('/studentsearch/loadmore', 'AdvancedStudentSearchController@loadMore');
Route::post('/studentsearch/loadProfileData', 'AdvancedStudentSearchController@loadProfileData');
Route::post('/studentsearch/saveFilter', 'AdvancedStudentSearchController@saveFilter');
Route::post('/studentsearch/getAdvancedSearchFilter', 'AdvancedStudentSearchController@getAdvancedSearchFilter');
Route::post('/studentsearch/deleteFilter', 'AdvancedStudentSearchController@deleteFilter');
Route::post('/studentsearch/addStudentManual', 'AdvancedStudentSearchController@addStudentManual');
Route::post('/studentsearch/setNote', 'AjaxController@setAdvancedSearchNote');

Route::post('/studentsearch/searchForCollegesByKeyword', 'AdvancedStudentSearchController@searchForCollegesByKeyword');
Route::post('/studentsearch/getOrganizationPortalsForThisCollege','AdvancedStudentSearchController@getOrganizationPortalsForThisCollege');

// upload prescreen file
Route::post('/studentsearch/uploadPreScreenFile', 'AjaxController@uploadPreScreenFile');
//export approved students
Route::post('/exportApprovedStudentsFile/{admintype}', 'AjaxController@exportApprovedStudentsFile');

Route::get('/chat/', 'ChatMessageController@getChatPage');

//Messaging Ajax Routes.
Route::get('/ajax/messages/getUserMsg/{userid}', 'AdminController@messageIndex');
Route::get('/ajax/messages/getNewMsgs/{threadid}/{lastMsgId?}', 'Controller@getUserMessages');
Route::get('/ajax/messages/getUserMsgHistory/{userid}/{count}', 'AdminController@messageIndex');
Route::get('/ajax/messages/getInitialThreadList/{user_id?}/{type?}', 'CollegeMessageController@getInitialThreadList');

Route::get('/ajax/messages/getUserNewTopics/{userid?}/{type?}', 'CollegeMessageController@getThreadListHeartBeat');


Route::post('/ajax/messages/postMsg/{thread_id}/{userid}/{type}', 'CollegeMessageController@postMessage');
Route::get('/ajax/messages/setMsgRead/{threadid}', 'Controller@setMsgRead');

// Set college viewed student profile
Route::post('/ajax/setCollegeViewingYourProfile', 'AjaxController@setCollegeViewingYourProfile');

//filter ajax calls
Route::post('/ajax/setRecommendationFilter/{tab_name}', 'AjaxController@setAdminRecommendationFilter');
Route::post('/ajax/resetRecommendationFilter/{tab_name}', 'AjaxController@resetAdminRecommendationFilter');
Route::get('/ajax/getNumberOfUsersForFilter', 'AjaxController@getNumberOfUsersForFilter');

// Get student's info from ajax
Route::post('/ajax/getStudentsInfo', 'AjaxController@adminGetStudentInfo');

// Send email to request becoming a paid customer
Route::post('/ajax/requestToBecomeMember', 'AjaxController@requestToBecomeMember');


//ajax results for manage stundets
Route::get('/ajax/inquiries/{is_ajax?}', 'AdminController@inquirieIndex');
Route::get('/ajax/pending/{is_ajax?}', 'AdminController@pendingIndex');
Route::get('/ajax/approved/{is_ajax?}', 'AdminController@approvedIndex');
Route::get('/ajax/removed/{is_ajax?}', 'AdminController@removedIndex');
Route::get('/ajax/rejected/{is_ajax?}', 'AdminController@rejectedIndex');
Route::get('/ajax/recommendations/{is_ajax?}', 'AdminController@recommendationIndex');
Route::get('/ajax/prescreened/{is_ajax?}', 'AdminController@prescreenedIndex');
Route::get('/ajax/verifiedHs/{is_ajax?}', 'AdminController@verifiedHsIndex');
Route::get('/ajax/verifiedApp/{is_ajax?}', 'AdminController@verifiedAppIndex');
Route::get('/ajax/converted/{is_ajax?}', 'AdminController@convertedIndex');

//save applied student
Route::post('/ajax/applied', 'AjaxController@saveAppliedStudent');
Route::post('/ajax/enrolled', 'AjaxController@saveEnrolledStudent');
Route::post('/ajax/appliedReminder', 'AjaxController@appliedReminder');
Route::post('/ajax/appliedRemindMeLater', 'AjaxController@appliedRemindMeLater');

//Send an email/text to internal users about an urgent matter.
Route::post('/ajax/sendAdminUrgentMatterMsg', 'AjaxController@sendAdminUrgentMatterMsg');

//Dismiss an announcement so it dont show up on your dashboard no more
Route::post('/ajax/dismissPlexussAnnouncement', 'AjaxController@dismissPlexussAnnouncement');

// Prescreened Ajax calls
Route::post('/ajax/setInterviewStatus', 'AdminController@setInterviewStatus');
Route::post('/ajax/setAppliedEnrolledPreScreened', 'AdminController@setAppliedEnrolledPreScreened');

//group messaging
Route::get('/groupmsg', 'GroupMessagingController@index');
Route::get('/products', 'ProductsController@myIndex');
Route::post('/ajax/createNewCampaign', 'GroupMessagingController@createNewCampaign');
Route::post('/setGroupMsg', 'GroupMessagingController@setGroupMsg');
Route::post('/setGroupMsgForText', 'GroupMessagingController@setGroupMsgForText');
Route::post('/saveCampaign', 'GroupMessagingController@saveCampaign');
Route::post('/viewCampaign', 'GroupMessagingController@viewCampaign');
Route::post('/approvedStudentSearch', 'GroupMessagingController@approvedStudentSearch');
Route::post('/removeStudentFromList', 'GroupMessagingController@removeStudentFromList');
Route::post('/uploadAttachment/{type}', 'GroupMessagingController@uploadAttachment');
Route::post('/removeCampaign', 'GroupMessagingController@removeCampaign');
Route::post('/setAutomaticCampaign/{type}', 'GroupMessagingController@setAutomaticCampaign');


//text messaging
Route::group(array('prefix' => 'textmsg'), function(){
	Route::get('/', 'GroupMessagingController@textmsgIndex');
	Route::post('/textmsgSummary', 'GroupMessagingController@textmsgSummary');
	Route::post('/searchForPhoneNumbers', 'TwilioController@searchForPhoneNumbers');
	Route::post('/purchasePhone', 'TwilioController@purchasePhone');
	Route::get('/releasePurchasePhone', 'TwilioController@releasePurchasePhone');
	Route::post('/uploadCsv', 'GroupMessagingController@uploadCsv');
	Route::post('/getNumOfEligbleTextUsers', 'GroupMessagingController@getNumOfEligbleTextUsers');
	Route::post('/getTextmsgOrder', 'GroupMessagingController@getTextmsgOrder');
});


//goal setting
Route::post('/setgoals', 'AdminController@setGoals');
Route::post('/appointmentWasSet', 'AdminController@appointmentWasSet');

//Request to upgrade
Route::post('/upgradeRequest', 'MandrillAutomationController@adminUpgradeRequest');

//Set the portal for this user
Route::post('/ajax/setOrgnizationPortal', 'AjaxController@setOrgnizationPortal');
Route::post('/ajax/createEditPortal', 'AjaxController@createEditPortal');
Route::any('/ajax/addRemoveUsers', 'AjaxController@addRemoveUsers');
Route::post('/ajax/saveSettingInfo', 'AjaxController@saveSettingInfo');
Route::post('/ajax/addUserFromManageUser', 'AjaxController@addUserFromManageUser');
Route::post('/ajax/deleteUserFromOrganization', 'AjaxController@deleteUserFromOrganization');

Route::get('/ajax/logBackIn/{user_id}', 'AjaxController@logBackIn');
Route::get('/ajax/logBackInAdvancedSearch/{user_id}', 'AjaxController@logBackInAdvancedSearch');

// AOR Routes
Route::get('/manageCollege', 'AdminController@manageCollege');
Route::get('/aor/loginas/{user_id}', 'AdminController@loginas');

//Get users email automatically
Route::get( '/getUsersAutocomplete', 'AutoComplete@getUsersByEmail' );

Route::group(array('prefix' => 'distribution'), function(){
	Route::post('/generatePostUrl', 'DistributionController@generatePostUrl');
	Route::get('/isEligible/{user_id}', 'DistributionController@isEligible');
});

Route::get('/getPortals', 'AjaxController@getPortals');
Route::get('/getProfile', 'AjaxController@getProfile');
Route::post('/saveProfile', 'AjaxController@saveProfile');
Route::get('/setupCompleted', 'AjaxController@setupCompleted');
Route::get('/getDashboardData', 'AdminController@getDashboardData');
Route::get('/initDashboardStats/{block}', 'AdminController@initDashboardStats');
Route::get('/getUsers', 'AjaxController@getUsers');
Route::post('/updateUsersForManageUsers', 'AjaxController@updateUsersForManageUsers');

Route::post('/savePhoneWithUserId', 'AjaxController@savePhoneWithUserId');

Route::post('/ajax/hasAppliedToFilterAutoComplete', 'AdminController@hasAppliedToFilterAutoComplete');

Route::post('/getPreviousCalls', 'AdminController@getPreviousCalls');

Route::post('/ajax/getThisStudentThreads', 'AdminController@getThisStudentThreads');

Route::post('/postInterestedPremiumServices', 'AdminController@postInterestedPremiumServices');

// Reporting ajax call
Route::get('/ajax/getReport', 'AdminController@getReport');
Route::post('/ajax/saveCRMAutoReporting', 'AdminController@saveCRMAutoReporting');

Route::post('/ajax/getCallLogsWithTimeZone', 'AdminController@getCallLogsWithTimeZone');

// Get Transfer Organizations
Route::get('/inquiries/getTransferOrgs', 'AdminController@getTransferOrgs');

// Manually post student
Route::get('/inquiries/getManualPostingDistributionData', 'AdminController@getManualPostingDistributionData');
Route::post('/inquiries/manuallyPostStudent', 'AdminController@manuallyPostStudent');

Route::get('/inquiries/getInquiriesCount', 'AdminController@getInquiriesCount');
Route::get('/inquiries/getConvertedCount', 'AdminController@getConvertedCount');

Route::get('/inquiries/getProgramData/{col_id}', 'AdminController@getProgramData');