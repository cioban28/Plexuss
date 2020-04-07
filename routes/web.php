<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/', 'HomepageController@index');

// Route::any('/premium-india-front-page', 'HomepageController@indianIndex');
Route::any('/general', 'HomepageController@generalIndex');
Route::any('/india', 'HomepageController@indianIndex');


Route::get('/chat', 'HomepageController@enableChat');

Route::get('/carousel', 'HomepageController@getTopRankedSchools');
Route::get('/getCarouslesData/{section?}', 'HomepageController@getCarouslesData');

//needed for a google campain link
Route::get('/welcome', 'HomeController@index');

//For modal on home page
Route::post('/home/dont_show_modal', 'HomeController@modal');

// Route::get('/premium-plans-info', 'HomeController@premiumIndex');

Route::get('/premium-plans-info/{country?}', 'HomeController@premiumPage');
// Route::get('/premium-general', 'HomeController@premiumGeneral');

// Checkout India Premium
Route::get('/congratulations-page', 'checkoutController@congratulationsPage');
Route::get('/confirmation-page', 'checkoutController@confirmationPage');

// B2B pages
Route::get('/solutions/', 'B2BController@index');  //first load
Route::get('/solutions/{subpage}', 'B2BController@partialIndex');  //switching tabs
Route::get('/b2b/blog/all', 'B2BController@getAllArticles');  //get all blog articles (on load)
Route::get('/b2b/blog/get-articles', 'B2BController@getArticles'); //get articles based on sub-category (press and new features tabs)
Route::get('/b2b/blog/getmore', 'B2BController@getMoreArticles');  //get more articles infinite scroll
Route::get('/b2b/blog/getmoreNewFeatures','B2BController@getMoreNewFeatures');
Route::get('/solutions/news/articles/{slug?}', 'B2BController@getBlogView');
Route::get('/b2b/blog/searchBlog', 'B2BController@searchBlog'); //searches for blogs given search term
Route::get('/b2b/blog/newFeatures', 'B2BController@newFeatures'); //gets new features section on blog
Route::get('/b2b/blog/pressReleases', 'B2BController@pressReleases'); // gets press releases section on blog
Route::get('/b2b-info/resources/plexuss-pixel', 'B2BController@plexussPixel');//plexuss pixel
Route::get('/b2b-info/resources/onboarding', 'B2BController@plexussOnboarding');//plexuss onboarding
Route::post('/postOnboardingSignup', 'B2BController@postOnboardingSignup');//plexuss onboarding
Route::post('/postOnboardingApplication', 'B2BController@postOnboardingApplication');//plexuss onboarding
Route::post('/postAdRedirectCampaign', 'B2BController@postAdRedirectCampaign');//plexuss onboarding
Route::post('/savePlexussPixelInfo', 'AjaxController@savePlexussPixelInfo');//save plexuss pixel information
Route::post('/saveAudienceInfo', 'AjaxController@saveAudienceInfo');//save plexuss pixel information
Route::post('/saveStudentJourney', 'AjaxController@saveStudentJourney');//save plexuss pixel information
Route::post('/saveClientJourney', 'AjaxController@saveClientJourney');//save plexuss pixel information


Route::get('/onboarding', 'B2BController@plexussOnboarding');//plexuss onboarding


// Ajax for closing Getting Started Help Pins on the homepage
Route::get('/home/close-getting-started-pin', 'HomeAjaxController@closeGettingStartedPin');

// Infinite Scroll Load More News
Route::post('/home/load-more-news', 'HomeAjaxController@loadMoreNews');

// news routes
// Route::get('/news/article/{articleName?}/{articleType?}','NewsController@view');
Route::get('/news/essay/{articleName?}/{articleType?}','NewsController@view');
Route::get('/news/catalog/{categoryName?}/{pageNumber?}','NewsController@index');
// Route::get('/news/subcategory/{categoryName?}/{pageNumber?}','NewsController@index');
// Route::get('/news/{pageNumber?}','NewsController@index');

// Routes for the College Page
Route::get('/college', 'HomepageController@signedOutIndex');
Route::get('/college-majors/{slug}', 'HomepageController@signedOutIndex');
Route::get('/college-majors/{slug}/{major}', 'HomepageController@signedOutIndex');
Route::get('/college-majors', 'HomepageController@signedOutIndex');
Route::get('/college/{slug}/{type?}', 'HomepageController@signedOutIndex');
Route::get('/scholarships', 'HomepageController@signedOutIndex');
Route::get('/ranking/categories', 'HomepageController@signedOutIndex');
Route::get('/ranking/list/categories', 'RankingController@categories');
Route::get('/ranking', 'HomepageController@signedOutIndex');
Route::get('/college-essays', 'HomepageController@signedOutIndex');
Route::get('/college-essays/{slug?}', 'HomepageController@signedOutIndex');
Route::get('/international-resources', 'HomepageController@signedOutIndex');
Route::get('/news', 'HomepageController@signedOutIndex');
Route::get('/news/subcategory/{name?}', 'HomepageController@signedOutIndex');
Route::get('/news/article/{slug?}', 'HomepageController@signedOutIndex');
Route::get('/ncsa', 'HomepageController@signedOutIndex');
Route::get('/comparison', 'HomepageController@signedOutIndex');
Route::get('/college-fair-events', 'HomepageController@signedOutIndex');
Route::get('/download-app', 'HomepageController@signedOutIndex');
Route::get('/college-search', 'HomepageController@signedOutIndex');
Route::get( '/terms-of-service', 'HomepageController@signedOutIndex');
Route::get( '/privacy-policy', 'HomepageController@signedOutIndex');
// Route::get('/college', 'CollegeController@index');
// Route::get('/college-majors/{slug?}/{major?}', 'CollegeController@department');
Route::post('/social/comparison/', 'NewBattleController@comparison');

// College Majors
Route::get('/college/state/{slug}', 'SearchController@byState');
// Route::get('/college/{slug}/{type?}', 'CollegeController@view');
Route::get('/social-college/{slug}/{type?}', 'CollegeController@view');
// Route::get('/social-college/{slug}/{type?}', 'CollegeController@getSocialCollege');
Route::get('/majors', 'CollegeController@getMajorsView');

