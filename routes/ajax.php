<?php

// Notification routes
Route::group(['middleware' => ['user']], function () {
    Route::get( 'notifications/setRead', 'AjaxController@setReadNotification' );
	Route::get('/getAllNotificationsJSON', 'AjaxController@getAllNotificationsJSON');
});

Route::get('homepage/getCityByState/{state}', 'Controller@getCityByState');
Route::get('homepage/getCarouselItems/{carouselName}', 'HomepageController@getCarouselItems');
Route::get('homepage/getSection/{section}', 'HomepageController@getSection');
Route::get('homepage/getMembersInOurNetwork/{skip}/{limit}', 'HomepageController@getMembersInOurNetwork');
Route::get('homepage/getBackground', 'HomepageController@getBackground');
Route::post('homepage/getCityByStateFilt', 'Controller@getCityByStateFilt');

Route::get('/homepage/getGetStartedThreeCollegesPins', 'GetStartedController@getGetStartedThreeCollegesPins');
Route::post('/homepage/saveGetStartedThreeCollegesPins', 'GetStartedController@saveGetStartedThreeCollegesPins');

Route::post('messaging/postMsg', 'Controller@postMessage');
Route::get ('messaging/getHistoryMsg/{threadid}/{lastMsgId?}/{firstMsgId?}', 'Controller@getUserMessages');

Route::get('righthandsidecarousel', 'AjaxController@getRightHandsidePin');
Route::post('populateNearYouCarousel', 'AjaxController@getRightHandsidePin');

Route::post('setLikesTally', 'AjaxController@setLikesTally');

Route::post('carepackagenotifyme', 'AjaxController@setCarePackageNotifyMe');

Route::get('getMajorByDepartment/{name}', 'AjaxController@getMajorByDepartment');
Route::get('getMajorByDepartmentWithIds/{id}', 'AjaxController@getMajorByDepartmentWithIds');
Route::get('getMajorByDepartmentWithNamesAndIds/{id}', 'AjaxController@getMajorByDepartmentWithNamesAndIds');
Route::post('getMajorByDepartmentWithNames', 'AjaxController@getMajorByDepartmentWithNames');
Route::any('getAllMajorByDepartment', 'AjaxController@getAllMajorByDepartment');

Route::get('getAllDepts', 'AjaxController@getAllDepts');
Route::get('getAllTargetDates', 'AjaxController@getAllDates');
Route::get('getAllMilitaries', 'AjaxController@getAllMilitaries');
Route::get('getAllMajors', 'AjaxController@getAllMajors');
Route::get('getAllEthnicities', 'AjaxController@getAllEthnicities');
Route::get('getAllReligionsCustom', 'AjaxController@getAllReligionsCustom');
Route::get('getStudentData/{user_id?}', 'AjaxController@getStudentData');
Route::get('getStudentProfile/{user_id?}', 'AjaxController@getStudentProfile');
Route::get('getProfileData/{user_id?}', 'AjaxController@getProfileData');
Route::get('getGPAGradingScales/{country_id?}', 'AjaxController@getGPAGradingScales');

// Public Profile
Route::get('getLikedColleges/{user_id?}', 'ProfilePageController@getLikedColleges');
Route::get('getProjectsAndPublications/{user_id?}', 'ProfilePageController@getProjectsAndPublications');
Route::get('getProfileClaimToFame/{user_id?}', 'ProfilePageController@getProfileClaimToFame');
Route::get('getSkillsAndEndorsements/{user_id?}', 'ProfilePageController@getSkillsAndEndorsements');
Route::get('getEducation', 'ProfilePageController@getEducation');
Route::post('/removeLikedCollege', 'ProfilePageController@removeLikedCollege');
Route::post('/insertPublicProfilePublication', 'ProfilePageController@insertPublicProfilePublication');
Route::post('/removePublicProfilePublication', 'ProfilePageController@removePublicProfilePublication');
Route::post('/searchCollegesWithLogos', 'ProfilePageController@searchCollegesWithLogos');

Route::get('convertToUnitedStatesGPA/{gch_id?}/{old_value?}/{conversion_type?}', 'AjaxController@convertToUnitedStatesGPA');

