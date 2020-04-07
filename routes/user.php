<?php
// Home page
// Route::get('/home', 'HomeController@index');

// oneapp/college_application routes
Route::get('/college-application', 'CollegeController@collegeApplication');
Route::get('/college-application/basic', 'CollegeController@collegeApplication');
Route::get('/college-application/planned_start', 'CollegeController@collegeApplication');
Route::get('/college-application/identity', 'CollegeController@collegeApplication');
Route::get('/college-application/contact', 'CollegeController@collegeApplication');
Route::get('/college-application/citizenship', 'CollegeController@collegeApplication');
Route::get('/college-application/financials', 'CollegeController@collegeApplication');
Route::get('/college-application/gpa', 'CollegeController@collegeApplication');
Route::get('/college-application/scholarships', 'CollegeController@collegeApplication');
Route::get('/college-application/scholarships-thanks', 'CollegeController@collegeApplication');

Route::get('/college-application/scores', 'CollegeController@collegeApplication');
Route::get('/college-application/colleges', 'CollegeController@collegeApplication');
Route::get('/college-application/family', 'CollegeController@collegeApplication');
Route::get('/college-application/uploads', 'CollegeController@collegeApplication');
Route::get('/college-application/courses', 'CollegeController@collegeApplication');
Route::get('/college-application/awards', 'CollegeController@collegeApplication');
Route::get('/college-application/clubs', 'CollegeController@collegeApplication');
Route::get('/college-application/essay', 'CollegeController@collegeApplication');
Route::get('/college-application/sponsor', 'CollegeController@collegeApplication');
Route::get('/college-application/review', 'CollegeController@collegeApplication');
Route::get('/college-application/submit', 'CollegeController@collegeApplication');
Route::get('/college-application/study', 'CollegeController@collegeApplication');
Route::get('/college-application/additional_info', 'CollegeController@collegeApplication');
Route::get('/college-application/declaration', 'CollegeController@collegeApplication');
Route::post('/college-application/removeTranscriptAttachment', 'AjaxController@removeTranscriptAttachment');
Route::get('/college-application/scholarships', 'CollegeController@collegeApplication');



//scholarships application routes
// Route::get('/scholarships-application', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/basic', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/planned_start', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/identity', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/contact', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/citizenship', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/financials', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/gpa', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/scholarships', 'CollegeController@collegeApplication');
// Route::get('/scholarships-application/scores', 'CollegeController@collegeApplication');

// premium plans/checkout routes
// Route::get('/premium-plans', 'CollegeController@userPremiumPlans');
Route::get('/checkout/premium', 'ProductsController@checkout');
Route::get('/indian-checkout/premium', 'ProductsController@checkout');
Route::get('/indian-checkout/premium-plus', 'ProductsController@checkout');
Route::get('/indian-checkout/monthly', 'ProductsController@checkout');
Route::get('/indian-checkout/onetime_unlimited', 'ProductsController@checkout');

Route::get('/checkout/premium-plus', 'ProductsController@checkout');
Route::get('/checkout/monthly', 'ProductsController@checkout');
Route::get('/checkout/onetime_unlimited', 'ProductsController@checkout');
Route::get('/payment-success/{plan?}', 'ProductsController@checkout');
Route::get('/payment-failed', 'ProductsController@checkout');

// Route for notifications
Route::get( '/notifications', 'NotificationController@index' );

// profile/Me routes
Route::get('profile','ProfilePageController@getIndex');
Route::get('profile/edit_public','ProfilePageController@getIndex');
Route::get('profile/documents','ProfilePageController@getIndex');

//old profile routes
Route::post('profile/setUserCountry', 'ProfilePageController@setUserCountry');
Route::post('profile/setProfileIntlCountryChange', 'ProfilePageController@setProfileIntlCountryChange');
Route::get('/profile/score', 'ProfilePageController@score');
Route::get('/profile/highschool', 'ProfilePageController@highschool');
Route::get('/profile/college', 'ProfilePageController@college');
Route::get('/profile/experience', 'ProfilePageController@experience');
Route::get('/profile/skills', 'ProfilePageController@skills');
Route::get('/profile/interests', 'ProfilePageController@interests');
Route::get('/profile/cluborg', 'ProfilePageController@cluborg');
Route::get('/profile/honoraward', 'ProfilePageController@honoraward');
Route::get('/profile/language', 'ProfilePageController@language');
Route::get('/profile/certifications', 'ProfilePageController@certifications');
Route::get('/profile/patents', 'ProfilePageController@patents');
Route::get('/profile/publications', 'ProfilePageController@publications');