// international-student routes
Route::get('/international-students', 'CollegeController@getInternationalStudentsView');
// Route::get('/international-resources', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/main', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/application-checklist', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/finding-schools', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/aid', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/prep', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/working-in-us', 'CollegeController@getInternationalResourcesView');
Route::get('/international-resources/student-visa', 'CollegeController@getInternationalResourcesView');
Route::get('/international-students/getInternationalStudentsAjax', 'CollegeController@getInternationalStudentsAjax');
Route::get('/resources', 'CollegeController@getInternationalStudentsResourcesView');
Route::get('/college-application/getInternationalStudentsAjax', 'AjaxController@getInternationalStudentsAjax');

// view student application - only can be done by admins/plexuss, so maybe put in admin routes
Route::get('/view-student-application/', 'CollegeController@viewStudentApplication');
Route::get('/view-student-application/{user_id}', 'CollegeController@viewStudentApplication');

//Routes for scholarships
// Route::get('/scholarships', 'ScholarshipsController@index');
Route::get('/scholarships-new','ScholarshipsController@schlorships');
Route::get('/social/scholarships/getScholarship', 'NewScholarshipsController@index');



// Routes for the Ranking Page
Route::get('/ranking-lists', 'NewRankingController@index');
Route::get('/ranking/listing', 'RankingController@listing');
Route::get('/ranking/getschools', 'RankingController@getschools');

// Routes for the Search Page
Route::get('/search/', 'SearchController@index');
Route::post('/search/', 'SearchController@index');
Route::get('/getSelectBoxVal/', 'AutoComplete@getSelectBoxVal');

/* Routes related to their respective Boxes */
Route::post('/infoboxdoughnut','InfoBoxController@GetDoughnutBox');
Route::post('/infoavgboxdoughnut','InfoBoxController@GetAvgDoughnutBox');
Route::post('/infoboxgraphscores','InfoBoxController@GetGraphScoresBox');
Route::post('/infoboxtotalranking','InfoBoxController@GetTotalRankingBoxes');
Route::post('/infoboxtotalvalue','InfoBoxController@GetTotalValueBoxes');
Route::post('/infoboxgradrate','InfoBoxController@GetGraduationRateBoxes');
Route::post('/infoboxcomparison','InfoBoxController@GetComparisonBoxes');
Route::post('/infoboxlearnskills','InfoBoxController@GetLearningSkillsBoxes');
Route::post('/infoboxsalarybox','InfoBoxController@GetPopularSalaryBoxes');
Route::post('/infoboxconsideradmissionbox','InfoBoxController@GetConsideredAdmissionBoxes');
Route::post('/infoappinfobox','InfoBoxController@GetAppInfoBox');
Route::post('/infonotablesbox','InfoBoxController@GetNotablesBox');
Route::post('/infotuitionbox','InfoBoxController@GetTuitionBox');
Route::post('/infocalculatorbox','InfoBoxController@GetCalculatorBox');
Route::post('/infoloanratebox','InfoBoxController@GetLoanRateBox');
Route::post('/infocampusdinebox','InfoBoxController@GetCampusDineBox');
Route::post('/infobiglistbox','InfoBoxController@GetBigListBox');
Route::post('/infoweatherbox','InfoBoxController@GetWeatherBox');
Route::post('/infocollegesportsbox','InfoBoxController@GetCollegeSportsBox');
Route::post('/infocollegeexpensebox','InfoBoxController@GetCollegeExpensesBox');
Route::post('/infocollegeugethnicbox','InfoBoxController@GetUgEthnicBox');
Route::post('/infoboxgeneral','InfoBoxController@GetGeneralInfoBox');
Route::post('/infoboxrankdata','CollegeController@getCollegeListSchools');
Route::post('/infoenrollnutbox','InfoBoxController@GetEnrollNutBox');
Route::post('/infothreewaynutbox','InfoBoxController@GetThreeNutBox');
Route::post('/infoboxmajorprograms','InfoBoxController@GetMajorProgramBox');

// news/quad routes
Route::get('/newslisting', 'NewsController@newsListing');
Route::get('/newstest', 'NewsController@newsTest');
Route::get('/addnews', 'NewsController@addNews');
Route::post('/addnews', 'NewsController@postNews');
Route::get('/editnews/{id}', 'NewsController@editNews');
Route::post('/editnews/{id}', 'NewsController@postEditedNews');
Route::get('/listnews', 'NewsController@listNews');
Route::get('/getsubcategory', 'NewsController@getSubCategory');
Route::post('/news/ajaxdata', 'NewsController@newsAjaxData');
Route::post('/news/search', 'NewsController@search');
Route::post('/news/purchaseEssay', 'NewsController@purchaseEssay');

// footer page routes
Route::get( '/college-prep', 'footerPageController@collegeprep');
Route::get( '/college-submission', 'footerPageController@collegeSubmission');
Route::get( '/scholarship-submission', 'footerPageController@scholarshipSubmission');
Route::get( '/about', 'footerPageController@about');
Route::get( '/team', 'footerPageController@team');
Route::get( '/contact', 'footerPageController@contact');
Route::get( '/text-privacy-policy', 'footerPageController@txtPrivacyPolicy');
Route::post( '/contact', 'FooterFormController@contactPost');
Route::post( '/college-prep', 'FooterFormController@collegePrepPost');
Route::post( '/college-submission/thankyou', 'FooterFormController@collegeSubmissionPost');
Route::post( '/scholarship-submission', 'FooterFormController@scholarshipSubmissionPost');
Route::post( '/scholarship-submission/{random_token}', 'FooterFormController@scholarshipSubmission2Post');
Route::get( '/scholarship-submission/ajax/{type}', 'FooterFormController@scholarshipSubmissionGetList');
Route::get( '/careers-internships', 'FooterFormController@showCareersAndInternships');
Route::get( '/careers-internships/{id}', 'FooterFormController@showCareer' );
Route::post( '/careers-internships/{id}', 'FooterFormController@postCareer');

Route::post('/saveCollegeSubmission', 'AjaxController@saveCollegeSubmission');

// help page routes
Route::get('/help','helpController@index');
Route::get('/help/faq/{faq_type}', 'helpController@faq');
Route::get('/help/helpful_videos', 'helpController@helpfulVideos');

//Sign up area.
Route::get('signup','AuthController@getSignup');
Route::post('/signup/{is_api?}','AuthController@postSignup');

//Sign in area
Route::get('signin','AuthController@getSignin');
Route::post('signin','AuthController@postSignin');
Route::post('only-signin', 'AuthController@onlySignin');