//personal info
Route::get('profile/personalInfo/{token}', 'AjaxController@getPersonalInfo');
Route::post('profile/personalInfo/{token}', 'AjaxController@postPersonalInfo');
Route::post('profile/personalInfoPhoto/{token?}', 'AjaxController@personalInfoPhoto');
Route::post( 'profile/personalInfoPhotoRemove/{token}', 'AjaxController@personalInfoPhotoRemove' );
Route::get('profile/suppress-progress-alert', 'AjaxController@suppressProgressAlert');
Route::post('profile/saveFinancialInfo', 'AjaxController@saveFinancialInfo');
Route::post('updateStudentProfile', 'AjaxController@updateStudentProfile');
Route::post('/profile/saveMeTab', 'AjaxController@saveMeTab');
Route::post('profile/saveEducation', 'ProfilePageController@saveEducation');
Route::post('/profile/saveClaimToFameSection', 'ProfilePageController@saveClaimToFameSection');
Route::post('/profile/saveSkillsAndEndorsements', 'ProfilePageController@saveSkillsAndEndorsements');
Route::post('/profile/saveLikedCollegesSection', 'ProfilePageController@saveLikedCollegesSection');
Route::post('/profile/uploadProfilePicture', 'ProfilePageController@uploadProfilePicture');

//objective
Route::get('profile/objective/{token}', 'AjaxController@getObjective');
Route::post('profile/objective/{token}', 'AjaxController@postObjective');


//Scores
Route::get('profile/scores/{token}', 'AjaxController@getScores');
Route::post('profile/scores/{token}', 'AjaxController@postScores');

//Upload Center
Route::get('profile/uploadcenter/{token}', 'AjaxController@getUploadCenter');
Route::post('profile/uploadcenter/{token}', 'AjaxController@postUploadCenter');
Route::get('/profile/getUserTranscript', 'AjaxController@getUserTranscript');

//Financial Info
Route::get('profile/financialinfo/{token}', 'AjaxController@getFinancialInfo');
Route::post('profile/financialinfo/{token}', 'AjaxController@postFinancialInfo');

//High School Info
Route::get('profile/highschoolInfo/{token}', 'AjaxController@getHighSchoolInfo');
Route::post('profile/highschoolInfo/{token}', 'AjaxController@postHighSchoolInfo');
Route::post('profile/highschoolInfoTranscript/{token}', 'AjaxController@postHighSchoolTranscriptInfo');

Route::get('profile/DropDownData/', 'AjaxController@getDropDownData');
Route::get('profile/DropDownDataCol/{token}', 'AjaxController@getDropDownDataCol');

//College Info
Route::get('profile/collegeInfo/{token}', 'AjaxController@getCollegeInfo');
Route::post('profile/collegeInfo/{token}', 'AjaxController@postCollegeInfo');

Route::get('/searchCollegeWithBackgroundImage', 'AjaxController@searchCollegeWithBackgroundImage');

//Experience Info
Route::get('profile/experience/{token}', 'AjaxController@getExperienceInfo');
Route::post('profile/experience/{token}', 'AjaxController@postExperienceInfo');
Route::post('profile/removeExperience/{token}', 'AjaxController@removeExperienceInfo');

//Club Orgs Info
Route::get('profile/clubOrgs/{token}', 'AjaxController@getClubsOrgsInfo');
Route::post('profile/clubOrgs/{token}', 'AjaxController@postClubsOrgsInfo');
Route::post('profile/removeClubOrgInfo/{token}', 'AjaxController@removeClubOrgInfo');

//Honor and Awards Info
Route::get('profile/honorsAwards/{token}', 'AjaxController@getHonorAndAwardsInfo');
Route::post('profile/honorsAwards/{token}', 'AjaxController@postHonorAndAwardsInfo');
Route::post('profile/removeHonorsAwards/{token}', 'AjaxController@removeHonorAndAwardsInfo');

//Skills Info
Route::get('profile/skills/{token}', 'AjaxController@getSkillsInfo');
Route::post('profile/skills/{token}', 'AjaxController@postSkillsInfo');

//Languages Info
Route::get('profile/languages/{token}', 'AjaxController@getLanguagesInfo');
Route::post('profile/languages/{token}', 'AjaxController@postLanguagesInfo');

//Interest Info
Route::get('profile/interests/{token}', 'AjaxController@getInterestInfo');
Route::post('profile/interests/{token}', 'AjaxController@postInterestInfo');

//Certification Info
Route::get('profile/certifications/{token}', 'AjaxController@getCertificationInfo');
Route::post('profile/certifications/{token}', 'AjaxController@postCertificationInfo');
Route::post('profile/removeCertificationInfo/{token}', 'AjaxController@removeCertificationInfo');