//auth route filter only allows users that are signed in to signout
Route::get('/signout/{is_api?}', 'AuthController@signOut');

//comehere
// Routes for Setting Page
Route::any('/settings/{page?}', 'SettingController@getIndex');

Route::get('/setting/accountSetting/{token}', 'SettingController@getAccountSettinInfo');
Route::post('/setting/accountSetting', 'SettingController@postAccountSettinInfo');

// Routes for sending invites
Route::post('/ajax/sendInvites', 'InviteController@sendInvites');
Route::post('/ajax/sendSingleInvite', 'InviteController@sendSingleInvite');
Route::post('/ajax/sendReferralInvitesByQueue', 'InviteController@sendReferralInvitesByQueue');

Route::get('/ajax/getImportedStudents', 'SettingController@getImportedStudents');
Route::post('/settings/billing', 'OmniPayController@createCustomer');
Route::post('/setting/deleteUserAccount', 'AjaxController@deleteUserAccount');
Route::get('/setting/getInvoiceForUsers', 'AjaxController@getInvoiceForUsers');
Route::get('/setting/getInvoiceForAdmin', 'AjaxController@getInvoiceForAdmin');
Route::post('/createCustomer', 'OmniPayController@createCustomer');

Route::get('/setting/getPayPal', 'OmniPayController@getPayPal');
Route::get('/setting/payPalCallBack', 'OmniPayController@paypalCallBack');

Route::post('/chargeCustomer', 'OmniPayController@chargeCustomer');
Route::post('/createCustomer', 'OmniPayController@createCustomer');

Route::post('/setting/togglePremiumUserRecurring', 'OmniPayController@togglePremiumUserRecurring');
Route::post('/setting/toggleAdminUserRecurring', 'OmniPayController@toggleAdminUserRecurring');

Route::post('/settings/save/saveUserAccountPrivacy', 'SettingController@saveUserAccountPrivacy');
Route::post('/settings/save/saveEmailNotifications', 'SettingController@saveEmailNotifications');
Route::post('/settings/save/savePhoneInfo', 'SettingController@savePhoneInfo');
Route::post('/settings/save/saveEditedPhoneNumber', 'SettingController@saveEditedPhoneNumber');
Route::post('/settings/save/optInUserForText', 'SettingController@optInUserForText');
Route::post('/settings/save/deleteUsersPhoneNumber', 'SettingController@deleteUsersPhoneNumber');
Route::post('/settings/save/saveDataPreferences', 'SettingController@saveDataPreferences');

// Portal message List calls
Route::get('/portal/{section?}', 'PortalController@Index');
Route::get('/portal/messages/{org_id?}/{type?}/{thread_id?}', 'PortalController@portalMessageCenter');
Route::get('/ajax/portal/messages/{org_id?}/{type?}/{thread_id?}', 'PortalController@getMessageCenter');

Route::get('/portal/ajax/messages/getUserMsg/{userid}', 'PortalController@messageIndex');
Route::get('/portal/ajax/messages/getNewMsgs/{threadid}/{lastMsgId?}', 'Controller@getUserMessages');
Route::get('/portal/ajax/messages/getUserNewTopics/{receiver_id?}/{type?}/{thread_id?}', 'PortalController@getThreadListHeartBeat');
Route::post('/portal/ajax/messages/postMsg/{thread_id}/{userid}/{type}', 'PortalController@postMessage');
Route::get('/portal/ajax/messages/setMsgRead/{threadid}', 'Controller@setMsgRead');
Route::post('/portal/ajax/messages/getMyCounselorThread', 'Controller@getMyCounselorThread');
Route::post('/portal/ajax/messages/createThread', 'Controller@createThread');
Route::post('/portal/ajax/messages/setReadTime', 'Controller@setReadTime');
Route::post('/portal/ajax/messages/addUserToThread', 'Controller@addUserToThread');

Route::get('/portal/ajax/getUserScholarships' , 'ScholarshipsController@getUsersScholarships');