//login for ccp
Route::post('login/{returl?}/{section?}', 'AuthController@signInForCCP');
Route::post('signupForCCP', 'AuthController@signupForCCP');

//Confirm Email
Route::get('confirmemail/{token}','AuthController@confirmEmail');

Route::group(['middleware' => 'auth.user'], function(){
	Route::post('ajax/resendconfirmemail','AjaxController@resendConfirmationEmail');
});

//Password Reset
Route::get('forgotpassword','ResetPasswordController@getRemind');
Route::post('forgotpassword','ResetPasswordController@postRemind');
Route::get('resetpassword/{token}', [
	'as' => 'password.reset',
	'uses' => 'ResetPasswordController@getReset'
]);
Route::post('resetpassword','ResetPasswordController@postReset');

Route::group(array('prefix' => 'modal'), function(){
	Route::get('profile/firstTimeHomepageModal_1/{ajaxtoken}', function($ajaxtoken){
		return View('private.homepage.modals.firstTimeHomepageModal_1', array('ajaxtoken' => $ajaxtoken));
	});

	Route::get('profile/firstTimeHomepageModal_2/{ajaxtoken}', function($ajaxtoken){
		return View('private.homepage.modals.firstTimeHomepageModal_2', array('ajaxtoken' => $ajaxtoken));
	});
});

//Profile Public Pages
Route::get('/colleges/admin/recommendations', 'CollegeRecommendationController@index');
Route::get('/colleges/admin/recommendations/recycleOldRecs', 'CollegeRecommendationController@recycleOldRecs');

//webinar form
Route::get('/webinar', 'WebinarController@index');
Route::get('/webinar/submit', 'WebinarController@submit');
Route::post('/webinar/saveWebinarLiveSignups', 'HomepageController@saveWebinarLiveSignups');

// fb & google & linkedin login
Route::get('facebook', 'AuthController@loginWithFacebook');
Route::get('googleSignin', 'AuthController@loginWithGoogle');
// Route::get('linkedinSignin', 'AuthController@loginWithLinkedIn');

// Invite Contacts Routes
Route::get('googleInvite/', 'InviteController@inviteWithGoogle');
Route::get('googleInviteForSocialApp/', 'InviteController@inviteWithGoogleForSocialApp');
Route::get('yahooInvite/', 'InviteController@inviteWithYahoo');
Route::get('microsoftInvite/', 'InviteController@inviteWithMicrosoft');

// Get Contact Routes
Route::get('getGoogleContacts/', 'InviteController@getGoogleContacts');
Route::get('getYahooContacts/', 'InviteController@getYahooContacts');
Route::get('getMicrosoftContacts/', 'InviteController@getMicrosoftContacts');

// Special Events Routes
Route::get('/happy-birthday-to-you', 'SpecialEventsController@getHappyBirthday');

// Plexuss Conference Routes
Route::get('/conferences', 'ConferenceController@index');

// Get AutoComplete json reeturn for Highschool textbox
Route::get( 'getAutoCompleteData', 'AutoComplete@school_autocomplete' );
Route::get( 'getBattleAutocomplete', 'AutoComplete@getBattleAutocomplete' );
Route::get( 'getTopSearchAutocomplete', 'AutoComplete@getTopSearchAutocomplete' );
Route::get( 'getslugAutoCompleteData', 'AutoComplete@getslugAutoCompleteData' );
Route::get( '/getObjectiveMajors', 'AutoComplete@getMajors' );
Route::get( '/getObjectiveProfessions', 'AutoComplete@getProfessions' );

// Autocompletes for kayak search
Route::get( '/getStates', 'AutoComplete@getStates' );
Route::get( '/getCities', 'AutoComplete@getCities' );
Route::get( '/getDegrees', 'AutoComplete@getDegrees' );
Route::get( '/getCollegeReligions', 'AutoComplete@getCollegeReligions' );

// Beta Form submit
Route::post( 'submitBetaForm', 'BetaSignupController@submitBetaForm' );

// Temp betauser area for brett. This will be replaced soon
Route::get( 'betausers', 'BetaUserController@getBetaUsers' );
Route::get( 'revenueReport', 'BetaUserController@revenueReport');
Route::get( 'cleanRevenueReport', 'BetaUserController@cleanRevenueReport');
// Routes for infoboxes
Route::post('/letterfilter', 'InfoBoxController@getFilterLetter');

// Phone routes
Route::group(array('prefix' => 'phone'), function(){
	Route::get('/sms/send', 'TwilioController@sendSms');
	Route::any('/sms/receive', 'TwilioController@smsReceive');
	Route::post('/sms/callback', 'TwilioController@smsCallBack');
	Route::get('/sms/infilawSms', 'TwilioController@infilawSms');
	Route::get('/sms/infilawFollowUp', 'TwilioController@followupInfilawSms');
	Route::post('/validatePhoneNumber/', 'Controller@validatePhoneNumber');

	Route::post('/plexussAppSendInvitation', 'Controller@plexussAppSendInvitation');

	Route::post('/initilizePhoneLog', 'TwilioController@initilizePhoneLog');
	Route::post('/twiml', 'TwilioController@phoneTwiml');
	Route::get('/makeCall', 'TwilioController@makeCall');
	Route::post('/recordCallBack', 'TwilioController@recordCallBack');
	Route::post('/callStatus', 'TwilioController@callStatus');
    Route::post('/incomingCall', 'TwilioController@incomingCall');

    Route::get('/redirectPhone', 'TwilioController@redirectPhone');
    Route::post('/modifyLiveCalls', 'TwilioController@modifyLiveCalls');

    Route::any('/conference/wait',
	    ['uses' => 'TwilioController@wait', 'as' => 'conference-wait']
	);
	Route::get('/conference/twiml', 'TwilioController@conferenceTwiml');
	Route::get('/conference/connect/{conferenceId}', 'TwilioController@connectAgent');
	Route::get('/conference/connectClient', 'TwilioController@connectClient');

	Route::get('/conference', 'TwilioController@conferenceCall');
	Route::get('/removeParticipant', 'TwilioController@removeParticipant');

});

