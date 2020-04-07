<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\StartController, App\Http\Controllers\CronEmailController, App\Http\Controllers\CollegeRecommendationController;
use App\Http\Controllers\MandrillAutomationController, App\Http\Controllers\Controller, App\Http\Controllers\EmailParserController;
use App\Http\Controllers\GroupMessagingController, App\Http\Controllers\TwilioController, App\Http\Controllers\UtilityController;
use App\Http\Controllers\AjaxController, App\Http\Controllers\DistributionController;
use App\Http\Controllers\SalesController, App\Http\Controllers\TrackingPageController;


use App\Jobs\RunRevenueReportCronJobMonthly, App\Jobs\RunRevenueReportCronJob, App\Jobs\PartnerEmailCronJobQueue, App\Jobs\TrackingPageSetTrackingUrl;

use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // $startcontroller = new StartController();
        // $startcontroller->setupcore();

        \Event::listen('cron.collectJobs', function() {

            // \Cron::add('emailInternalCollegesLiveChat', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesLiveChat();

            //     return $ret;
            // });

            // \Cron::add('emailInternalCollegesScholarship', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesScholarship();

            //     return $ret;
            // });

            // \Cron::add('emailInternalCollegesTexting', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesTexting();

            //     return $ret;
            // });

            // \Cron::add('emailInternalCollegesCalling', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesCalling();

            //     return $ret;
            // });

            // \Cron::add('emailInternalCollegesChatBot', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesChatBot();

            //     return $ret;
            // });

            // \Cron::add('emailInternalCollegesRecruitment', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesRecruitment();

            //     return $ret;
            // });

            // \Cron::add('emailInternalCollegesTCPA', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesTCPA();

            //     return $ret;
            // });
            
            \Cron::setDisablePreventOverlapping();

            // \Cron::add('getBirthdayUsingAccurateAppend', '*/3 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->getBirthdayUsingAccurateAppend();

            //     return $ret;
            // });

            // \Cron::add('getAddressUsingAccurateAppend', '*/3 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->getAddressUsingAccurateAppend();

            //     return $ret;
            // });

            // \Cron::add('manualNrccua', '* * * * *', function(){
            //     $mac = new DistributionController;
            //     $ret = $mac->manualNrccua();

            //     return $ret;
            // });
            
            // \Cron::add('manualNrccuaRecruitment', '* * * * *', function(){
            //     $mac = new DistributionController;
            //     $ret = $mac->manualNrccuaRecruitment();

            //     return $ret;
            // });

            \Cron::add('updateCollegeDataForApi', '0 0 15 * *', function(){
                $mac = new UtilityController;
                $ret = $mac->updateCollegeDataForApi();

                return $ret;
            });
            
            \Cron::add('setCollegeDataForApis', '0 */7 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->setCollegeDataForApis();

                return $ret;
            });
            
            \Cron::add('setTrackingFragmentsForCollegeMajors', '*/13 * * * *', function(){
                $mac = new TrackingPageController;
                $ret = $mac->setTrackingFragmentsForCollegeMajors();

                return $ret;
            });

            \Cron::add('setTrackingFragmentsForCollegePages', '*/13 * * * *', function(){
                $mac = new TrackingPageController;
                $ret = $mac->setTrackingFragmentsForCollegePages();

                return $ret;
            });

            \Cron::add('sendManualNRCCUA', '*/10 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->sendManualNRCCUA();

                return $ret;
            });

            \Cron::add('TrackingPageSetTrackingUrl', '*/7 * * * *', function(){
                // $ten_mins = Carbon::now()->addMinutes(10);

                // $mac = TrackingPageSetTrackingUrl::dispatch()
                //                                  ->delay($ten_mins);

                $mac = new TrackingPageController;
                $ret =  $mac->setTrackingUrls();

                return $ret;
            });
            
            \Cron::add('assignProfileFlowAssignment', '45 16 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->assignProfileFlowAssignment();

                return $ret;
            });

            \Cron::add('assignProfileFlowAssignmentIntl', '15 17 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->assignProfileFlowAssignmentIntl();

                return $ret;
            });

            \Cron::add('updateTimeZoneQueries', '15 16 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->updateTimeZoneQueries();

                return $ret;
            });

            \Cron::add('addUsersToEmailLogicHelper', '*/11 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->addUsersToEmailLogicHelper();

                return $ret;
            });

            \Cron::add('userEngagementEmailProcess_us_non_complete', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->userEngagementEmailProcess('us_non_complete');

                return $ret;
            });

            \Cron::add('userEngagementEmailProcess_us_complete', '14 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->userEngagementEmailProcess('us_complete');

                return $ret;
            });
            
            \Cron::add('userEngagementEmailProcess_non_us_non_complete', '30 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->userEngagementEmailProcess('non_us_non_complete');

                return $ret;
            });

            \Cron::add('setNrccuaCronSchedule', '8 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->setNrccuaCronSchedule();

                return $ret;
            });
            
            \Cron::add('fixIpLocation', '*/7 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->fixIpLocation();

                return $ret;
            });

            \Cron::add('populateUsersIpLocations', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->populateUsersIpLocations();

                return $ret;
            });

            \Cron::add('getNrccuaClicks', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getNrccuaClicks();

                return $ret;
            });

            \Cron::add('getNrccuaClicks2', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getNrccuaClicks(2);

                return $ret;
            });

            \Cron::add('getEmailClicks', '0 */2 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getEmailClicks();

                return $ret;
            });

            \Cron::add('getEmailClicks2', '0 */2 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getEmailClicks(2);

                return $ret;
            });

            \Cron::add('getEmailOpens', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getEmailOpens();

                return $ret;
            });

            \Cron::add('getEmailOpens2', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getEmailOpens(2);

                return $ret;
            });

            \Cron::add('updateHelperTableUnsub', '0 */4 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->updateHelperTableUnsub();

                return $ret;
            });

            \Cron::add('updateIsCompleteProfile', '0 */2 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->updateIsCompleteProfile();

                return $ret;
            });

            \Cron::add('updateIsCompleteProfileIntl', '0 */2 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->updateIsCompleteProfileIntl();

                return $ret;
            });
            
            \Cron::add('isUserDuplicate', '30 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->isUserDuplicate();

                return $ret;
            });

            // \Cron::add('SalesController_getUserStats', '*/11 * * * *', function(){
            //     $mac = new SalesController;
            //     $ret = $mac->getUserStats();

            //     return $ret;
            // });

            // \Cron::add('SalesController_getUserInviteStats', '*/11 * * * *', function(){
            //     $mac = new SalesController;
            //     $ret = $mac->getUserInviteStats();

            //     return $ret;
            // });

            \Cron::add('SalesController_getUserStats_today_yesterday', '0 * * * *', function(){
                
                $start_date = Carbon::now()->today()->toDateString();
                $end_date   = Carbon::now()->today()->toDateString();

                $mac = new SalesController;
                $ret = $mac->getUserStats($start_date, $end_date);

                $start_date = Carbon::now()->today()->subDay(1)->toDateString();
                $end_date   = Carbon::now()->today()->subDay(1)->toDateString();

                $mac = new SalesController;
                $ret = $mac->getUserStats($start_date, $end_date);

                return $ret;
            });

            \Cron::add('SalesController_getUserInviteStats_today_yesterday', '0 * * * *', function(){
                
                $start_date = Carbon::now()->today()->toDateString();
                $end_date   = Carbon::now()->today()->toDateString();

                $mac = new SalesController;
                $ret = $mac->getUserInviteStats($start_date, $end_date);

                $start_date = Carbon::now()->today()->subDay(1)->toDateString();
                $end_date   = Carbon::now()->today()->subDay(1)->toDateString();

                $mac = new SalesController;
                $ret = $mac->getUserInviteStats($start_date, $end_date);
                
                return $ret;
            });

            \Cron::add('sendRecommendationForRevenueSchoolMatching', '*/13 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->sendRecommendationForRevenueSchoolMatching();

                return $ret;
            });
            
            \Cron::add('fixUsersState', '07 06 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->fixUsersState();

                return $ret;
            });
            
            \Cron::add('generateScholarshipRecommendation', '00 07 * * *', function(){
                $mac = new CollegeRecommendationController;
                $ret = $mac->generateScholarshipRecommendation();

                return $ret;
            });

            \Cron::add('sendEmailToUsersWhoReceivedScholarshipRecommendation', '07 05 * * *', function(){
                $mac = new CollegeRecommendationController;
                $ret = $mac->sendEmailToUsersWhoReceivedScholarshipRecommendation();

                return $ret;
            });

            // setGusAccessToken
            \Cron::add('setGusAccessToken', '*/15 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->setGusAccessToken();

                return $ret;
            });

            // addZipAndDedupEAB
            // \Cron::add('addZipAndDedupEAB', '*/5 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->addZipAndDedupEAB();

            //     return $ret;
            // });

            // sendCollegeXpressLeads post leads.
            \Cron::add('sendCollegeXpressLeads', '*/2 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->sendCollegeXpressLeads();

                return $ret;
            });

            // sendCollegeXpressLeadsIntl post leads.
            \Cron::add('sendCollegeXpressLeadsIntl', '* * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->sendCollegeXpressLeadsIntl();

                return $ret;
            });

            // Add users to converted buckets for support account for post clients
            \Cron::add('addUsersToConvertedBucketInSupportAccount_post', '*/13 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->addUsersToConvertedBucketInSupportAccount('post');

                return $ret;
            });

            // Add users to converted buckets for support account for click clients
            \Cron::add('addUsersToConvertedBucketInSupportAccount_click', '*/7 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->addUsersToConvertedBucketInSupportAccount('click');

                return $ret;
            });

            // Add users to inquiries buckets for support account for click clients
            \Cron::add('addUsersToConvertedBucketInSupportAccount_click_inquiries', '*/7 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->addUsersToConvertedBucketInSupportAccount('click', 'inquiries');

                return $ret;
            });

            // Auto upload College logos
            // \Cron::add('autoUploadLogoCollege', '*/2 * * * *', function(){
            //     $util = new UtilityController;
            //     $ret = $util->autoUploadLogoCollege();

            //     return $ret;
            // });


            // Auto upload College overview images
            \Cron::add('autoUploadCollegeOverviewImage', '*/7 * * * *', function(){
                $util = new UtilityController;
                $ret = $util->autoUploadCollegeOverviewImage();

                return $ret;
            });

            // Beta page revenue report page
            \Cron::add('runRevenueReportCronJob', '*/13 * * * *', function(){
                $ret = RunRevenueReportCronJob::dispatch();
                
                return $ret;
            });

            // Beta page revenue report page
            \Cron::add('runRevenueReportCronJobMonthly', '*/59 * * * *', function(){
                $ret = RunRevenueReportCronJobMonthly::dispatch();

                return $ret;
            });

            // this method adds users to keypath portal on support account
            \Cron::add('addKeypathUsersToSupportAccount', '*/19 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->addKeypathUsersToSupportAccount();

                return $ret;
            });

            // Ebureau Appending cron job
            \Cron::add('getAddressUsingEbureau', '*/10 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->getAddressUsingEbureau();

                return $ret;
            });

            \Cron::add('runClustersForAllUsers', '*/3 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->runClustersForAllUsers();

                return $ret;
            });
            
            \Cron::add('fixCappexMatchesForGenderAndZip', '* * * * *', function() {
                $mac = new DistributionController;

                $ret = $mac->fixCappexMatchesForGenderAndZip();

                return $ret;
            });

            \Cron::add('sendDailyClientReport', '30 03 * * *', function() {
                $mac = new MandrillAutomationController;

                $ret = $mac->sendDailyClientReport();

                return $ret;
            });

            \Cron::add('sendDailyCollegeInquiriesReport', '30 04 * * *', function() {
                $mac = new MandrillAutomationController;

                $ret = $mac->sendDailyCollegeInquiriesReport();

                return $ret;
            });

            \Cron::add('sendDailyCRMReport', '00 04 * * *', function() {
                $mac = new MandrillAutomationController;

                $ret = $mac->sendDailyCRMReport();

                return $ret;
            });

            \Cron::add('sendPassthroughReminders', '*/43 * * * *', function() {
                $mac = new MandrillAutomationController;
                $ret = $mac->sendPassthroughReminders();

                return $ret;
            });

            // Remainder to send clients to give us revenue numbers.
            \Cron::add('sendClientWeeklyRemainder', '00 05 * * Thu', function() {
                $mac = new MandrillAutomationController;
                $ret = $mac->sendClientWeeklyRemainders();

                return $ret;
            });

            // \Cron::add('cleanUpUserInvitesTable', '*/3 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->cleanUpUserInvitesTable();

            //     return $ret;
            // });

            \Cron::add('importSendGridSuppression', '0 */6 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->importSendGridSuppression();

                return $ret;
            });

            \Cron::add('emailInternalPlexussEmailForAnalytics', '*/16 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalPlexussEmailForAnalytics();

                return $ret;
            });

            \Cron::add('autoTurnoffPostingLeadsCappexCappex', '*/2 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoTurnoffPostingLeadsCappexCappex();

                return $ret;
            });

            \Cron::add('autoTurnoffPostingLeadsNrccua', '*/3 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoTurnoffPostingLeadsNrccua();

                return $ret;
            });
            
            \Cron::add('autoPostingCappexUsersWhoSelectedAPickACollege', '* * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingCappexUsersWhoSelectedAPickACollege();

                return $ret;
            });

            \Cron::add('addToEmailSuppressionQueue_new_transactional', '*/2 * * * *', function(){
                $key = 'e9c1ef34b21eb57bed7fe55c766db78329466053';
                $type = 'transactional';

                $mac = new UtilityController;
                $ret = $mac->addToEmailSuppressionQueue($type, $key);

                return $ret;
            });

            \Cron::add('addToEmailSuppressionQueue_new_non_transactional', '*/2 * * * *', function(){
                $key = 'e9c1ef34b21eb57bed7fe55c766db78329466053';
                $type = 'non_transactional';

                $mac = new UtilityController;
                $ret = $mac->addToEmailSuppressionQueue($type, $key);

                return $ret;
            });

            \Cron::add('addToEmailSuppressionQueue_old_transactional', '*/2 * * * *', function(){
                $key = '1ea5c03655ad330db691c648646d756a80784e76';
                $type = 'transactional';

                $mac = new UtilityController;
                $ret = $mac->addToEmailSuppressionQueue($type, $key);

                return $ret;
            });

            \Cron::add('addToEmailSuppressionQueue_old_non_transactional', '*/2 * * * *', function(){
                $key = '1ea5c03655ad330db691c648646d756a80784e76';
                $type = 'non_transactional';

                $mac = new UtilityController;
                $ret = $mac->addToEmailSuppressionQueue($type, $key);

                return $ret;
            });

            \Cron::add('procInsertUsersProfileCompletion', '00 00 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->procInsertUsersProfileCompletion();

                return $ret;
            });

            \Cron::add('autoPostingInquiries_get_started', '*/3 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingInquiries('get_started');

                return $ret;
            }); 

            \Cron::add('autoPostingInquirie', '*/7 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingInquiries();

                return $ret;
            }); 

            \Cron::add('sendNrccuaQueue', '*/5 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->sendNrccuaQueue();

                return $ret;
            });

            \Cron::add('addToNrccuaQueue', '*/7 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->addToNrccuaQueue();

                return $ret;
            });
            

            \Cron::add('autoPostingCappex_pick_a_college_views', '*/2 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingCappex('pick_a_college_views');

                return $ret;
            });

            \Cron::add('autoPostingCappex_all_users', '*/4 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingCappex('all_users');

                return $ret;
            });

            \Cron::add('autoPostingCappex_in_college_not_set', '*/13 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingCappex('in_college_not_set');

                return $ret;
            });

            \Cron::add('autoPostingCappex_in_college_user', '*/7 * * * *', function(){
                $mac = new DistributionController;
                $ret = $mac->autoPostingCappex('in_college_user');

                return $ret;
            });

            \Cron::add('unsubInternalEmailColleges', '*/2 * * * *', function(){
                $mac = new EmailParserController;
                $ret = $mac->unsubInternalEmailColleges();

                return $ret;
            });

            \Cron::add('emailInternalCollegesVCs', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesVCs();

                return $ret;
            });

            // \Cron::add('emailInternalCollegesPresidents', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailInternalCollegesPresidents();

            //     return $ret;
            // });

            // \Cron::add('emailIntlStudentLeads', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->emailIntlStudentLeads();

            //     return $ret;
            // });

            \Cron::add('emailIntlAgentWebinar', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailIntlAgentWebinar();

                return $ret;
            });

            \Cron::add('emailIntlNavitas', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailIntlNavitas();

                return $ret;
            });

            \Cron::add('emailInternalCollegesSEEDFunding', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesSEEDFunding();

                return $ret;
            });

            \Cron::add('customForSchoolsEnrollmentDepartmentCron', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->customForSchoolsEnrollmentDepartmentCron();

                return $ret;
            });
            
            \Cron::add('emailInternalCollegesSecondaryEmail', '*/3 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesSecondaryEmail();

                return $ret;
            });

            \Cron::add('emailInternalCollegesThirdEmail', '*/3 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesThirdEmail();

                return $ret;
            });

            \Cron::add('emailInternalCollegesVirtualCollegeApplicationSystem', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesVirtualCollegeApplicationSystem();

                return $ret;
            });

            \Cron::add('emailInternalCollegesFBMarketing', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesFBMarketing();

                return $ret;
            });

            \Cron::add('emailInternalCollegesAdvertising', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesAdvertising();

                return $ret;
            });

            \Cron::add('emailInternalCollegesCRM', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesCRM();

                return $ret;
            });

            \Cron::add('emailInternalCollegesHigherEducationEngineeringServices', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesHigherEducationEngineeringServices();

                return $ret;
            });

            \Cron::add('emailInternalCollegesDataScienceAndRegressionAnalysis', '*/2 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->emailInternalCollegesDataScienceAndRegressionAnalysis();

                return $ret;
            });
            // temp remove
            \Cron::add('edxEmailCronJob_1', '*/17 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->edxEmailCronJob(1);

                return $ret;
            });

            \Cron::add('edxEmailCronJob_2', '*/17 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->edxEmailCronJob(2);

                return $ret;
            });

            \Cron::add('edxEmailCronJob_3', '*/17 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->edxEmailCronJob(3);

                return $ret;
            });

            // \Cron::add('edxEmailCronJobRepeat_2', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxEmailCronJobRepeat(2, 1);

            //     return $ret;
            // });

            // \Cron::add('edxEmailCronJobRepeat_3', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxEmailCronJobRepeat(2, 2);

            //     return $ret;
            // });


            // \Cron::add('edxEmailCronJobRepeat_1_1', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxEmailCronJobRepeat(1, 1);

            //     return $ret;
            // });

            // \Cron::add('edxEmailCronJobRepeat_1_2', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxEmailCronJobRepeat(1, 2);

            //     return $ret;
            // });

            // \Cron::add('edxEmailCronJobRepeat_3_1', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxEmailCronJobRepeat(3, 1);

            //     return $ret;
            // });

            // \Cron::add('edxEmailCronJobRepeat_3_2', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxEmailCronJobRepeat(3, 2);

            //     return $ret;
            // });

            // Auto portal emails (Engagement emails) start here
            // Alliant
            // \Cron::add('autoPortalEmail_school_viewed_you_alliant', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'alliant');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_alliant', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'alliant');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_alliant', '*/5 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'alliant');

            //     return $ret;
            // });

            // EDX
            // \Cron::add('autoPortalEmail_school_viewed_you_edx', '*/27 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'edx');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_edx', '*/19 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'edx');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_edx', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'edx');

            //     return $ret;
            // });

            // Shorelight
            // \Cron::add('autoPortalEmail_school_viewed_you_shorelight', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'shorelight');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_shorelight', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'shorelight');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_shorelight', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'shorelight');

            //     return $ret;
            // });

            // NRCCUA
            // \Cron::add('autoPortalEmail_school_viewed_you_nrccua', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'nrccua');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_nrccua', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'nrccua');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_nrccua', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'nrccua');

            //     return $ret;
            // });

            //qs_grad
            // \Cron::add('autoPortalEmail_school_viewed_you_qs_grad', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'qs_grad');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_qs_grad', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'qs_grad');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_qs_grad', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'qs_grad');

            //     return $ret;
            // });

            //qs_mba
            // \Cron::add('autoPortalEmail_school_viewed_you_qs_mba', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'qs_mba');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_qs_mba', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'qs_mba');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_qs_mba', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'qs_mba');

            //     return $ret;
            // });

            //cornellcollege
            // \Cron::add('autoPortalEmail_school_viewed_you_cornellcollege', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'cornellcollege');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_cornellcollege', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'cornellcollege');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_cornellcollege', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'cornellcollege');

            //     return $ret;
            // });

            //eddy_click GCU
            // \Cron::add('autoPortalEmail_school_viewed_you_eddy_click_GCU', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'eddy_click', 105);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_eddy_click_GCU', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'eddy_click', 105);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_eddy_click_GCU', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'eddy_click', 105);

            //     return $ret;
            // });

            //eddy_click CALU
            // \Cron::add('autoPortalEmail_school_viewed_you_eddy_click_CALU', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'eddy_click', 6698);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_eddy_click_CALU', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'eddy_click', 6698);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_eddy_click_CALU', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'eddy_click', 6698);

            //     return $ret;
            // });

            //eddy_click POST
            // \Cron::add('autoPortalEmail_school_viewed_you_eddy_click_POST', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'eddy_click', 726);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_eddy_click_POST', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'eddy_click', 726);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_eddy_click_POST', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'eddy_click', 726);

            //     return $ret;
            // });

            //eddy_reg
            // \Cron::add('autoPortalEmail_school_viewed_you_eddy_reg', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'eddy_reg');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_eddy_reg', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'eddy_reg');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_eddy_reg', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'eddy_reg');

            //     return $ret;
            // });

            // USF Engagement
            // \Cron::add('autoPortalEmail_school_viewed_you_usf', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'usf');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_usf', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'usf');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_usf', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'usf');

            //     return $ret;
            // });

            /* STUDY GROUP BEGINS HERE */
            // study_group Royal_Roads_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Royal_Roads_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 262227);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Royal_Roads_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 262227);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Royal_Roads_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 262227);

            //     return $ret;
            // });


            // study_group The_City_College_of_New_York
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_The_City_College_of_New_York', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 2573);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_The_City_College_of_New_York', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 2573);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_The_City_College_of_New_York', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 2573);

            //     return $ret;
            // });


            // study_group James_Madison_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_James_Madison_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 4121);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_James_Madison_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 4121);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_James_Madison_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 4121);

            //     return $ret;
            // });


            // study_group Lipscomb_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Lipscomb_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 3737);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Lipscomb_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 3737);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Lipscomb_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 3737);

            //     return $ret;
            // });


            // study_group Merrimack_College
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Merrimack_College', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 1816);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Merrimack_College', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 1816);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Merrimack_College', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 1816);

            //     return $ret;
            // });


            // study_group Oglethorpe_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Oglethorpe_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 1000);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Oglethorpe_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 1000);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Oglethorpe_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 1000);

            //     return $ret;
            // });


            // study_group Roosevelt_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Roosevelt_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 1213);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Roosevelt_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 1213);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Roosevelt_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 1213);

            //     return $ret;
            // });


            // study_group Texas_AM_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Texas_AM_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 4000);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Texas_AM_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 4000);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Texas_AM_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 4000);

            //     return $ret;
            // });


            // study_group University_of_Vermont
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_University_of_Vermont', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 4091);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_University_of_Vermont', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 4091);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_University_of_Vermont', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 4091);

            //     return $ret;
            // });

            // Study group engagement emails
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group');

            //     return $ret;
            // });

            // // Study group The_University_of_Sheffield
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_The_University_of_Sheffield', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 296348);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_The_University_of_Sheffield', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 296348);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_The_University_of_Sheffield', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 296348);

            //     return $ret;
            // });

            // // Study group Durham_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Durham_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 296392);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Durham_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 296392);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Durham_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 296392);

            //     return $ret;
            // });



            // intostudy General Cron jobs 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy');

            //     return $ret;
            // });


            // oregonstateuniversity Engagement emails
            // \Cron::add('autoPortalEmail_school_viewed_you_oregonstateuniversity', '*/57 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'oregonstateuniversity', 3298);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_oregonstateuniversity', '*/47 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'oregonstateuniversity', 3298);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_oregonstateuniversity', '*/31 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'oregonstateuniversity', 3298);

            //     return $ret;
            // });

            // benedictine Engagement emails
            // \Cron::add('autoPortalEmail_school_viewed_you_benedictine', '*/47 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'benedictine');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_benedictine', '*/57 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'benedictine');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_benedictine', '*/31 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'benedictine');

            //     return $ret;
            // });

            // // intostudy Colorado_State_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Colorado_State_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 6175);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Colorado_State_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 6175);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Colorado_State_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 6175);

            //     return $ret;
            // });

            // // intostudy Drew_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Drew_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 2388);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Drew_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 2388);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Drew_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 2388);

            //     return $ret;
            // });

            // // intostudy George_Mason_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_George_Mason_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 4115);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_George_Mason_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 4115);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_George_Mason_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 4115);

            //     return $ret;
            // });

            // // intostudy Illinois_State_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Illinois_State_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 1138);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Illinois_State_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 1138);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Illinois_State_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 1138);

            //     return $ret;
            // });

            // // intostudy Marshall_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Marshall_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 4286);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Marshall_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 4286);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Marshall_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 4286);

            //     return $ret;
            // });

            // // intostudy Oregon_State_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Oregon_State_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 3298);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Oregon_State_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 3298);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Oregon_State_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 3298);

            //     return $ret;
            // });

            // // intostudy Saint_Louis_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Saint_Louis_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 2218);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Saint_Louis_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 2218);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Saint_Louis_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 2218);

            //     return $ret;
            // });

            // // intostudy Suffolk_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Suffolk_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 1859);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Suffolk_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 1859);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Suffolk_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 1859);

            //     return $ret;
            // });

            // // intostudy University_of_Alabama_at_Birmingham 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_University_of_Alabama_at_Birmingham', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 2);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_University_of_Alabama_at_Birmingham', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 2);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_University_of_Alabama_at_Birmingham', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 2);

            //     return $ret;
            // });

            // // intostudy University_of_South_Florida-St_Petersburg 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_University_of_South_Florida-St_Petersburg', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 6317);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_University_of_South_Florida-St_Petersburg', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 6317);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_University_of_South_Florida-St_Petersburg', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 6317);

            //     return $ret;
            // });

            // // intostudy Washington_State_University 
            // \Cron::add('autoPortalEmail_school_viewed_you_intostudy_Washington_State_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'intostudy', 4257);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_intostudy_Washington_State_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'intostudy', 4257);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_intostudy_Washington_State_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'intostudy', 4257);

            //     return $ret;
            // });

            // study_group West_Virginia_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_West_Virginia_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 4313);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_West_Virginia_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 4313);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_West_Virginia_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 4313);

            //     return $ret;
            // });


            // study_group Western_Washington_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Western_Washington_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 4261);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Western_Washington_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 4261);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Western_Washington_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 4261);

            //     return $ret;
            // });


            // study_group Widener_University
            // \Cron::add('autoPortalEmail_school_viewed_you_study_group_Widener_University', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'study_group', 3601);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_study_group_Widener_University', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'study_group', 3601);

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_study_group_Widener_University', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'study_group', 3601);

            //     return $ret;
            // });

            /* STUDY GROUP ENDS HERE */

            //cappex
            // \Cron::add('autoPortalEmail_school_viewed_you_cappex', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_viewed_you', 'cappex');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_recommendation_cappex', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('recommendation', 'cappex');

            //     return $ret;
            // });

            // \Cron::add('autoPortalEmail_school_want_to_recruit_you_cappex', '*/17 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->autoPortalEmail('school_want_to_recruit_you', 'cappex');

            //     return $ret;
            // });
            // Auto portal emails ends here

            // EDX Certificate Cron job
            // \Cron::add('partnerEmailCronJob23_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(23, 'first');

            //     return $ret;
            // });

            // Study group direct email
            // \Cron::add('partnerEmailCronJob36_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(36, 'first');

            //     return $ret;
            // });

            // // Update profile for more info
            // \Cron::add('partnerEmailCronJob35_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(35, 'first');

            //     return $ret;
            // });

            //  // QS Grad Latin America email
            // \Cron::add('partnerEmailCronJob34_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(34, 'first');

            //     return $ret;
            // });

            // // QS Grad European email
            // \Cron::add('partnerEmailCronJob33_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(33, 'first');

            //     return $ret;
            // });

            // // QS Grad India email
            // \Cron::add('partnerEmailCronJob32_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(32, 'first');

            //     return $ret;
            // });

            // // QS Grad email
            // \Cron::add('partnerEmailCronJob31_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(31, 'first');

            //     return $ret;
            // });

            // OpenClassrooms Cron job
            \Cron::add('partnerEmailCronJob30_first', '*/7 * * * *', function(){
                $ret = PartnerEmailCronJobQueue::dispatch(30, 'first');
                
                return $ret;
            });    

            // Scholarship Owl Cron job
            \Cron::add('partnerEmailCronJob29_first', '*/7 * * * *', function(){
                $ret = PartnerEmailCronJobQueue::dispatch(29, 'first');
                
                return $ret;
            });
            
            // Into UK Cron job
            // \Cron::add('partnerEmailCronJob28_first', '*/13 * * * *', function(){
            //     $ret = PartnerEmailCronJobQueue::dispatch(28, 'first');
                
            //     return $ret;
            // });

            // NCSA Scholarship Cron job
            \Cron::add('partnerEmailCronJob37_first', '*/7 * * * *', function(){
                $ret = PartnerEmailCronJobQueue::dispatch(37, 'first');
                
                return $ret;
            });


            // // Plexuss Premium Cron job
            // \Cron::add('partnerEmailCronJob27_first', '*/14 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(27, 'first');

            //     return $ret;
            // });

            // // Cappex Scholarship Cron job
            \Cron::add('partnerEmailCronJob26_first', '*/11 * * * *', function(){
                $ret = PartnerEmailCronJobQueue::dispatch(26, 'first');
                
                return $ret;
            });

            // // Alliant Cron job
            // \Cron::add('partnerEmailCronJob25_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(25, 'first');

            //     return $ret;
            // });
            
            // Oregon State University Cron job
            // \Cron::add('partnerEmailCronJob24_first', '*/23 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(24, 'first');

            //     return $ret;
            // });

            // Hult University Cron job
            // \Cron::add('partnerEmailCronJob22_first', '*/23 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(22, 'first');

            //     return $ret;
            // });

            // // USF Program based Cron job
            // \Cron::add('partnerEmailCronJob21_first', '*/59 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(21, 'first');

            //     return $ret;
            // });

            // // USF Program based Cron job
            // \Cron::add('partnerEmailCronJob20_first', '*/13 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(20, 'first');

            //     return $ret;
            // });

            // Study Portal Cron job
            \Cron::add('partnerEmailCronJob19_first', '*/13 * * * *', function(){
                $ret = PartnerEmailCronJobQueue::dispatch(19, 'first');
                
                return $ret;
            });

            // // USF General Cron job
            // \Cron::add('partnerEmailCronJob18_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(18, 'first');

            //     return $ret;
            // });
            
            // Cornell College Cron job
            // \Cron::add('partnerEmailCronJob15_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(15, 'first');

            //     return $ret;
            // });

            // Exampal Cron job
            // \Cron::add('partnerEmailCronJob14_first', '*/4 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(14, 'first');

            //     return $ret;
            // });

            // Magoosh Cron job
            // \Cron::add('partnerEmailCronJob16_first', '*/9 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(16, 'first');

            //     return $ret;
            // });

            // SDSU Cron job
            // \Cron::add('partnerEmailCronJob17_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(17, 'first');

            //     return $ret;
            // });

            // CALU direct Cron job
            // \Cron::add('partnerEmailCronJob13_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(13, 'first');

            //     return $ret;
            // });

            // GCU direct Cron job
            // \Cron::add('partnerEmailCronJob12_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(12, 'first');

            //     return $ret;
            // });

            // Music Institute Cron job
            // \Cron::add('partnerEmailCronJob5_first', '*/11 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(5, 'first');

            //     return $ret;
            // });

            // QS MBA Cron job
            // \Cron::add('partnerEmailCronJob1_first', '*/4 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(1, 'first');

            //     return $ret;
            // });

            // QS GRAD Cron job
            // \Cron::add('partnerEmailCronJob2_first', '*/4 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(2, 'first');

            //     return $ret;
            // });

            // Truscribe Cron job
            // \Cron::add('partnerEmailCronJob3_first', '*/4 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(3, 'first');

            //     return $ret;
            // });

            // springboard Cron job
            \Cron::add('partnerEmailCronJob4_first', '*/11 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->partnerEmailCronJob(4, 'first');

                return $ret;
            });

            // springboard Cron job
            // \Cron::add('partnerEmailCronJob4_second', '*/4 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(4, 'second');

            //     return $ret;
            // });

            // QS MBA Cron job
            // \Cron::add('partnerEmailCronJob1_second', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(1, 'second');

            //     return $ret;
            // });

            // QS GRAD Cron job
            // \Cron::add('partnerEmailCronJob2_second', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailCronJob(2, 'second');

            //     return $ret;
            // });

            // // // QS MBA Cron job, for ad clicked user invite ppl
            // \Cron::add('partnerEmailForUsersInviteCronJob6_first', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailForUsersInviteCronJob(6, 'first');

            //     return $ret;
            // });

            // // // Springboard Cron job, for ad clicked user invite ppl
            // \Cron::add('partnerEmailForUsersInviteCronJob7_first', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailForUsersInviteCronJob(7, 'first');

            //     return $ret;
            // });

            // // // Music Institute Cron job, for ad clicked user invite ppl
            // \Cron::add('partnerEmailForUsersInviteCronJob8_first', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailForUsersInviteCronJob(8, 'first');

            //     return $ret;
            // });

            // // // EDX Cron job, for ad clicked user invite ppl
            // \Cron::add('partnerEmailForUsersInviteCronJob9_first', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailForUsersInviteCronJob(9, 'first');

            //     return $ret;
            // });

            // // GCU Cron job, for ad clicked user invite ppl
            // \Cron::add('partnerEmailForUsersInviteCronJob10_first', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailForUsersInviteCronJob(10, 'first');

            //     return $ret;
            // });

            // // CALU Cron job, for ad clicked user invite ppl
            // \Cron::add('partnerEmailForUsersInviteCronJob11_first', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->partnerEmailForUsersInviteCronJob(11, 'first');

            //     return $ret;
            // });
            

            \Cron::add('addRecommendationsToSupportAccountCronJob', '01 00 * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->addRecommendationsToSupportAccountCronJob();

                return $ret;
            });

            \Cron::add('setUsersPortalEmailEffortLogsDateId', '0 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->setUsersPortalEmailEffortLogsDateId();

                return $ret;
            });         
            
            // \Cron::add('uploadMissingFBImages', '*/2 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->uploadMissingFBImages();

            //     return $ret;
            // });
            
            \Cron::add('agencyGenerateNewLeads', '*/10 * * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->agencyGenerateNewLeads();

                return $ret;
            });
            
            \Cron::add('iOSappInvite', '*/3 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->iOSappInvite();

                return $ret;
            });

            \Cron::add('agentOneADay', '*/3 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->agentOneADay();

                return $ret;
            });
            
            // \Cron::add('edxUserInviteListCronJon', '*/6 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxUserInviteListCronJon();

            //     return $ret;
            // });

            // \Cron::add('edxUserInviteListCronJon_second', '*/7 * * * *', function(){
            //     $mac = new UtilityController;
            //     $ret = $mac->edxUserInviteListCronJon("second");

            //     return $ret;
            // });

            \Cron::add('releaseB2B', '*/7 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->releaseB2B();

                return $ret;
            });

            \Cron::add('freemiumEmail', '*/3 * * * *', function(){
                $mac = new UtilityController;
                $ret = $mac->freemiumEmail();

                return $ret;
            });
            
            /*
            \Cron::add('example1', '* * * * *', function() {
                            // Do some crazy things unsuccessfully every minute
                            return 'No';
                        });
            */

            \Cron::add('set_user_long_lat', '*/10 * * * *', function() {

                // set users longitude and latitude for users table
                $crc = new CollegeRecommendationController();

                $ret = $crc->setUsersLongLat();

                return $ret;
            });

            \Cron::add('college_recommendations', '00 06 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->create();

                return $ret;
            });

            // \Cron::add('college_recommendations6', '15 06 * * *', function() {

            //     $crc = new CollegeRecommendationController();

            //     $ret = $crc->create();

            //     return $ret;
            // });

            \Cron::add('college_recommendations2', '30 06 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->create();

                return $ret;
            });

            // \Cron::add('college_recommendations3', '45 06 * * *', function() {

            //     $crc = new CollegeRecommendationController();

            //     $ret = $crc->create();

            //     return $ret;
            // });


            \Cron::add('college_recommendations4', '00 07 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->create();

                return $ret;
            });

            \Cron::add('college_recommendations5', '30 07 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->create();

                return $ret;
            });

            \Cron::add('college_recommendations_for_inquiries', '30 08 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->convertRecommendationsToInquiries();

                return $ret;
            });

            \Cron::add('recycleOldRecs1', '45 07 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->recycleOldRecs();

                return $ret;
            });

            \Cron::add('recycleOldRecs2', '00 08 * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->recycleOldRecs();

                return $ret;
            });

            // =======================COLLEGES CRON EMAILS============================= //
            // temp remove
            \Cron::add('usersWantToGetRecruitedForColleges', '00 10 * * Wed', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->usersWantToGetRecruitedForColleges();

                return $ret;

            });

           //    // temp remove
            \Cron::add('usersAcceptRequestToBeRecruitedForColleges', '*/20 * * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->usersAcceptRequestToBeRecruitedForColleges();

                return $ret;
            });

           //  // temp remove
            \Cron::add('usersSendMessagesForColleges', '*/25 * * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->usersSendMessagesForColleges();

                return $ret;
            });

            //  // =========================USERS CRON EMAILS=============================== //
            //    // temp remove
            // \Cron::add('collegeWantsToRecruitUsersFromRecommendation', '*/19 * * * *', function() {

            //     $mac = new MandrillAutomationController();

            //     $ret = $mac->collegeWantsToRecruitUsersFromRecommendation();

            //     return $ret;
            // });
            //    // temp remove
            \Cron::add('collegeViewedYourProfile', '*/10 * * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->collegeViewedYourProfile();

                return $ret;
            });
           // // temp remove
           // \Cron::add('collegeRecommendationsForUsers', '00 16 * * Wed', function() {
           //      $mac = new MandrillAutomationController();

           //      $ret = $mac->collegeRecommendationsForUsers();

           //      return $ret;
           //  });
           //    // temp remove
            \Cron::add('collegesSendMessagesForUsers', '*/20 * * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->collegesSendMessagesForUsers();

                return $ret;
            });
            /*
            \Cron::add('disabled job', '0 * * * *', function() {
                // Do some crazy things successfully every hour
            }, false);
            */

            // \Cron::add('user_preview', '*/3 * * * *', function() {

            //     $lc = new LeadilityController();

            //     $ret = $lc->importUser();

            //     return $ret;
            // });

            // =========================AGENCY CRON EMAILS=============================== //
              // temp remove
            \Cron::add('usersWantToGetRecruitedForAgencies', '*/20 * * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->usersWantToGetRecruitedForAgencies();

                return $ret;
            });
              // temp remove
            // \Cron::add('cronRunColleges', '*/2 * * * *', function() {

            //     $mac = new CronEmailController();

            //     $ret = $mac->runColleges();

            //     return $ret;
            // });
              // temp remove
            // \Cron::add('cronRunAgencies', '*/2 * * * *', function() {

            //     $mac = new CronEmailController();

            //     $ret = $mac->runAgencies();

            //     return $ret;
            // });

            // \Cron::add('autoSendInvites', '0 * * * *', function() {

            //     $ic = new InviteController();

            //     $ret = $ic->autoSendInvites();

            //     return $ret;
            // });

            // \Cron::add('autoSendInvitesFollowUp1', '0 * * * *', function() {

            //     $ic = new InviteController();

            //     $ret = $ic->autoSendInvitesFollowUp(1);

            //     return $ret;
            // });

            // \Cron::add('autoSendInvitesFollowUp2', '0 * * * *', function() {

            //     $ic = new InviteController();

            //     $ret = $ic->autoSendInvitesFollowUp(2);

            //     return $ret;
            // });


            \Cron::add('salesControllerGenerateDate', '*/7 * * * *', function() {

                $bc = new Controller;

                $ret = $bc->salesGenerator();

                return $ret;
            });

            // \Cron::add('autoApproveRecommendationColleges', '00 18 * * *', function() {

            //     $bc = new Controller();

            //     $ret = $bc->autoApproveRecommendationColleges();

            //     return $ret;
            // });
              // temp remove
            // \Cron::add('sendInfilawCollegeEmail', '*/7 * * * *', function() {

            //     $mac = new MandrillAutomationController();

            //     $ret = $mac->sendInfilawCollegeEmail();

            //     return $ret;
            // });
              // temp remove
            // \Cron::add('sendInfilawAmazonCode', '*/7 * * * *', function() {

            //     // $mac = new MandrillAutomationController();

            //     // $ret = $mac->sendInfilawAmazonCode();

            //     // return $ret;
            // });
              // temp remove
            \Cron::add('birthdayEmailForUsers', '30 18 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->birthdayEmailForUsers();

                return $ret;
            });
            // temp remove
            \Cron::add('emailAfterThreeWeekForUsers', '45 18 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->emailAfterThreeWeekForUsers();

                return $ret;
            });
            // temp remove
            \Cron::add('emailAfterTwoWeekForUsers', '00 19 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->emailAfterTwoWeekForUsers();

                return $ret;
            });
            // // temp remove
            \Cron::add('emailAfterOneWeekForUsers', '15 19 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->emailAfterOneWeekForUsers();

                return $ret;
            });
            // // temp remove
            \Cron::add('newRankingForColleges', '00 15 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->newRankingForColleges();

                return $ret;
            });

            \Cron::add('parseUserResponds', '*/29 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseUserResponds();

                return $ret;
            });

            \Cron::add('parseCollegeResponds', '*/29 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseCollegeResponds();

                return $ret;
            });

            \Cron::add('parseCollegeWantsToRecruitYou', '*/29 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseCollegeWantsToRecruitYou();

                return $ret;
            });

            \Cron::add('parseIsItAGoodFitForYou', '*/11 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseIsItAGoodFitForYou();

                return $ret;
            });

            \Cron::add('parseCollegeRankingUpdateForUsers', '*/11 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseCollegeRankingUpdateForUsers();

                return $ret;
            });

            \Cron::add('parseSchoolsYouLiked', '*/11 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseSchoolsYouLiked();

                return $ret;
            });

            \Cron::add('parseSchoolsNearYou', '*/11 * * * *', function() {

                $epc = new EmailParserController();

                $ret = $epc->parseSchoolsNearYou();

                return $ret;
            });

            \Cron::add('sendScheduleCampaign', '*/5 * * * *', function() {

                $gmc = new GroupMessagingController();

                $ret = $gmc->sendScheduleCampaign();

                return $ret;
            });

            \Cron::add('sendReadyCampaign', '*/10 * * * *', function() {

                $gmc = new GroupMessagingController();

                $ret = $gmc->sendReadyCampaign();

                return $ret;
            });

            // \Cron::add('setDailyRecGoals', '00 05 * * *', function() {
            //
            //     $bc = new Controller();
            //
            //     $ret = $bc->setDailyRecGoals();
            //
            //     return $ret;
            // });

            \Cron::add('setCountryWithIp', '* * * * *', function() {

                $bc = new Controller();

                $ret = $bc->setCountryWithIp();

                return $ret;
            });

            \Cron::add('collegeWeeklyEmail', '00 05 * * Tue', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->collegeWeeklyEmail();

                return $ret;

            });

            \Cron::add('normalTriggerDaily', '00 08 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->normalTrigger('daily');

                return $ret;

            });

             \Cron::add('normalTriggerWeekly', '00 04 * * Wed', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->normalTrigger('weekly');

                return $ret;

            });

            \Cron::add('emeregencyTrigger', '*/17 * * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->emeregencyTrigger();

                return $ret;

            });

            //  \Cron::add('marchMadnessEmail', '*/10 * * * *', function() {

            //     $mac = new MandrillAutomationController();

            //     $ret = $mac->marchMadnessEmail();

            //     return $ret;

            // });

            \Cron::add('setupRecommendationTier', '0 * * * *', function() {

                $crc = new CollegeRecommendationController();

                $ret = $crc->setupRecommendationTier();

                return $ret;

            });

            \Cron::add('updateProfilePercent', '*/17 * * * *', function() {

                $crc = new Controller();

                $ret = $crc->updateProfilePercent();

                return $ret;

            });

            // \Cron::add('samplePremierContract', '00 10 * * *', function() {

            //     $mac = new MandrillAutomationController();

            //     $ret = $mac->samplePremierContract();

            //     return $ret;

            // });

            \Cron::add('premierProgramWelcomeEmail', '00 17 * * *', function() {

                $mac = new MandrillAutomationController();

                $ret = $mac->premierProgramWelcomeEmail();

                return $ret;

            });

            \Cron::add('chargePremiumMonthlyUsersCron', '0 22 * * *', function() {

                $opc = new OmniPayController();

                $ret = $opc->chargePremiumMonthlyUsersCron();

                return $ret;

            });

            \Cron::add('generateAutoCampaign', '00 18 * * 4', function() {

                $gmc = new GroupMessagingController();

                $ret = $gmc->generateAutoCampaign();

                return $ret;

            });

            \Cron::add('generateAutoCampaign2', '00 18 * * Fri', function() {

                $gmc = new GroupMessagingController();

                $ret = $gmc->generateAutoCampaign();

                return $ret;

            });

            \Cron::add('generateAutoCampaign3', '00 18 * * Sat', function() {

                $gmc = new GroupMessagingController();

                $ret = $gmc->generateAutoCampaign();

                return $ret;

            });

            \Cron::add('deactivatePhone', '00 22 * * *', function() {

                $tc = new TwilioController();

                $ret = $tc->releasePurchasePhone();

                return $ret;

            });

            \Cron::add('buildExportStudentsExcelFile', '*/2 * * * *', function(){

                $aj = new AjaxController();

                $ret = $aj->saveExportsToEmailLater();

                return $ret;
            });

             \Cron::add('inviteWebinarUsers', '*/10 * * * *', function(){

                $bc = new Controller;

                $ret = $bc->inviteWebinarUsers();

                return $ret;
            });

            \Cron::add('inviteWebinarFifteenMin', '45 07 * * Wed', function(){
                $bc = new Controller;

                $ret = $bc->inviteWebinarFifteenMin();
                
                return $ret();
            });

            // retrain machine learning algorithms
            \Cron::add('retrainModels', '0 03 * * *', function(){
                
                $bc = new Controller;

                $ret = $bc->retrainModels();

                return "success";
            });

            // send meeting reminders to clients
            \Cron::add('sendReminders', '0 10 * * *', function(){
                $bc = new Controller;

                $ret = $bc->sendReminders();

                return "success";
            });

            // send meeting prep data to Chris
            \Cron::add('sendPrepData', '0 10 * * *', function(){
                $bc = new Controller;

                $ret = $bc->sendPrepData();

                return "success";
            });

            \Cron::add('changePriorityTier', '*/11 * * * *', function(){
                $bc = new Controller;

                $ret = $bc->changePriorityTier();

                return "success";
            });

            \Cron::add('setUserTargettedForPickACollege', '*/7 * * * *', function(){
                $bc = new Controller;

                $ret = $bc->setUserTargettedForPickACollege();

                return "success";
            });

            \Cron::add('setRecruitmentTag', '*/7 * * * *', function(){
                $bc = new Controller;

                $ret = $bc->setRecruitmentTag();

                return "success";
            });

            // recruitment tags cron jobs
            // \Cron::add('checkGeneralRecruitmentTag', '*/7 * * * *', function(){
            //     $bc = new Controller;

            //     $ret = $bc->checkGeneralRecruitmentTag();

            //     return $ret;
            // });

            // \Cron::add('checkTaggedRecruitmentTag', '*/7 * * * *', function(){
            //     $bc = new Controller;

            //     $ret = $bc->checkTaggedRecruitmentTag();

            //     return $ret;
            // });

            \Cron::add('addTargettingUsersToRecruitmentTagCronJob', '*/11 * * * *', function(){
                $bc = new Controller;

                $ret = $bc->addTargettingUsersToRecruitmentTagCronJob();

                return $ret;
            });
            // end of recruitment tags cron jobs

            // \Cron::add('autoApproveTargettedInquiries', '*/23 * * * *', function(){
            //     $bc = new Controller;

            //     $ret = $bc->autoApproveTargettedInquiries();

            //     return "success";
            // });

            \Cron::add('autoPrescreenedUserAppliedColleges', '*/7 * * * *', function(){
                $bc = new Controller;

                $ret = $bc->autoPrescreenedUserAppliedColleges();

                return "success";
            });

            \Cron::add('devryCollegesTempFixUps', '00 16 * * *', function(){
                $bc = new Controller;

                $ret = $bc->devryCollegesTempFixUps();

                return "success";
            });

            \Cron::add('prescreenedRemoveBadPhone1', '00 06 * * *', function(){
                $bc = new Controller;

                $ret = $bc->prescreenedRemoveBadPhone();

                return $ret;
            });
            
            \Cron::add('prescreenedRemoveBadPhone2', '30 12 * * *', function(){
                $bc = new Controller;

                $ret = $bc->prescreenedRemoveBadPhone();

                return $ret;
            });

            // APPLICATION EMAILS STARTS HERE
            // \Cron::add('applicationEmailForCovetedUSersPostTen', '45 19 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationEmailForCovetedUSersPostTen();

            //     return $ret;
            // });

            // 20K and more starts
            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-0', '00 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 0);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-1', '01 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 1);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-2', '02 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 2);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-3', '03 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 3);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-4', '04 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 4);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-5', '05 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 5);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-6', '06 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 6);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-7', '07 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 7);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-8', '08 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 8);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-20K-More-CNT-9', '09 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('20k or more', 9);

                return $ret;
            });
            // 20K and more ends here

            // 10k to 20k starts here
            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-0', '10 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 0);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-1', '11 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 1);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-2', '12 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 2);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-3', '13 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 3);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-4', '14 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 4);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-5', '15 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 5);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-6', '16 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 6);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-7', '17 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 7);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-8', '18 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 8);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-10K-TO-20K-CNT-9', '19 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('10k to 20k', 9);

                return $ret;
            });
            // 10k to 20k ends here

            // 5k to 10k starts here
            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-0', '20 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 0);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-1', '21 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 1);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-2', '22 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 2);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-3', '23 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 3);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-4', '24 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 4);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-5', '25 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 5);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-6', '26 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 6);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-7', '27 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 7);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-8', '28 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 8);

                return $ret;
            });

            \Cron::add('applicationEmailForPeopleWhoHaventStarted-5K-TO-10K-CNT-9', '29 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailForPeopleWhoHaventStarted('5k to 10k', 9);

                return $ret;
            });

            // // temp remove
            // \Cron::add('applicationEmailStartedButNotFinished-CNT-0', '30 04 * * *', function(){
                
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationEmailStartedButNotFinished(0);

            //     return $ret;
            // });
            // // temp remove
            // \Cron::add('applicationEmailStartedButNotFinished-CNT-1', '35 04 * * *', function(){
                
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationEmailStartedButNotFinished(1);

            //     return $ret;
            // });

            \Cron::add('applicationEmailFinishedApp', '40 04 * * *', function(){
                
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationEmailFinishedApp();

                return $ret;
            });

            \Cron::add('followupWeeklyEmailForVerifiedApplicationUsers', '00 05 * * Sun', function(){
                
                $mac = new MandrillAutomationController;
                $ret = $mac->followupWeeklyEmailForVerifiedApplicationUsers();

                return $ret;
            });
            
            
            // 5k to 10k ends here

            // APPLICATION EMAILS ENDS HERE

            // APPLICATION TEXT WORKFLOW BEGINS
            // \Cron::add('applicationTextForPeopleWhoHaventStarted-toefl-CNT-0', '00 03 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationTextForPeopleWhoHaventStarted(0, true);

            //     return $ret;
            // });

            // \Cron::add('applicationTextForPeopleWhoHaventStarted-no-toefl-CNT-0', '05 03 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationTextForPeopleWhoHaventStarted(0, false);

            //     return $ret;
            // });

            // \Cron::add('applicationTextForPeopleWhoHaventStarted-no-yes-toefl-CNT-1', '15 03 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationTextForPeopleWhoHaventStarted(1, false);

            //     return $ret;
            // });

            // \Cron::add('applicationTextForPeopleWhoHaventStarted-no-yes-toefl-CNT-2', '30 03 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationTextForPeopleWhoHaventStarted(2, false);

            //     return $ret;
            // });

            // \Cron::add('applicationTextForPeopleWhoHaventStarted-no-yes-toefl-CNT-3', '45 03 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationTextForPeopleWhoHaventStarted(3, false);

            //     return $ret;
            // });

            // \Cron::add('applicationTextForPeopleWhoHaventStarted-no-yes-toefl-CNT-4', '55 03 * * *', function(){
            //     $mac = new MandrillAutomationController;
            //     $ret = $mac->applicationTextForPeopleWhoHaventStarted(4, false);

            //     return $ret;
            // });
            // APPLICATION TEXT WORKFLOW ENDS

            // Admitsee email
            \Cron::add('admitseeEmails', '00 02 * * Tue', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->admitseeEmails();

                return $ret;
            });

            // ELS Additional Info email
            \Cron::add('elsAdditionalInfoEmails', '*/2 * * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->elsAdditionalInfoEmails();

                return $ret;
            });
            
            // fixPresscreenedAddToRecTab
            \Cron::add('fixPresscreenedAddToRecTab', '*/2 * * * *', function(){
                $bc = new Controller;
                $ret = $bc->fixPresscreenedAddToRecTab();

                return $ret;
            });

            \Cron::add('generatePickACollegeData', '*/12 * * * *', function() {

                $bc = new Controller;

                $ret = $bc->generatePickACollegeData();

                return "success";
            });

            \Cron::add('applicationAdditionalUploadRequired', '02 04 * * *', function(){
                $mac = new MandrillAutomationController;
                $ret = $mac->applicationAdditionalUploadRequired();

                return $ret;
            });

            $report = \Cron::run();

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //        

    }
}