//Patents Info
Route::get('profile/patents/{token}', 'AjaxController@getPatentsInfo');
Route::post('profile/patents/{token}', 'AjaxController@postPatentsInfo');
Route::post('profile/removePatentsInfo/{token}', 'AjaxController@removePatentsInfo');

//Publications Info
Route::get('profile/publications/{token}', 'AjaxController@getPublicationsInfo');
Route::post('profile/publications/{token}', 'AjaxController@postPublicationsInfo');
Route::post('profile/removePublicationsInfo/{token}', 'AjaxController@removePublicationsInfo');

//Checklist Info
Route::get('profile/checklist/{token}', 'AjaxController@getCheckListsInfo');
Route::post('profile/checklist/{token}', 'AjaxController@postCheckListsInfo');

//Timeline Info
Route::get('profile/timeline/{token}', 'AjaxController@getTimelineInfo');
Route::post('profile/timeline/{token}', 'AjaxController@postTimelineInfo');

//Digital Profile Info
Route::get('profile/digitalprofile/{token}', 'AjaxController@getDigitalprofileInfo');
Route::post('profile/digitalprofile/{token}', 'AjaxController@postDigitalprofileInfo');

Route::post('search/topnavsearch/{token}', 'AjaxController@getTopnavSearch');

// Message Template Info
Route::get('getMessageTemplatesList', 'AjaxController@getMessageTemplatesList');
Route::post('saveMessageTemplates', 'AjaxController@saveMessageTemplates');
Route::post('loadMessageTemplates', 'AjaxController@loadMessageTemplates');
Route::post('deleteMessageTemplates', 'AjaxController@deleteMessageTemplates');

// Org Saved Attachments
Route::get('getOrgSavedAttachmentsList', 'AjaxController@getOrgSavedAttachmentsList');
Route::post('saveOrgSavedAttachments', 'AjaxController@saveOrgSavedAttachments');
Route::post('loadOrgSavedAttachments', 'AjaxController@loadOrgSavedAttachments');
Route::post('deleteOrgSavedAttachments', 'AjaxController@deleteOrgSavedAttachments');

/*============== College Pages routes ==================*/
Route::get('/college/overview/', 'CollegeController@getOverviewInfo');
Route::get('/college/stats/', 'CollegeController@getStatsInfo');
Route::get('/college/ranking/', 'CollegeController@getRankingInfo');
Route::get('/college/value/', 'CollegeController@getValueInfo');
Route::get('/college/admissions/', 'CollegeController@getAdmissionsInfo');
Route::get('/college/notables/', 'CollegeController@getNotablesInfo');
Route::get('/college/tuition/', 'CollegeController@getTuitionInfo');
Route::get('/college/financial-aid/', 'CollegeController@getFinancialInfo');
Route::get('/college/campus/', 'CollegeController@getCampusInfo');
Route::get('/college/athletics/', 'CollegeController@getAthleticsInfo');
Route::get('/college/enrollment/', 'CollegeController@getEnrollmentInfo');
Route::get('/college/programs/', 'CollegeController@getProgramsInfo');
Route::get('/college/chat/', 'CollegeController@getChatInfo');
Route::get('/college/news/', 'CollegeController@getNewsInfo');
Route::get('/college/undergrad/', 'CollegeController@getUnderGradInfo');
Route::get('/college/grad/', 'CollegeController@getGradInfo');
Route::get('/college/epp/', 'CollegeController@getPathwayInfo');
Route::get('/college/current-students/', 'CollegeController@getCurrentStudent');
Route::get('/college/alumni/', 'CollegeController@getAlumni');

Route::any('/college/chat/init', 'ChatMessageController@init');
Route::get('/college/chat/islive/{schoolid}', 'ChatMessageController@isLive');
Route::get('/college/chat/showcache', 'Controller@showCache');
Route::get('/college/chat/showCacheThread/{user_id}', 'Controller@showCacheThread');

Route::get('/college/chat/threadHeartBeat/{schoolId}/{type}', 'ChatMessageController@getThreadListHeartBeat');
Route::get('/college/chat/getNewMsgs/{threadid}/{lastMsgId?}', 'Controller@getUserMessages');
Route::post('/college/chat/postMsg/{thread_id}/{userid}/{type}', 'ChatMessageController@postMessage');