Route::group(array('prefix' => 'infilaw'), function(){
	Route::get('/survey', 'HomepageController@signedOutIndex');
	Route::post('/survey/saveInfo', 'InfilawController@saveInfo');
	Route::get('/survey/step/{step_num}', 'InfilawController@step');

	Route::get('/sendInfilawCollegeEmail', 'MandrillAutomationController@sendInfilawCollegeEmail');
	Route::get('/sendInfilawAmazonCode', 'MandrillAutomationController@sendInfilawAmazonCode');
	Route::get('/convert', 'InfilawController@convert');
	Route::get('/match', 'InfilawController@match');
	Route::get('/dup', 'InfilawController@dup');
});

Route::get('/emailUserParser', 'EmailParserController@parseUserResponds');
Route::get('/emailCollegeParser', 'EmailParserController@parseCollegeResponds');
Route::get('/parseIsItAGoodFitForYou', 'EmailParserController@parseIsItAGoodFitForYou');
Route::get('/parseCollegeRankingUpdateForUsers', 'EmailParserController@parseCollegeRankingUpdateForUsers');
Route::get('/parseSchoolsYouLiked', 'EmailParserController@parseSchoolsYouLiked');
Route::get('/parseSchoolsNearYou', 'EmailParserController@parseSchoolsNearYou');

Route::get('/usersSendMessagesForCollegesTEST', 'MandrillAutomationController@usersSendMessagesForCollegesTEST');

Route::get('/forgetCache', 'GroupMessagingController@forgetCache');
Route::get('/autoSendInvitesFollowUp/{type}', 'InviteController@autoSendInvitesFollowUp');
Route::get('/autoApproveRecommendationColleges', 'Controller@autoApproveRecommendationColleges');
Route::get('/inviteWebinarUsers', 'Controller@inviteWebinarUsers');
Route::get('/inviteWebinarFifteenMin', 'Controller@inviteWebinarFifteenMin');

Route::get('/retrainModels', 'Controller@retrainModels');
Route::get('/sendReminders', 'Controller@sendReminders');
Route::get('/sendPrepData', 'Controller@sendPrepData');

Route::get('/setRecruitmentTag', 'Controller@setRecruitmentTag');
Route::get('/setUserTargettedForPickACollege', 'Controller@setUserTargettedForPickACollege');

Route::get('/populateSentMsg', 'Controller@populateSentMsg');

Route::get('/checkGeneralRecruitmentTag', 'Controller@checkGeneralRecruitmentTag');

Route::get('/generateAutoCampaign', 'GroupMessagingController@generateAutoCampaign');

// unsubscribe routes
Route::get('/unsubscribe/{email?}', 'AjaxController@getUnsubscribe');
Route::post('/unsubscribe/{email}', 'AjaxController@unsubscribeThisEmail');
Route::post('/reasonToUnsubscribe', 'AjaxController@whyUserUnsubscribed');

Route::get('/replyText', function(){
	$user = User::find(93);
	$college_id = 663;

	$tc = new TwilioController;
	$tc->sendReplyTextMessage($user, $college_id);
});

Route::get('flushcache', function(){

	$input =  Input::all();

	if (!isset($input['t']) ) {
		return Redirect::to( '/' );
	}

	if(isset($input['t']) && $input['t'] == "joie3e23riuhneisuio"){
		Cache::flush();
		echo 'FLUSH Cache';
		exit();
	}

	return Redirect::to( '/' );

});

Route::get('sitemap', function(){
	return View( 'public.sitemap.sitemap');
});

Route::get('sitemap1', function(){
	return View( 'public.sitemap.sitemap1');
});

Route::get('sitemap2', function(){
	return View( 'public.sitemap.sitemap2');
});


// Routes for Social Features
Route::group( array( 'prefix' => '/social' ), function(){
	// Share routes
	Route::group( array( 'prefix' => '/share' ), function(){
		// linkedin share routes
		Route::group( array( 'prefix' => '/linkedin' ), function(){
			Route::get( '/storeArticle', 'ShareController@storeLinkedinArticle' );
			Route::get( '/getAccessToken', 'ShareController@getLinkedinAccessToken' );
			Route::get( '/preview', 'ShareController@showLinkedinPreview' );
			Route::post( '/submit', 'ShareController@submitLinkedin' );
		} );
	} );
	Route::group( array( 'prefix' => '/comment' ), function(){
		Route::post( '/new', 'CommentController@newComment' );
		Route::post( '/like', 'CommentController@likeComment' );
		Route::get( '/getLatest', 'CommentController@getLatest' );
		Route::get( '/getEarlier', 'CommentController@getEarlier' );
	} );
} );

// Update AWS S3
Route::get( 'update-news-image-Content-Type', function(){
	return Response::view('errors.missing', array(), 404);
} );

Route::get('/collegesSendMessagesForUsers', 'MandrillAutomationController@collegesSendMessagesForUsers');
Route::get('/sendScheduleCampaign', 'GroupMessagingController@sendScheduleCampaign');
Route::get('/sendReadyCampaign', 'GroupMessagingController@sendReadyCampaign');
Route::get('/setDailyRecGoals', 'Controller@setDailyRecGoals');
Route::get('/changeFinancial', 'Controller@changeFinancial');
Route::get('/salesGenerator', 'Controller@salesGenerator');
Route::get('/salesGeneratorCache', 'Controller@salesGeneratorCache');
Route::get('/diceRoll', 'AjaxController@diceRoll');

//Ad click route for eddy
Route::post('/adClicked', 'AjaxController@adClicked');

// Apply now clicks
Route::post('/applyNowClicked', 'AjaxController@applyNowClicked');
Route::get('/trackApplyPixel', 'AjaxController@trackApplyPixel');

// Ad Impression added
Route::post('/addAdImpression', 'AjaxController@addAdImpression');

// College news clicked.
Route::post('/collegeNewsClicked', 'AjaxController@collegeNewsClicked');

Route::get('/trackPixel', 'Controller@trackingPixel');
Route::get('/changePriorityTier', 'Controller@changePriorityTier');

Route::get('/autoApproveInquiryColleges', 'Controller@autoApproveInquiryColleges');
Route::get('/autoApproveTargettedInquiries', 'Controller@autoApproveTargettedInquiries');

Route::get('/lightbox/{id?}', 'AjaxController@lightbox');
Route::get('/college-youniversity/{id?}', 'AjaxController@collegeYouniversity');
Route::get('/getNumberOfHandshakes', 'AjaxController@getNumberOfHandshakes');
Route::get('/forgetdismissPlexussAnnouncement', 'AdminController@forgetdismissPlexussAnnouncement');

Route::post('/postTest', 'Controller@postTest');