//new signup process routes - get_started routes
Route::get('get_started/{step?}','GetStartedController@index');
Route::get('/get_step_status/{step?}', 'GetStartedController@getStepStatus');
Route::get('get_user_name','GetStartedController@getusername');
Route::post('get_started/save','GetStartedController@save');
Route::post('get_started/saveEmail','GetStartedController@saveEmail');
Route::get('get_started/getDataFor/{name}','GetStartedController@getDataFor');
Route::post('get_started/searchFor/{name}','GetStartedController@searchFor');
Route::post('get_started/getRecruitedStepDone','GetStartedController@getRecruitedStepDone');
Route::post('get_started/upgradeMembershipStepDone','GetStartedController@upgradeMembershipStepDone');
Route::post('get_started/getGradeCountries','GetStartedController@getGradeCountries');
Route::post('get_started/getGradeConversions','GetStartedController@getGradeConversions');
Route::post('get_started/checkPhoneConfirmation','TwilioController@checkPhoneConfirmation');
Route::post('get_started/sendPhoneConfirmation','TwilioController@sendPhoneConfirmation');
Route::post('get_started/saveNewPhone','GetStartedController@saveNewPhone');
Route::post('get_started/savePickACollegeView', 'AjaxController@savePickACollegeView');

// Social Networking routes

Route::get('/home', 'SocialController@index');
Route::get('/post/{id}', 'SocialController@index');
Route::get('/social/link-preview-info', 'SocialController@getLinkPreview');
Route::get('/social/networking', 'SocialController@index');
Route::get('/social/networking/{slug}', 'SocialController@index');
Route::get('/social/networking/{slug}/{totalContacts}', 'SocialController@index');
Route::get('/social/article-editor', 'SocialController@index');
Route::get('/social/article-editor/{id}', 'SocialController@index');
Route::get('/social/article-dashboard', 'SocialController@index');
Route::get('/social/article/{id}', 'SocialController@index');
Route::get('/social/profile/{id}', 'SocialController@index');
Route::get('/social/edit-profile', 'SocialController@index');
Route::get('/social/document-profile', 'SocialController@index');
Route::get('/social/me', 'SocialController@index');
Route::get('/social/notifications', 'SocialController@index');
Route::get('/social/settings/{page?}', 'SocialController@settings');
Route::post('/social/settings/{page?}', 'SocialController@changePassword');
Route::get('/social/setting-data', 'SocialController@getSettingData');
Route::get('/social/messages', 'SocialController@index');
Route::get('/social/messages/{id}', 'SocialController@index');

Route::get('/social/manage-colleges', 'SocialController@index');
Route::get('/social/manage-colleges/application', 'SocialController@index');
Route::get('/social/manage-colleges/scholarship', 'SocialController@index');
Route::get('/social/manage-colleges/favorites', 'SocialController@index');
Route::get('/social/manage-colleges/rec-by-plex', 'SocialController@index');
Route::get('/social/manage-colleges/colleges-rec', 'SocialController@index');
Route::get('/social/manage-colleges/colleges-view', 'SocialController@index');
Route::get('/social/manage-colleges/trash', 'SocialController@index');
Route::get('/social/manage-colleges/getAutoCompleteSearchForPortalAddColleges', 'AutoComplete@getTopSearchAutocomplete');

// Social Mobile
Route::get('/social/mbl-messages', 'SocialController@index');
Route::get('/social/mbl-networking', 'SocialController@index');
Route::get('/social/mbl-manage-colleges', 'SocialController@index');

// Social New OneApp
Route::get('/social/one-app', 'SocialController@index');
Route::get('/social/one-app/basic', 'SocialController@index');
Route::get('/social/one-app/start', 'SocialController@index');
Route::get('/social/one-app/identity', 'SocialController@index');
Route::get('/social/one-app/contact', 'SocialController@index');
Route::get('/social/one-app/verify', 'SocialController@index');
Route::get('/social/one-app/citizenship', 'SocialController@index');
Route::get('/social/one-app/financials', 'SocialController@index');
Route::get('/social/one-app/gpa', 'SocialController@index');
Route::get('/social/one-app/scholarships', 'SocialController@index');
Route::get('/social/one-app/scholarships-thanks', 'SocialController@index');
Route::get('/social/one-app/scores', 'SocialController@index');
Route::get('/social/one-app/colleges', 'SocialController@index');
Route::get('/social/one-app/family', 'SocialController@index');
Route::get('/social/one-app/uploads', 'SocialController@index');
Route::get('/social/one-app/courses', 'SocialController@index');
Route::get('/social/one-app/awards', 'SocialController@index');
Route::get('/social/one-app/clubs', 'SocialController@index');
Route::get('/social/one-app/demographics', 'SocialController@index');
Route::get('/social/one-app/essay', 'SocialController@index');
Route::get('/social/one-app/sponsor', 'SocialController@index');
Route::get('/social/one-app/review', 'SocialController@index');
Route::get('/social/one-app/submit', 'SocialController@index');
Route::get('/social/one-app/study', 'SocialController@index');
Route::get('/social/one-app/additional_info', 'SocialController@index');
Route::get('/social/one-app/declaration', 'SocialController@index');
Route::post('/social/one-app/removeTranscriptAttachment', 'AjaxController@removeTranscriptAttachment');
Route::get('/social/one-app/scholarships', 'SocialController@index');
Route::get('/social/one-app/applications', 'SocialController@index');