Route::group(['middleware' => 'auth.user'], function(){

	Route::get('profile/getnotifications', 'AjaxController@getNotifications');

	// portal routes
	Route::get('/portal/portal', 'PortalController@getManageSchool');
	Route::get('/portal/recommendationlist', 'PortalController@getUsrRecommendationList');
	Route::get('/portal/getTrashSchoolList', 'PortalController@getUsrPortalTrash');
	Route::get('/portal/collegesrecruityou', 'PortalController@getCollegesRecruitYou');
	Route::get('/portal/collegesviewedprofile', 'PortalController@getCollegesViewedYourProfile');
	Route::post('/portal/didJoyride', 'PortalController@didJoyride');
	Route::get('/portal/getPortalData', 'PortalController@getPortalData');
	Route::get('/portal/applications', 'PortalController@getApplicationData');
	Route::get('/portal/applications/data', 'NewPortalController@applicationData');
	// Route::get('/portal/favcolleges/data', 'NewPortalController@applicationData');
	Route::get('/portal/favcolleges/data', 'NewPortalController@getManageSchool');
	Route::get('/portal/reccolleges/data', 'NewPortalController@Recommendations');
	Route::get('/portal/recruitcolleges/data', 'NewPortalController@getRecruitment');
	Route::get('/portal/viewedcolleges/data', 'NewPortalController@getCollegesViewedYourProfile');
	Route::get('/portal/trash/data', 'NewPortalController@getUsrPortalTrash');
	Route::get('/portal/scholarships', 'PortalController@getScholarships');
	Route::post('/portal/dont_show_modal/{userId}', 'PortalController@dontShowModal');

	// Get Top Nav Notification
	Route::post('/getTopNavNotification', 'AjaxController@getTopNavNotification');
    Route::post('/updateTopNavNotification', 'AjaxController@updateTopNavNotification');
    Route::post('/deleteUserEmailSupressionList', 'AjaxController@deleteUserEmailSupressionList');


    //recruit me ajax calls for modal
	Route::get('/recruiteme/portalcollegeinfo/{schoolId}', 'AjaxController@getPortalCollegeInfo');
	Route::get('/recruiteme/portalcollegeinfo/data/{schoolId}', 'NewPortalController@getPortalCollegeInfo');
	Route::get('/recruite-me/portalcollegeinfo/{schoolId}', 'NewPortalController@getPortalCollegeInfo');
	Route::post('/recruiteme/restore', 'AjaxController@restoreSchool');
	Route::post('/recruiteme/restore/data', 'NewPortalController@restoreSchool');
	Route::post('/recruiteme/adduserschooltotrash', 'AjaxController@adduserschooltotrash');
	Route::post('/recruiteme/adduserschooltotrash/data', 'NewPortalController@adduserschooltotrash');
	Route::get('/recruiteme/{schoolId}', 'AjaxController@getUserRecruitMe');

	Route::get('/json/recruiteme/{schoolId}', 'AjaxController@getUserRecruitMeJson');
	Route::post('/json/recruiteme/{schoolId}', 'AjaxController@saveUserRecruitMeJson');
	Route::post('/json/multiplerecruiteme', 'AjaxController@saveUserRecruitMeJsonMultipleColleges');

	Route::get('/recruitmepls/{schoolId}', 'AjaxController@recruitMePls');
	Route::post('/recruiteme/{schoolId}', 'AjaxController@saveUserRecruitMe');
	Route::post('/recruitmeinfo', 'AjaxController@saveUserContactInfo');
	Route::post('/portal/trashScholarships', 'PortalController@trashScholarships');
	Route::post('/portal/trashScholarships/data', 'NewPortalController@trashScholarships');

	Route::post('/saveCollegeApplication', 'AjaxController@saveCollegeApplication');

	Route::post('/findSchools', 'AjaxController@findSchools');


	//scholarship get
    Route::post('/queueScholarship', 'ScholarshipsController@queueScholarship');
	Route::get('/getAllScholarships', 'ScholarshipsController@getAllScholarships');

	Route::get('/getScholarshipsCms', 'ScholarshipsController@getScholarshipsCms');
	Route::get('/getAllScholarshipsCms', 'ScholarshipsController@getAllScholarshipsCms');
	Route::get('/deleteScholarshipCms', 'ScholarshipsController@deleteScholarshipCms');


	Route::get('/getUserSubmitScholarships', 'ScholarshipsController@getUserSubmitScholarships');

	//get list of majors
	Route::post('profile/objective/searchFor/{name}','GetStartedController@searchFor');

	// user specific routes
	Route::post('/searchForCollegesForThisUser','AjaxController@searchForCollegesForThisUser');

});