Route::get('/saveExportsToEmailLater', 'AjaxController@saveExportsToEmailLater');

Route::get('/clearfilterNoMatch', function(){
	Cache::forget(env('ENVIRONMENT').'_'.'filterNoMatch');
});

// Agency routes
Route::get('/agency-signup', 'AuthController@getAgencySignup');
Route::post('/postAgencySignup', 'AuthController@postAgencySignup');
Route::post('/postAgencyApplication', 'AuthController@postAgencyApplication');

Route::get('/agency-search', 'AgencyController@agencySearchIndex');
Route::get('/agency-search/{search_type}/{search_string}', 'AgencyController@agencySearch');

Route::get('/agency-profile/{agency_id}/{agent_id}', 'AgencyController@agentProfileIndex');
Route::post('/agency-profile/addReview', 'AgencyController@addReview');
Route::post('/agency-profile/getReviews', 'AgencyController@getReviews');

// College admin routes
Route::get('/admin-signup', 'AuthController@getAdminSignup');
Route::post('/postAdminSignup', 'AuthController@postAdminSignup');
Route::post('/postAdminApplication', 'AuthController@postAdminApplication');

Route::get('/crfWithSuppression', function(){
	$viewDataController = new ViewDataController();
	$data = $viewDataController->buildData();
	if (!in_array($data['user_id'], array(93,340310))){
		return;
	}
	$data['org_branch_id'] = 134;
	$data['org_school_id'] = 2490;
	$college_id = $data['org_school_id'];
	$data['default_organization_portal'] = (object) array();
	$data['default_organization_portal']->id = null;
	$data['aor_id'] = 3;
	$data['aor_portal_id'] = 2;
	$crf = new CollegeRecommendationFilters;
	$qry = $crf -> generateFilterQry($data);
	$qry = app('Controller')->getRawSqlWithBindings($qry);

	$qry = str_replace("*", "`userFilter`.`id`", $qry);
	$users = DB::connection('bk')->table('users as u')
		->join(DB::raw('('.$qry.') as t1'), 't1.id', '=', 'u.id')
		->where('u.is_alumni', 0)
		->where('u.is_parent', 0)
		->where('u.is_counselor', 0)
		->where('u.is_organization', 0)
		->where('u.is_agency', 0)
		->where('u.is_university_rep', 0)
		->where('u.is_plexuss', 0)

		->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
		->groupBy('u.id');

	$users = app('CollegeRecommendationController')->addSuppressionQry($users, $college_id);

	$users = app('AjaxController')->getRawSqlWithBindings($users);

	return $users;
});

Route::get('/crfForInsert', function(){
	$viewDataController = new ViewDataController();
	$data = $viewDataController->buildData();
	if (!in_array($data['user_id'], array(93,340310))){
		return;
	}
	$data['org_branch_id'] = 134;
	$data['org_school_id'] = 2490;
	$college_id = $data['org_school_id'];
	$data['default_organization_portal'] = (object) array();
	$data['default_organization_portal']->id = null;
	$data['aor_id'] = 3;
	$data['aor_portal_id'] = 2;
	$crf = new CollegeRecommendationFilters;
	$qry = $crf -> generateFilterQry($data);
	$qry = app('Controller')->getRawSqlWithBindings($qry);

	$qry = str_replace("*", "`userFilter`.`id`", $qry);
	$users = DB::connection('bk')->table('users as u')
		->join(DB::raw('('.$qry.') as t1'), 't1.id', '=', 'u.id')
		->where('u.is_alumni', 0)
		->where('u.is_parent', 0)
		->where('u.is_counselor', 0)
		->where('u.is_organization', 0)
		->where('u.is_agency', 0)
		->where('u.is_university_rep', 0)
		->where('u.is_plexuss', 0)

		->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
		->groupBy('u.id');

	$users = app('CollegeRecommendationController')->addSuppressionQry($users, $college_id);
	$users = app('AjaxController')->getRawSqlWithBindings($users);
	$users = str_replace("*", "`u`.`id`, @college_id,1,1, 'advanced_search', date_sub(current_timestamp,interval 7 hour), date_sub(current_timestamp,interval 7 hour)", $users);

	return $users;
});

Route::get('/crf', function(){
	$viewDataController = new ViewDataController();
	$data = $viewDataController->buildData();
	if (!in_array($data['user_id'], array(93,340310))){
		return;
	}
	$data['org_branch_id'] = 134;
	$data['org_school_id'] = 2490;
	$college_id = $data['org_school_id'];
	$data['default_organization_portal'] = (object) array();
	$data['default_organization_portal']->id = null;
	$data['aor_id'] = 3;
	$data['aor_portal_id'] = 2;
	$crf = new CollegeRecommendationFilters;
	$qry = $crf -> generateFilterQry($data);
	$qry = app('Controller')->getRawSqlWithBindings($qry);

	$qry = str_replace("*", "`userFilter`.`id`", $qry);
	$users = DB::connection('bk')->table('users as u')
		->join(DB::raw('('.$qry.') as t1'), 't1.id', '=', 'u.id')
		->where('u.is_alumni', 0)
		->where('u.is_parent', 0)
		->where('u.is_counselor', 0)
		->where('u.is_organization', 0)
		->where('u.is_agency', 0)
		->where('u.is_university_rep', 0)
		->where('u.is_plexuss', 0)
		->select('u.id')

		->orderByRaw('`u`.`profile_percent` IS NULL,`u`.`profile_percent` DESC')
		->groupBy('u.id');

	$users = app('AjaxController')->getRawSqlWithBindings($users);

	return $users;
});

Route::get('/addTargettingUsersToRecruitmentTagCronJob', 'Controller@addTargettingUsersToRecruitmentTagCronJob');

// Generate pdf url is required.
Route::get('/generatePDF', 'Controller@generatePDF');

Route::get('applicationSuppression', 'Controller@applicationSuppression');

Route::get('/applicationEmailFinishedApp', 'MandrillAutomationController@applicationEmailFinishedApp');
Route::get('/scheduleoneapp', 'Controller@scheduleoneapp');
Route::get('/applicationEmailForCovetedUSersPostTen', 'MandrillAutomationController@applicationEmailForCovetedUSersPostTen');

Route::get('/applicationTextForPeopleWhoHaventStarted', function(){
	$mac = new MandrillAutomationController;
	$mac->applicationTextForPeopleWhoHaventStarted(4, false);
});