Route::get('/social/view-student-application/', 'CollegeController@viewStudentApplication');
Route::get('/social/view-student-application/{user_id}', 'CollegeController@viewStudentApplication');

// Social App Scholarship API routes
Route::post('/queueScholarship', 'NewScholarshipsController@queueScholarship');

// Social Network API routes
Route::get('/social/getNetworkingSuggestions', 'SocialController@getNetworkingSuggestions');
Route::post('/social/get-home-posts', 'SocialController@getHomePosts');

Route::post('/social/get-user-profile', 'SocialController@getProfile');
Route::post('/social/get-profile-completeness', 'SocialController@getProfileCompleteness');
Route::post('/social/get-profile-posts', 'SocialController@getProfilePosts');
Route::post('/social/save-public-profile-settings', 'SocialController@savePublicProfileSettings');

Route::get('/social/get-single-post', 'SocialController@getSinglePost');
Route::get('/social/get-network-users', 'SocialController@networkUsers');

Route::post('/social/save-hide-post-article', 'SocialController@hidePostArticle');
Route::post('/social/undo-hide-post-article', 'SocialController@undoHidePostArticle');

Route::post('/social/save-post', 'SocialController@savePost');
Route::post('/social/update-post-share', 'SocialController@updatePostShareCount');
Route::delete('/social/delete-post', 'SocialController@deletePost');
Route::post('/social/update-post-status', 'SocialController@updatePostStatus');
Route::post('/social/save-post-comment', 'SocialController@savePostComment');
Route::post('/social/add-like', 'SocialController@addLikes');
Route::post('/social/remove-like', 'SocialController@removeLikes');
Route::post('/social/getPostLikes', 'SocialController@getPostLikes');

Route::get('/social/get-single-articles', 'SocialController@getSingleArticle');
Route::get('/social/get-articles', 'SocialController@getArticles');
Route::post('/social/save-article', 'SocialController@saveArticle');
Route::post('/social/update-article', 'SocialController@updateArticle');
Route::post('/social/update-article-shares', 'SocialController@updateSharedArticleCount');
Route::delete('/social/delete-article', 'SocialController@deleteArticle');
Route::post('/social/save-article-comment', 'SocialController@savePostComment');

Route::delete('/social/delete-comments', 'SocialController@deleteComments');
Route::post('/social/edit-comments', 'SocialController@updateComment');

Route::post('/social/add-friend', 'SocialController@addFriend');
Route::get('/social/get-friends-list', 'SocialController@getFriends');
Route::post('/social/decline-friend', 'SocialController@declineFriend');
Route::post('/social/cancel-friend', 'SocialController@cancelFriend');
Route::post('/social/friend-status', 'SocialController@friendStatus');
Route::post('/social/read-notification', 'SocialController@readNotification');
Route::post('/social/add-thread', 'SocialController@addThread');
Route::post('/social/type-message', 'SocialController@typeMsg');
Route::post('/social/cancel-typing', 'SocialController@cancelTyping');

// Route::post('/social/save-message', 'SocialController@saveMsg');

Route::get('/social/get-test', 'SocialController@getTest');

Route::get('/social/get-imported-contacts', 'SocialController@getImportedContacts');

// Skills Endorsment
Route::post('/social/saveMyEndorsement', 'SocialController@setMyEndorsment');

// Abuse
Route::post('/social/addAbuser', 'SocialController@addAbuser');