// Clear show first time homepage modal
Route::get('/ajax/clearShowFirstTimeHomepageModal', 'AjaxController@clearShowFirstTimeHomepageModal');

//Modal Forms schoolInfo and interests on homepage and schoolInfo profile.
Route::post('modalForm/schoolInfo/{token}', 'AjaxController@modalFormSchoolInfoPost');
Route::post('modalForm/interests/{token}', 'AjaxController@modalFormCollegeInterestsPost');
Route::post('modalForm/schoolInfoSkip/{token}', 'AjaxController@modalFormSkipSchoolInfo');
Route::get('modalForm/firstTimeHomepageModal_1/{ajaxtoken}', function($ajaxtoken){
	return View('private.homepage.modals.firstTimeHomepageModal_1', array('ajaxtoken' => $ajaxtoken));
});

Route::get('modalForm/firstTimeHomepageModal_2/{ajaxtoken}', function($ajaxtoken){
	return View('private.homepage.modals.firstTimeHomepageModal_2', array('ajaxtoken' => $ajaxtoken));
});

// WHAT'S NEXT AJAX
Route::get('whatsNext/', 'AjaxController@whatsNext');
Route::post('whatsNext/', 'AjaxController@postWhatsNext');
Route::post('getUserZip/', 'AjaxController@getUserZip');

//Quiz ajax calls
Route::post('/quiz', 'AjaxController@submitQuizResult');

Route::get('/getAllCountries', 'AjaxController@getAllCountries');
Route::get('/getAllLanguages', 'AjaxController@getAllLanguages');
Route::get('/getAllReligions', 'AjaxController@getAllReligions');
Route::get('/getCountriesWithNameId', 'AjaxController@getCountriesWithNameId');
Route::get('/getAllStates', 'AjaxController@getAllStates');
Route::post('/getGradeConversions', 'AjaxController@getGradeConversions');
Route::get('/getAttendedSchools', 'AjaxController@getAttendedSchools');
Route::post('/saveUploadedFiles', 'AjaxController@saveUploadedFiles');
Route::post('/findAllSchools', 'AjaxController@findSchoolsForCollegeAndHS');
Route::post('/searchForHighSchools', 'AjaxController@searchForHighSchools');
Route::post('/searchForColleges', 'AjaxController@searchForColleges');
Route::post('/searchForCollegesForSales', 'AjaxController@searchForCollegesForSales');
Route::post('/searchForMajors', 'AjaxController@searchForMajors');
Route::post('/searchForProfessions', 'AjaxController@searchForProfessions');

Route::get('/bingImage', 'Controller@generateRandBingImage');
Route::get('/getBingBackground/{query}', 'Controller@getBingBackground');
// for user feedback modal
Route::post('/saveAppliedToSchools', 'AjaxController@saveAppliedToSchools');

Route::get('getCoursesSubjects', 'AjaxController@getCoursesSubjects');
Route::get('getClassesBasedOnSubjects/{subject_id}', 'AjaxController@getClassesBasedOnSubjects');
Route::post('removeCourse', 'AjaxController@removeCourse');

Route::get('getViewDataController/{user_id}', 'AjaxController@getViewDataController');

Route::post('/saveSignupFacebookShare', 'AjaxController@saveSignupFacebookShare');


///// routes for search /////
Route::get('/getMajorsFromCat/', 'SearchController@getMajorFiltersFromCatAjax');
Route::get('/getAllMajorsFromCat/', 'SearchController@getAllMajorsFromCatAjax');

Route::get('/getCollegesWithDept/', 'SearchController@getCollegesWithDept');

Route::post('/getCollegesWithMajorsFilters', 'SearchController@getCollegesWithMajorsFilters');
Route::any('/getScholarshipTargeting', 'SalesController@getScholarshipTargeting');
Route::any('/getscholarshippopup', 'SalesController@getScholarshipPopup');
Route::get('/getAllScholarshipsNotApplied', 'ScholarshipsController@getAllScholarshipsNotApplied');
Route::get('/getAllScholarshipsNotSubmitted', 'ScholarshipsController@getAllScholarshipsNotSubmitted');

/* onboarding */
Route::post('/checkCompany', 'AjaxController@checkCompany');

Route::post('/plexussCookieAgree', 'AjaxController@plexussCookieAgree');
Route::post('/closeSignupOffer', 'AjaxController@closeSignupOffer');

// Begin of Social route
Route::get('/oneapp/getDataFor/applicationAndMycolleges', 'SocialController@getApplicationAndMycollegesData');
// End of Social route