Route::any('/fbchatbot', 'ChatBotController@index');
Route::get('/admitseeEmails', 'MandrillAutomationController@admitseeEmails');
Route::get('/sendPressRelease', 'MandrillAutomationController@sendPressRelease');
Route::get('/changeApplicationStates', 'Controller@changeApplicationStates');
Route::get('/changeApplicationStatesUploads', 'Controller@changeApplicationStatesUploads');
Route::get('/fixPresscreenedAddToRecTab', 'Controller@fixPresscreenedAddToRecTab');
Route::get('/elsAdditionalInfoEmails', 'MandrillAutomationController@elsAdditionalInfoEmails');
Route::get('/sendTest', 'MandrillAutomationController@sendTest');

Route::group(array('before' => 'auth'), function(){
	Route::get('/redis', 'SocketController@index');
});

Route::get('/socket', function(){
	Redis::set('name', 'john');

	return Redis::get('name');
});

Route::post('/setRedisOnlineUser', 'Controller@setRedisOnlineUser');
Route::post('/removeRedisOnlineUser', 'Controller@removeRedisOnlineUser');
Route::get('/getRedis', 'SocketController@getRedis');
Route::get('/forgetChatCache', 'Controller@forgetChatCache');

Route::get('/demoteOneAppEmailForUsers', 'MandrillAutomationController@demoteOneAppEmailForUsers');
// Route::get('/campaignFix', 'Controller@campaignFix');
Route::get('/autoPrescreenedUserAppliedColleges', 'Controller@autoPrescreenedUserAppliedColleges');
Route::get('/emailInternalCollegesRecruitment', 'UtilityController@emailInternalCollegesRecruitment');


// Route::get('/unsubInternalEmailColleges', 'EmailParserController@unsubInternalEmailColleges');
// Route::get('/emailInternalCollegesSecondaryEmail', 'UtilityController@emailInternalCollegesSecondaryEmail');
// Route::get('/emailInternalCollegesThirdEmail', 'UtilityController@emailInternalCollegesThirdEmail');
// Route::get('/emailInternalCollegesVCs', 'UtilityController@emailInternalCollegesVCs');
// Route::get('/emailInternalCollegesLiveChat', 'UtilityController@emailInternalCollegesLiveChat');
// Route::get('/emailInternalCollegesScholarship', 'UtilityController@emailInternalCollegesScholarship');
Route::get('/edxEmailCronJob/{rand}', 'UtilityController@edxEmailCronJob');
Route::get('/edxEmailCronJobRepeat/{rand}/{v}', 'UtilityController@edxEmailCronJobRepeat');
Route::get('/usersOneappScholarshipEmail', 'UtilityController@usersOneappScholarshipEmail');
// Route::get('/getWorkspace', 'WatsonController@getWorkspace');
// Route::get('/watson/sendMessage', 'WatsonController@sendMessage');
// Route::get('/forget', 'Controller@forget');
// Route::get('/unsubInternalEmailColleges', 'EmailParserController@unsubInternalEmailColleges');
// Route::get('/emailInternalCollegesPresidents', 'UtilityController@emailInternalCollegesPresidents');
// Route::get('/addingCollegeIdToContactInfo', 'Controller@addingCollegeIdToContactInfo');
// Route::get('/customForSchoolsEnrollmentDepartmentCron', 'UtilityController@customForSchoolsEnrollmentDepartmentCron');
Route::get('/testNumOfHandShake', 'Controller@testNumOfHandShake');
Route::get('/adRedirect', 'Controller@adRedirect');
Route::get('/ar/{company}/{cid}/{utm_source}', 'Controller@ar');

Route::get('/agencyGenerateNewLeads', 'MandrillAutomationController@agencyGenerateNewLeads');
Route::get('/iOSappInvite', 'UtilityController@iOSappInvite');
Route::get('/agentOneADay', 'UtilityController@agentOneADay');
Route::get('/edxUserInviteListCronJon/{cron_type?}', 'UtilityController@edxUserInviteListCronJon');

// Route::get('/nrccuaInsertSchools', 'AjaxController@nrccuaInsertSchools');
// Route::get('/nrccuaDistributionClientFieldMapping', 'AjaxController@nrccuaDistributionClientFieldMapping');
// Route::get('/nrccuaDistributionClientValueMapping', 'AjaxController@nrccuaDistributionClientValueMapping');
// Route::get('/nrccuaHandleMajorMapping', 'AjaxController@nrccuaHandleMajorMapping');
// Route::get('/testNRCCUADistribution', 'DistributionController@testNRCCUADistribution');
// Route::get('/sendCustomNRCCUA', 'DistributionController@sendCustomNRCCUA');
// Route::get('/insertEductionDynamicsClientFieldMappings', 'AjaxController@insertEductionDynamicsClientFieldMappings');
// Route::get('/insertEducationDynamicsClientValueMappings', 'AjaxController@insertEducationDynamicsClientValueMappings');

// Route::get('/testEducationDynamicsDistribution', 'DistributionController@testEducationDynamicsDistribution');

// Route::get('/testCappexDistribution', 'DistributionController@testCappexDistribution');
Route::get('/freemiumEmail', 'UtilityController@freemiumEmail');
// Route::get('/autoPortalEmail/{template_name}/{ro_name}/{forced_college_id?}', 'UtilityController@autoPortalEmail');
Route::get('/uploadMissingFBImages', 'UtilityController@uploadMissingFBImages');

// Route::get('/insertCappexFilters', 'AjaxController@insertCappexFilters');
// Route::get('/insertCappexFilterLogs', 'AjaxController@insertCappexFilterLogs');
// Route::get('/insertCappexFilterLogsForStates', 'AjaxController@insertCappexFilterLogsForStates');
// Route::get('/testNRCCUAPost', 'DistributionController@testNRCCUAPost');
// Route::get('/checkIpedsIdExist', 'AjaxController@checkIpedsIdExist');
// Route::get('/insertCappexSchools', 'AjaxController@insertCappexSchools');
// Route::get('/insertCappexClientFieldMappings', 'AjaxController@insertCappexClientFieldMappings');
// Route::get('/insertCappexClientValueMappings', 'AjaxController@insertCappexClientValueMappings');

Route::get('/next-steps', 'GetStartedController@nextStepsIndex');

// Route::get('/insertCappexGradYearsFilterLogs', 'AjaxController@insertCappexGradYearsFilterLogs');
// Route::get('/insertCappexGenderFilterLogs', 'AjaxController@insertCappexGenderFilterLogs');

// Route::get('/findCappexCountries', 'AjaxController@findCappexCountries');

// Route::get('/insertCappexStateFilterLogs', 'AjaxController@insertCappexStateFilterLogs');

// Route::get('/testAgent', 'Controller@testAgent');
Route::get('/resendCappedEmails', 'UtilityController@resendCappedEmails');
Route::get('/resendCappedEmailsOffset', 'UtilityController@resendCappedEmailsOffset');
Route::get('/setResendCappedEmailsOffset/{offset}', 'UtilityController@setResendCappedEmailsOffset');
// Route::get('/checkData', 'Controller@checkData');
Route::get('/testAgent', 'Controller@testAgent');

Route::get('/partnerEmailCronJob/{rand}/{cron_type?}', 'UtilityController@partnerEmailCronJob');
// Route::get('/partnerEmailForUsersInviteCronJob/{rand}/{cron_type}', 'UtilityController@partnerEmailForUsersInviteCronJob');
// Route::get('/autoPostingInquiries/{utm?}', 'DistributionController@autoPostingInquiries');

Route::get('/autoPostingCappex/{cron_type}', 'DistributionController@autoPostingCappex');

Route::get('/ajax/getReport', 'AdminController@getReport');
// Route::get('/suppressionJson', 'Controller@suppressionJson');

////////////////////
Route::get('/emailInternalPlexussEmailForAnalytics', 'UtilityController@emailInternalPlexussEmailForAnalytics');
// Route::get('/autoPostingCappexUsersWhoSelectedAPickACollege', 'DistributionController@autoPostingCappexUsersWhoSelectedAPickACollege');
// Route::get('/suppressionJson', 'Controller@suppressionJson');

// Route::get('/autoTurnoffPostingLeadsCappex', 'DistributionController@autoTurnoffPostingLeadsCappex');

// Route::get('/testSES', 'Controller@testSES');
// Route::get('/sesJson', 'Controller@sesJson');
// Route::get('/importSendGridSuppression', 'UtilityController@importSendGridSuppression');
Route::get('/cleanUpUserInvitesTable', 'UtilityController@cleanUpUserInvitesTable');
Route::get('/onceADayProcedureForMakingPostingReady', 'DistributionController@onceADayProcedureForMakingPostingReady');
// Route::get('/testPixel', 'Controller@testPixel');
// Route::get('/fixCappexMatchesForGenderAndZip', 'DistributionController@fixCappexMatchesForGenderAndZip');
// Route::get('/runClustersForAllUsers', 'UtilityController@runClustersForAllUsers');

Route::get('/insertKeypathClientFieldMappings', 'AjaxController@insertKeypathClientFieldMappings');
Route::get('/insertKeypathClientValueMappings', 'AjaxController@insertKeypathClientValueMappings');

Route::get('/testKeyPathDistribution', 'DistributionController@testKeyPathDistribution');

Route::get('/insertKeypathMissingClientFieldMappings', 'AjaxController@insertKeypathMissingClientFieldMappings');

// Route::get('/getBirthdayUsingAccurateAppend', 'UtilityController@getBirthdayUsingAccurateAppend');
// Route::get('/getAddressUsingAccurateAppend', 'UtilityController@getAddressUsingAccurateAppend');

Route::get('/userMissingFields/{company?}/{cid?}/{uid?}/{uiid?}/{utm_source?}/{section?}', 'GetStartedController@userMissingFields');

Route::post('/saveMissingFields/', 'GetStartedController@saveMissingFields');

// Route::get('/internalAddUsersToAPortal', 'UtilityController@internalAddUsersToAPortal');
// Route::get('/getAddressUsingEbureau', 'UtilityController@getAddressUsingEbureau');

Route::get('/ebureauXMLParser', 'UtilityController@ebureauXMLParser');

Route::get('/proc_revenue_fill_missingdata', 'DistributionController@proc_revenue_fill_missingdata');
Route::get('/insertZetaClientFieldMappings', 'AjaxController@insertZetaClientFieldMappings');
Route::get('/insertZetaClientValueMappings', 'AjaxController@insertZetaClientValueMappings');
Route::get('/zetaProgramGenerator', 'AjaxController@zetaProgramGenerator');

Route::get('/insertCollegeXpressClientFieldMappings', 'AjaxController@insertCollegeXpressClientFieldMappings');
Route::get('/insertCollegeXpressClientValueMappings', 'AjaxController@insertCollegeXpressClientValueMappings');

// Route::get('/proc_revenue_fill_missingdata', 'DistributionController@proc_revenue_fill_missingdata');
// Route::get('/addKeypathUsersToSupportAccount', 'UtilityController@addKeypathUsersToSupportAccount');


//Scholarship Submision
Route::get( '/scholarship-get-started', 'footerPageController@scholarshipStepOne');
Route::any( '/scholarship-info', 'footerPageController@scholarshipStepTwo');
Route::get( '/scholarship-intrest/{type?}', 'footerPageController@scholarshipStepThree');
Route::get( '/scholarship-thankyou', 'footerPageController@scholarshipStepFour');

Route::get('/passthruIntermission/{company}/{cid}/{ad_passthrough_id?}/{uid?}/{uiid?}/{utm_source?}/{status?}', 'GetStartedController@passthruIntermission');
Route::get('/testTemplateEmail', 'UtilityController@testTemplateEmail');
// Route::get('/distributeInquiriesToAgents', 'UtilityController@distributeInquiriesToAgents');
Route::post('/college/currentStudentAjaxData', 'CollegeController@currentStudentAjaxData');
Route::post('/college/alumniAjaxData', 'CollegeController@alumniAjaxData');
Route::any('/ivr/answerACall', 'TwilioController@answerACall');
// Route::get('/sendZetaTextCampaign', 'UtilityController@sendZetaTextCampaign');

Route::get('/addRoId', 'UtilityController@addRoId');

Route::get('/addUsersToConvertedBucketInSupportAccount/{ro_type}/{lead_type?}', 'UtilityController@addUsersToConvertedBucketInSupportAccount');
// Route::get('/getAddressUsingWhitepages', 'UtilityController@getAddressUsingWhitepages');

Route::get('/sendClientWeeklyRemainders', 'MandrillAutomationController@sendClientWeeklyRemainders');

// Route::get('/sendFirstNafsaEmail/', 'UtilityController@sendFirstNafsaEmail');
Route::get('/sendCollegeXpressLeads', 'DistributionController@sendCollegeXpressLeads');
// Route::get('/sendSecondNafsaEmail', 'UtilityController@sendSecondNafsaEmail');

// Route::get('/setGusAccessToken', 'DistributionController@setGusAccessToken');
// Route::get('/insertGusClientFieldMappings', 'AjaxController@insertGusClientFieldMappings');
Route::get('/insertGusClientValueMappings', 'AjaxController@insertGusClientValueMappings');
// Route::get('/sendThirdNafsaEmail', 'UtilityController@sendThirdNafsaEmail');
Route::get('/generateScholarshipRecommendation', 'CollegeRecommendationController@generateScholarshipRecommendation');

Route::get('/revenueReportMonthly', 'BetaUserController@revenueReportMonthly');
Route::get('/nrccuaRevenueReport', 'BetaUserController@nrccuaRevenueReport');

Route::any('/scholarshipadmin/delScholarshipAdmin', 'AdminScholarshipsController@delScholarshipAdmin');
Route::get('/autoUploadLogoCollege', 'UtilityController@autoUploadLogoCollege');
Route::get('/queueAddRecommendationsToSupportAccount', 'UtilityController@queueAddRecommendationsToSupportAccount');
// Route::get('/sendEmailToUsersWhoReceivedScholarshipRecommendation', 'CollegeRecommendationController@sendEmailToUsersWhoReceivedScholarshipRecommendation');
Route::get('/setUsersPortalEmailEffortLogsDateId', 'UtilityController@setUsersPortalEmailEffortLogsDateId');

Route::get('/getListForInfoGroup', 'UtilityController@getListForInfoGroup');
Route::get('/fillValueMappingNrccua', 'UtilityController@fillValueMappingNrccua');
Route::get('/autoTurnoffPostingLeadsNrccua', 'DistributionController@autoTurnoffPostingLeadsNrccua');


// events routes
Route::get('/college-fairs-events', 'EventsController@index');
Route::get('/ajax/getOnlineEvents', 'EventsController@getOnlineEvents');
Route::get('/ajax/getOfflineEvents', 'EventsController@getOfflineEvents');
Route::get('/ajax/getnearestEvents', 'EventsController@getNearestEvents');
Route::get('/ajax/getCountryNames', 'EventsController@getCountryNames');
Route::post('/ajax/getnearestCityEvents', 'EventsController@getnearestCityEvents');

Route::post('/ajax/getcityNames','EventsController@getCityName');

// Route::get('/sendRecommendationForRevenueSchoolMatching', 'UtilityController@sendRecommendationForRevenueSchoolMatching');
Route::get('/autoUploadCollegeOverviewImage', 'UtilityController@autoUploadCollegeOverviewImage');
Route::get('/fillValueMappingCappex', 'UtilityController@fillValueMappingCappex');
Route::get('/manualNrccua', 'DistributionController@manualNrccua');
Route::get('/manualNrccuaRecruitment', 'DistributionController@manualNrccuaRecruitment');
Route::get('/tmpNrccuaAddUserId', 'DistributionController@tmpNrccuaAddUserId');
Route::get('/tmpNrccuaFixInquiryDate', 'DistributionController@tmpNrccuaFixInquiryDate');
Route::get('/tmpFindNrccuaSource', 'DistributionController@tmpFindNrccuaSource');

// Route::get('/addZipAndDedupEAB', 'UtilityController@addZipAndDedupEAB');
Route::get('/sendNrccuaQueue', 'DistributionController@sendNrccuaQueue');
Route::get('/addToNrccuaQueue', 'DistributionController@addToNrccuaQueue');
// Route::get('/sendCollegeXpressLeadsIntl', 'DistributionController@sendCollegeXpressLeadsIntl');


// Jesus Test - NCSA route
Route::post('sendNCSAinquiries', 'DistributionController@sendNCSAinquiries');


Route::get('/deleteSinaLabel', 'EmailParserController@deleteSinaLabel');
Route::get('/setLoggedInUserTrackingLog', 'UtilityController@setLoggedInUserTrackingLog');
Route::get('/getNrccuaClicks/{source?}', 'UtilityController@getNrccuaClicks');

// Set tracking for modals
Route::post('/tracking/modals/{modal_name}/{trigged_action}', 'TrackingPageController@setTrackingModal');

// Route::get('/autoTurnoffPostingLeadsCappexCappex', 'DistributionController@autoTurnoffPostingLeadsCappexCappex');

Route::get('/populateUsersIpLocations', 'UtilityController@populateUsersIpLocations');

Route::get('/setNrccuaCronSchedule', 'DistributionController@setNrccuaCronSchedule');

Route::get('/getEmailClicks', 'UtilityController@getEmailClicks');
Route::get('/getEmailOpens' , 'UtilityController@getEmailOpens');
Route::get('/fixIpLocation', 'UtilityController@fixIpLocation');

Route::get('/userEngagementEmailProcess/{type}', 'UtilityController@userEngagementEmailProcess');
Route::get('/emailGateway', 'UtilityController@emailGateway');
// Route::get('/testTime', 'BetaUserController@testTime');

Route::get('/addUsersToEmailLogicHelper', 'UtilityController@addUsersToEmailLogicHelper');
// Route::get('/setTrackingUrls', 'TrackingPageController@setTrackingUrls');
Route::get('/runRevenueReportCronJob', 'BetaUserController@runRevenueReportCronJob');
// Route::get('/setTrackingFragmentsForCollegePages', 'TrackingPageController@setTrackingFragmentsForCollegePages');
// Route::get('/setTrackingFragmentsForCollegeMajors', 'TrackingPageController@setTrackingFragmentsForCollegeMajors');

// Route::get('/setCollegeDataForApis', 'UtilityController@setCollegeDataForApis');
// Route::get('/updateCollegeDataForApi', 'UtilityController@updateCollegeDataForApi');

// Route::get('/sendHolidayEmail', 'UtilityController@sendHolidayEmail');
Route::get('/newRevenueReport', 'BetaUserController@newRevenueReport');
Route::get('/nowruz', 'UtilityController@nowruz');

Route::get('/social/get-user-data', 'SocialController@getUser');
Route::get('/testId', 'Controller@testId');
