<?php

namespace App\Http\Controllers;

use Request, DB, DateTime, Queue;
use Carbon\Carbon;
use App\User, App\College, App\Country, App\CovetedUser, App\Recruitment, App\CollegeMessageThreadMembers, App\SettingNotificationLog, App\NotificationTopNav;
use App\EmailPlatformToggle, App\SparkpostModel, App\Priority, App\AgencyRecruitment, App\TrackingPage, App\ServicesByAgency, App\OrganizationBranch;
use App\ApplicationEmailLog, App\AdmitseeCronToggle, App\SendgridIpWarmupSchedule;
use App\Transcript, App\PressReleaseContact;
use App\Http\Controllers\TwilioController, App\Http\Controllers\AjaxController, App\Http\Controllers\CollegeRecommendationController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\RevenueController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\AgencyProfileInfo, App\AgencyProfileInfoServices;
use App\CollegeSelfSignupApplications;
use App\MandrillModel;

use App\MandrillLog;
use App\CrmAutoReporting;
use App\Http\Controllers\AdminController;
use App\OrganizationBranchPermission;
use App\Console\Commands\SendDailyCrmReport;
use App\Console\Commands\SendDailyClientReport;
use App\Console\Commands\SendPassthroughReminderEmail;
use App\Console\Commands\SendCollegeInquiryReport;
use App\SendGridModel;
use App\SesModel;
use App\EmailTemplateSenderProvider;
use App\DailyRevenueClientEmails;
use App\WeeklyClientRemindersToSendNumbers;
use App\DailyCollegeInquiryReport;
use App\AdPassthroughs;


class MandrillAutomationController extends Controller
{

	private $template_name = '';
	private $reply_email = '';

	// User Automation methods starts here 

    public function sendB2bResourcesEmail($email) {
        $this->template_name = 'b2b_plexuss_resources';

        $reply_email = 'support@plexuss.com';

        $email_arr = [
            'email' => $email,
            'name' => 'Client',
            'type' =>'to',
        ];

        $params = [
            'SUBJECT' => 'Plexuss B2B Resources',
        ];

        $this->sendMandrillEmail('b2b_plexuss_resources', $email_arr, $params, $reply_email);

        return 'success';
    }

    /**
     * CLIENTS: Send weekly client Reminder(s)
     * 
     */
    public function sendClientWeeklyRemainders() {
        if (Cache::has( env('ENVIRONMENT') .'_'. 'is_sendClientWeeklyRemainders')) {
            
            $cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_sendClientWeeklyRemainders');

            if ($cron == 'already_sent_this_week') {
                return "a weekly cron has already been sent this week";
            }
        }

        Cache::put( env('ENVIRONMENT') .'_'. 'is_sendClientWeeklyRemainders', 'already_sent_this_week', 30);

        $reminders = WeeklyClientRemindersToSendNumbers::select('id as reminder_id', 'fname', 'email', 'last_sent')
            ->where('active', '=', 1)
            ->get();

        foreach ($reminders as $person) {
            $this->sendSingleClientWeeklyRemainders($person);
        }

        return 'success';
    }

    public function sendSingleClientWeeklyRemainders($person) {
        $this->template_name = 'plexuss_email_reminder_send_numbers_r2';
        $reply_email = 'support@plexuss.com';
        $right_now_date = date('Y-m-d H:i:s');

        $required_params = [
            'reminder_id',
            'fname',
            'email',
        ];

        foreach ($required_params as $param) {
            if (!isset($person->{$param})) {
                return 'Missing ' . $param;
            }
        }

        $params = [
            'FNAME' => $person->fname,
        ];

        $email_arr = [
            'email' => $person->email,
            'name' => $person->fname,
            'type' =>'to',
        ];

        $this->sendMandrillEmail('plexuss_email_reminder_send_numbers_r2', $email_arr, $params, $reply_email);

        // Send email to Sina/JP
        $jp_email_arr = [
            'email' => 'jp.novin@plexuss.com',
            'name' => 'JP',
            'type' =>'to',
        ];

        $this->sendMandrillEmail('plexuss_email_reminder_send_numbers_r2', $jp_email_arr, $params, $reply_email);

        $sina_email_arr = [
            'email' => 'sina.shayesteh@plexuss.com',
            'name' => 'Sina',
            'type' =>'to',
        ];

        $this->sendMandrillEmail('plexuss_email_reminder_send_numbers_r2', $sina_email_arr, $params, $reply_email);
        // End Send email to Sina/JP

        $reminder = WeeklyClientRemindersToSendNumbers::find($person->reminder_id);
        $reminder->last_sent = $right_now_date;
        $reminder->save();

        return 'success';
    }

    /**
     * PASSTHROUGH: Send Passthrough Conversion Reminder(s)
     * 
     * Emails campaign to send passthrough reminder for users who have 
     * completed passthrough but have not converted.
     *
     */
    public function sendPassthroughReminders() {
        $twentyFourhoursAgo = Carbon::now()->subHours(24);

        $reminders = DB::connection('bk')
                       ->table('ad_passthroughs as apt')
                       ->join('ad_clicks as ac', function($join) {
                            $join->on('apt.id', '=', 'ac.ad_passthrough_id')
                                 ->whereNotNull('apt.user_id')
                                 ->whereNotNull('ac.ad_passthrough_id')
                                 ->where('ac.pixel_tracked', '=', 0);
                       })
                       ->join('users as u', function($join) {
                            $join->on('u.id', '=', 'apt.user_id')
                                 ->whereNotNull('u.email')
                                 ->where('u.email', '!=', 'none');
                       })
                       ->join('ad_redirect_campaigns as arc', function($join) {
                            $join->on('arc.company', '=', 'apt.company');
                       })
                       // SELECT STATEMENT
                       ->select('apt.id as ad_passthrough_id', 'apt.user_id', 'arc.logo as company_logo', 'arc.label as company_label', 'u.fname', 'u.lname', 'u.email', 'u.profile_img_loc as profile_pic', 'apt.company', 'apt.cid', 'apt.utm_source')
                       // SELECT STATEMENT
                       ->whereNotNull('arc.logo')
                       ->where('apt.created_at', '<=', $twentyFourhoursAgo)
                       ->where('apt.second_email_sent', '=', 0)
                       ->where('apt.utm_source', '!=', 'email_interestfollowup_cta_learnmore')
                       ->distinct()
                       ->take(100)
                       ->get();

        $sm = new SparkpostModel('test_template');

        foreach ($reminders as $person) {
            
            $is_suppressed = $sm->isSupressed($person->email, $person->user_id);
            if (isset($is_suppressed) && $is_suppressed == true){
                continue;
            }

            // $this->sendSinglePassthroughReminder($person);
            Queue::push(new SendPassthroughReminderEmail($person));

        }

        return 'success';
    }

    /**
     * PASSTHROUGH: Send Single Passthrough Conversion Reminder(s)
     * 
     * Emails campaign to send passthrough reminder for users who have 
     * completed passthrough but have not converted.
     *
     * $person is an object that should include:
     *    { ad_passthrough_id, user_id, company_label, company_logo, fname, lname, email, profile_pic, company, cid, utm_source }
     *
     */
    public function sendSinglePassthroughReminder($person) {
        $required_params = [
            'ad_passthrough_id',
            'user_id',
            'company_logo',
            'company_label',
            'fname',
            'lname',
            'email',
            'company',
            'cid',
        ];

        foreach ($required_params as $param) {
            if (!isset($person->{$param})) {
                return 'Missing ' . $param;
            }
        }

        $reply_email = 'support@plexuss.com';

        $this->template_name = 'passthrough_second_email';

        $profilePicturePrefix = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/";

        $profilePictureSlug = isset($person->profile_pic) ? $person->profile_pic : 'default.png';

        $hashed_user_id = '';

        if (isset($person->user_id) && $person->user_id !== -1) {
            $hashed_user_id = Crypt::encrypt($person->user_id);
        }

        $params = [
            'CLIENTNAME' => $person->company_label,
            'FNAME' => $person->fname,
            'LNAME' => $person->lname,
            'PROFILEPIC' => $profilePicturePrefix . $profilePictureSlug,
            'CLIENTPIC' => $person->company_logo,
            'SUBJECT' => 'Thank you for your interest',
            'ADLINK' => 'https://plexuss.com/adRedirect?cid=' . $person->cid . '&company=' . $person->company . '&utm_source=email_interestfollowup_cta_learnmore&hid=' . $hashed_user_id,
        ];

        $email_arr = [
            'email' => $person->email,
            'name' => $person->fname,
            'type' =>'to',
        ];

        $this->sendMandrillEmail('passthrough_second_email', $email_arr, $params, $reply_email);

        $apt = AdPassthroughs::find($person->ad_passthrough_id);
        $apt->second_email_sent = 1;
        $apt->save();

        return 'success';
    }

    /**
     * CLIENTS: Daily Client Report
     * 
     * Emails campaign data (clicks & conversions) to clients (Will be triggered via cron job)
     *
     */
    public function sendDailyClientReport() {
        $clients = DailyRevenueClientEmails::select('id', 'fname', 'email', 'client_name')
                                           ->where('active', '=', 1)
                                           ->get();

        foreach($clients as $client) {
            // $this->sendSingleDailyClientReport($client);
            Queue::push(new SendDailyClientReport($client));
        }

        return 'success';
    }

    public function testDailyClientReport() {
        $client = new \stdClass;

        $client->fname = 'Tony';
        $client->client_name = 'SDSU';
        $client->email = 'tony.tran@plexuss.com';

        return $this->sendSingleDailyClientReport($client);
    }

    public function sendDailyCollegeInquiriesReport() {
        $today = date("Y-m-d");
        
        $day_of_week = date('l', strtotime($today));

        if ($day_of_week == "Saturday" || $day_of_week == "Sunday") {
            return 'Do not run in the weekend';
        }

        $reports = DailyCollegeInquiryReport::select('id', 'fname', 'is_pixel_set' , 'email', 'school_name', 'org_branch_id')
                                            ->where('active', '=', 1)
                                            ->get();

        foreach ($reports as $report) {
            // $this->sendSingleDailyCollegeInquiryReport($report); // Function call
            Queue::push(new SendCollegeInquiryReport($report)); // Queue
        }

        return 'success';
    }

    public function testSingleInquiryReport() {
        $report = new \stdClass;

        $org_branch_ids = [172];

        $report->email = 'tony.tran@plexuss.com';
        $report->fname = 'Tony Test Report';
        $report->id = 1;
        $report->is_pixel_set = 0;
        $report->school_name = 'University of Arkansas';

        foreach ($org_branch_ids as $org_branch_id) {
            $report->org_branch_id = $org_branch_id;

            $this->sendSingleDailyCollegeInquiryReport($report);
        }
    }

    public function sendSingleDailyCollegeInquiryReport($report) {
        $reply_email = 'support@plexuss.com';
        $email_arr = [
            'email' => $report->email,
            'name' => $report->fname,
            'type' =>'to',
        ];

        $params = [];
        $params['TODAYDATE'] = date('m/d/Y');
        $params['FNAME'] = $report->fname;
        $params['SCHOOLNAME'] = $report->school_name;
        $params['ISPIXELSET'] = $report->is_pixel_set;
        
        $admin_controller = new AdminController;

        $report_data = $admin_controller->collegeAdminRevenueOrganizationQry($report->org_branch_id);

        $params = array_merge($params, $report_data);

        $this->sendMandrillEmail('daily_college_inquiries_report', $email_arr, $params, $reply_email);

        // Send email to Sina/JP
        $jp_email_arr = [
            'email' => 'jp.novin@plexuss.com',
            'name' => 'JP',
            'type' =>'to',
        ];

        $this->sendMandrillEmail('daily_college_inquiries_report', $jp_email_arr, $params, $reply_email);

        $sina_email_arr = [
            'email' => 'sina.shayesteh@plexuss.com',
            'name' => 'Sina',
            'type' =>'to',
        ];

        $this->sendMandrillEmail('daily_college_inquiries_report', $sina_email_arr, $params, $reply_email);
        // End Send email to Sina/JP

        DailyCollegeInquiryReport::updateLastSent($report->id);

        return 'success';
    }

    /**
     * CLIENTS: Daily Client Report
     * 
     * This function is to be queued by sendDailyClientReport to send single report per client
     *
     */
    public function sendSingleDailyClientReport($client = NULL) {
        if (!isset($client)) {
            return 'No client given';
        }

        // No queue for now, just testing.
        $this->template_name = 'daily_client_report';

        $reply_email = 'support@plexuss.com';

        // This should be dynamic depending on client
        $email_arr = [
            'email' => $client->email,
            'name' => $client->fname,
            'type' =>'to',
        ];

        $beginning_of_month = date('Y-m-01');

        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $today_formatted = date('m/d/Y');

        $revenueController = new RevenueController;

        $report = $revenueController->getRevenueByOrganization($client->client_name);
        
        $params = [
            'TODAYDATE' => $today_formatted,
            'CLIENTNAME' => $client->client_name,
            'FNAME' => $client->fname,
            'REPORTDATA' => $report['report_data']['days'],
            'MONTHDATA' => $report['report_data']['month'],
        ];

        // $attachments = [];

        // $attachments[] = $revenueController->createLeadSpreadsheet($client->client_name, $beginning_of_month, $yesterday);

        $this->sendMandrillEmail('daily_client_report', $email_arr, $params, $reply_email);

        DailyRevenueClientEmails::updateLastSent($client->id);

        return 'success';
    }

    public function sendPlexussPixelRequestEmail($params) {
        $template_name = 'plexuss_pixel_request';
        $reply_email = 'support@plexuss.com';

        $email_list = [
            ['email' => 'jp.novin@plexuss.com', 'name' => 'JP Novin', 'type' => 'to'],
            ['email' => 'sina.shayesteh@plexuss.com', 'name' => 'Sina Shayesteh', 'type' => 'to'],
            ['email' => 'anthony.shayesteh@plexuss.com', 'name' => 'Anthony Shayesteh', 'type' => 'to'],
        ];

        if (empty($params)) return 'No Parameters';

        foreach ($email_list as $email_arr) {
            $this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);
        }

        return 'success';
    }

    /**
     * COLLEGES: Daily CRM Report
     * 
     * Takes a look at the crm_auto_reporting table to see if it has been a day since 
     * the last report has been delivered, if so, send an email.
     * 
     * Will also send an email if the starting date set is now.
     *
     */
    public function sendDailyCRMReport() {
        $right_now_date = date('Y-m-d H:i:s');

        $daily_reports = CrmAutoReporting::on('bk')
                                          ->select('user_id', 'date', 'time', 'last_sent')
                                          ->get();

        foreach ($daily_reports as $report) {
            Queue::push(new SendDailyCrmReport($report, $right_now_date));
        }

        return 'success';
    }

    /**
     * COLLEGES: Daily CRM Report - Helper Function
     * 
     * Sends the report after already filtering out others.
     * 
     */
    public function sendSingleDailyCRMReport($report, $right_now_date) {
        $this->template_name = 'colleges_daily_crm_report';

        $reply_email = 'support@plexuss.com';

        $user = User::select('fname', 'lname', 'email')->where('id', $report->user_id)->first();

        $email_arr = ['email' => $user->email,
                      'name' => $user->fname. ' '. $user->lname,
                      'type' =>'to',
        ];

        $start_date = date('Y-m-d', strtotime('-1 day'));

        $end_date = date('Y-m-d', strtotime($right_now_date));

        $admin_controller = new AdminController;

        $obp = OrganizationBranchPermission::on('bk')
                                           ->select('organization_branch_id as org_branch_id', 'super_admin')
                                           ->where('user_id', $report->user_id)
                                           ->first();

        $params = [
            'org_branch_id' => $obp->org_branch_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'requester_user_id' => $report->user_id,
            'is_super_admin' => $obp->super_admin,
        ];

        $report_data = $admin_controller->getReport($params);

        $attributes = ['user_id' => $report->user_id];
        $values = ['last_sent' => $right_now_date];

        $email_params = [
            'FNAME' => $user->fname,
            'STARTDATE' => $start_date,
            'ENDDATE' => $end_date,
            'REPORTDATA' => $report_data,
        ];

        $this->sendMandrillEmail('colleges_daily_crm_report', $email_arr, $email_params, $reply_email);

        // Also send crm daily reporting to JP and Sina for NCC (org_branch_id 254) or Plexuss (org_branch_id 1)
        if ($obp->org_branch_id == 254 || $obp->org_branch_id == 1) {
            $jp_email_arr = [
                'email' => 'jp.novin@plexuss.com',
                'name' => 'JP Novin',
                'type' =>'to',
            ];

            $this->sendMandrillEmail('colleges_daily_crm_report', $jp_email_arr, $email_params, $reply_email);

            $sina_email_arr = [
                'email' => 'sina.shayesteh@plexuss.com',
                'name' => 'Sina Shayesteh',
                'type' =>'to',
            ];

            $this->sendMandrillEmail('colleges_daily_crm_report', $sina_email_arr, $email_params, $reply_email);
        }
        /////


        // Update last_sent to today's date.
        CrmAutoReporting::updateOrCreate($attributes, $values);

        return 'success';
    }

	/**
	 * USERS: A School Wants to Recruit You
	 * College recruited the student from recommendation list, email notification
	 *
	 * @return null
	 */
	public function collegeWantsToRecruitUsersFromRecommendation(){

		// SettingNotificationName ids
		$email_snn_id = 1;
		$text_snn_id  = 8;

		$reply_email = 'support@plexuss.com';

		$a_day_ago   = Carbon::yesterday();

		$college = DB::connection('bk')->table('colleges as c')

						->join('recruitment as r', 'c.id' , '=', 'r.college_id' )
						->join('users as u', 'u.id', '=', 'r.user_id')
						->leftjoin('colleges_admissions as ca', 'c.id', '=','ca.college_id')
						->leftjoin('colleges_enrollment as ce', 'c.id', '=','ce.college_id')
						->leftjoin('colleges_athletics as cat', 'c.id', '=','cat.college_id')
						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->leftjoin('colleges_tuition as ct', 'c.id', '=', 'ct.college_id')
						->leftjoin('priority as p', 'c.id', '=', 'p.college_id')
						->leftjoin('coveted_users as cu', 'cu.user_id', '=', 'u.id')
						->select('u.id as user_id','u.fname','u.lname','u.email',
								  'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state', 'c.id as college_id',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio',
								  'ca.deadline',
								  'cat.class_name as athletics',
								  'ct.tuition_avg_in_state_ftug as inStateTuition', 'ct.tuition_avg_out_state_ftug as outStateTuition',
								  'coi.url as img_url',
								  'ce.undergrad_full_time_total',
								  'ce.undergrad_part_time_total',
								  'r.id as rec_id', 'r.aor_id as aor_id')
						->where(function($college){
							$college = $college->orWhereNull('cu.id')
											   ->orWhereNotNull('p.id');
						})
						->where('r.status', 1)
						->where('r.user_recruit', 0)
						->where('r.college_recruit', 1)
						->where('r.email_sent', 0)
						->where('c.verified', 1)
						->whereNotNull('u.id')
						->where('r.created_at', '>=', $a_day_ago)
						->where('u.is_ldy', 0)
						->groupBy('rec_id')
						->orderBy('r.is_aor', 'DESC')
						->orderBy('r.id', 'DESC')
						->take(1000)
						->get();


		$recruitment_arr = array();


		foreach ($college as $key) {
			
			$email_arr = array();

			$email_arr = array('email' => $key->email, 
								   'name' => $key->fname. ' '. $key->lname,
								   'type' =>'to');

			$params = array();

			$params['EMAIL'] = $key->email;
			$params['FNAME'] = $key->fname;
			$params['LNAME'] = $key->lname;
			$params['COLLNAME'] = $key->school_name;	
			$params['COLLLOC'] = $key->city . ', '. $key->state;
            $params['COLLEGEID'] = $key->college_id;

			if (isset($key->logo_url)) {
				$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;	
			}else{
				$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
			}

			$params['COLLINK'] = 'https://plexuss.com/college/'.$key->slug;

			
			if (isset($key->gradRate)) {
				$params['GRADRATE'] = $key->gradRate;
			}else{
				$params['GRADRATE'] = 'N/A';
			}

			if (isset($key->studentRatio)) {
				$params['STDTCHRTIO'] = $key->studentRatio;
			}else{
				$params['STDTCHRTIO'] = 'N/A';
			}

			if (isset($key->deadline)) {
				$params['ADMSNDLN'] = $key->deadline;
			}else{
				$params['ADMSNDLN'] = 'N/A';
			}
			
			if (isset($key->athletics)) {
				$params['ATHLETICS'] = $key->athletics;
			}else{
				$params['ATHLETICS'] = 'N/A';
			}

			if (isset($key->inStateTuition)) {
				$params['INSTTUITN'] = '$'.number_format($key->inStateTuition);
			}else{
				$params['INSTTUITN'] = 'N/A';
			}

			if (isset($key->outStateTuition)) {
				$params['OUTSTTUITN'] = '$'.number_format($key->outStateTuition);
			}else{
				$params['OUTSTTUITN'] = 'N/A';
			}

			if (isset($key->undergrad_full_time_total)) {
				$params['FTUNDERGR'] = number_format($key->undergrad_full_time_total);
			}else{
				$params['FTUNDERGR'] = 'N/A';
			}

			if (isset($key->undergrad_part_time_total)) {
				$params['PTUNDERGR'] = number_format($key->undergrad_part_time_total);
			}else{
				$params['PTUNDERGR'] = 'N/A';
			}

			if (isset($key->img_url) && $key->img_url != "") {
				$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
			}else{
				$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
			}

			// if this person has not filtered email
			$email_snn = SettingNotificationLog::on('bk')
											   ->where('type', 'email')
											   ->where('user_id', $key->user_id)
											   ->where('snn_id', $email_snn_id)
											   ->first();

			if (!isset($email_snn)) {
				
				$this->template_name = 'college_wants_to_recruit_you';
				$this->sendMandrillEmail('college_wants_to_recruit_you',$email_arr, $params, $reply_email);
			}

			// if user is coveted (or 12 month portal match) and college is priority, send pending text
			$cu = CovetedUser::on('bk')->where('user_id', $key->user_id)->count();

			$pc = Priority::on('bk')->where('college_id', $key->college_id);
			if(isset($key->aor_id)){
				$pc = $pc->where('aor_id', $key->aor_id);
			}else{
				$pc = $pc->whereNull('aor_id');
			}
			$pc = $pc->count();

			$portal_match = $this->checkPortalMatch($key->college_id, $key->user_id, $key->aor_id, 'Next 12 Months');

			if(!empty($pc) && (!empty($cu) || $portal_match || isset($key->aor_id))){

				// if this person has not filtered text
				$text_snn = SettingNotificationLog::on('bk')
											   ->where('type', 'text')
											   ->where('user_id', $key->user_id)
											   ->where('snn_id', $text_snn_id)
											   ->first();
				if (!isset($text_snn)) {
					$user = User::on('bk')->where('id', $key->user_id)->first();

					$school = array();
					$school['school_name'] = $key->school_name;
		            $school['college_id'] = $key->college_id;
					// $tc = new TwilioController();
					// $tc->sendPendingTxt($user, $school);
				}
	
			}

			$recruitment_arr[] = $key->rec_id;
		}

		$affectedRows = Recruitment::whereIn('id',$recruitment_arr)->update(array('email_sent' => 1, 'email_sent_at' => Carbon::now()));
	}

	/**
	 * USERS: A School Agreed to Recruit You
	 * College agreed to reruit the student
	 *
	 * @param  name 	The school name that wants to recruit the student
	 * @param  user_id  The user id targetted 
	 * @return null
	 */
	public function collegeAgreedToRecruitYou($name, $user_id){

		$reply_email = 'support@plexuss.com';

		$user = User::find($user_id);

		$email_arr = array('email' => $user->email, 
							   'name' => $user->fname. ' '. $user->lname,
							   'type' =>'to');

		$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('recruitment as r', 'c.id' , '=', 'r.college_id' )
						->leftjoin('coveted_users as cu', 'r.user_id', '=', 'cu.user_id')
						->select('c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio',
								  'r.id as rec_id')
						->whereNull('cu.id')
						->where('c.school_name', $name)
						->where('r.user_id', $user_id)
						->where('c.verified', 1)
						->groupBy('rec_id')
						->first();

		$params = array();

		$params['EMAIL'] = $user->email;
		$params['FNAME'] = $user->fname;
		$params['LNAME'] = $user->lname;
		$params['COLLNAME'] = $college->school_name;
		$params['COLLLOC'] = $college->city . ', '. $college->state;
		if (isset($college->logo_url)) {
			$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;	
		}else{
			$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
		}


		$params['COLLINK'] = 'https://plexuss.com/college/'.$college->slug;

		$this->template_name = 'college_agreed_to_recruit_you';
		$this->sendMandrillEmail('college_agreed_to_recruit_you',$email_arr, $params, $reply_email);

		$affectedRows = Recruitment::where('id',$college->rec_id)->update(array('email_sent' => 1));
	}

	/**
	 * USERS: Schools Viewed Your Profile
	 * College viewed user's profile page email notification
	 *
	 * @return null
	 */
	public function collegeViewedYourProfile(){
		
		// SettingNotificationName ids
		$email_snn_id = 2;
		$text_snn_id  = 9;

		$reply_email = 'support@plexuss.com';

		$college = DB::connection('bk')->table('colleges as c')
						->join('notification_topnavs as nt', 'c.school_name', '=','nt.name')
						->join('users as u', 'u.id', '=','nt.type_id')
						->leftjoin('coveted_users as cu', 'u.id', '=', 'cu.user_id')
						->select('c.slug','c.school_name','c.logo_url','c.city','c.long_state as state',
							'u.fname','u.lname','u.email', 'u.id as user_id',
							'nt.id as ntId')
						->whereNull('cu.id')
						->where('nt.command', '1')
						//->where('nt.is_read', '=', "'0'")
						->whereRaw('nt.is_read = "0"')
						->where('nt.email_sent', 0)
						->where('c.verified', 1)
						->where('nt.type', 'user')
						->where('nt.updated_at', '<=', Carbon::now()->subMinutes(30))
						->where('u.is_ldy', 0)
						->groupBy('c.id', 'u.id')
						->orderBy('u.id', 'DESC')
						//->orderBy('nt.updated_at', 'DESC')
						->get();

		
		$email_arr = array();
		$params = array();
		$nt_arr = array();

		$cnt = 1;

		// echo "<pre>";
		// print_r($college);
		// echo "</pre>";
		// exit();
		foreach ($college as $key) {

			// if this person has not filtered email
			$email_snn = SettingNotificationLog::on('bk')
											   ->where('type', 'email')
											   ->where('user_id', $key->user_id)
											   ->where('snn_id', $email_snn_id)
											   ->first();

			if (isset($email_snn)) {
				continue;
			}
			if (isset($params) && isset($email_arr) && !empty($email_arr) && $email_arr['email'] != $key->email ) {
				
				$this->template_name = 'colleges_viewed_your_profile';
				$this->sendMandrillEmail('colleges_viewed_your_profile',$email_arr, $params, $reply_email);

				$email_arr = array();
				$params = array();

				$cnt = 1;
			}

			if (empty($email_arr)) {
				$email_arr = array('email' => $key->email, 
							   'name' => $key->fname. ' '. $key->lname,
							   'type' =>'to');
			}
			$nt_arr[] = $key->ntId;

			$params['EMAIL'] = $key->email;
			$params['FNAME'] = $key->fname;
			$params['LNAME'] = $key->lname;

			$params['COLLEGE'.$cnt] = $key->school_name;
			$params['COLL'.$cnt.'LOC'] = $key->city. ", ". $key->state;
			$params['COLL'.$cnt.'LOC'] = $key->city. ", ". $key->state;
			if (isset($key->logo_url)) {
				$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;	
			}else{
				$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
			}
			
			$params['COLL'.$cnt.'LINK'] = 'https://plexuss.com/college/'.$key->slug;
			$cnt++;
		}
		
		if (isset($params) && !empty($params)) {

			$this->template_name = 'colleges_viewed_your_profile';
			$this->sendMandrillEmail('colleges_viewed_your_profile',$email_arr, $params, $reply_email);

			$affectedRows = NotificationTopNav::whereIn('id',$nt_arr)->update(array('email_sent' => 1));
		}
		
	}

	/**
	 * USERS: College Recommendations
	 * User's recommendation list for the week email notification
	 * 
	 * @return null
	 */
	public function collegeRecommendationsForUsers(){

		$reply_email = 'support@plexuss.com';

		$college = DB::connection('bk')->table('colleges as c')
						->join('portal_notifications as pt', function($join)
						{
						    $join->on('pt.school_id', '=' , 'c.id');
						})

						->join('users as u', 'u.id', '=','pt.user_id')
						->leftjoin('coveted_users as cu', 'u.id', '=', 'cu.user_id')
						->select('c.slug','c.school_name','c.logo_url','c.city','c.long_state as state',
							'u.fname','u.lname','u.email')
						->whereNull('cu.id')
						->where('c.verified', 1)
						->where('pt.is_recommend', 1)
						->where('pt.created_at', '<=', Carbon::now()->tomorrow())
						->where('pt.created_at', '>=', Carbon::now()->subDays(7))
						->where('u.is_ldy', 0)
						->orderBy('pt.user_id')
						->get();
		
		$email_arr = array();
		$params = array();

		$cnt = 1;

		foreach ($college as $key) {

			if (isset($params) && isset($email_arr) && !empty($email_arr) && $email_arr['email'] != $key->email ) {
				
				$this->template_name = 'college_recommendation_for_users';
				$this->sendMandrillEmail('college_recommendation_for_users',$email_arr, $params, $reply_email);

				$email_arr = array();
				$params = array();

				$cnt = 1;
			}

			if (empty($email_arr)) {
				$email_arr = array('email' => $key->email, 
							   'name' => $key->fname. ' '. $key->lname,
							   'type' =>'to');
			}
			
			$params['EMAIL'] = $key->email;
			$params['FNAME'] = $key->fname;
			$params['LNAME'] = $key->lname;

			$params['COLLEGE'.$cnt] = $key->school_name;
			$params['COLL'.$cnt.'LOC'] = $key->city. ", ". $key->state;
			$params['COLL'.$cnt.'LOC'] = $key->city. ", ". $key->state;
			$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;
			$params['COLL'.$cnt.'LINK'] = 'https://plexuss.com/college/'.$key->slug;
			$cnt++;
		}
		
		$this->template_name = 'college_recommendation_for_users';
		$this->sendMandrillEmail('college_recommendation_for_users',$email_arr, $params, $reply_email);
	}

	/**
	 * USERS: A School Sent You a Message
	 * College sends a message to users email notification
	 *
	 * @return null
	 */
	public function collegesSendMessagesForUsers(){

		// SettingNotificationName ids
		$email_snn_id = 3;
		$text_snn_id  = 10;

		$reply_email = 'support@plexuss.com';

		$college = DB::connection('bk')->table('college_message_thread_members as cmtm')
						->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
						->join('users as u', 'u.id', '=','cmtm.user_id')
						->join('organization_branches as ob', function($join)
						{
						    $join->on('ob.id', '=', 'cmtm.org_branch_id');
						})
						->join('colleges as c', 'ob.school_id', '=', 'c.id' )
						->join('college_message_logs as cml', 'cml.thread_id', '=', 'cmtm.thread_id')
						->leftjoin('college_message_logs as cml2', function($join)
						{
						    $join->on('cml2.thread_id', '=', 'cmtm.thread_id');
						    $join->on('cml.id', '<', 'cml2.id');
						})
						->select('c.slug','c.school_name','c.logo_url','c.city','c.long_state as state', 'c.application_url', 'c.id as college_id',
								'u.fname','u.lname','u.email', 'u.id as user_id',
								'cml.msg', 'cml.user_id as cmlUserId',
								'cmtm.thread_id', 'cmtm.id as cmtmId')

						//->where('u.is_organization', '!=', 1)
						->whereNull('cmt.campaign_id')
						->where('u.is_organization', 0)
						->where('u.is_agency', 0)
						->where('u.is_plexuss', 0)
						->whereNull('cml2.id')
						->where('cmt.is_chat', 0)
						->where('cmtm.num_unread_msg', '!=', 0)
						->where('cmtm.email_sent', 0)
						->where('cmtm.updated_at', '<=', Carbon::now()->subMinutes(30))
						->where('cmtm.updated_at', '>=', Carbon::now()->today())
						->where('u.is_ldy', 0)
						->orderBy('c.id')
						->take(1000)
						->get();
		

		$email_arr = array();
		$params = array();
		$cmtm_arr = array();

		foreach ($college as $key) {
			
			// if this person has not filtered email
			$email_snn = SettingNotificationLog::on('bk')
											   ->where('type', 'email')
											   ->where('user_id', $key->user_id)
											   ->where('snn_id', $email_snn_id)
											   ->first();

			if (isset($email_snn)) {
				continue;
			}

			$cmtm_arr[] = $key->cmtmId;
	
			$email_arr = array('email' => $key->email, 
						   'name' => $key->fname. ' '. $key->lname,
						   'type' =>'to');
			
			$params['EMAIL'] = $key->email;
			$params['FNAME'] = $key->fname;
			$params['LNAME'] = $key->lname;
			$params['COLLEGE'] = $key->school_name;

			$sender_user = User::find($key->cmlUserId);

			$params['FROMCOLL'] = $sender_user->fname . " ". $sender_user->lname . " at ". $key->school_name;
			$params['MESSAGEPRV'] = $key->msg;
			$params['THREADID'] = Crypt::encrypt($key->thread_id);
			

			$rec = Recruitment::on('bk')->where('user_id',    $key->user_id)
									      ->where('college_id', $key->college_id)
									      ->first();		
			
			if (isset($rec)) {
				if ($rec->user_recruit == 1 && $rec->college_recruit == 1) {
					$params['ACTIONURL'] = $key->application_url;
					$params['ACTIONTYPE'] = 'application';
				}else{
					$params['ACTIONURL'] = 'https://plexuss.com/home?requestType=recruitme&collegeId='.$key->college_id;
					$params['ACTIONTYPE'] = 'recruit';
				}
			}else{
				$params['ACTIONURL'] = 'https://plexuss.com/home?requestType=recruitme&collegeId='.$key->college_id;
				$params['ACTIONTYPE'] = 'recruit';
			}
			
			$this->template_name = 'college_send_message_for_users';
			$this->sendMandrillEmail('college_send_message_for_users',$email_arr, $params, $reply_email);

		}
		
		if (!empty($cmtm_arr)) {
			$affectedRows = CollegeMessageThreadMembers::whereIn('id',$cmtm_arr)->update(array('email_sent' => 1));
		}
		
	}

	/**
	 * USERS: Engagement Email 1 - What is Plexuss (MDL)
	 * welcome email for users
	 *
	 * @return null
	 */
	public function welcomeEmailForUsers($user_id){

        $reply_email = 'social@plexuss.com';
        
        $user = User::find($user_id);
        $email_arr = array('email' => $user->email, 
                           'name' => $user->fname. ' '. $user->lname,
                           'type' =>'to');
        $params = array();
        $params['email'] = $user->email;
        $params['FNAME']         = trim(ucwords(strtolower($user->fname)));
        isset($user->lname)        ? $params['LNAME']       = ucwords(strtolower($user->lname)) : $params['LNAME'] = '';
        $params['NAME']          = $params['FNAME'] . " " . $params['LNAME'];
        $params['NAME']          = trim($params['NAME']);                
        isset($user->profile_img_loc) ? $params['USER_IMAGE_URL'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$user->profile_img_loc : $params['USER_IMAGE_URL'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';

        $this->template_name = 'welcome_email';
        $this->sendMandrillEmail($this->template_name, $email_arr, $params, $reply_email);
    }

	/**
	 * USERS: OneApp Demoted
	 * demote message for user OneApp
	 *
	 * @return null
	 */
	public function demoteOneAppEmailForUsers($user_id, $demoted_state){

		$reply_email = 'support@plexuss.com';

		$template_name = 'users_oneapp_demoted';
		
		$user_info = User::on('bk')->select('email', 'fname')->where('id', $user_id)->first();

		$params = [];
		$params['ONEAPP_LINK'] = env('CURRENT_URL') . 'college-application/' . $demoted_state;
		$params['FNAME'] = $user_info->fname;

		$email_arr = array('email' => $user_info->email, 
			   'name' => '',
			   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

		return "success";
	}

	/**
	 * USERS: OneApp Promoted
	 * demote message for user OneApp
	 *
	 * @return null
	 */
	public function promoteOneAppEmailForUsers($user_id, $demoted_state){

		$reply_email = 'support@plexuss.com';

		$template_name = 'users_oneapp_promoted';
		
		$user_info = User::on('bk')->select('email', 'fname')->where('id', $user_id)->first();

		$params = [];
		$params['ONEAPP_LINK'] = env('CURRENT_URL') . 'college-application/' . $demoted_state;
		$params['FNAME'] = $user_info->fname;

		$email_arr = array('email' => $user_info->email, 
			   'name' => '',
			   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

		return "success";
	}


	/**
	 * USERS: financial email
	 * international users only
	 *
	 * @return null
	 */
	public function financialEmailForUsers($user){

		$reply_email = 'support@plexuss.com';

		$email_arr = array('email' => $user->email, 
						   'name' => '',
						   'type' =>'to');

		$params['FNAME'] = ucfirst($user->fname);

		$this->template_name = 'users_financial_documents';
		$this->sendMandrillEmail($this->template_name, $email_arr, $params, $reply_email);

	}

	/**
	 * USERS: Engagement Email 2 - Recruitment (MDL)
	 * Engagement email for new users after a week
	 *
	 * @return null
	 */
	public function emailAfterOneWeekForUsers(){

		// User::where('created_at', '>=', Carbon::parse('last week 00:00:01 am'))
		// 	->where('created_at', '<=', Carbon::parse('last week 11:59:59 pm'))

		$this->reply_email = 'social@plexuss.com';

		$now1 = Carbon::today();
		$now2 = Carbon::today();
		
		$begin = $now1->subDays(7);
		$end   = $now2->subDays(6);
		
		User::on('bk')
			->leftjoin('coveted_users as cu', 'users.id', '=', 'cu.user_id')
			->whereNull('cu.id')
			->where('users.created_at', '>=', $begin)
			->where('users.created_at', '<', $end)
			->orderBy('users.id', 'DESC')
			->chunk(200, function($users){
		    foreach ($users as $key){

		        $email_arr = array('email' => $key->email, 
						   'name' => $key->fname. ' '. $key->lname,
						   'type' =>'to');
		        $params = array();

				$this->template_name = 'how_recruitment_work_after_one_week';
				$this->sendMandrillEmail($this->template_name, $email_arr, $params, $this->reply_email);
		    }
		});
	}

	/**
	 * USERS: Engagement Email 2 - Recruitment (MDL)
	 * Engagement email for new users after 2 weeks
	 *
	 * @return null
	 */
	public function emailAfterTwoWeekForUsers(){

		$this->reply_email = 'social@plexuss.com';

		$now1 = Carbon::today();
		$now2 = Carbon::today();
		
		$begin = $now1->subDays(14);
		$end   = $now2->subDays(13);

		User::on('bk')
			->leftjoin('coveted_users as cu', 'users.id', '=', 'cu.user_id')
			->whereNull('cu.id')
			->where('users.created_at', '>=', $begin)
			->where('users.created_at', '<', $end)
			->orderBy('users.id', 'DESC')
			->chunk(200, function($users){
		    foreach ($users as $key){

		        $email_arr = array('email' => $key->email, 
						   'name' => $key->fname. ' '. $key->lname,
						   'type' =>'to');
		        $params = array();

				$this->template_name = 'how_recruitment_work_after_two_week';
				$this->sendMandrillEmail($this->template_name, $email_arr, $params, $this->reply_email);
		    }
		});
	}

	/**
	 * USERS: Engagement Email 3 - Indicators (MDL)
	 * Engagement email for new users after 3 weeks
	 * @return null
	 */
	public function emailAfterThreeWeekForUsers(){

		$this->reply_email = 'social@plexuss.com';

		$now1 = Carbon::today();
		$now2 = Carbon::today();
		
		$begin = $now1->subDays(21);
		$end   = $now2->subDays(20);

		User::on('bk')
			->leftjoin('coveted_users as cu', 'users.id', '=', 'cu.user_id')
			->whereNull('cu.id')
			->where('users.created_at', '>=', $begin)
			->where('users.created_at', '<', $end)
		    ->where('users.profile_percent', '<', 30)
			->orderBy('users.id', 'DESC')
			->chunk(200, function($users){
			    foreach ($users as $key){

			        $email_arr = array('email' => $key->email, 
							   'name' => $key->fname. ' '. $key->lname,
							   'type' =>'to');
			        $params = array();

			        $params['FNAME'] = $key->fname;
			        $params['EMAIL'] = $key->email;

			        if (!isset($key->profile_percent) || $key->profile_percent == '') {
			        	$profile_percent = 0;
			        }else{
			        	$profile_percent = $key->profile_percent;
			        }

			        $params['PRFLPCNT'] = $profile_percent.'%';
			        $params['PRFLDIFF'] = 30 - $profile_percent.'%';

					$this->template_name = 'how_recruitment_work_after_three_week';
					$this->sendMandrillEmail($this->template_name, $email_arr, $params, $this->reply_email);
			    }
		});
	}

	/**
	 * USERS: Happy birthday!
	 * birthday email
	 *
	 * @return null
	 */
	public function birthdayEmailForUsers(){

		$this->reply_email = 'social@plexuss.com';

		$today = Carbon::now();

		$today = explode(" ", $today);

		$today = substr($today[0], 5);

		User::on('bk')
			->where('birth_date', 'LIKE', '%'.$today)
			->orderBy('id', 'DESC')
			->chunk(200, function($users){
		    foreach ($users as $key){

		        $email_arr = array('email' => $key->email, 
						   'name' => $key->fname. ' '. $key->lname,
						   'type' =>'to');
		        $params = array();

		        $params['FNAME'] = $key->fname;
		        $params['LNAME'] = $key->lname;

				$this->template_name = 'birthday_email';
				$this->sendMandrillEmail($this->template_name, $email_arr, $params, $this->reply_email);
		    }
		});
	}

	// User Automation methods ends here 

	// College Automation methods starts here 

	/**
	 * College: Users Want to Get Recruited
	 * users wants to get recruited by college email notification
	 *
	 * @return null
	 */
	public function usersWantToGetRecruitedForColleges(){

		$reply_email = 'collegeservices@plexuss.com';

		$college = DB::connection('bk')->table('colleges as c')
						->join('recruitment as r', 'r.college_id', '=','c.id')
						->join('users as u', 'u.id', '=','r.user_id')
						->leftjoin('scores as s', 's.user_id', '=','u.id')
						->leftjoin('colleges as c2', 'c2.id','=', 'u.current_school_id')
						->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')

						->select('c.id as college_id','c.slug','c.school_name','c.logo_url','c.city','c.state as state',
							'u.fname','u.lname','u.email', 'u.in_college', 'u.hs_grad_year', 'u.college_grad_year',
							's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.max_weighted_gpa', 's.weighted_gpa',
							'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
							'c2.school_name as userCollegeName',
							'r.id as rec_id')

						->where('c.verified', 1)
						->where('c.in_our_network', 1)
						->where('r.college_recruit', 0)
						->where('r.user_recruit', 1)
						->where('r.status', 1)
						->where('r.email_sent', 0)
						->where('r.created_at', '>=', Carbon::now()->subDays(7))
						->where('u.is_ldy', 0)
						->orderBy('c.id')
						->get();
		
		$email_arr = array();
		$params = array();
		$recruitment_arr = array();
		$college_id = '';

		$cnt = 1;

		foreach ($college as $key) {

			if ($college_id !='' && $college_id != $key->college_id) {
				

				$users = DB::table('users as u')
				->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')

				->where('u.is_organization', 1)
				->where('ob.school_id', $params['org_college_id'])
				->groupBy('u.id')
				->select('u.email', 'u.fname', 'u.lname')
				->get();
				
				$params['NUMINQRY'] = $cnt -1;
				foreach ($users as $k) {
					$email_arr = array('email' => $k->email, 
						   'name' => $k->fname. ' '. $k->lname,
						   'type' =>'to');

					$this->template_name = 'user_want_to_get_recruited_for_colleges';
					$this->sendMandrillEmail('user_want_to_get_recruited_for_colleges',$email_arr, $params, $reply_email);
				}

				$email_arr = array();
				$params = array();

				$cnt = 1;
			}

			$college_id = $key->college_id;

			$params['EMAIL'] = $key->email;

			$params['org_college_id'] = $key->college_id;

			$params['NAME'.$cnt] = ucwords(strtolower($key->fname)) . ' '. ucwords(strtolower($key->lname));

			if(isset($key->in_college) && $key->in_college == 1){
				
				if (isset($key->collegeName)) {
					$params['HS'.$cnt] = $key->collegeName;
				}else{
					$params['HS'.$cnt] = 'N/A';
				}
				if (isset($key->college_grad_year)) {
					$params['GRADYEAR'.$cnt] = $key->college_grad_year;
				}else{
					$params['GRADYEAR'.$cnt] = 'N/A';
				}
				
			}else{
				if (isset($key->hsName)) {
					$params['HS'.$cnt] = $key->hsName;
				}else{
					$params['HS'.$cnt] = 'N/A';
				}
				if (isset($key->hs_grad_year)) {
					$params['GRADYEAR'.$cnt] = $key->hs_grad_year;
				}else{
					$params['GRADYEAR'.$cnt] = 'N/A';
				}
			}

			if (isset($key->overall_gpa) && $key->overall_gpa != 0) {

				$params['GPA'.$cnt] = $key->overall_gpa;
			}elseif (isset($key->hs_gpa) && $key->hs_gpa != 0 && !isset($params['GPA'.$cnt])) {
				
				$params['GPA'.$cnt] = $key->hs_gpa;
			}elseif (isset($key->weighted_gpa) && $key->weighted_gpa != 0 && !isset($params['GPA'.$cnt])) {
				
				$params['GPA'.$cnt] = $key->weighted_gpa;
			}elseif (isset($key->max_weighted_gpa) && $key->max_weighted_gpa != 0 && !isset($params['GPA'.$cnt])) {
				
				$params['GPA'.$cnt] = $key->max_weighted_gpa;
			}else{
				$params['GPA'.$cnt] = 'N/A';
			}
			
			if (isset($key->sat_total)) {
				$params['SAT'.$cnt] = $key->sat_total;
			}else{
				$params['SAT'.$cnt] = 'N/A';
			}

			if (isset($key->act_composite)) {
				$params['ACT'.$cnt] = $key->act_composite;
			}else{
				$params['ACT'.$cnt] = 'N/A';
			}

			$params['NUMINQRY'] = $cnt;

			$recruitment_arr[] = $key->rec_id;

			$cnt++;
		}

		if (isset($params) && !empty($params)) {
			$users = DB::table('users as u')
				->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')

				->where('u.is_organization', 1)
				->where('ob.school_id', $params['org_college_id'])
				->groupBy('u.id')
				->select('u.email', 'u.fname', 'u.lname')
				->get();
				
			$params['NUMINQRY'] = $cnt -1;
			foreach ($users as $k) {
				$email_arr = array('email' => $k->email, 
					   'name' => $k->fname. ' '. $k->lname,
					   'type' =>'to');

				$this->template_name = 'user_want_to_get_recruited_for_colleges';
				$this->sendMandrillEmail('user_want_to_get_recruited_for_colleges',$email_arr, $params, $reply_email);
			}
		}

		$affectedRows = Recruitment::whereIn('id',$recruitment_arr)->update(array('email_sent' => 1));
	}

	/**
	 * Colleges: Users Accepts Request to Be Recruited
	 * users accept a college inquiry to get recruited email notification
	 *
	 * @return null
	 */
	public function usersAcceptRequestToBeRecruitedForColleges(){

		$reply_email = 'collegeservices@plexuss.com';

		$college = DB::connection('bk')->table('users as u')
						->join('notification_topnavs as nt', function($join){
							$join->on('nt.submited_id', '=', 'u.id');
							//$join->on('nt.type_id', '=', 'r.college_id');
						})
						->join('colleges as c2', 'c2.id', '=', 'nt.type_id')
						->join('recruitment as r', function($join) {
							$join->on('r.user_id', '=','u.id');
							$join->on('r.college_id', '=', 'c2.id');
						})

						->leftjoin('scores as s', 's.user_id', '=','u.id')
						->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
						
						->leftjoin('colleges as c', 'c.id', '=', 'u.current_school_id')

						->select('c.id as college_id','c.slug','c.school_name','c.logo_url','c.city','c.state as state',
							'u.fname','u.lname','u.email', 'u.in_college',
							's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.max_weighted_gpa', 's.weighted_gpa',
							'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
							'nt.type_id as this_college_id', 'nt.id as ntId',
							'c2.school_name as this_college_name')

						->where('nt.command', 2)
						->where('nt.email_sent', 0)
						->where('nt.type', 'college')
						->where('c2.verified', 1)
						->where('c2.in_our_network', 1)
						->where('r.college_recruit', 1)
						->where('r.user_recruit', 1)
						->where('r.status', 1)
						->where('u.is_ldy', 0)
						->orderBy('c.id')
						->groupBy('nt.id')
						->get();
		
		$email_arr = array();
		$params = array();
		$nt_arr = array();

		foreach ($college as $key) {

			$nt_arr[] = $key->ntId;

			$params['EMAIL'] = $key->email;

			$params['COLLNAME'] = $key->this_college_name;

			$params['NAME'] = $key->fname . ' '. $key->lname;

			if(isset($key->in_college) && $key->in_college == 1){
				
				if (isset($key->collegeName)) {
					$params['HS'] = $key->collegeName;
				}else{
					$params['HS'] = 'N/A';
				}
				if (isset($key->college_grad_year)) {
					$params['GRADYEAR'] = $key->college_grad_year;
				}else{
					$params['GRADYEAR'] = 'N/A';
				}
				
			}else{
				if (isset($key->hsName)) {
					$params['HS'] = $key->hsName;
				}else{
					$params['HS'] = 'N/A';
				}
				if (isset($key->hs_grad_year)) {
					$params['GRADYEAR'] = $key->hs_grad_year;
				}else{
					$params['GRADYEAR'] = 'N/A';
				}
			}

			if (isset($key->overall_gpa) && $key->overall_gpa != 0) {

				$params['GPA'] = $key->overall_gpa;
			}elseif (isset($key->hs_gpa) && $key->hs_gpa != 0 && !isset($params['GPA'])) {
				
				$params['GPA'] = $key->hs_gpa;
			}elseif (isset($key->weighted_gpa) && $key->weighted_gpa != 0 && !isset($params['GPA'])) {
				
				$params['GPA'] = $key->weighted_gpa;
			}elseif (isset($key->max_weighted_gpa) && $key->max_weighted_gpa != 0 && !isset($params['GPA'])) {
				
				$params['GPA'] = $key->max_weighted_gpa;
			}else{
				$params['GPA'] = 'N/A';
			}
			
			if (isset($key->sat_total)) {
				$params['SAT'] = $key->sat_total;
			}else{
				$params['SAT'] = 'N/A';
			}

			if (isset($k->act_composite)) {
				$params['ACT'] = $k->act_composite;
			}else{
				$params['ACT'] = 'N/A';
			}
			
			$users = DB::table('users as u')
				->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')

				->where('u.is_organization', 1)
				->where('ob.school_id', $key->this_college_id)
				->groupBy('u.id')
				->select('u.email', 'u.fname', 'u.lname')
				->get();
		
			foreach ($users as $key) {
				$email_arr = array('email' => $key->email, 
					   'name' => $key->fname. ' '. $key->lname,
					   'type' =>'to');
				
				$this->template_name = 'user_accepts_request_to_be_recruited_for_colleges';
				$this->sendMandrillEmail('user_accepts_request_to_be_recruited_for_colleges',$email_arr, $params, $reply_email);
			}	

		}

		if (!empty($nt_arr)) {
			$affectedRows = NotificationTopNav::whereIn('id',$nt_arr)->update(array('email_sent' => 1));
		}
		
	}

	/**
	 * COLLEGES: User Sends Your College a Message
	 * Colleges send user a message email notification
	 *
	 * @return null
	 */
	public function usersSendMessagesForColleges(){

		$reply_email = 'collegeservices@plexuss.com';

		$college = DB::connection('bk')->table('college_message_thread_members as cmtm')
						->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
						->join('users as u', 'u.id', '=','cmtm.user_id')
						->join('organization_branches as ob', function($join)
						{
						    $join->on('ob.id', '=', 'cmtm.org_branch_id');
						})
						->join('colleges as c', 'ob.school_id', '=', 'c.id' )
						->join('college_message_logs as cml', 'cml.thread_id', '=', 'cmtm.thread_id')
						->leftjoin('college_message_logs as cml2', function($join)
						{
						    $join->on('cml2.thread_id', '=', 'cmtm.thread_id');
						    $join->on('cml.id', '<', 'cml2.id');
						})

						->select('c.slug','c.school_name','c.logo_url','c.city','c.long_state as state',
								'u.fname','u.lname','u.email', 'u.id as user_id',
								'cml.msg', 'cml.user_id as cmlUserId',
								'cmtm.thread_id', 'cmtm.id as cmtmId')
						->where('u.is_organization', '=', 1)
						
						->whereRaw('cml.user_id NOT IN (select distinct user_id from organization_branch_permissions)')
						->where('cmt.is_chat', 0)
						->where('cmtm.num_unread_msg', '!=', 0)
						->where('cmtm.email_sent', 0)
						->where('cmtm.updated_at', '<=', Carbon::now()->subMinutes(30))
						->where('cmtm.updated_at', '>=', Carbon::now()->today())
						->where('u.is_ldy', 0)
						// ->where('u.id', 449344)
						->whereRaw('cml2.id IS NULL')
						->orderBy('c.id')
						->get();
		

		$email_arr = array();
		$params = array();
		$cmtm_arr = array();

		foreach ($college as $key) {

			$cmtm_arr[] = $key->cmtmId;
	
			$email_arr = array('email' => $key->email, 
						   'name' => $key->fname. ' '. $key->lname,
						   'type' =>'to');
			
			$params['EMAIL'] = $key->email;
			// $params['FNAME'] = $key->fname;
			// $params['LNAME'] = $key->lname;
			$params['COLLNAME'] = $key->school_name;

			$sender_user = User::find($key->cmlUserId);
			if (isset($sender_user)) {
				$params['FROMSTDNT'] = $sender_user->fname . " ". $sender_user->lname;
			}else{
				$params['FROMSTDNT'] = "A representative";
			}
			
			$params['MESSAGEPRV'] = $key->msg;
			$params['THREADID'] = Crypt::encrypt($key->thread_id);
			
			$this->template_name = 'user_send_message_for_colleges';
			$this->sendMandrillEmail('user_send_message_for_colleges',$email_arr, $params, $reply_email);

		}

		if (!empty($cmtm_arr)) {
			$affectedRows = CollegeMessageThreadMembers::whereIn('id',$cmtm_arr)->update(array('email_sent' => 1));
		}
	}

	/**
	 * College admin everyday recommendations
	 *
	 * @param  arr        The organizaiton school id 
	 * @param  users_arr  users ids that need to be supressed out of the recommendation list 
	 * @return null
	 */
	public function collegeAdminEverydayRecommendations($arr, $users_arr, $portal_qry=null){
		$reply_email = 'collegeservices@plexuss.com';
		if (isset($arr[0][0]['college_id'])) {
			$_college_id = $arr[0][0]['college_id'];
		}else{
			$_college_id = $arr[0]['college_id'];
		}

		if (isset($arr[0][0]['aor_id'])){
			$_aor_id = $arr[0][0]['aor_id'];
		}elseif(isset($arr[0]['aor_id'])){
			$_aor_id = $arr[0]['aor_id'];
		}else{
			$_aor_id = null;
		}

		$pc = Priority::on('bk')->where('college_id', $_college_id);
		if(isset($_aor_id)){
			$pc = $pc->where('aor_id', $_aor_id);
		}else{
			$pc = $pc->whereNull('aor_id');
		}
		$pc = $pc->count();

		if(!empty($pc)){
			$inquiries = DB::connection('bk')->table('recruitment')
											   ->where('status', 1)
											   ->where('college_recruit', 0)
											   ->where('user_recruit', 1)
											   ->where('college_id', $_college_id);
		}

		$users = DB::table('users as u')
			->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'u.id')
			->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')

			->where('u.is_organization', 1)
			->where('ob.school_id', $_college_id)
			->where('u.is_ldy', 0)
			->groupBy('u.id')
			->select('u.email', 'u.fname', 'u.lname')
			->addSelect(DB::raw("'client' as `client_type`"));

		if (isset($arr[0]['org_portal_id']) || isset($arr[0][0]['org_portal_id'])) {

			$portal_id = isset($arr[0]['org_portal_id']) ? $arr[0]['org_portal_id'] : $arr[0][0]['org_portal_id'];

			if (isset($portal_qry) && !empty($portal_qry) && isset($inquiries)) {
				$inquiries = $inquiries->join(DB::raw('('.$portal_qry.')  as t2'), 't2.userFilterId' , '=', 'recruitment.user_id');
			}

			$portal_name = OrganizationPortal::where('id', $portal_id)->pluck('name');

			$portal_name = ucwords($portal_name);
			if(strpos($portal_name, 'Portal') == false){
				$portal_name = $portal_name.' Portal';
			}

			$users = $users->join('organization_portals as op', 'op.org_branch_id', '=', 'ob.id')
						   ->join('organization_portal_users as opu', function($query){
						   		$query = $query->on('opu.org_portal_id', '=', 'op.id')
						   					   ->on('opu.user_id', '=', 'u.id');
						   })
						   ->where('op.active', 1)
						   ->where('opu.org_portal_id', $portal_id);
		}

		if (isset($_aor_id)) {
			$users = $users->where('u.is_aor', 1);
			if(isset($inquiries)){
				$inquiries = $inquiries->where('aor_id', $_aor_id);
			}
		}else{
			$users = $users->where('is_aor', 0);
			if(isset($inquiries)){
				$inquiries = $inquiries->whereNull('aor_id');
			}
		}

		$users = $users->get();

		if(isset($inquiries)){
			$inquiries = $inquiries->select(DB::raw('count(distinct user_id) as count'))
								   ->pluck('count');
			$inquiries = $inquiries[0];
		}

		
		$rec_users = count($users_arr);

		$params = array();

		$params['PORTAL_NAME'] = isset($portal_name) ? 'in your '.$portal_name : 'to you';

		if(isset($inquiries) && $inquiries == 1){
			$params['INQUIRY_SENTENCE'] = 'You also have 1 active inquiry from a prospective student.';
			$params['AND_INQUIRIES1'] = 'and inquiries';
			$params['AND_INQUIRIES2'] = 'and Inquiries';

		}elseif(empty($inquiries)){
			$params['INQUIRY_SENTENCE'] = '';
			$params['AND_INQUIRIES1'] = '';
			$params['AND_INQUIRIES2'] = '';

		}else{
			$params['INQUIRY_SENTENCE'] = 'You also have '.$inquiries.' active inquiries from prospective students.';
			$params['AND_INQUIRIES1'] = 'and inquiries';
			$params['AND_INQUIRIES2'] = 'and Inquiries';
		}

		$params['NUM_RECS'] = $rec_users;

		foreach ($users as $key) {

			$email_arr = array('email' => $key->email, 
							   'name' => $key->fname. ' '. $key->lname,
							   'type' =>'to');

			$params['EMAIL'] = $key->email;

			$this->template_name = 'colleges_daily_recs_and_inquiries';
			$this->sendMandrillEmail('colleges_daily_recs_and_inquiries', $email_arr, $params, $reply_email);
		}

	}

	/**
	 * COLLEGES: New Ranking
	 * A new ranking is uploaded and we email the college reps about it
	 *
	 * @return null
	 */
	public function newRankingForColleges(){

		$reply_email = 'collegeservices@plexuss.com';
		$today    = Carbon::today();
		$tomorrow = Carbon::tomorrow();

		DB::connection('bk')->table('potential_clients_1 as pt')
				->join('lists as l', 'l.custom_college', '=', 'pt.college_id')
				->join('colleges as c', 'c.id', '=', 'pt.college_id')
				->select('l.image', 'l.slug', 'c.school_name', 'l.title', 'l.rank_num', 'pt.email')
				->orderBy('pt.email')
				->groupBy('pt.email')
				->where('l.created_at', '>=', $today)
				->where('l.created_at', '<',  $tomorrow)
				->chunk(200, function($dt){

					foreach ($dt as $key) {
						$email_arr = array('email' => $key->email, 
										   'name' => '',
										   'type' =>'to');
						$params = array();

						$params['RNKLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/'. $key->image;
						$params['RNKLINK'] = 'https://plexuss.com/college/'.$key->slug.'/ranking';
						$params['COLNAME'] = $key->school_name;
						$params['RNKTITLE'] = $key->title;
						$params['RNKNUM']  = $key->rank_num;

						$this->template_name = 'new_college_ranking_for_colleges';
						$this->sendMandrillEmail($this->template_name, $email_arr, $params, $reply_email);
					}
		});
	}


	/**
	 * COLLEGES: User Sends Your College a Message
	 * Colleges send user a message email notification
	 *
	 * @return null
	 */
	public function usersSendMessagesForCollegesTEST(){

		$email_arr = array();
		$params = array();
		$cmtm_arr = array();

		$reply_email = 'collegeservices@plexuss.com';
	
		$email_arr = array('email' => 'anthony.shayesteh@gmail.com', 
					   'name' => 'Reza Shayesteh',
					   'type' =>'to');
		
		$params['EMAIL'] = 'anthony.shayesteh@gmail.com';
		// $params['FNAME'] = $key->fname;
		// $params['LNAME'] = $key->lname;
		$params['COLLNAME'] = 'College Name';

		$params['FROMSTDNT'] = "fromFname fromLname";
		$params['MESSAGEPRV'] = 'private message here';
		$params['THREADID'] = Crypt::encrypt(12);
		
		$this->template_name = 'test_message';
		$this->sendMandrillEmail('test_message',$email_arr, $params, $reply_email);

	}
	// College Automation methods ends here 

	public function confirmationEmailForUsers($name, $email, $confirmation){	

		$reply_email = 'support@plexuss.com';

		$email_arr = array('email' => $email, 
							   'name' => $name,
							   'type' =>'to');

		$params = array();

		$params['EMAIL'] = $email;
		$params['CNFRMLINK'] = 'https://plexuss.com/confirmemail/'.$confirmation;

		$this->template_name = 'new_user_confirmation_email';
		$this->sendMandrillEmail('new_user_confirmation_email',$email_arr, $params, $reply_email);
	}

	/**
	 * Send emails to Plexuss Sales team that admin user wants to become a paid member
	 *
	 * @return null
	 */
	public function requestToBecomeMember(){

		$reply_email = 'collegeservices@plexuss.com';

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$email_arr = array();

		$params = array();
		for ($i=0; $i <2 ; $i++) { 

			if ($i ==0) {
				$email_arr = array('email' => 'sina.shayesteh@plexuss.com', 
					   'name' => 'Sina Shayesteh',
					   'type' =>'to');
			}else{
				$email_arr = array('email' => 'collegeservices@plexuss.com', 
					   'name' => 'College Services',
					   'type' =>'to');
			}
			if (isset($data['school_name']) && !empty($data['school_name'])) {
				$params['COLLNAME'] = $data['school_name'];
				$user_table = Session::get('user_table');
				$params['ADMNPHONE'] = $user_table['phone'];
			}else{
				$params['COLLNAME'] = $data['agency_collection']->name;
				$params['ADMNPHONE'] = $data['agency_collection']->phone;
			}
			
			$params['ADMNNAME'] = $data['fname']. ' '.$data['lname'];
			$params['ADMNEMAIL'] = $data['email'];

			
			$this->template_name = 'paid_member_request_to_sales';
			$this->sendMandrillEmail('paid_member_request_to_sales',$email_arr, $params, $reply_email);
		}

	}

	public function userEmailColleges($template_name, $type_collection, $user){

		$reply_email = 'support@plexuss.com';

		$email_arr = array('email' => $user->email, 
							   'name' => $user->fname . ' ' . $user->lname,
							   'type' =>'to');

		$params = array();

		switch ($template_name) {
			case 'in_network_comparison':

				$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('colleges_admissions as ca', 'c.id', '=','ca.college_id')
						->leftjoin('colleges_enrollment as ce', 'c.id', '=','ce.college_id')
						->leftjoin('colleges_athletics as cat', 'c.id', '=','cat.college_id')
						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->leftjoin('colleges_tuition as ct', 'c.id', '=', 'ct.college_id')

						->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state', 'c.id as college_id',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'ca.deadline', 'ca.sat_read_75', 'ca.sat_write_75',
								  'ca.sat_read_25', 'ca.sat_write_25', 'ca.act_composite_25', 'ca.act_composite_25', 
								  'ca.act_composite_75', 'ca.act_composite_75',
								  'cat.class_name as athletics',
								  'ct.tuition_avg_in_state_ftug as inStateTuition', 'ct.tuition_avg_out_state_ftug as outStateTuition',
								  'coi.url as img_url')
						
						->where('c.id', $type_collection->school_id)
						->where('c.verified', 1)
						->first();

				$this_user = DB::table('users as u')
								->leftjoin('scores as s', 's.user_id', '=','u.id')
								->select('s.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.max_weighted_gpa', 's.weighted_gpa')
								->where('u.id', $user->id)
								->where('u.is_ldy', 0)
								->first();

				$params['COLLNAME'] = $college->school_name;

				if (isset($college->logo_url)) {
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;	
				}else{
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
				}
				$params['COLLLOC'] = $college->city . ", ". $college->state;
				$params['COLLLINK'] = 'https://plexuss.com/college/'.$college->slug;

				if (isset($college->img_url) && $college->img_url != "") {
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$college->img_url;
				}else{
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
				}

				if (isset($college->average_freshman_gpa)) {
					$params['COLGPA'] = $college->average_freshman_gpa;
				}else{
					$params['COLGPA'] = 'N/A';
				}
				
				$params['COLSAT25'] = $college->sat_read_25+$college->sat_write_25;
				$params['COLSAT75'] = $college->sat_read_75+$college->sat_write_75;
				$params['COLACT25'] = $college->act_composite_25;
				$params['COLACT75'] = $college->act_composite_75;
				$params['COLLEGEID'] = Crypt::encrypt($college->college_id);

				if (isset($this_user->overall_gpa) && $this_user->overall_gpa != 0) {

					$params['STDNTGPA'] = $this_user->overall_gpa;
				}elseif (isset($this_user->hs_gpa) && $this_user->hs_gpa != 0 && !isset($params['STDNTGPA'])) {
					
					$params['STDNTGPA'] = $this_user->hs_gpa;
				}elseif (isset($this_user->weighted_gpa) && $this_user->weighted_gpa != 0 && !isset($params['STDNTGPA'])) {
					
					$params['STDNTGPA'] = $this_user->weighted_gpa;
				}elseif (isset($this_user->max_weighted_gpa) && $this_user->max_weighted_gpa != 0 && !isset($params['STDNTGPA'])) {
					
					$params['STDNTGPA'] = $this_user->max_weighted_gpa;
				}else{
					$params['STDNTGPA'] = 'N/A';
				}
				
				if (isset($this_user->sat_total)) {
					$params['STDNTSAT'] = $this_user->sat_total;
				}else{
					$params['STDNTSAT'] = 'N/A';
				}

				if (isset($this_user->act_composite)) {
					$params['STDNTACT'] = $this_user->act_composite;
				}else{
					$params['STDNTACT'] = 'N/A';
				}
				break;

			case 'ranking_update':

				$college = DB::connection('bk')->table('colleges as c')
							->leftjoin('college_overview_images as coi', function($join)
								{
								    $join->on('c.id', '=', 'coi.college_id');
								    $join->on('coi.url', '!=', DB::raw('""'));
								})
							->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'coi.url as img_url')
						
							->where('c.id', $type_collection->school_id)
							->where('c.verified', 1)
							->first();

				
				$params['COLLNAME'] = $college->school_name;
				$params['COLLLINK'] = 'https://plexuss.com/college/'.$college->slug.'/ranking';
				if (isset($college->img_url) && $college->img_url != "") {
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$college->img_url;
				}else{
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
				}
				$params['RNKTITLE'] = $type_collection->title;
				$params['RNKNUM'] = $type_collection->rank_num;

				break;

			case 'near_you':

				$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('colleges_admissions as ca', 'c.id', '=','ca.college_id')
						->leftjoin('colleges_enrollment as ce', 'c.id', '=','ce.college_id')
						->leftjoin('colleges_athletics as cat', 'c.id', '=','cat.college_id')
						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->leftjoin('colleges_tuition as ct', 'c.id', '=', 'ct.college_id')

						->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state', 'c.id as college_id',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'ca.deadline', 'ca.sat_read_75', 'ca.sat_write_75',
								  'ca.sat_read_25', 'ca.sat_write_25', 'ca.act_composite_25', 'ca.act_composite_25', 
								  'ca.act_composite_75', 'ca.act_composite_75',
								  'cat.class_name as athletics',
								  'ct.tuition_avg_in_state_ftug as inStateTuition', 'ct.tuition_avg_out_state_ftug as outStateTuition',
								  'coi.url as img_url', 
								  'ce.undergrad_full_time_total', 'ce.undergrad_part_time_total')
						
						->where('c.id', $type_collection->college_id)
						->where('c.verified', 1)
						->first();

				$params['COLLNAME'] = $college->school_name;
				
				if (isset($college->logo_url)) {
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;	
				}else{
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
				}
				$params['COLLLOC'] = $college->city . ", ". $college->state;
				$params['COLLLINK'] = 'https://plexuss.com/college/'.$college->slug;
				if (isset($college->img_url) && $college->img_url != "") {
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$college->img_url;
				}else{
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
				}
				$params['ADMSNDLN'] = $college->deadline;
				$params['GRADRATE'] = $college->gradRate."%";
				$params['STDTCHRTIO'] = "1:".$college->studentRatio;
				$params['INSTTUITN'] = "$".$college->inStateTuition;
				$params['OUTSTTUITN'] = "$".$college->outStateTuition;
				$params['FTUNDERGR'] = $college->undergrad_full_time_total;
				$params['PTUNDERGR'] = $college->undergrad_part_time_total;
				$params['ATHLETICS'] = $college->athletics;
				$params['COLLEGEID'] = Crypt::encrypt($college->college_id);
				break;

			case 'chat_session':

				$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'coi.url as img_url')
						
						->whereIn('c.id', $type_collection)
						->where('c.verified', 1)
						->groupBy('c.id')
						->get();
				$cnt = 1;
				foreach ($college as $key) {
					$params['COLLEGE'.$cnt] = $key->school_name;
					$params['COLL'.$cnt.'LOC'] = $key->city . ", ". $key->state;
			
					if (isset($key->logo_url)) {
						$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url;	
					}else{
						$params['COLL'.$cnt.'LOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
					}
					$params['COLL'.$cnt.'LINK'] = 'https://plexuss.com/college/'.$key->slug;
					$cnt++;
				}
				// if (!isset($params['COLLEGE1']) || $params['COLLEGE1'] == '') {
				// 	$params['COLLEGE1'] = 'N/A';
				// 	$params['COLL1LOC'] = 'N/A';
				// 	$params['COLL1LOGO'] = 'N/A';
				// 	$params['COLL1LINK'] = 'N/A';
				// }

				// if (!isset($params['COLLEGE2']) || $params['COLLEGE2'] == '') {
				// 	$params['COLLEGE2'] = 'N/A';
				// 	$params['COLL2LOC'] = 'N/A';
				// 	$params['COLL2LOGO'] = 'N/A';
				// 	$params['COLL2LINK'] = 'N/A';
				// }

				// if (!isset($params['COLLEGE3']) || $params['COLLEGE3'] == '') {
				// 	$params['COLLEGE3'] = 'N/A';
				// 	$params['COLL3LOC'] = 'N/A';
				// 	$params['COLL3LOGO'] = 'N/A';
				// 	$params['COLL3LINK'] = 'N/A';
				// }

				// if (!isset($params['COLLEGE4']) || $params['COLLEGE4'] == '') {
				// 	$params['COLLEGE4'] = 'N/A';
				// 	$params['COLL4LOC'] = 'N/A';
				// 	$params['COLL4LOGO'] = 'N/A';
				// 	$params['COLL4LINK'] = 'N/A';
				// }


				break;

			case 'school_you_liked':
						
				$params['COLNMEB'] = $type_collection['college_name_you_liked'];
				$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('colleges_admissions as ca', 'c.id', '=','ca.college_id')
						->leftjoin('colleges_enrollment as ce', 'c.id', '=','ce.college_id')
						->leftjoin('colleges_athletics as cat', 'c.id', '=','cat.college_id')
						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->leftjoin('colleges_tuition as ct', 'c.id', '=', 'ct.college_id')

						->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state', 'c.id as college_id',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'ca.deadline', 'ca.sat_read_75', 'ca.sat_write_75',
								  'ca.sat_read_25', 'ca.sat_write_25', 'ca.act_composite_25', 'ca.act_composite_25', 
								  'ca.act_composite_75', 'ca.act_composite_75',
								  'cat.class_name as athletics',
								  'ct.tuition_avg_in_state_ftug as inStateTuition', 'ct.tuition_avg_out_state_ftug as outStateTuition',
								  'coi.url as img_url', 
								  'ce.undergrad_full_time_total', 'ce.undergrad_part_time_total')
						
						->where('c.id', $type_collection['college_id_recommended'])
						->where('c.verified', 1)
						->first();

				$params['COLLNAME'] = $college->school_name;
				if (isset($college->logo_url)) {
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;	
				}else{
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
				}
				$params['COLLLOC'] = $college->city . ", ". $college->state;
				$params['COLLLINK'] = 'https://plexuss.com/college/'.$college->slug;
				if (isset($college->img_url) && $college->img_url != "") {
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$college->img_url;
				}else{
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
				}
				$params['ADMSNDLN'] = $college->deadline;
				$params['GRADRATE'] = $college->gradRate."%";
				$params['STDTCHRTIO'] = '1:'.$college->studentRatio;
				$params['INSTTUITN'] = "$".$college->inStateTuition;
				$params['OUTSTTUITN'] = "$".$college->outStateTuition;
				$params['FTUNDERGR'] = $college->undergrad_full_time_total;
				$params['PTUNDERGR'] = $college->undergrad_part_time_total;
				$params['ATHLETICS'] = $college->athletics;
				$params['COLLEGEID'] = Crypt::encrypt($college->college_id);
				break;
			
			case 'users_in_network_school_you_messaged':
						
				$params['COLNMEB'] = $type_collection['college_name_you_messaged'];
				$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('colleges_admissions as ca', 'c.id', '=','ca.college_id')
						->leftjoin('colleges_enrollment as ce', 'c.id', '=','ce.college_id')
						->leftjoin('colleges_athletics as cat', 'c.id', '=','cat.college_id')
						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->leftjoin('colleges_tuition as ct', 'c.id', '=', 'ct.college_id')

						->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state', 'c.id as college_id',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'ca.deadline', 'ca.sat_read_75', 'ca.sat_write_75',
								  'ca.sat_read_25', 'ca.sat_write_25', 'ca.act_composite_25', 'ca.act_composite_25', 
								  'ca.act_composite_75', 'ca.act_composite_75',
								  'cat.class_name as athletics',
								  'ct.tuition_avg_in_state_ftug as inStateTuition', 'ct.tuition_avg_out_state_ftug as outStateTuition',
								  'coi.url as img_url', 
								  'ce.undergrad_full_time_total', 'ce.undergrad_part_time_total')
						
						->where('c.id', $type_collection['college_id_recommended'])
						->where('c.verified', 1)
						->first();

				$params['COLLNAME'] = $college->school_name;
				if (isset($college->logo_url)) {
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;	
				}else{
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
				}
				$params['COLLLOC'] = $college->city . ", ". $college->state;
				$params['COLLLINK'] = 'https://plexuss.com/college/'.$college->slug;
				if (isset($college->img_url) && $college->img_url != "") {
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$college->img_url;
				}else{
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
				}
				$params['ADMSNDLN'] = $college->deadline;
				$params['GRADRATE'] = $college->gradRate."%";
				$params['STDTCHRTIO'] = '1:'.$college->studentRatio;
				$params['INSTTUITN'] = "$".$college->inStateTuition;
				$params['OUTSTTUITN'] = "$".$college->outStateTuition;
				$params['FTUNDERGR'] = $college->undergrad_full_time_total;
				$params['PTUNDERGR'] = $college->undergrad_part_time_total;
				$params['ATHLETICS'] = $college->athletics;
				$params['COLLEGEID'] = Crypt::encrypt($college->college_id);
				break;

			case 'users_in_network_school_u_wanted_to_get_recruited':
						
				$params['COLNMEB'] = $type_collection['college_name_you_recruited'];
				$college = DB::connection('bk')->table('colleges as c')

						->leftjoin('colleges_admissions as ca', 'c.id', '=','ca.college_id')
						->leftjoin('colleges_enrollment as ce', 'c.id', '=','ce.college_id')
						->leftjoin('colleges_athletics as cat', 'c.id', '=','cat.college_id')
						->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						})

						->leftjoin('colleges_tuition as ct', 'c.id', '=', 'ct.college_id')

						->select( 'c.slug', 'c.school_name', 'c.logo_url', 'c.city', 'c.long_state as state', 'c.id as college_id',
								  'c.graduation_rate_4_year as gradRate', 'c.student_faculty_ratio as studentRatio', 'c.average_freshman_gpa',
								  'ca.deadline', 'ca.sat_read_75', 'ca.sat_write_75',
								  'ca.sat_read_25', 'ca.sat_write_25', 'ca.act_composite_25', 'ca.act_composite_25', 
								  'ca.act_composite_75', 'ca.act_composite_75',
								  'cat.class_name as athletics',
								  'ct.tuition_avg_in_state_ftug as inStateTuition', 'ct.tuition_avg_out_state_ftug as outStateTuition',
								  'coi.url as img_url', 
								  'ce.undergrad_full_time_total', 'ce.undergrad_part_time_total')
						
						->where('c.id', $type_collection['college_id_recommended'])
						->where('c.verified', 1)
						->first();

				$params['COLLNAME'] = $college->school_name;
				if (isset($college->logo_url)) {
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$college->logo_url;	
				}else{
					$params['COLLLOGO'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png';
				}
				$params['COLLLOC'] = $college->city . ", ". $college->state;
				$params['COLLLINK'] = 'https://plexuss.com/college/'.$college->slug;
				if (isset($college->img_url) && $college->img_url != "") {
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$college->img_url;
				}else{
					$params['COLLPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
				}
				$params['ADMSNDLN'] = $college->deadline;
				$params['GRADRATE'] = $college->gradRate."%";
				$params['STDTCHRTIO'] = '1:'.$college->studentRatio;
				$params['INSTTUITN'] = "$".$college->inStateTuition;
				$params['OUTSTTUITN'] = "$".$college->outStateTuition;
				$params['FTUNDERGR'] = $college->undergrad_full_time_total;
				$params['PTUNDERGR'] = $college->undergrad_part_time_total;
				$params['ATHLETICS'] = $college->athletics;
				$params['COLLEGEID'] = Crypt::encrypt($college->college_id);
				break;
				
			default:
				# code...
				break;
		}

		$template_name = $template_name. '_college_users_email';
		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);
	}

    /**
     * College self-signup request. This email will only be sent internally
     *
     * @param $user_id which will be used to extract other data from the database
     */
    public function collegesSelfSignUpRequest($user_id) {
        $reply_email = 'support@plexuss.com';
        $template_name = 'colleges_college_self_sign_up_request';
        $email_arr = [];

        $tmp = array();
        $tmp['email'] = 'sina.shayesteh@plexuss.com';
        $tmp['name'] = "Sina Shayesteh";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'jp.novin@plexuss.com';
        $tmp['name'] = "Jp Novin";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'anthony.shayesteh@plexuss.com';
        $tmp['name'] = "Anthony Shayesteh";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        if (!isset($user_id)) {
            return;
        }

        $params = [
            'USER_ID' => $user_id,
            'REP_NAME' => '',
            'COLLEGE_ID' => '',
            'EMAIL' => '',
            'PHONE' => '',
            'SKYPE' => '',
            'COLLEGE_SELF_SIGNUP_APPLICATIONS_ID' => '',
        ];

        $application = CollegeSelfSignupApplications::on('bk')
                                                    ->where('user_id', $user_id)
                                                    ->first();
        if (isset($application)) {
            if (isset($application->agreement_name)) {
                $params['REP_NAME'] = $application->agreement_name;
            }

            if (isset($application->college_id)) {
                $params['COLLEGE_ID'] = $application->college_id;
            }

            if (isset($application->id)) {
                $params['COLLEGE_SELF_SIGNUP_APPLICATIONS_ID'] = $application->id;
            }

            if (isset($application->skype_id)) {
                $params['SKYPE'] = $application->skype_id;
            }
        }

        $profile = User::on('bk')
                       ->where('id', $user_id)
                       ->first();

        if (isset($profile)) {
            if (isset($profile->email)) {
                $params['EMAIL'] = $profile->email;
            }

            if (isset($profile->phone)) {
                $params['PHONE'] = $profile->phone;
            }
        }

        foreach ($email_arr as $key) {
            $this->template_name = $template_name;
            $this->sendMandrillEmail($template_name, $key, $params, $reply_email, NULL, NULL);
        }

        $msg = $params["REP_NAME"]. " has requested to sign up for the Colleges Freemium Service. \n \n" . "college_self_signup_applications id: " . $params["COLLEGE_SELF_SIGNUP_APPLICATIONS_ID"] . "\n" . "Representative Name: " . $params['REP_NAME'] . "\n" . "user_id: " . $params['USER_ID'] . "\n" . "college id: " . $params['COLLEGE_ID'] . "\n" . "Email: " . $params['EMAIL'] . "\n" . "Phone: " . $params['PHONE'] . "\n" . "Skype: " . $params['SKYPE'] . "\n";

        $tc = new TwilioController;
        $tc->sendPlexussMsg($msg);
    }

    public function collegeInterestPremiumServices($services){
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();
        $template_name = 'colleges_college_interested_in_premium_service';

        $reply_email = 'support@plexuss.com';
        $email_arr = [];

        $tmp = array();
        $tmp['email'] = 'sina.shayesteh@plexuss.com';
        $tmp['name'] = "Sina Shayesteh";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'jp.novin@plexuss.com';
        $tmp['name'] = "Jp Novin";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'anthony.shayesteh@plexuss.com';
        $tmp['name'] = "Anthony Shayesteh";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $params = [
            'FULL_NAME' => $data['fname'] . ' ' . $data['lname'],
            'ORG_NAME' => $data['org_name'],
            'USER_ID' => $data['user_id'],
            'EMAIL' => $data['email'],
            'PHONE' => $data['phone'],
            'COLLEGE_ID' => $data['org_school_id'],
            'SERVICES' => implode(", ", $services),
        ];

        foreach ($email_arr as $key) {
            $this->template_name = $template_name;
            $this->sendMandrillEmail($template_name, $key, $params, $reply_email, NULL, NULL);
        }

        $msg = $params["ORG_NAME"]. " has expressed interest in using Plexuss Premium Services. \n \n" . "Name: " . $params["FULL_NAME"] . "\n" . "user id: " . $params['USER_ID'] . "\n" . "college id: " . $params['COLLEGE_ID'] . "\n" . "Email: " . $params['EMAIL'] . "\n" . "Phone: " . $params['PHONE'] . "\n" . "Services: " . $params['SERVICES'] . "\n";

        $tc = new TwilioController;
        $tc->sendPlexussMsg($msg);
    }

    public function alertInternalScholarshipSubmission($data, $user){
        
        $template_name = 'internal_scholarship_submission';

        $reply_email = 'support@plexuss.com';
        $email_arr = [];

        $tmp = array();
        $tmp['email'] = 'sina.shayesteh@plexuss.com';
        $tmp['name'] = "Sina Shayesteh";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'jp.novin@plexuss.com';
        $tmp['name'] = "Jp Novin";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'anthony.shayesteh@plexuss.com';
        $tmp['name'] = "Anthony Shayesteh";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

        $params = [
            'FULL_NAME' => $user->fname . ' ' . $data['lname'],
            'TITLE' => $data->scholarship_title,
            'CONTACT' => $data->contact,
            'PHONE' => $data->phone,
            'EMAIL' => $data->email,
            'DEADLINE' => $data->deadline,
            'NUMBER_OF_AWARDS' => $data->number_of_awards,
            'MAX_AMOUNT' => $data->max_amount,
            'WEBSITE' => $data->website,
            'SCHOLARSHIP_DESCRIPTION' => $data->scholarship_description,
            'SERVICES' => $data->interested_service,
            'USER_ID' => $user->id,
        ];

        foreach ($email_arr as $key) {
            $this->template_name = $template_name;
            $this->sendMandrillEmail($template_name, $key, $params, $reply_email, NULL, NULL);
        }

        $msg = "Someone has expressed interest in using Plexuss Scholarship Services. \n \n" . "Name: " . $params["FULL_NAME"] . "\n" . "user id: " . $params['USER_ID'] . "\n" . " Title: " . $params['TITLE'] . "\n" . "Contact: " . $params['CONTACT'] . "\n" . "Phone: " . $params['PHONE'] . "\n" . "Email: " . $params['EMAIL'] . "\n" . "Deadline: " . $params['DEADLINE'] . "\n";

        $tc = new TwilioController;
        $tc->sendPlexussMsg($msg);
    }
	// Agency methods start here

	/**
	 * Agency signup request. This email will only be sent internally.
	 *
	 * @param  data        contains all the data required for the email
	 * @return null
	 */
	public function agencySignUpRequest($user_id){
		$reply_email = 'support@plexuss.com';
		$template_name = 'agencies_agency_sign_up_request';
		$services = [];
		$email_arr = [];

		if (!isset($user_id)) {
			return;
		}

		$params = [];
		$params['AGENCY_PROFILE_INFO_ID'] = '';
		$params['AGENCY_NAME'] = '';
		$params['AGENCY_WEBSITE'] = '';
		$params['REP_NAME'] = '';
		$params['PHONE'] = '';
		$params['EMAIL'] = '';
		$params['COUNTRY'] = '';
		$params['SERVICES'] = '';
		$params['COUNTRY'] = '';

		$agency_profile = AgencyProfileInfo::on('bk')
										   ->where('user_id', $user_id)
										   ->first();

		if (isset($agency_profile)) {
			$params['AGENCY_PROFILE_INFO_ID'] = $agency_profile->id;

			if (isset($agency_profile->company_name)) {
				$params['AGENCY_NAME'] = $agency_profile->company_name;
			}

			if (isset($agency_profile->website_url)) {
				$params['AGENCY_WEBSITE'] = $agency_profile->website_url;
			}

			if (isset($agency_profile->representative_name)) {
				$params['REP_NAME'] = $agency_profile->representative_name;
			}

			$params['SKYPE_ID'] = isset($agency_profile->skype_id) ? $agency_profile->skype_id : 'N/A';

			$user_profile = User::on('bk')
							->where('id', $user_id)
							->first();

			if (isset($user_profile)) {
				if (isset($user_profile->phone)) {
					$params['PHONE'] = $user_profile->phone;
				}

				if (isset($user_profile->phone)) {
					$params['EMAIL'] = $user_profile->email;
				}

				if (isset($user_profile->country_id)) {
					$country = Country::find($user_profile->country_id);
					$params['COUNTRY'] = $country->country_name;
				}
			}

			$services_qry = AgencyProfileInfoServices::on('bk')
				->where('agency_profile_info_id', $agency_profile->id)
				->get();

			foreach ($services_qry as $key => $service) {
				$services[] = $service->service_name;
			}

			if (!empty($services))
				$params['SERVICES'] = join(', ', $services);

			$tmp = array();
			$tmp['email'] = 'sina.shayesteh@plexuss.com';
			$tmp['name'] = "Sina Shayesteh";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

			$tmp = array();
			$tmp['email'] = 'jp.novin@plexuss.com';
			$tmp['name'] = "Jp Novin";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

			$tmp = array();
			$tmp['email'] = 'anthony.shayesteh@plexuss.com';
			$tmp['name'] = "Anthony Shayesteh";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

			foreach ($email_arr as $key) {
				$this->template_name = $template_name;
				$this->sendMandrillEmail($template_name, $key, $params, $reply_email, NULL, NULL);
			}

			$msg = $params["REP_NAME"]. " of ". $params["AGENCY_NAME"] ." has requested to sign up for the International Agency Service. \n \n" . "agency_profile_info id: " . $params["AGENCY_PROFILE_INFO_ID"] . "\n" . "Agency Name: " . $params['AGENCY_NAME'] . "\n" . "Representative Name: " . $params['REP_NAME'] . "\n" . "Country: " . $params["COUNTRY"] . "\n" . "Phone: " . $params['PHONE'] . "\n" . "Email: " . $params['EMAIL'] . "\n" . "Company Website: " . $params['AGENCY_WEBSITE']. "\n" . "Skype ID: " . $params['SKYPE_ID'];

			$tc = new TwilioController;
			$tc->sendPlexussMsg($msg);
		}
	}

	/**
	 * Send an email to a user indicating Agency wants to recruit them.
	 *
	 * @return null
	 */
	public function agencyWantsToRecuruitUser($receiver_id = null){

		$reply_email = 'support@plexuss.com';

		if ($receiver_id == null) {
			return;
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$email_arr = array();

		$params = array();

		$user = User::find($receiver_id);
		
		$email_arr = array('email' => $user->email, 
					   'name' => $user->fname . ' '. $user->lname,
					   'type' =>'to');

		$agency =  $data['agency_collection'];
		
		$ar = AgencyRecruitment::where('user_id', $receiver_id)
								->where('agency_id', $agency->agency_id)
								->first();

		$params['AGENCY_IMG'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'. $agency->logo_url;
		$params['AGENCY_NAME'] = $agency->name;

		$services = '';

		if(isset($college_counseling) && $college_counseling == 1){
			$services .= 'College Counseling, ';
		}

		if(isset($tutoring_center) && $tutoring_center == 1){
			$services .= 'Tutoring Center, ';
		}

		if(isset($test_preparation) && $test_preparation == 1){
			$services .= 'Test Preparation, ';
		}

		if(isset($international_student_assistance) && $international_student_assistance == 1){
			$services .= 'International Student Assistance, ';
		}

		$sba = ServicesByAgency::where('agency_id', $agency->agency_id)->get();

		foreach ($sba as $key) {
			$services .= $key->name.', ';
		}

		$services = substr($services, 0, -2);
		$params['SERVICES_OFFERED'] = $services;
		$params['WEB_URL'] = $agency->web_url;
		$params['COUNTRY'] = $agency->country;
		$params['AGENCY_ABOUT'] = $agency->detail;
		$params['TOKEN'] = $ar->token;
		$params['LOCATION'] = $agency->city . ' ,'. $agency->country;

		$template_name = '';

		if ($agency->type == 'College Prep') {
			$template_name = 'college_prep_recruiting_in_area';
		}
		if ($agency->type == 'International Agency') {
			$template_name = 'agency_recruiting_visa_help';
		}
		if ($agency->type == 'English Institution') {
			$template_name = 'english_recruiting_in_area';
		}

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name,$email_arr, $params, $reply_email);
	}

	public function userEmailAgencies($template_name, $type_collection, $user){

		$reply_email = 'support@plexuss.com';

		$email_arr = array('email' => $user->email, 
							   'name' => $user->fname . ' ' . $user->lname,
							   'type' =>'to');
		$params = array();

		$college_counseling = $type_collection->college_counseling;
		$tutoring_center = $type_collection->tutoring_center;
		$test_preparation =$type_collection->test_preparation;
		$international_student_assistance = $type_collection->international_student_assistance;

		if(isset($college_counseling)){
			$college_counseling = 1;
		}else{
			$college_counseling = 0;
		}
		if(isset($tutoring_center)){
			$tutoring_center = 1;
		}else{
			$tutoring_center = 0;
		}
		if(isset($test_preparation)){
			$test_preparation = 1;
		}else{
			$test_preparation = 0;
		}
		if(isset($international_student_assistance)){
			$international_student_assistance = 1;
		}else{
			$international_student_assistance = 0;
		}

		$services = '';

		if(isset($college_counseling) && $college_counseling == 1){
			$services .= 'College Counseling, ';
		}

		if(isset($tutoring_center) && $tutoring_center == 1){
			$services .= 'Tutoring Center, ';
		}

		if(isset($test_preparation) && $test_preparation == 1){
			$services .= 'Test Preparation, ';
		}

		if(isset($international_student_assistance) && $international_student_assistance == 1){
			$services .= 'International Student Assistance, ';
		}

		$sba = ServicesByAgency::where('agency_id', $type_collection->id)->get();

		foreach ($sba as $key) {
			$services .= $key->name.', ';
		}

		$services = substr($services, 0, -2);

		$params['AGENCY_IMG'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/'.$type_collection->logo_url;
		$params['AGENCY_NAME'] = $type_collection->name;
		$params['SERVICES_OFFERED'] = $services;
		$params['WEB_URL'] = $type_collection->web_url;
		$params['COUNTRY'] = $type_collection->country;
		$params['AGENCY_ABOUT'] = $type_collection->detail;
		$params['LINKVALUE'] = 'https://plexuss.com/agency/agencyUserInquiry/'.urlencode(Crypt::encrypt($type_collection->id));
		$params['LOCATION'] = $type_collection->city. ' '. $type_collection->country;

		$template_name = $template_name. '_agency_users_email';
		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);
	}

	/**
	 * Agency everyday recommendations
	 *
	 * @param  arr        The agency id 
	 * @param  users_arr  users ids that need to be supressed out of the recommendation list 
	 * @return null
	 */
	public function agencyAdminEverydayRecommendations($arr, $users_arr){

		$reply_email = 'collegeservices@plexuss.com';

		if (isset($arr[0][0]['agency_id'])) {
			$_agency_id = $arr[0][0]['agency_id'];
		}else{
			$_agency_id = $arr[0]['agency_id'];
		}

		$users = DB::table('agency as a')
 					->join('agency_permissions as ap', 'a.id', '=', 'ap.agency_id')
 					->join('users as u', 'u.id', '=', 'ap.user_id')
 					->where('a.id', $_agency_id)
 					->select('email', 'fname', 'lname')
 					->where('u.is_ldy', 0)
 					->get();;


		$rec_users = DB::table('users as u')
			
			->leftjoin('scores as s', 's.user_id', '=','u.id')
			->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
			->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')

			->groupBy('u.id')
			->select('u.email', 'u.fname', 'u.lname', 'u.in_college', 'u.id as user_id', 'u.hs_grad_year', 'u.college_grad_year',
				's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.weighted_gpa', 's.max_weighted_gpa',
				'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
				'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState')
			->whereIn('u.id', $users_arr)
			->get();

		foreach ($users as $key) {
			
			$cnt = 1;
			$email_arr = array('email' => $key->email, 
							   'name' => $key->fname. ' '. $key->lname,
							   'type' =>'to');
			$params = array();

			foreach ($rec_users as $k) {
				
				$params['EMAIL'] = $key->email;

				$params['NAME'.$cnt] = $k->fname . ' '. $k->lname;

				if(isset($k->in_college) && $k->in_college == 1){
					
					if (isset($k->collegeName)) {
						$params['HS'.$cnt] = $k->collegeName;
					}else{
						$params['HS'.$cnt] = 'N/A';
					}
					if (isset($k->college_grad_year)) {
						$params['GRADYEAR'.$cnt] = $k->college_grad_year;
					}else{
						$params['GRADYEAR'.$cnt] = 'N/A';
					}
					
				}else{
					if (isset($k->hsName)) {
						$params['HS'.$cnt] = $k->hsName;
					}else{
						$params['HS'.$cnt] = 'N/A';
					}
					if (isset($k->hs_grad_year)) {
						$params['GRADYEAR'.$cnt] = $k->hs_grad_year;
					}else{
						$params['GRADYEAR'.$cnt] = 'N/A';
					}
				}

				if (isset($k->overall_gpa) && $k->overall_gpa != 0) {

					$params['GPA'.$cnt] = $k->overall_gpa;
				}elseif (isset($k->hs_gpa) && $k->hs_gpa != 0 && !isset($params['GPA'.$cnt])) {
					
					$params['GPA'.$cnt] = $k->hs_gpa;
				}elseif (isset($k->weighted_gpa) && $k->weighted_gpa != 0 && !isset($params['GPA'.$cnt])) {
					
					$params['GPA'.$cnt] = $k->weighted_gpa;
				}elseif (isset($k->max_weighted_gpa) && $k->max_weighted_gpa != 0 && !isset($params['GPA'.$cnt])) {
					
					$params['GPA'.$cnt] = $k->max_weighted_gpa;
				}else{
					$params['GPA'.$cnt] = 'N/A';
				}
				
				if (isset($k->sat_total)) {
					$params['SAT'.$cnt] = $k->sat_total;
				}else{
					$params['SAT'.$cnt] = 'N/A';
				}

				if (isset($k->act_composite)) {
					$params['ACT'.$cnt] = $k->act_composite;
				}else{
					$params['ACT'.$cnt] = 'N/A';
				}

				$cnt++;
			}

			$this->template_name = 'college_recommendations';
			$this->sendMandrillEmail('college_recommendations',$email_arr, $params, $reply_email);
			
		}
	}

	/**
	 * Agency Generate leads everyday.
	 *
	 * @return null
	 */
	public function agencyGenerateNewLeads(){

		$reply_email = 'collegeservices@plexuss.com';

		$users = DB::table('agency as a')
 					->join('agency_permissions as ap', 'a.id', '=', 'ap.agency_id')
 					->join('users as u', 'u.id', '=', 'ap.user_id')
 					
 					->select('email', 'fname', 'lname', 'a.id as agency_id')
 					->where('u.is_ldy', 0)
 					->where('a.active', 1)
 					->groupBy('a.id')
 					->get();;


		

		foreach ($users as $key) {
			
			// $key->email = "anthony.shayesteh@plexuss.com";

			$cnt = 1;
			$email_arr = array('email' => $key->email, 
							   'name' => $key->fname. ' '. $key->lname,
							   'type' =>'to');
			$params = array();

			$ac = new AgencyController();

			$dt = array();
			$dt['agency_collection'] = new \stdClass;
			$dt['agency_collection']->agency_id = $key->agency_id;

			$res  = $ac->generateAgencyLeads($dt);
			$res  = json_decode($res); 

			if ($res->status != "success" || empty($res->rec_user_ids)) {
				continue;
			}
			$users_arr = $res->rec_user_ids;

			$rec_users = DB::table('users as u')
			
							->leftjoin('scores as s', 's.user_id', '=','u.id')
							->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
							->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')

							->groupBy('u.id')
							->select('u.email', 'u.fname', 'u.lname', 'u.in_college', 'u.id as user_id', 'u.hs_grad_year', 'u.college_grad_year',
								's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.weighted_gpa', 's.max_weighted_gpa',
								'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
								'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState', 'u.profile_img_loc')
							->whereIn('u.id', $users_arr)
							->get();

			foreach ($rec_users as $k) {
				
				$params['EMAIL'] = $key->email;

				$params['NAME'.$cnt] = $k->fname . ' '. $k->lname;

				$params['IMG'.$cnt] = $k->profile_img_loc;

				if(isset($k->in_college) && $k->in_college == 1){
					
					if (isset($k->collegeName)) {
						$params['HS'.$cnt] = $k->collegeName;
					}else{
						$params['HS'.$cnt] = 'N/A';
					}
					if (isset($k->college_grad_year)) {
						$params['GRADYEAR'.$cnt] = $k->college_grad_year;
					}else{
						$params['GRADYEAR'.$cnt] = 'N/A';
					}
					
				}else{
					if (isset($k->hsName)) {
						$params['HS'.$cnt] = $k->hsName;
					}else{
						$params['HS'.$cnt] = 'N/A';
					}
					if (isset($k->hs_grad_year)) {
						$params['GRADYEAR'.$cnt] = $k->hs_grad_year;
					}else{
						$params['GRADYEAR'.$cnt] = 'N/A';
					}
				}

				if (isset($k->overall_gpa) && $k->overall_gpa != 0) {

					$params['GPA'.$cnt] = $k->overall_gpa;
				}elseif (isset($k->hs_gpa) && $k->hs_gpa != 0 && !isset($params['GPA'.$cnt])) {
					
					$params['GPA'.$cnt] = $k->hs_gpa;
				}elseif (isset($k->weighted_gpa) && $k->weighted_gpa != 0 && !isset($params['GPA'.$cnt])) {
					
					$params['GPA'.$cnt] = $k->weighted_gpa;
				}elseif (isset($k->max_weighted_gpa) && $k->max_weighted_gpa != 0 && !isset($params['GPA'.$cnt])) {
					
					$params['GPA'.$cnt] = $k->max_weighted_gpa;
				}else{
					$params['GPA'.$cnt] = 'N/A';
				}
				
				if (isset($k->sat_total)) {
					$params['SAT'.$cnt] = $k->sat_total;
				}else{
					$params['SAT'.$cnt] = 'N/A';
				}

				if (isset($k->act_composite)) {
					$params['ACT'.$cnt] = $k->act_composite;
				}else{
					$params['ACT'.$cnt] = 'N/A';
				}

				$cnt++;
			}

			$this->template_name = 'college_recommendations';
			$this->sendMandrillEmail('college_recommendations',$email_arr, $params, $reply_email);

		}
	}
	/**
	 * Agency: Users Want to Get Recruited
	 * users wants to get recruited by agenct email notification
	 *
	 * @return null
	 */
	public function usersWantToGetRecruitedForAgencies(){

		$reply_email = 'collegeservices@plexuss.com';

		$agency = DB::connection('bk')->table('agency as a')
						->join('agency_recruitment as r', 'r.agency_id', '=','a.id')
						->join('users as u', 'u.id', '=','r.user_id')
						->leftjoin('scores as s', 's.user_id', '=','u.id')
						->leftjoin('colleges as c2', 'c2.id','=', 'u.current_school_id')
						->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')


						->select('a.id as agency_id', 'a.type as agency_type', 'a.name as agency_name',
								 'r.id as rec_id','r.email_sent',
								 's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 
								 's.max_weighted_gpa', 's.weighted_gpa', 's.toefl_total', 's.ielts_total',
								 'u.fname','u.lname','u.email', 'u.in_college', 'u.hs_grad_year', 'u.college_grad_year',
								 'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
								 'c2.school_name as userCollegeName')

						->where('r.agency_recruit', 0)
						->where('r.user_recruit', 1)
						->where('r.active', 1)
						->where('r.email_sent', 0)
						->where('r.created_at', '<=', Carbon::now()->subMinutes(30))
						->where('u.is_ldy', 0)
						->orderBy('a.id')
						->get();
		
		
		$recruitment_arr = array();

		$template_name = '';

		foreach ($agency as $key) {

			$email_arr = array();
			$params = array();

			if ($key->agency_type == 'College Prep') {
				$params['CPNAME'] = $key->agency_name;
				$template_name = 'college_prep_user_interested_in_your_services';
			}
			if ($key->agency_type == 'International Agency') {
				$params['AGNTNAME'] = $key->agency_name;
				$template_name = 'agencies_user_interested_in_your_services';
			}
			if ($key->agency_type == 'English Institution') {
				$params['ENGPNAME'] = $key->agency_name;
				$template_name = 'english_program_user_interested_in_your_services';
			}

			$params['NAME'] = ucwords(strtolower($key->fname)).' '.ucwords(strtolower($key->lname));
			$params['EMAIL'] = $key->email;

			if(isset($key->in_college) && $key->in_college == 1){
				
				if (isset($key->collegeName)) {
					$params['HS'] = $key->collegeName;
				}else{
					$params['HS'] = 'N/A';
				}
				if (isset($key->college_grad_year)) {
					$params['GRADYEAR'] = $key->college_grad_year;
				}else{
					$params['GRADYEAR'] = 'N/A';
				}
				
			}else{
				if (isset($key->hsName)) {
					$params['HS'] = $key->hsName;
				}else{
					$params['HS'] = 'N/A';
				}
				if (isset($key->hs_grad_year)) {
					$params['GRADYEAR'] = $key->hs_grad_year;
				}else{
					$params['GRADYEAR'] = 'N/A';
				}
			}

			if (isset($key->overall_gpa) && $key->overall_gpa != 0) {

				$params['GPA'] = $key->overall_gpa;
			}elseif (isset($key->hs_gpa) && $key->hs_gpa != 0 && !isset($params['GPA'])) {
				
				$params['GPA'] = $key->hs_gpa;
			}elseif (isset($key->weighted_gpa) && $key->weighted_gpa != 0 && !isset($params['GPA'])) {
				
				$params['GPA'] = $key->weighted_gpa;
			}elseif (isset($key->max_weighted_gpa) && $key->max_weighted_gpa != 0 && !isset($params['GPA'])) {
				
				$params['GPA'] = $key->max_weighted_gpa;
			}else{
				$params['GPA'] = 'N/A';
			}
			
			if (isset($key->sat_total)) {
				$params['SAT'] = $key->sat_total;
			}else{
				$params['SAT'] = 'N/A';
			}

			if (isset($key->act_composite)) {
				$params['ACT'] = $key->act_composite;
			}else{
				$params['ACT'] = 'N/A';
			}

			if (isset($key->toefl_total)) {
				$params['TOEFL'] = $key->toefl_total;
			}else{
				$params['TOEFL'] = 'N/A';
			}

			if (isset($key->ielts_total)) {
				$params['IELTS'] = $key->ielts_total;
			}else{
				$params['IELTS'] = 'N/A';
			}

			$recruitment_arr[] = $key->rec_id;

			$users = DB::table('agency as a')
				->join('agency_permissions as ap', 'ap.agency_id', '=', 'a.id')
				->join('users as u', 'u.id', '=', 'ap.user_id')

				->where('u.is_agency', 1)
				->where('a.id', $key->agency_id)
				->groupBy('u.id')
				->select('u.email', 'u.fname', 'u.lname')
				->get();
		
			foreach ($users as $key) {
				$email_arr = array('email' => $key->email, 
					   'name' => $key->fname. ' '. $key->lname,
					   'type' =>'to');

				$this->template_name = $template_name;
				$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);
			}	
		}

		$affectedRows = AgencyRecruitment::whereIn('id',$recruitment_arr)->update(array('email_sent' => 1));
		
	}

	// Agency methods ends here

	// Infilaw methods start here

	public function sendInfilawCollegeEmail($isu = null){

		$reply_email = 'support@plexuss.com';

		if ($isu != null && $isu->college_emailed == 1) {
			return;
		}

		$isu = InfilawSurveyUser::where('is_finished' , 1)
								->where('college_emailed', 0)
								->first();

		if (!isset($isu)) {
			return;
		}
		$template_name = 'infilaw_survey_completion_notification_and_data';
		$email_arr = array();
		$params = array();

		$tmp = array();
		$tmp['email'] = 'anthony.shayesteh@plexuss.com';
		$tmp['name'] = "Anthony Shay";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

		if (env('ENVIRONMENT') != 'DEV') {
			$tmp = array();
			$tmp['email'] = 'sina.shayesteh@plexuss.com';
			$tmp['name'] = "Sina Shayesteh";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

			$tmp = array();
			$tmp['email'] = 'jp.novin@plexuss.com';
			$tmp['name'] = "Jp Novin";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

			$tmp = array();
			$tmp['email'] = 'sdukes@infilaw.com';
			$tmp['name'] = "Shannon Dukes";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

			$tmp = array();
			$tmp['email'] = 'scrumbley@infilaw.com';
			$tmp['name'] = "Sarah Crumbley";
			$tmp['type'] = 'to';

			$email_arr[] = $tmp;

		}

		$params['SCHOOL'] =    (isset($isu->school_name) ? $isu->school_name : '');
		$params['NAME'] =      (isset($isu->name) ? $isu->name : '');
		$params['EMAIL'] = 	   (isset($isu->email) ? $isu->email : '');
		$params['PHONE'] =     (isset($isu->phone) ? $isu->phone : '');
		$params['ADDRESS'] =   (isset($isu->address) ? $isu->address : '');
		$params['LSRATING'] =  (isset($isu->experience_satisfy) ? $isu->experience_satisfy : '');
		$params['CAREERPLC'] = (isset($isu->career_satisfy) ? $isu->career_satisfy : '');
		$params['NETWORK'] =   (isset($isu->networking_alum) ? $isu->networking_alum : '');
		$params['INLEGAL'] =   (isset($isu->in_legal) ? $isu->in_legal : '');
		$params['INCOME'] =    (isset($isu->income) ? $isu->income : '');
		$params['STIPEND'] =   (isset($isu->interested_in_stipend) ? $isu->interested_in_stipend : '');
		$params['BARPASS'] =   (isset($isu->pass_bar) ? $isu->pass_bar : '');
		$params['BARSTATE'] =  (isset($isu->bar_state) ? $isu->bar_state : '');
		$params['PRACTICING'] =(isset($isu->practicing_attorney) ? $isu->practicing_attorney : '');
		$params['EMPLOYER'] =  (isset($isu->current_employer) ? $isu->current_employer : '');

		$params['EXPERIENCE'] = '';
		if (isset($isu->practicing_attorney_experience)) {
			$pae_arr = explode(",", $isu->practicing_attorney_experience);

			$ipae = InfilawPracticingAttorneyExperiences::whereIn('id', $pae_arr)->get();

			$temp_str = '';
			foreach ($ipae as $key) {
				$temp_str .= $key->name.",";
			}
			$params['EXPERIENCE'] = $temp_str;
		}

		$params['STARTDATE'] = (isset($isu->start_date) ? $isu->start_date : '');
		$params['TITLE'] =     (isset($isu->title) ? $isu->title : '');
		$params['EXTINCOME'] = (isset($isu->exact_income) ? $isu->exact_income : '');


		foreach ($email_arr as $key) {
			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $key, $params, $reply_email);
		}


		// We have emailed this person!
		$isu->college_emailed = 1;
		$isu->save();


	}

	public function sendInfilawAmazonCode(){

		$reply_email = 'support@plexuss.com';

		$iac = DB::table('infilaw_amazon_codes as iac')
					->join('infilaw_survey_users as isu', 'isu.id', '=', 'iac.isu_id')
					->where('iac.emailed', 0)
					->where('claimed_code', '!=', 1)
					->select('isu.school_name', 'isu.name', 'iac.id as iac_id', 'iac.claimed_code', 'iac.awarded_email')
					->first();
					
		if (!isset($iac)) {
			return;
		}

		if ($iac->school_name == 'Florida Coastal School of Law') {
			$template_name = 'fcsl_amazon_code';
		}else{
			$template_name = 'csl_amazon_code';
		}
		
		$email_arr = array();
		$params = array();

		$email_arr = array('email' => $iac->awarded_email, 
					   'name' => $iac->name,
					   'type' =>'to');

		$params['FNAME'] = $iac->name;
		$params['SCHOOL'] = $iac->school_name;
		$params['CODE'] = $iac->claimed_code;

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email);

		$tmp = InfilawAmazonCode::find($iac->iac_id);

		$tmp->emailed = 1;
		$tmp->save();
	}

	// Infilaw methods ends here

	/**
	 * collegeWeeklyEmail: Send a weekly email for colleges with list of their all time students
	 *
	 * @return null
	 */
	public function collegeWeeklyEmail(){

		$obp = DB::connection('bk')->table('organization_branch_permissions as obp')
									->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
									->join('colleges as c', 'c.id', '=', 'ob.school_id')
									->join('users as u', 'u.id', '=', 'obp.user_id')
									->where('ob.num_of_applications', '>', 0)
									->select('c.school_name', 'u.fname', 'u.lname', 'u.email',
											 'ob.premier_trial_end_date', 'c.id as college_id',
											 'ob.num_of_applications', 'ob.num_of_enrollments', 
											 'ob.id as org_branch_id', 'ob.slug')
									// ->where('obp.user_id', 93)
									->get();
		$usrCnt = new User;
		$rec = new Recruitment;
		$ac = new AjaxController;

		$usrCnts = $usrCnt->totalUserCount();

		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'colleges_weekly_plexuss_update_mdl';

		foreach ($obp as $key) {
			$email = $key->email;
			$fname = $key->fname;
			$lname = $key->lname;

			$email_arr = array('email' => $email, 
					   'name' => $fname. ' '. $lname,
					   'type' =>'to');

			$params = array();

			$params['COLLNAME']  = $key->school_name; 
			$params['FNAME']     = $key->fname;	

			$approvedGoal  = ceil(($key->num_of_applications * 10 ) /12);

			if ($approvedGoal > 0) {
				$firstDayMonth = Carbon::now()->startOfMonth()->toDateTimeString();
				$tmp = $rec->getNumOfTotalApprovedForColleges(array($key->college_id), $firstDayMonth);
				foreach ($tmp as $k) {
					$approvedMonthly =  $k->cnt;
				}
				$approvedMonthly =  isset($approvedMonthly) ? $approvedMonthly : 0;

				$temp_mon_perc = $approvedMonthly / $approvedGoal;
				$params['APPRVMTLYPERC']  = $temp_mon_perc > 1 ? 100 : (int)($temp_mon_perc * 100);
				$params['APPRVMTLYPERC']  = $params['APPRVMTLYPERC'];
				$params['APPRVMTLY'] = $approvedMonthly;


				$firstDayYear = Carbon::now()->startOfYear()->toDateTimeString();
				$tmp = $rec->getNumOfTotalApprovedForColleges(array($key->college_id), $firstDayYear);
				foreach ($tmp as $k) {
					$approvedAnnually =  $k->cnt;
				}
				$approvedAnnually =  isset($approvedAnnually) ? $approvedAnnually : 0;
				$temp_annual_perc = $approvedAnnually / ($approvedGoal * 12);
				$params['APPRVANNUALLYPERC'] = $temp_annual_perc > 1 ? 100 : (int)($temp_annual_perc * 100);
				$params['APPRVANNUALLYPERC']  = $params['APPRVANNUALLYPERC'];
				$params['APPRVANNUALLY'] = $approvedAnnually;

				$params['APPRVANNUALLYGOAL'] = $approvedGoal * 12;

			}else{
				$params['APPRVMTLYPERC']  = 0;
				$params['APPRVMTLY'] = 0;

				$params['APPRVANNUALLYPERC']  = 0;
				$params['APPRVANNUALLY'] = 0;

				$params['APPRVANNUALLYGOAL'] = 0;
			}
			
			if (isset($key->premier_trial_end_date) && !empty($key->premier_trial_end_date)) {
				
				$today = date('Y-m-d');

                $trial_date = $key->premier_trial_end_date;
                $datetime1 = new DateTime($today);
                $datetime2 = new DateTime($trial_date);

                $interval = $datetime1->diff($datetime2);

                $params['DAYS_TRL'] = $interval->format('%R%a');

                $params['DAYS_TRL'] = str_replace('+', '', $params['DAYS_TRL']);


				$params['DAYS_TRL']  = $params['DAYS_TRL'] > 0 ? $params['DAYS_TRL'] : 'N/A';
			}else{
				$params['DAYS_TRL']  = 'N/A';
			}
			$params['USR_CNT']   = $usrCnts;

			$params['STDNTPND']  = Recruitment::where('college_id', $key->college_id)
						  ->where('status', 1)
						  ->where('user_recruit', 0)
						  ->where('college_recruit', 1)
						  ->count();

			$params['APPRVGOAL'] = $approvedGoal;

			// goal setting info
			$app_or_enr_array = array();
			$applied = 0;
			$enrolled = 0;
			$app_perc = 0;
			$enr_perc = 0;

			$recrt = DB::table('recruitment')
					->where('college_id', '=', $key->college_id)
					->where('applied', '=', 1)
					->orWhere('enrolled', '=', 1)
					->get();

			foreach ($recrt as $k) {
				if( $k->applied == 1 ){
					$applied++;
				}

				if( $k->enrolled == 1 ){
					$enrolled++;
				}
			}
			$params['APPLICATIONCOUNT'] = $applied;
			$params['ENROLLMENTCOUNT'] = $enrolled;

			if( $key->num_of_applications > 0 ){
				$app_perc = $applied / $key->num_of_applications;
				$params['APPLICATIONPERCENT'] = $app_perc > 1 ? 100 : (int)($app_perc * 100) ;
			}else{
				$params['APPLICATIONPERCENT'] = 0;
			}

			if( $key->num_of_enrollments > 0 ){
				$enr_perc = $enrolled / $key->num_of_enrollments;
				$params['ENROLLMENTPERCENT'] = $enr_perc > 1 ? 100 : (int)($enr_perc * 100) ;
			}else{
				$params['ENROLLMENTPERCENT'] = 0;
			}

			$params['APPLICATIONGOAL'] = isset($key->num_of_applications) ? $key->num_of_applications : 0;
			$params['ENROLLMENTGOAL'] = isset($key->num_of_enrollments) ? $key->num_of_enrollments : 0;

			$ac_arr = array();
			$sevenDaysAgo = Carbon::now()->subDays(7);
			$from = $sevenDaysAgo->toDateString();
			$from = date("m-d-Y", strtotime($from));
			$from = str_replace("-", "/", $from);

			$today = Carbon::today()->toDateString();
			$today = date("m-d-Y", strtotime($today));
			$today = str_replace("-", "/", $today);
			
			$ac_arr['date'] = $from. ' to '. $today;
			$ac_arr['org_school_id'] = $key->college_id;
			$ac_arr['user_id'] = -1;
			$ac_arr['org_branch_id'] = $key->org_branch_id;
			$ac_arr['school_name'] = ucwords(str_replace("-", "_", $key->slug));

			// Select all of the export fields here. these are the ids in export_fields table
			for ($i=1; $i <29 ; $i++) { 
				$ac_arr[$i] = $i;
			}
	
			$tmp_ac = $ac->exportApprovedStudentsFile('admin', $ac_arr);
			$path = storage_path();
			$path = $path."/excel/exports/".$tmp_ac;
			$file = file_get_contents($path, FILE_USE_INCLUDE_PATH);
			$file_base64 = base64_encode ($file);

			$attachments = array();
			$attachments['type'] = 'application/vnd.ms-excel';
			$attachments['name'] = $tmp_ac;
			$attachments['content'] = $file_base64;

			$bcc_address =  "collegeservices@plexuss.com";

			$this->template_name = $template_name;
			$this->sendThroughMandrill($template_name, $email_arr, $params, $reply_email, $attachments, $bcc_address);

			//remove file from server
			unlink($path);
		}
	}

	/**
	 * emeregencyTrigger: Send an emergency email if the school approved goal is less than emeregency percentage
	 *
	 * @return null
	 */
	public function emeregencyTrigger(){

		$today = Carbon::today();
		$firstDayMonth = Carbon::now()->startOfMonth();
		$seventhtDayMonth = $firstDayMonth->addDays(7);
		
		if ($today > $seventhtDayMonth) {
			$ob = OrganizationBranch::on('bk')->whereNotNull('trigger_notify_emails')
												 ->where('trigger_emergency_set', 1)
												 ->where('trigger_emergency_percentage', '>', 0)
												 ->where(function ($query) use($today) {
													  $query->orWhereNull('trigger_sent_on')
													        ->orWhere('trigger_sent_on', '<', $today);
												  })
												 ->join('colleges as c', 'c.id', '=', 'organization_branches.school_id')
												 ->select('organization_branches.*', 'c.school_name')
												 ->get();
			$this->emeregencyTemplate($ob);
		}
	}

	/**
	 * emeregencyTrigger: Send an emergency email if the school approved goal is less than emeregency percentage
	 *
	 * @return null
	 */
	public function normalTrigger($frequency){

		$ob = OrganizationBranch::on('bk')->whereNotNull('trigger_notify_emails')
											 ->where('trigger_set', 1)
											 ->where('trigger_frequency', $frequency)
											 ->join('colleges as c', 'c.id', '=', 'organization_branches.school_id')
											 ->select('organization_branches.*', 'c.school_name')
											 ->get();
		$this->emeregencyTemplate($ob);

	}

	/**
	 * emeregencyTemplate: a template to send all kinds of triggers
	 *
	 * @return null
	 */
	public function emeregencyTemplate($ob){

		$today = Carbon::today();
		$firstDayMonth = Carbon::now()->startOfMonth()->toDateTimeString();
		$lastDayMonth  = Carbon::now()->lastOfMonth()->toDateTimeString();
		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'internal_triggers';
		$rec = new Recruitment;

		foreach ($ob as $key) {
			
			$params = array();

			$params['COLLNAME'] = $key->school_name;

			$approvedGoal  = ceil(($key->num_of_applications * 10 ) /12);

			if ($approvedGoal > 0) {
				$firstDayMonth = Carbon::now()->startOfMonth()->toDateTimeString();
				$tmp = $rec->getNumOfTotalApprovedForColleges(array($key->school_id), $firstDayMonth);
				foreach ($tmp as $k) {
					$approvedMonthly =  $k->cnt;
				}
				$approvedMonthly =  isset($approvedMonthly) ? $approvedMonthly : 0;

				$temp_mon_perc = $approvedMonthly / $approvedGoal;
				$params['APPRVMTLYPERC']  = $temp_mon_perc > 1 ? 100 : (int)($temp_mon_perc * 100);
				$params['APPRVMTLY'] = $approvedMonthly;
				$params['APPRVMTLYGOAL'] = $approvedGoal;

			}else{
				$params['APPRVMTLYPERC']  = 0;
				$params['APPRVMTLY'] 	  = 0;
				$params['APPRVMTLYGOAL']  = 0;
			}
			
			//if approved percent is 0, or its bigger than emregency percentage we don't need to send the email
			if (isset($key->trigger_emergency_percentage)) {
				if ($params['APPRVMTLYPERC'] == 0 || ($params['APPRVMTLYPERC'] > $key->trigger_emergency_percentage)) {
					continue;
				}
			}

			// goal setting info
			$app_or_enr_array = array();
			$applied = 0;
			$enrolled = 0;
			$app_perc = 0;
			$enr_perc = 0;

			$recrt = Recruitment::on('bk')->where('college_id', $key->school_id)
											->where('applied', 1)
											->whereBetween('applied_at', array($firstDayMonth, $lastDayMonth))
											->get();

			foreach ($recrt as $k) {
				if( $k->applied == 1 ){
					$applied++;
				}
			}

			$recrt = Recruitment::on('bk')->where('college_id', $key->school_id)
											->where('enrolled', 1)
											->whereBetween('enrolled_at', array($firstDayMonth, $lastDayMonth))
											->get();

			foreach ($recrt as $k) {
				if( $k->enrolled == 1 ){
					$enrolled++;
				}
			}

			$params['APPLDMTLY'] = $applied;
			$params['ENRLLMTLY'] = $enrolled;

			if( $key->num_of_applications > 0 ){
				$app_perc = $applied / $key->num_of_applications;
				$params['APPLDMTLYPERC'] = $app_perc > 1 ? 100 : (int)($app_perc * 100) ;
			}else{
				$params['APPLDMTLYPERC'] = 0;
			}

			if( $key->num_of_enrollments > 0 ){
				$enr_perc = $enrolled / $key->num_of_enrollments;
				$params['ENRLLMTLYPERC'] = $enr_perc > 1 ? 100 : (int)($enr_perc * 100) ;
			}else{
				$params['ENRLLMTLYPERC'] = 0;
			}

			$params['APPLDMTLYGOAL'] = isset($key->num_of_applications) ? $key->num_of_applications : 0;
			$params['ENRLLMTLYGOAL'] = isset($key->num_of_enrollments) ? $key->num_of_enrollments : 0;
			
			if (isset($key->trigger_emergency_percentage)) {
				$params['TRIGRPCNT']     = $key->trigger_emergency_percentage - $params['APPRVMTLYPERC'];
			}else{
				$params['TRIGRPCNT']     = 0;
			}
			

			$trigger_notify_emails = $key->trigger_notify_emails;
			$trigger_notify_emails_arr = explode(",", $trigger_notify_emails);
			
			foreach ($trigger_notify_emails_arr as $k => $v) {
				$email_arr = array('email' => $v, 
					   'name' => '',
					   'type' =>'to');
				$bcc_address =  "jp.novin@plexuss.com";

				$this->template_name = $template_name;
				$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, $bcc_address);
			}

			$ob_tmp = OrganizationBranch::find($key->id);
			$ob_tmp->trigger_sent_on = Carbon::now()->toDateTimeString();
			$ob_tmp->save();
		}

	}
	/**
	 * emeregencyTemplate: a template to send all kinds of triggers
	 *
	 * @return null
	 */
	public function marchMadnessEmail(){

		$reply_email = 'social@plexuss.com';
		$template_name = 'march_madness_invite_2016';
		$params = array();
		
		$hsc = HighSchoolContact::where('sent', 0)->take(2000)->get();
		//$hsc = HighSchoolContact::where('email', 'linkinster@gmail.com')->take(1000)->get();

		foreach ($hsc as $key) {

			$email_arr = array('email' => $key->email, 
					   'name' => '',
					   'type' =>'to');
			$key->sent = 1;
			$key->save();

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		}
	}

	/**
	 * adminUpgradeRequest: a template to ask Plexuss to upgrade their admin account
	 *
	 * @return null
	 */
	public function adminUpgradeRequest(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$ob = OrganizationBranch::find($data['org_branch_id']);
		$ob->requested_upgrade = 1;
		$ob->save();

		Session::put('userinfo.session_reset', 1);

		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'internal_college_upgrade_to_premiere_request';
		$params = array();
		
		$arr = array();
		$arr[]['email'] = 'jp.novin@plexuss.com';
		$arr[]['email'] = 'sina.shayesteh@plexuss.com'; 
		// $arr[]['email'] = 'anthony.shayesteh@plexuss.com'; 

		$params['REP_NAME'] = $data['fname'] . ' '. $data['lname'];
		$params['COLLNAME'] = $data['school_name'];
		$params['COLL_EMAIL'] = $data['email'];
		$params['COLL_PHONE'] = isset($data['phone']) ? $data['phone'] : '';

		foreach ($arr as $key) {

			$email_arr = array('email' => $key['email'], 
					   'name' => '',
					   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		}
		
		return "success";
	
	}

	/**
	 * addNewUserFromPortal: a template to ask an admin user to sign up on Plexuss
	 *
	 * @return null
	 */
	public function addNewUserFromPortal($email, $portal_id, $super_admin = NULL){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'colleges_manage_portal_new_user';

		// Building cache array
		$cache_arr = array();
		$cache_arr['portal_id'] = $portal_id;
		$cache_arr['org_branch_id'] = $data['org_branch_id'];
		if (isset($super_admin)) {
			$cache_arr['super_admin'] = $super_admin;
		}else{
			$cache_arr['super_admin'] = 0;
		}

		// Getting chat thread id
		$cmtm_qry = DB::connection('bk')
					  ->table('college_message_thread_members as cmtm')
					  ->join('college_message_threads as cmt', 'cmt.id', '=', 'cmtm.thread_id')
					  ->join('organization_branch_permissions as obp', 'obp.user_id', '=', 'cmtm.user_id')
					  ->where('cmtm.org_branch_id', $data['org_branch_id'])
					  ->where('cmt.is_chat', 1)
					  ->groupBy('cmt.id')
					  ->orderBy('cmt.id')
					  ->select('cmt.id as thread_id')
					  ->first();

		if(!isset($cmtm_qry)){
			return "Error 252!";
		}			  

		$cache_arr['chat_thread_id'] = $cmtm_qry->thread_id;

		$now = Carbon::now();

		// We wanted to use time as part of encryption, but that has been causing issues for us
		// $adminToken = Crypt::encrypt($data['user_id'].$now);
		$adminToken = Crypt::encrypt($data['user_id'].$now->timestamp);

		Cache::forever(env('ENVIRONMENT') . '_'. $adminToken, $cache_arr);

		$params = array();

		$params['REPNAME']  = ucwords(strtolower($data['fname'] . ' ' . $data['lname']));
		$params['COLLNAME'] = $data['school_name'];
		
		if (env('ENVIRONMENT') == 'LIVE') {
			$params['PORTAL']   = 'https://plexuss.com/signup?utm_source=email&utm_medium=adminUser&adminToken='.$adminToken;
		}else{
			//$params['PORTAL']   = 'http://plexuss.dev/signup?utm_source=email&utm_medium=adminUser&adminToken='.$adminToken;
			$params['PORTAL']   = 'https://dev.plexuss.com/signup?utm_source=email&utm_medium=adminUser&adminToken='.$adminToken;
		}
		


		$email_arr = array('email' => $email, 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		
		
		return "success";
	}

	/**
	 * addPortalExistingUser: a template to ask an admin user to sign up on Plexuss
	 *
	 * @return null
	 */
	public function addPortalExistingUser($email, $portal_id){

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'colleges_manage_portal_existing_account';

		$params = array();

		$params['REPNAME']  = ucwords(strtolower($data['fname'] . ' ' . $data['lname']));
		$params['COLLNAME'] = $data['school_name'];

		if (env('ENVIRONMENT') == 'LIVE') {
			$params['PORTAL']   = 'https://plexuss.com/admin';
		}else{
			//$params['PORTAL']   = 'http://plexuss.dev/signup?utm_source=email&utm_medium=adminUser&adminToken='.$adminToken;
			$params['PORTAL']   = 'https://dev.plexuss.com/admin';
		}

		$email_arr = array('email' => $email, 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		
		
		return "success";
	}

	/**
	 * samplePremierContract: a template to send to colleges when they begin premier contract
	 *
	 * @return null
	 */
	public function samplePremierContract(){

		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'colleges_sample_premier_contract';

		$now = Carbon::today();
		$twoWeeksAgo = $now->subDays(14)->toDateString();

		$org = DB::connection('bk')->table('organization_branch_permissions as obp')
    				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
    				->join('organizations as o', 'o.id', '=', 'organization_id')
    				->join('colleges as c', 'c.id', '=', 'ob.school_id')
    				->join('users as u', 'u.id', '=', 'obp.user_id')
    				->select('u.fname', 'u.email',
    						 'ob.premier_trial_begin_date')
                    ->where('u.is_plexuss', 0)
                    ->where('obp.super_admin', 1)
                    ->where('ob.premier_trial_begin_date' , $twoWeeksAgo)
    				->get();

		foreach ($org as $key) {
			$params = array();	

			$params['NAME']  = ucwords(strtolower($key->fname));

			$email_arr = array('email' => $key->email, 
				   'name' => $key->fname,
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		}

		$org = DB::connection('bk')->table('organization_branch_permissions as obp')
    				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
    				->join('organizations as o', 'o.id', '=', 'organization_id')

    				->join('organization_portals as op', 'op.org_branch_id', '=', 'ob.id')
    				->join('organization_portal_users as opu', function($query){
    					$query = $query->on('opu.org_portal_id', '=', 'op.id');
    					$query = $query->on('opu.user_id', '=', 'obp.user_id');
    				})
    				->join('colleges as c', 'c.id', '=', 'ob.school_id')
    				->join('users as u', 'u.id', '=', 'obp.user_id')
    				->select('u.fname', 'u.email',
    						 'op.premier_trial_begin_date')
                    ->where('u.is_plexuss', 0)
                    ->where('obp.super_admin', 0)
                    ->where('op.premier_trial_begin_date' , $twoWeeksAgo)
    				->get();

		foreach ($org as $key) {
			$params = array();	

			$params['NAME']  = ucwords(strtolower($key->fname));

			$email_arr = array('email' => $key->email, 
				   'name' => $key->fname,
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		}

		return "success";
	}


	/**
	 * premierProgramWelcomeEmail: a template to send to colleges when they sign up on Plexuss for primer program.
	 *
	 * @return null
	 */
	public function premierProgramWelcomeEmail(){

		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'colleges_welcome_to_the_plexuss_premier_program';

		$today = Carbon::today()->toDateString();

		$org = DB::connection('bk')->table('organization_branch_permissions as obp')
    				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
    				->join('organizations as o', 'o.id', '=', 'organization_id')
    				->join('colleges as c', 'c.id', '=', 'ob.school_id')
    				->join('users as u', 'u.id', '=', 'obp.user_id')
    				->join('colleges_tuition as ct', 'ct.college_id', '=', 'c.id')

    				->select('u.fname', 'u.email',
    						 'ob.premier_trial_begin_date', 'ob.num_of_applications', 'ob.num_of_enrollments',
    						 'ct.tuition_avg_out_state_ftug')
                   
                    ->whereNotNull('ob.num_of_applications')
                    ->whereNotNull('ob.num_of_enrollments')
                    ->where('u.is_plexuss', 0)
                    ->where('obp.super_admin', 1)
                    ->where('ob.premier_trial_begin_date' , $today)
    				->get();

		foreach ($org as $key) {
			$params = array();	

			$params['NAME']             = ucwords(strtolower($key->fname));

			$date                       = date_create($key->premier_trial_begin_date);
			$params['BEGIN']            = date_format($date,"m/d/Y");
			$params['APPROVED']         = $key->num_of_applications * 10;
			$params['ENROLLMENTS']      = $key->num_of_enrollments;
			$params['APPLICATIONS']		= $key->num_of_applications;
			$params['YIELD']		    = $key->num_of_applications > 0 ? number_format((($key->num_of_enrollments / $key->num_of_applications) * 100)) : 0;
			$params['MONTHLY_APPROVED'] = number_format(($key->num_of_applications * 10) /12);
			$params['REVENUE']          = isset($key->tuition_avg_out_state_ftug) ? number_format($key->tuition_avg_out_state_ftug * $key->num_of_enrollments) : 0; 

			$email_arr = array('email' => $key->email, 
				   'name' => $key->fname,
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		}

		$org = DB::connection('bk')->table('organization_branch_permissions as obp')
    				->join('organization_branches as ob', 'ob.id', '=', 'obp.organization_branch_id')
    				->join('organizations as o', 'o.id', '=', 'organization_id')

    				->join('organization_portals as op', 'op.org_branch_id', '=', 'ob.id')
    				->join('organization_portal_users as opu', function($query){
    					$query = $query->on('opu.org_portal_id', '=', 'op.id');
    					$query = $query->on('opu.user_id', '=', 'obp.user_id');
    				})
    				->join('colleges as c', 'c.id', '=', 'ob.school_id')
    				->join('users as u', 'u.id', '=', 'obp.user_id')
    				->join('colleges_tuition as ct', 'ct.college_id', '=', 'c.id')

    				->select('u.fname', 'u.email',
    						 'op.premier_trial_begin_date', 'op.num_of_applications', 'op.num_of_enrollments',
    						 'ct.tuition_avg_out_state_ftug')
                    ->where('u.is_plexuss', 0)
                    ->where('obp.super_admin', 0)
                    ->whereNotNull('op.num_of_applications')
                    ->whereNotNull('op.num_of_enrollments')

                    ->where('op.premier_trial_begin_date' , $today)
    				->get();

		foreach ($org as $key) {
			$params = array();	

			$params['NAME']             = ucwords(strtolower($key->fname));

			$date                       = date_create($key->premier_trial_begin_date);
			$params['BEGIN']            = date_format($date,"m/d/Y");
			$params['APPROVED']         = $key->num_of_applications * 10;
			$params['ENROLLMENTS']      = $key->num_of_enrollments;
			$params['APPLICATIONS']		= $key->num_of_applications;
			$params['YIELD']		    = $key->num_of_applications > 0 ? number_format((($key->num_of_enrollments / $key->num_of_applications) * 100)) : 0;
			$params['MONTHLY_APPROVED'] = number_format(($key->num_of_applications * 10) /12);
			$params['REVENUE']          = isset($key->tuition_avg_out_state_ftug) ? number_format($key->tuition_avg_out_state_ftug * $key->num_of_enrollments) : 0; 


			$email_arr = array('email' => $key->email, 
				   'name' => $key->fname,
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
		}

		return "success";
	}

	/**
	 * deleteAccountInternalEmail: a template to send to support about a person removing their account.
	 *
	 * @return null
	 */
	public function deleteAccountInternalEmail($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'internal_remove_plexuss_account';

		$params = array();	

		$params['name']  		 = ucwords(strtolower($data['fname'] .' '. $data['lname']));
		$params['sender_email']  = $data['email'];
		$params['reason']		 = $data['reason'];
		$params['user_id']		 = $data['user_id'];

		$email_arr = array('email' => 'support@plexuss.com', 
				   'name' => $data['fname'],
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	/**
	 * manuallyAddToSuppresionList: a template to send if we fail to add an email address to suppression list
	 *
	 * @return null
	 */
	public function manuallyAddToSuppresionList($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'internal_manually_add_to_suppression';

		$params = array();	

		$params['sender_email']  = $data['email'];
		$params['user_id']		 = $data['user_id'];

		$email_arr = array('email' => 'anthony.shayesteh@plexuss.com', 
				   'name' => $data['fname'],
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	/**
	 * whyUserUnsubscribed: a template to send to support about why a person has removed their account.
	 *
	 * @return null
	 */
	public function whyUserUnsubscribed($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'internal_why_unsbuscribed';

		$params = array();	

		$params['sender_email']  = $data['email'];
		$params['reason']		 = $data['reason'];
		$params['user_id']		 = $data['user_id'];

		$email_arr = array('email' => 'support@plexuss.com', 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	public function usersPremiumOrderConfirmation($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'users_premium_order_confirmation';

		$params = array();	

		$params['sender_email']  = $data['email'];
		$params['DESCRIPTION']	 = $data['description'];
		$params['AMOUNT']		 = $data['amount'];
		$params['TOTAL']		 = $data['total'];
		$params['FNAME']		 = $data['fname'];

		$email_arr = array('email' => $data['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	public function exportAFileForColleges($input){
		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'college_export_file';

		$params = array();	

		$params['FNAME']  = $input['fname'];
		$params['LINK']   = $input['link'];

		$email_arr = array('email' => $input['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	public function webinarConfirmation($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'users_webinar_3_1_liberty_confirmation';

		$params = array();	

		$params['FNAME']  = $data['fname'];

		$email_arr = array('email' => $data['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	public function webinarInitialInvite($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'users_webinar_3_1_liberty_initial_invite';

		$params = array();	

		$params['FNAME']  = $data['fname'];

		$email_arr = array('email' => $data['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	public function webinarFifteenMinBefore($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'users_webinar_3_1_liberty_15_minute_reminder';

		$params = array();	

		$params['FNAME']  = $data['fname'];

		$email_arr = array('email' => $data['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	/**
	 * if a user responded yes to getting recruited, but hasn't filled out phone/address
	 * requires fname, school_name, college_id, and email in an array
 	 */
	public function collegeNeedsMoreInfo($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'users_college_needs_more_info';

		$params = array();

		$params['FNAME'] = $data['fname'];
		$params['COLLNAME'] = $data['school_name'];
		$params['COLLEGEID'] = $data['college_id'];

		$email_arr = array('email' => $data['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	/**
	 * if a user is coveted and the college is priority, send handshake email
	 * requires fname, school_name, and email in an array
 	 */
	public function handshakeNextSteps($data){
		$reply_email = 'support@plexuss.com';
		$template_name = 'users_handshake_next_steps';

		$params = array();

		$params['FNAME'] = ucfirst($data['fname']);
		$params['COLLNAME'] = $data['school_name'];

		$email_arr = array('email' => $data['email'], 
				   'name' => '',
				   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
	}

	/**
	 * Internal email to send Plexuss for urgent matters.
 	 */
	public function sendUrgentAdminEmail($data){
		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'internal_urgent_email_from_college_rep';

		$params = array();

		$params['REP_NAME']   = ucfirst($data['REP_NAME']);
		$params['COLLNAME']   = $data['COLLNAME'];
		$params['MESSAGEPRV'] = $data['msg'];
		$params['REP_EMAIL']  = $data['REP_EMAIL'];
		$params['REP_PHONE']  = $data['REP_PHONE'];

		$tmp = array();
		$tmp['email'] = 'sina.shayesteh@plexuss.com';
		$tmp['name'] = "Sina Shayesteh";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

		$tmp = array();
		$tmp['email'] = 'jp.novin@plexuss.com';
		$tmp['name'] = "Jp Novin";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

		// $tmp = array();
		// $tmp['email'] = 'chris.colligan@plexuss.com';
		// $tmp['name'] = "Chris Colligan";
		// $tmp['type'] = 'to';

		// $email_arr[] = $tmp;

		$tmp = array();
		$tmp['email'] = 'anthony.shayesteh@plexuss.com';
		$tmp['name'] = "Anthony Shayesteh";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

        $tmp = array();
        $tmp['email'] = 'tony.tran@plexuss.com';
        $tmp['name'] = "Tony Tran";
        $tmp['type'] = 'to';

        $email_arr[] = $tmp;

		foreach ($email_arr as $key) {
			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $key, $params, $reply_email, NULL, NULL);
		}
		
		$msg = $params["REP_NAME"]. " of ". $params["COLLNAME"] ." has the following urgent msg \n \n ".$params["MESSAGEPRV"]. " \n \n You can respond via ".  $params["REP_EMAIL"]. " or ".$params["REP_PHONE"];
		 
		$tc = new TwilioController;
		$tc->sendPlexussMsg($msg);
	}

	/**
	 * Internal email to send Plexuss for urgent matters.
 	 */
	public function sendUrgentAgencyEmail($data){
		$reply_email = 'collegeservices@plexuss.com';
		$template_name = 'internal_urgent_email_from_agency_rep';

		$params = array();

		$params['REP_NAME']   = ucfirst($data['REP_NAME']);
		$params['AGENCY_NAME']   = $data['AGENCY_NAME'];
		$params['MESSAGEPRV'] = $data['msg'];
		$params['REP_EMAIL']  = $data['REP_EMAIL'];
		$params['REP_PHONE']  = $data['REP_PHONE'];

		$tmp = array();
		$tmp['email'] = 'sina.shayesteh@plexuss.com';
		$tmp['name'] = "Sina Shayesteh";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

		$tmp = array();
		$tmp['email'] = 'jp.novin@plexuss.com';
		$tmp['name'] = "Jp Novin";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

		// $tmp = array();
		// $tmp['email'] = 'chris.colligan@plexuss.com';
		// $tmp['name'] = "Chris Colligan";
		// $tmp['type'] = 'to';

		// $email_arr[] = $tmp;

		$tmp = array();
		$tmp['email'] = 'anthony.shayesteh@plexuss.com';
		$tmp['name'] = "Anthony Shayesteh";
		$tmp['type'] = 'to';

		$email_arr[] = $tmp;

		foreach ($email_arr as $key) {
			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $key, $params, $reply_email, NULL, NULL);
		}
		
		$msg = $params["REP_NAME"]. " of ". $params["AGENCY_NAME"] ." has the following urgent msg \n \n ".$params["MESSAGEPRV"]. " \n \n You can respond via ".  $params["REP_EMAIL"]. " or ".$params["REP_PHONE"];
		 
		$tc = new TwilioController;
		$tc->sendPlexussMsg($msg);
	}

	/*
	 * USERS: OneApp Invite Day One
	 * USERS: OneApp Invite Daily Followup
	 *
	 */
	public function applicationEmailForPeopleWhoHaventStarted($financial, $cnt){

		$reply_email = 'oneapp@plexuss.com';
		
		if ($cnt == 0) {
			$template_name = 'users_oneapp_invite_day_one';
		}else{
			$template_name = 'users_oneapp_invite_daily_followup';
		}
		$now = Carbon::now();
		$twoWeeksAgo = $now->subDays(14)->toDateString();
		$twoWeeksAgo = $twoWeeksAgo . " 00:00:00";

		$today = Carbon::today();

		$params = array();

		$qry = DB::connection('bk')->table('users as u')
									 ->leftjoin('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
									 ->leftjoin('application_email_logs as ael', function($q) use ($template_name){
									 		$q->on('u.id', '=', 'ael.user_id');
									 		$q->on('ael.id', '=', DB::raw('(select MAX(id) FROM `application_email_logs` as ael2 WHERE u.id = ael2.user_id)'));
									 })
									 ->leftjoin(DB::raw("(	Select count(*) as 'emails_sent', user_id from (
										Select distinct date(created_at), user_id
										from application_email_logs
										) tbl1 
										group by user_id ) as tbl_count"), 'u.id', '=', 'tbl_count.user_id')
									 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
											$q->on('u.id', '=', 'aes.user_id');
											$q->on('aes.is_supressed', '=', DB::raw(1));
											$q->on('aes.template_name', '=', DB::raw("'".$template_name."'"));	
									 })
									 ->where(function($q){
									 		$q->orWhereIn('u.planned_start_yr', array(2017, 2018))
									 		  ->orWhere('u.planned_start_yr');
									 })
									 ->where(function($q){
									 		$q->orWhere('u.is_student', '=', 1)
									 		  ->orWhere('u.is_intl_student', '=', 1);
									 })
									 ->whereNull('ucq.application_state')
									 ->whereNull('aes.id')
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', '!=', 1)
									 ->where('u.created_at', '<=', $twoWeeksAgo)
									 ->where(DB::raw("coalesce(emails_sent, 0)"), '=', $cnt)
									 ->select('u.id', 'u.email', DB::raw("coalesce(emails_sent, 0) as cnt"))
									 ->groupBy('u.id')
									 ->orderBy('u.id', 'DESC')
									 ->orderBy('emails_sent', 'ASC');

		if ($cnt == 0) {
			$qry = $qry->whereNull('ael.id')
					   ->take(1000);
		}else{
			$qry = $qry->where('ael.created_at', '<', $today);
		}

		if ($financial == '20k or more') {

			$qry = $qry->where(function($q){
						 		$q->orWhere('financial_firstyr_affordibility', '=', DB::raw("'20,000 - 30,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'30,000 - 50,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'50,000'"));
					   });
					   
		}elseif ($financial == '10k to 20k') {
			
			$qry = $qry->where(function($q){
						 		$q->orWhere('financial_firstyr_affordibility', '=', DB::raw("'10,000 - 20,000'"));
					   });
		}elseif ($financial == "5k to 10k") {
			$qry = $qry->where(function($q){
						 		$q->orWhere('financial_firstyr_affordibility', '=', DB::raw("'5,000 - 10,000'"));
					   });
		}else{
			return "financial is required";
		}

		$qry = $qry->get();

		foreach ($qry as $key) {
			// dd($key);
			$params = array();

			$hashed_user_id = Crypt::encrypt($key->id);
			$this_template  = Crypt::encrypt($template_name);

			$params['SEND_NEVER']  = $_ENV['CURRENT_URL'].'applicationSuppression?user_id='.$hashed_user_id.'&template_name='.$this_template;
			$params['DAYS_REMAIN'] = 10 - $cnt;

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->id;
			$ael->template_name = $template_name;

			$ael->save(); 
			// dd(4);
			// dd($key);
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}

	/*
	 * USERS: OneApp Daily Application Status
	 * 
	 *
	 */
	public function applicationEmailStartedButNotFinished($cnt){

		$reply_email = 'oneapp@plexuss.com';

		$template_name = 'users_oneapp_daily_application_status';
		
		$now = Carbon::now();
		$twoWeeksAgo = $now->subDays(14)->toDateString();
		$twoWeeksAgo = $twoWeeksAgo . " 00:00:00";

		$today = Carbon::today();

		$params = array();

		$qry = DB::connection('bk')->table('users as u')
									 ->leftjoin('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
									 ->leftjoin('application_email_logs as ael', function($q) use ($template_name){
									 		$q->on('u.id', '=', 'ael.user_id');
									 		$q->on('ael.template_name', '=', DB::raw("'".$template_name."'"));	
									 		$q->on('ael.id', '=', DB::raw('(select MAX(id) FROM `application_email_logs` as ael2 WHERE u.id = ael2.user_id AND ael2.template_name = "'.$template_name.'")'));
									 })
									 ->leftjoin(DB::raw("(	Select count(*) as 'emails_sent', user_id from (
										Select distinct date(created_at), user_id
										from application_email_logs
										) tbl1 
										group by user_id ) as tbl_count"), 'u.id', '=', 'tbl_count.user_id')
									 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
											$q->on('u.id', '=', 'aes.user_id');
											$q->on('aes.is_supressed', '=', DB::raw(1));
											$q->on('aes.template_name', '=', DB::raw("'".$template_name."'"));	
									 })
									 ->where(function($q){
									 		$q->orWhere('u.is_student', '=', 1)
									 		  ->orWhere('u.is_intl_student', '=', 1);
									 })
									 ->whereNull('aes.id')
									 ->whereNotNull('ucq.application_state')
									 ->where('application_state', '!=', 'submit')
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', '!=', 1)
									 ->select('u.id', 'u.email', 'ucq.created_at', 
									 DB::raw("coalesce(emails_sent , 0) as cnt ,
													Case
												When application_state = 'basic' Then
													'Identity and Basic Info'
												When application_state = 'identity' Then
													'Start Term and Year'
												When application_state = 'start' Then
													'Contact Information'
												When application_state = 'contact' Then
													'Countries want to study in'
												When application_state = 'study' Then
													'Citizenship Status'
												When application_state = 'citizenship' Then
													'Financial Ability'
												When application_state = 'financials' Then
													'GPA'
												When application_state = 'gpa' Then
													'College Entrance and Proficiency Scores'
												When application_state = 'scores' Then
													'School Selection'
												When application_state in(
													'colleges' ,
													'family' ,
													'clubs' ,
													'uploads' ,
													'courses' ,
													'essay' ,
													'additional_info'
												) Then
													'College Custom Questions'
												When application_state = 'declaration' Then
													'Submission Page'
												When application_state = 'submit' Then
													'Finished'
												Else
													application_state
												end as 'application_state'
												,
												 CONCAT(
													if(
															floor(TIMESTAMPDIFF(DAY, 
																ucq.created_at,
																	DATE_SUB(
																		current_timestamp() ,
																		interval 7 HOUR
																	)
																)) = 0 ,
														'' ,
														floor(TIMESTAMPDIFF(DAY, 
															ucq.created_at,
																DATE_SUB(
																	current_timestamp() ,
																		interval 7 HOUR
																)
															))
													) ,
													Case
												When floor(TIMESTAMPDIFF(DAY, 
																ucq.created_at,
																	DATE_SUB(
																		current_timestamp() ,
																		interval 7 HOUR
																	)
														)) = 0 Then
													''
												When 	floor(TIMESTAMPDIFF(DAY, 
																ucq.created_at,
																	DATE_SUB(
																		current_timestamp() ,
																		interval 7 HOUR
																	)
																)) = 1 Then
													' day '
												Else
													' days '
												end ,
												 MOD(
													HOUR(
														TIMEDIFF(
															ucq.created_at ,
															DATE_SUB(
																current_timestamp() ,
																interval 7 HOUR
															)
														)
													) ,
													24
												) ,

												if(
													MOD(
														HOUR(
															TIMEDIFF(
																ucq.created_at ,
																DATE_SUB(
																	current_timestamp() ,
																	interval 7 HOUR
																)
															)
														) ,
														24
													) = 1 ,
													' hour ' ,
													' hours '
												)
												) as 'Time_Elapsed'

									"))
									 ->groupBy('u.id')
									 ->orderBy('u.id', 'DESC')
									 ->orderBy('emails_sent', 'ASC');

		if ($cnt == 0) {
			$qry = $qry->whereNull('ael.id');
		}else{
			$qry = $qry->where('ael.created_at', '<', $today);
		}

		$qry = $qry->get();

		foreach ($qry as $key) {
			
			$params = array();

			$hashed_user_id = Crypt::encrypt($key->id);
			$this_template  = Crypt::encrypt($template_name);

			$params['SEND_NEVER']  = $_ENV['CURRENT_URL'].'applicationSuppression?user_id='.$hashed_user_id.'&template_name='.$this_template;
			$params['APP_START']   = date_format(date_create($key->created_at), 'F d, Y');
			$params['TIME_LAPSED'] = $key->Time_Elapsed;
			$params['NEXT_STEP']   = $key->application_state;

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->id;
			$ael->template_name = $template_name;

			$ael->save(); 
			// dd(10);
			// dd($key);
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}

	/*
	 * USERS: OneApp Daily Application Status
	 * 
	 *
	 */
	public function applicationEmailFinishedApp(){
		$reply_email = 'oneapp@plexuss.com';

		$template_name = 'users_oneapp_invite_daily_followup_finished_app';

		$qry = DB::connection('bk')->table('users as u')
									 ->join('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
									 ->join('users_applied_colleges as uac', function($q){
									 	$q->on('u.id', '=', 'uac.user_id');
									 	$q->on('uac.id', '=', DB::raw('(select MAX(id) FROM `users_applied_colleges` as uac2 WHERE u.id = uac2.user_id)'));
									 })
									 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
											$q->on('u.id', '=', 'aes.user_id');
											$q->on('aes.template_name', '=', DB::raw("'".$template_name."'"));	
									 })
									 ->leftjoin('colleges as c', 'c.id', '=', 'uac.college_id')
									 ->where('ucq.application_state', 'submit')
									 ->whereNull('aes.id')
									 ->select('u.id', 'u.email', 'c.school_name')
									 ->groupBy('u.id')
									 ->get();

		foreach ($qry as $key) {
			
			$params = array();

			$hashed_user_id = Crypt::encrypt($key->id);
			$this_template  = Crypt::encrypt($template_name);

			$params['SEND_NEVER']    = $_ENV['CURRENT_URL'].'applicationSuppression?user_id='.$hashed_user_id.'&template_name='.$this_template;
			$params['COL_NAME']	     = $key->school_name;
			$params['SCHEDULE_LINK'] = $_ENV['CURRENT_URL'].'scheduleoneapp?user_id='.$hashed_user_id.'&template_name='.$this_template;

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->id;
			$ael->template_name = $template_name;

			$ael->save(); 
			// dd(15);
			// dd($key);
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}

	public function followupWeeklyEmailForVerifiedApplicationUsers(){
		$reply_email = 'oneapp@plexuss.com';

		$template_name = 'users_oneapp_invite_weekly_followup';

		$qry = DB::connection('bk')->table('recruitment_verified_apps as eva')
									 ->join('users as u', 'eva.user_id', '=', 'u.id')
									 ->join(DB::raw("(Select user_id, group_concat(college_id) as 'colleges_applied_to' from users_applied_colleges group by user_id) as col_applied_to_tbl"), 'eva.user_id', '=', 'col_applied_to_tbl.user_id')
									 ->join('users_applied_colleges as uac', 'uac.user_id', '=', 'u.id')
									 ->join('colleges as c', 'uac.college_id', '=', 'c.id')
									 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
											$q->on('u.id', '=', 'aes.user_id');
											$q->on('aes.template_name', '=', DB::raw("'".$template_name."'"));	
									 })
									 ->whereNull('aes.id')
									 ->whereRaw('uac.id IN (Select max(id) from users_applied_colleges group by user_id)')
									 ->groupBy('u.id')
									 ->select('u.id as user_id', 'u.fname', 'u.email', 'colleges_applied_to', 'c.school_name')
									 ->get();
		foreach ($qry as $key) {

			$colleges_applied_to = explode(",", $key->colleges_applied_to);

			$crc = new CollegeRecommendationController;
			$matches = $crc->findCollegesForThisUser($key->user_id, true, $colleges_applied_to);
			
			if (empty($matches)) {
				continue;
			}
			
			$params = array();
			
			$hashed_user_id = Crypt::encrypt($key->user_id);
			$this_template  = Crypt::encrypt($template_name);

			$params['FNAME']		 = $key->fname;
			$params['SEND_NEVER']    = $_ENV['CURRENT_URL'].'applicationSuppression?user_id='.$hashed_user_id.'&template_name='.$this_template;
			$params['COL_APPLIED']	 = $key->school_name;

			for ($i=0; $i < count($matches); $i++) { 

				$cnt = $i;
				$cnt = $cnt + 1;
				$params['SCHOOL_NAME'. $cnt] = $matches[$i]['school_name'];
				$params['SCHOOL_LINK'. $cnt] = 'https://plexuss.com/college/'.$matches[$i]['slug'];
				$params['SCHOOL_RANK'. $cnt] = isset($matches[$i]['rank']) ? $matches[$i]['rank'] : 'N/A';

				// if ($cnt == 2) {
				// 	break;
				// }
			}
			
			//$key->email = 'anthony.shayesteh@plexuss.com';

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->user_id;
			$ael->template_name = $template_name;

			$ael->save(); 
			
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}

	/*
	 * USERS: OneApp Invite Coveted Wkly Followup Post 10 ( this is a daily email )
	 *
	 *
	 */
	public function applicationEmailForCovetedUSersPostTen(){

		$reply_email = 'oneapp@plexuss.com';
		
		$template_name = 'users_oneapp_invite_coveted_wkly_followup_post_10';

		$now = Carbon::now();
		$twoWeeksAgo = $now->subDays(14)->toDateString();
		$twoWeeksAgo = $twoWeeksAgo . " 00:00:00";
		$cnt = 10;

		$today = Carbon::today();

		$params = array();

		$qry = DB::connection('bk')->table('users as u')
									 ->leftjoin('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
									 ->leftjoin('application_email_logs as ael', function($q) use ($template_name){
									 		$q->on('u.id', '=', 'ael.user_id');
									 		$q->on('ael.id', '=', DB::raw('(select MAX(id) FROM `application_email_logs` as ael2 WHERE u.id = ael2.user_id)'));
									 })
									 ->leftjoin(DB::raw("(	Select count(*) as 'emails_sent', user_id from (
										Select distinct date(created_at), user_id
										from application_email_logs
										) tbl1 
										group by user_id ) as tbl_count"), 'u.id', '=', 'tbl_count.user_id')
									 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
											$q->on('u.id', '=', 'aes.user_id');
											$q->on('aes.is_supressed', '=', DB::raw(1));
											$q->on('aes.template_name', '=', DB::raw("'".$template_name."'"));	
									 })
									 ->where(function($q){
									 		$q->orWhereIn('u.planned_start_yr', array(2018, 2019, 2020))
									 		  ->orWhere('u.planned_start_yr');
									 })
									 ->where(function($q){
									 		$q->orWhere('u.is_student', '=', 1)
									 		  ->orWhere('u.is_intl_student', '=', 1);
									 })
									 ->whereNull('ucq.application_state')
									 ->whereNull('aes.id')
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', '!=', 1)
									 ->where(DB::raw("coalesce(emails_sent, 0)"), '>=', $cnt)
									 ->select('u.id', 'u.email', DB::raw("coalesce(emails_sent, 0) as cnt"), 'u.planned_start_term', 'u.planned_start_yr')
									 ->groupBy('u.id')
									 ->orderBy('u.id', 'DESC')
									 ->orderBy('emails_sent', 'ASC');

		$qry = $qry->where(function($q){
						 		$q->orWhere('financial_firstyr_affordibility', '=', DB::raw("'10,000 - 20,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'5,000 - 10,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'20,000 - 30,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'30,000 - 50,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'50,000'"));
					   })
					   ->take(5000)
					   ->where('ael.created_at', '<', $today);

		$qry = $qry->get();

		foreach ($qry as $key) {
			// dd($key);
			$params = array();

			$hashed_user_id = Crypt::encrypt($key->id);
			$this_template  = Crypt::encrypt($template_name);

			$params['SEND_NEVER']  = $_ENV['CURRENT_URL'].'applicationSuppression?user_id='.$hashed_user_id.'&template_name='.$this_template;
			$params['TERM_START']  = ucwords(strtolower($key->planned_start_term)) . ' '. $key->planned_start_yr;

			// $key->email = "reza.shayesteh@gmail.com";

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
			
			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->id;
			$ael->template_name = $template_name;

			$ael->save(); 
			// dd(4);
			// dd($key);
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}


	/*
	 * USERS: OneApp Invite Day One
	 * USERS: OneApp Invite Daily Followup
	 *
	 */
	public function applicationTextForPeopleWhoHaventStarted($cnt, $is_toefl){

		$bool = 'false';
		if (isset($is_toefl)) {
			$bool = 'true';
		}

		if (Cache::has(env('ENVIRONMENT').'_'.'_applicationTextForPeopleWhoHaventStarted_cnt_'. $cnt. '_is_toefl_'. $bool)) {
			return "cache found!";
		}
		Cache::put(env('ENVIRONMENT').'_'.'_applicationTextForPeopleWhoHaventStarted_cnt_'. $cnt. '_is_toefl_'. $bool, true, 60);

		$reply_email = 'oneapp@plexuss.com';
		
		if ($cnt == 0) {
			$template_name = 'users_oneapp_invite_day_one';
		}else{
			$template_name = 'users_oneapp_invite_daily_followup';
		}
		$now = Carbon::now();
		$twoWeeksAgo = $now->subDays(14)->toDateString();
		$twoWeeksAgo = $twoWeeksAgo . " 00:00:00";

		$today = Carbon::today();
		$week_ago = Carbon::now()->subDays(7);

		$params = array();

		$qry = DB::connection('bk')->table('users as u')
									 ->leftjoin('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
									 ->leftjoin('application_texts_logs as atl', function($q) use ($template_name){
									 		$q->on('u.id', '=', 'atl.user_id');
									 		$q->on('atl.id', '=', DB::raw('(select MAX(id) FROM `application_texts_logs` as atl2 WHERE u.id = atl2.user_id)'));
									 })
									 ->leftjoin(DB::raw("(	Select count(*) as 'texts_sent', user_id from (
										Select distinct date(created_at), user_id
										from application_texts_logs
										) tbl1 
										group by user_id ) as tbl_count"), 'u.id', '=', 'tbl_count.user_id')
									 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
											$q->on('u.id', '=', 'aes.user_id');
											$q->on('aes.is_supressed', '=', DB::raw(1));
									 })
									 ->leftjoin('text_suppression_list as tsl', 'tsl.user_id', '=', 'u.id')
									 // ->where(function($q){
									 // 		$q->orWhereIn('u.planned_start_yr', array(2017, 2018))
									 // 		  ->orWhere('u.planned_start_yr');
									 // })
									 ->where(function($q){
									 		$q->orWhere('u.is_student', '=', 1)
									 		  ->orWhere('u.is_intl_student', '=', 1);
									 })
									 ->whereNull('ucq.application_state')
									 ->whereNull('aes.id')
									 ->whereNull('tsl.id')
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', '!=', 1)
									 //->where('u.created_at', '<=', $twoWeeksAgo)
									 ->whereNotNull('u.phone')
									 ->where('u.txt_opt_in', 1)
									 ->select('u.id', 'u.email', 'u.phone', DB::raw("coalesce(texts_sent, 0) as cnt"))
									 ->groupBy('u.id')
									 ->orderBy('u.id', 'DESC')
									 ->orderBy('texts_sent', 'ASC');

		if ($is_toefl) {
			$qry = $qry->where('u.utm_term', "tstudents");
			$msg = "\xF0\x9F\x8E\x93 College application deadline approaching. Book an appointment and apply to colleges at no cost. http://bit.ly/2o7y9i4";
		}else{
			if ($cnt == 0) {
				$qry = $qry->where('u.utm_term', '!=', "tstudents");
			}
	
			switch ($cnt) {
				case 0:
					$msg = "Plexuss reminder: College application deadline approaching. Check email to begin your application. \xF0\x9F\x8E\x93";
					$qry = $qry->where(DB::raw("coalesce(texts_sent, 0)"), '=', $cnt);
					break;

				case 1:
					$msg = "Courtesy link to your college application.  http://bit.ly/2o7L8Ax";
					$qry = $qry->where(DB::raw("coalesce(texts_sent, 0)"), '=', $cnt);
					break;

				case 2:
					$msg = "Schedule a call with a university representative.  http://bit.ly/2o7y9i4";
					$qry = $qry->where(DB::raw("coalesce(texts_sent, 0)"), '=', $cnt);
					break;

				case 3:
					$msg = "Are you still interested to apply to a university?";
					$qry = $qry->where(DB::raw("coalesce(texts_sent, 0)"), '=', $cnt);
					break;
				
				default:
					$msg = "Plexuss reminder.  Application deadline approaching.  Your OneApp is not completed. http://bit.ly/2o7L8Ax";
					$qry = $qry->where(DB::raw("coalesce(texts_sent, 0)"), '>=', $cnt);
					break;
			}
		}
		
		if ($cnt == 0) {
			$qry = $qry->whereNull('atl.id')
					   ->take(1000);
		}else{
			$qry = $qry->where('atl.created_at', '<', $week_ago);
		}
	
		
		$qry = $qry->where(function($q){
						 		$q->orWhere('financial_firstyr_affordibility', '=', DB::raw("'20,000 - 30,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'30,000 - 50,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'50,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'5,000 - 10,000'"))
						 		  ->orWhere('financial_firstyr_affordibility', '=', DB::raw("'10,000 - 20,000'"));
					   });

		$qry = $qry->get();

		foreach ($qry as $key) {
			
			$input = array();
			$input['msg'] 	  = $msg;
			$input['user_id'] = $key->id;
			$input['phone']   = $key->phone;
			// $input['phone']   = '+1 310-598-0347';

			$atl = new ApplicationTextsLog;

			$atl->user_id 		= $key->id;
			$atl->template_name = $template_name;

			$atl->save(); 

			$tc = new TwilioController;
			$tc->sendTextWorkflow($input);
			
			// dd(4);
			// dd($key);
			// dd($input);
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}

	public function sendPressRelease(){
		$reply_email = 'collegeservices@plexuss.com';

		$template_name = 'b2b_plexuss_weekly_digest_1';
		
		$qry = PressReleaseContact::where('email_sent', 0)
								  ->take(5000)
								  ->get();

		foreach ($qry as $key) {
			// $key->email = "anthony.shayesteh@plexuss.com";

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			
			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, NULL, $reply_email, NULL, NULL);
			// dd(31313131);
			$key->email_sent = 1;
			$key->save();
		}

		return "success";
	}

	/*
	 * USERS: Admitsee Email - Major
	 *
	 *
	 */
	public function admitseeEmails(){

		$reply_email = 'social@plexuss.com';

		$week_ago = Carbon::now()->subDays(7);

		$params = array();

		// This method toggles which one are we running major/country/school
		$act_cnt = AdmitseeCronToggle::on('bk')->count();
		$act 	 = AdmitseeCronToggle::where('active', 1)->first();

		$step = $act->name;
		$template_name = $act->template_name;
		
		$update = DB::table('admitsee_cron_toggles')->update(array('active' => 0));
		
		$int = $act->id + 1;

		if ($int > $act_cnt) {
			$int = 1;
		}

		$act = AdmitseeCronToggle::find($int);
		$act->active = 1;
		$act->save();

		switch ($step) {
			case 'Major':
				$sub_qry = DB::connection('bk')->table('admitsee_recommendations_filters as arf')
										 ->join('admitsee_recommendations_main_majors as armm', 'arf.essay_id', '=', 'armm.essay_id')
										 ->join('admitsee_recommendations_sub_majors as arsm', 'armm.id', '=', 'arsm.main_major_id')
										 ->join('objectives as o', 'o.major_id', '=', 'arsm.sub_major_id')
										 ->join('majors as m', 'armm.major_id', '=', 'm.id')
										 ->join('news_articles as na', 'armm.essay_id', '=', 'na.id')
										 ->join('users as u', 'o.user_id', '=', 'u.id')
										 ->leftjoin('admitsee_recommendations_email_logs as arel', function($q){
										 			$q->on('u.id', '=', 'arel.user_id')
										 			  ->on('arf.essay_id', '=', 'arel.essay_id');
										 })
										 ->leftjoin('admitsee_recommendations_email_logs as arel_temp', function($q){
										 			$q->on('u.id', '=', 'arel_temp.user_id');
										 			$q->on('arel_temp.id', '=', DB::raw('(select MAX(id) FROM `admitsee_recommendations_email_logs` as arel_temp2 WHERE u.id = arel_temp2.user_id)'));  
										 })
										 ->where('rec_type', '=', 'major')
										 ->whereNull('arel.id')
										 ->where(function($q) use ($week_ago){
										 		 $q->orWhereNull('arel_temp.id')
										 		   ->orWhere('arel_temp.created_at', '<', $week_ago);
										 })
										 ->whereNotNull('na.premium_content')
										 ->where('u.email', '!=', 'none')
										 ->where('u.is_ldy', 0)
										 ->orderByRaw('rand()')
										 ->selectRaw("u.id as 'user_id', u.email, arf.essay_id
													, na.title as 'essay_title'
													, concat('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/', na.authors_img) as 'author_img'
													, left(authors_description, locate(substring_index(authors_description, ',', -1), authors_description) - 2) as 'col_attending'
													, substring_index(authors_description, ',', -1) as 'class_of'
												  , concat('https://plexuss.com/news/essay/', na.slug, '/essay') as 'essay_link'
													, replace(m.name, ' General', '') as 'major_match'");
				$qry = DB::connection('bk')->table( DB::raw("({$sub_qry->toSql()}) as tbl") )
						    				 ->mergeBindings($sub_qry) // you need to get underlying Query Builder
						    				 ->groupBy('user_id')
						    				 ->orderBy('user_id')
						    				 ->select('user_id', 'email', 'essay_id', 'essay_title', 'author_img', 
						    				 		  'col_attending', 'class_of', 'essay_link', 'major_match')
						    				 ->take(5000)
						    				 ->get();
				break;
			
			case 'Country':
				$sub_qry = DB::connection('bk')->table('admitsee_recommendations_filters as arf')
										 ->join('admitsee_recommendations_countries as arc', 'arf.essay_id', '=', 'arc.essay_id')
										 ->join('news_articles as na', 'arc.essay_id', '=', 'na.id')
										 ->join('users as u', 'u.country_id', '=', 'arc.country_code')
										 ->join('countries as ctr', 'ctr.id', '=', 'u.country_id')

										 ->leftjoin('admitsee_recommendations_email_logs as arel', function($q){
										 			$q->on('u.id', '=', 'arel.user_id')
										 			  ->on('arf.essay_id', '=', 'arel.essay_id');
										 })
										 ->leftjoin('admitsee_recommendations_email_logs as arel_temp', function($q){
										 			$q->on('u.id', '=', 'arel_temp.user_id');
										 			$q->on('arel_temp.id', '=', DB::raw('(select MAX(id) FROM `admitsee_recommendations_email_logs` as arel_temp2 WHERE u.id = arel_temp2.user_id)'));  
										 })
										 
										 
										 ->where(function($q) use ($week_ago){
										 		 $q->orWhereNull('arel_temp.id')
										 		   ->orWhere('arel_temp.created_at', '<', $week_ago);
										 })

										 ->whereNotNull('na.premium_content')
										 ->whereNull('arel.id')
										 ->where('rec_type', '=', 'country')
										 ->where('u.email', '!=', 'none')
										 ->where('u.is_ldy', 0)
										 ->orderByRaw('rand()')
										 ->selectRaw("u.id as user_id, u.email, arf.essay_id
													, na.title as 'essay_title'
													, concat('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/', arc.author_alt_img) as 'author_img'
													, authors_description
													, left(authors_description, locate(substring_index(authors_description, ',', -1), authors_description) - 2) as 'col_attending'
													, substring_index(authors_description, ',', -1) as 'class_of'
												  , concat('https://plexuss.com/news/essay/', na.slug, '/essay') as 'essay_link'
												  , country_name as 'country_match'");
				$qry = DB::connection('bk')->table( DB::raw("({$sub_qry->toSql()}) as tbl") )
						    				 ->mergeBindings($sub_qry) // you need to get underlying Query Builder
						    				 ->groupBy('user_id')
						    				 ->orderBy('user_id')
						    				 ->select('user_id', 'email', 'essay_id', 'essay_title', 'author_img', 'col_attending', 
						    				 		  'class_of', 'essay_link', 'country_match')
						    				 ->take(5000)
						    				 ->get();
				break;

			case 'School':
				$sub_qry = DB::connection('bk')->table('admitsee_recommendations_filters as arf')
										 ->join('admitsee_recommendations_schools_outcome as arso', 'arf.essay_id', '=', 'arso.essay_id')
										 ->join('recruitment as r', 'arso.college_id', '=', 'r.college_id')
										 ->join('news_articles as na', 'arso.essay_id', '=', 'na.id')
										 ->join('colleges as c', 'arso.college_id', '=', 'c.id')
										 ->join('users as u', 'r.user_id', '=', 'u.id')

										 ->leftjoin('admitsee_recommendations_email_logs as arel', function($q){
										 			$q->on('u.id', '=', 'arel.user_id')
										 			  ->on('arf.essay_id', '=', 'arel.essay_id');
										 })
										 ->leftjoin('admitsee_recommendations_email_logs as arel_temp', function($q){
										 			$q->on('u.id', '=', 'arel_temp.user_id');
										 			$q->on('arel_temp.id', '=', DB::raw('(select MAX(id) FROM `admitsee_recommendations_email_logs` as arel_temp2 WHERE u.id = arel_temp2.user_id)'));  
										 })
										 ->where(function($q) use ($week_ago){
										 		 $q->orWhereNull('arel_temp.id')
										 		   ->orWhere('arel_temp.created_at', '<', $week_ago);
										 })
										 
										 ->where('rec_type', '=', 'school')
										 ->whereIn('is_recommended', array(1,2))
										 ->where('user_recruit', 1)
										 ->whereNull('arel.id')
										 ->whereNotNull('na.premium_content')
										 ->where('u.email', '!=', 'none')
										 ->where('u.is_ldy', 0)
										 ->orderByRaw('rand()')
										 ->selectRaw("r.user_id, u.email, arso.college_id, arso.essay_id
													, na.title as 'essay_title'
													, concat('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/', na.authors_img) as 'author_img'
													, authors_description
													, left(authors_description, locate(substring_index(authors_description, ',', -1), authors_description) - 2) as 'col_attending'
													, substring_index(authors_description, ',', -1) as 'class_of'
												  	, concat('https://plexuss.com/news/essay/', na.slug, '/essay') as 'essay_link'
													, school_name as 'school_match'");									 
				$qry = DB::connection('bk')->table( DB::raw("({$sub_qry->toSql()}) as tbl") )
						    				 ->mergeBindings($sub_qry) // you need to get underlying Query Builder
						    				 ->groupBy('user_id')
						    				 ->orderBy('user_id')
						    				 ->select('user_id', 'email', 'essay_id', 'essay_title', 'author_img', 'col_attending',
						    				 		  'class_of', 'essay_link', 'school_match')
						    				 ->take(5000)
						    				 ->get();
				break;

			default:
				# code...
				break;
		}
		

		foreach ($qry as $key) {
			// dd($key);
			$params = array();

			$params['ESSAY_TITLE']    = $key->essay_title;
			$params['AUTHOR_IMG']  	  = $key->author_img;
			$params['COL_ATTENNDING'] = $key->col_attending;
			$params['CLASS_OF']  	  = $key->class_of;
			$params['ESSAY_LINK']     = $key->essay_link;

			isset($key->major_match)   ? $params['MAJOR_MATCH']   = $key->major_match : NULL;
			isset($key->school_match)  ? $params['SCHOOL_MATCH']  = $key->school_match : NULL;
			isset($key->country_match) ? $params['COUNTRY_MATCH'] = $key->country_match : NULL;

			// $key->email = "anthony.shayesteh@plexuss.com";

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
			// dd(7);
			$ael = new AdmitseeRecommendationsEmailLog;

			$ael->user_id  = $key->user_id;
			$ael->essay_id = $key->essay_id;

			$ael->save(); 
			// dd(6);
			// dd($key);
		}

		if (empty($qry)) {
			return "failed";
		}
		return "success";
	}

	/*
	 * USERS: ELS Additional Info
	 *
	 *
	 */
	public function elsAdditionalInfoEmails(){

		$reply_email = 'oneapp@plexuss.com';
		
		$template_name = 'users_els_additional_info';

		$qry = DB::connection('bk')->table('users_applied_colleges as uac')
									 ->join(DB::raw("(Select user_id, max(uac.id) as 'max_id'
														from users_applied_colleges uac
														join aor_colleges ac on uac.college_id = ac.college_id 
															and aor_id = 5
														group by user_id) as limit_one_per_user_tbl"), 'uac.id', '=', 'limit_one_per_user_tbl.max_id')
									 
									 ->join('users as u', 'uac.user_id', '=', 'u.id')
									 ->join('aor_colleges as ac', function($q){
									 		$q->on('uac.college_id', '=', 'ac.college_id')
									 		  ->on('aor_id', '=', DB::raw(5));
									 })
									 ->join('colleges as c', 'c.id', '=', 'uac.college_id')
									 ->leftjoin('application_email_suppressions as aes', 'uac.user_id', '=', 'aes.user_id')
									 ->leftjoin('application_email_logs as ael', function($q) use ($template_name){
									 			$q->on('uac.user_id', '=', 'ael.user_id');
									 			$q->on('ael.template_name', '=', DB::raw("'".$template_name."'"));	
									 })
									 ->whereNull('ael.id')
									 ->whereNull('aes.id')
									 ->select('uac.user_id', DB::raw('proper(fname) as fname'), 'email', 'school_name')
									 ->get();

		foreach ($qry as $key) {

			$params = array();

			$params['FNAME'] 	= $key->fname;
			$params['COLNAME'] = $key->school_name;

			// $key->email = 'anthony.shayesteh@plexuss.com';

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->user_id;
			$ael->template_name = $template_name;

			$ael->save(); 

		}							 
	}

	/*
	 * OTHER: Intern Survey
	 * OTHER: Hiring Survey
	 *
	 */
	public function careersSurveyEmail($input){

		$reply_email = 'careers@plexuss.com';
		
		switch ($input['type']) {
			case 'internships':
				$template_name = 'other_intern_survey';
				break;
			
			case 'careers':
				$template_name = 'other_hiring_survey';
				break;

			default:
				# code...
				break;
		}
		
		$params = array();
		$params['FNAME'] = $input['fname'];

		$email_arr = array('email' => $input['email'], 
				   'name' => '',
				   'type' =>'to');
		// print_r($email_arr);
		// print_r("=-------<br>");
		// print_r($template_name);
		// print_r("=-------<br>");

		// dd($params);
		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

	}

	/*
	 * oneapp_internal_finished_app
	 *
	 *
	 */
	public function internalFinishedApplicationEmail($user_id, $is_app = NULL){
		$reply_email = 'oneapp@plexuss.com';
		
		$template_name = 'oneapp_internal_finished_app';

		$qry = DB::connection('bk')->table('users as u')
									 ->join('users_applied_colleges as uac', 'u.id', '=', 'uac.user_id')
									 ->join('colleges as c', 'c.id', '=', 'uac.college_id')
									 ->join('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
									 ->leftjoin('aor_colleges as ac', 'ac.college_id', '=', 'c.id')
									 ->leftjoin('aor as a', 'a.id', '=', 'ac.aor_id')
									 ->select('a.name as aor_name', 'u.fname', 'u.lname', 'u.email', 'ucq.created_at', 
									 		  'ucq.updated_at', 'c.school_name', 'u.id as user_id')
									 ->groupBy('c.id')
									 ->where('u.id', $user_id)
									 ->get();
		$params = array();

		$transcript = Transcript::on('bk')->where('user_id', $user_id)
								->selectRaw('GROUP_CONCAT(DISTINCT( doc_type)) as doc_type')
								->pluck('doc_type');

		$params['LIST_OF_SCHOOLS'] = '';
		foreach ($qry as $key) {
			
			$params['NAME'] 		   = ucwords(strtolower($key->fname . ' '. $key->lname));
			$params['SL_SCHOOL_NAME']  = $key->school_name;
			$list_of_schools = $key->school_name;
			isset($key->aor_name) ? $list_of_schools = $list_of_schools. ' (' . $key->aor_name . ') <br>' : $list_of_schools = $list_of_schools. ' <br>' ;
			
			$params['LIST_OF_SCHOOLS'] .= $list_of_schools;

			$params['APP_START_DATE']  = Carbon::createFromTimestamp( strtotime($key->created_at) )->toDayDateTimeString();
			$params['APP_END_DATE']    = Carbon::createFromTimestamp( strtotime($key->updated_at) )->toDayDateTimeString();
			$params['APP_URL']		   = 'https://plexuss.com/view-student-application/'.Crypt::encrypt($key->user_id);
			$params['TRANSCRIPT_LIST'] = $transcript[0];
			$params['AGENCY_NAME']     = 'N/A';
			$params['IS_APP']          = isset($is_app) ? 'Yes' : 'No';

			$user = DB::connection('bk')->table('users as u')
										->join('agency as a', 'u.utm_source', 'a.utm_source')
										->where('u.id', $user_id)
										->select('a.name as agency_name')
										->first();

			if (isset($user)) {
				$params['AGENCY_NAME'] = $user->agency_name;
			}
		}


		if (isset($params['NAME'])) {
			$arr = array('anthony.shayesteh@plexuss.com');
			// $arr = array('anthony.shayesteh@plexuss.com');

			foreach ($arr as $key => $value) {
				$email_arr = array('email' => $value, 
					   'name' => '',
					   'type' =>'to');

				$this->template_name = $template_name;
				$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);
			}
			$msg = '';
			$msg .= "Is Mobile App: ". $params['IS_APP']. "\n" ;
			$msg .= "Name: ". $params['NAME']. "\n" ;
			$msg .= "Schools applied: ". $params['LIST_OF_SCHOOLS']. "\n" ;
			$msg .= "Start app: ". $params['APP_START_DATE']. "\n" ;
			$msg .= "End app: ". $params['APP_END_DATE']. "\n" ;
			$msg .= "App Url: ". $params['APP_URL']. "\n" ;
			$msg .= "Transcript list: ". $params['TRANSCRIPT_LIST']. "\n" ;
			($params['AGENCY_NAME'] != 'N/A') ? $msg .= "Agency Name: ". $params['AGENCY_NAME']. "\n" : NULL;

			$tc = new TwilioController;
			$tc->sendPlexussMsg($msg);

		}	
	}

	/*
	 *
	 * users_additional_uploads_required
	 *
	 */
	public function applicationAdditionalUploadRequired(){

		$reply_email = 'oneapp@plexuss.com';

		$template_name = 'users_additional_uploads_required';

		$today = Carbon::today();

		$qry = DB::connection('bk')->table('users as u')
							 ->leftjoin('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
							 ->leftjoin('application_email_logs as ael', function($q) use ($template_name){
							 		$q->on('u.id', '=', 'ael.user_id');
							 		$q->on('ael.id', '=', DB::raw('(select MAX(id) FROM `application_email_logs` as ael2 WHERE u.id = ael2.user_id)'));
							 		$q->on('ael.template_name', '=', DB::raw("'".$template_name."'"));
							 })
							 ->leftjoin(DB::raw("(	Select count(*) as 'emails_sent', user_id from (
								Select distinct date(created_at), user_id
								from application_email_logs where template_name ='".$template_name."'
								) tbl1 
								group by user_id ) as tbl_count"), 'u.id', '=', 'tbl_count.user_id')
							 ->leftjoin('application_email_suppressions as aes', function($q) use ($template_name){
									$q->on('u.id', '=', 'aes.user_id');
									$q->on('aes.is_supressed', '=', DB::raw(1));
									$q->on('aes.template_name', '=', DB::raw("'".$template_name."'"));	
							 })
							 ->leftjoin(DB::raw("( Select uac.user_id, school_name
													from users_applied_colleges uac 
													join (Select user_id, max(id)
																from users_applied_colleges
																group by user_id) 
														limit_a_college_per_user on uac.user_id = limit_a_college_per_user.user_id
													join colleges c on uac.college_id = c.id
												) as student_applied_to_tbl"), 'ucq.user_id', '=', 'student_applied_to_tbl.user_id')
							 ->where('ucq.application_state', 'uploads')
							 ->whereNull('aes.id')
							 ->where('u.is_ldy', 0)
							 ->where(DB::raw("coalesce(emails_sent, 0)"), '=', 0)
							 ->select('u.id as user_id', 'u.fname', 'u.email', DB::raw("coalesce(emails_sent, 0) as cnt, coalesce(school_name, 'one of our schools') as 'colname'"))
							 ->groupBy('u.id')
							 ->orderBy('u.id', 'DESC')
							 ->orderBy('emails_sent', 'ASC')
							 ->whereNull('ael.id')
							 ->where('ucq.updated_at', '<', $today)
							 ->take(1000)
							 ->get();
		// dd($qry);
		foreach ($qry as $key) {
			$params = array();

			$params['FNAME'] 	= $key->fname;
			$params['COLNAME']  = $key->colname;

			// $key->email = 'anthony.shayesteh@plexuss.com';

			$email_arr = array('email' => $key->email, 
				   'name' => '',
				   'type' =>'to');

			$this->template_name = $template_name;
			$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

			$ael = new ApplicationEmailLog;

			$ael->user_id 		= $key->user_id;
			$ael->template_name = $template_name;

			$ael->save(); 
		}

		return "success";
	} 
	

	/*
	 *
	 * edXEmail
	 *
	 */
	public function edXEmail($template_id, $fname, $email, $user_id = NULL, $user_invite_id = NULL){

		$reply_email = 'social@plexuss.com';

		switch ($template_id) {
			case 1:
				$template_name = "users_edx_college_credit_while_in_highschool";
                // $template_name = "edx_email_certificate";
				break;

			case 2:
				$template_name = "users_edx_free_courses_top_universities";
                // $template_name = "edx_emails_howto";
                // $template_name = "edx_email_certificate";
				break;

			case 3:
				$template_name = "users_edx_ielts_and_toefl";
                // $template_name = "edx_email_certificate";
				break;

			case 4:
				$template_name = "users_edx_free_courses_top_universities_nr";
				break;
			
			case 5:
				$template_name = 'users_edx_free_courses_top_universities_events';
				break;
				
			default:
				# code...
				break;
		}

		$params = array();

		$params['FNAME'] 	= $fname;
		$params['email']    = $email;
		isset($user_id) 	   ? $params['USER_ID']  = $user_id : -1;
		isset($user_invite_id) ? $params['USER_INVITE_ID']  = $user_invite_id : -1;

        isset($user_id)        ? $params['HID'] = Crypt::encrypt($user_id) : NULL;

		// $email = 'anthony.shayesteh@plexuss.com';

		$email_arr = array('email' => $email, 
			   'name' => '',
			   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

		return "success";
	}

	/*
	 *
	 * Users: OneApp Scholarship Email
	 *
	 */
	public function usersOneappScholarshipEmail($fname, $email, $attachments){

		$reply_email = 'support@plexuss.com';

		$template_name = "users_oneapp_scholarship_email";

		$params = array();

		$params['FNAME'] 	= $fname;

		$email = 'anthony.shayesteh@plexuss.com';

		$email_arr = array('email' => $email, 
			   'name' => '',
			   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL);

		return "success";
	}

	/*
	 *
	 * Users: OneApp Scholarship Email
	 *
	 */
	public function generalEmailSend($reply_email, $template_name, $params, $email, $subject = NULL){

		$email_arr = array('email' => $email, 
			   'name' => '',
			   'type' =>'to');

		$this->template_name = $template_name;
		$this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL, $subject);

		return "success";
	}

    /*
     *
     * New General emails to send
     *
     */
    public function newGeneralEmailSend($reply_email, $template_name, $params, $email, $subject = NULL, $user_id = NULL, $ab_test_id = NULL){
        
        $email_arr = array('email' => $email, 
               'name' => '',
               'type' =>'to');

        $this->template_name = $template_name;
        $this->sendMandrillEmail($template_name, $email_arr, $params, $reply_email, NULL, NULL, $subject, $ab_test_id);

        return "success";
    }


	public function sendMandrillEmail($type, $email_arr, $params, $reply_email, $attachments = NULL, $bcc_address = NULL, $subject = NULL, $ab_test_id = NULL){

		// $ept = EmailPlatformToggle::where('active', 1)->first();
		
		// if (isset($ept)) {

		// 	if ($ept->platform == 'mandrill') {
		// 		$this->sendThroughMandrill($type, $email_arr, $params, $reply_email, $attachments, $bcc_address);
		// 		return;	
		// 	}	
		// }	

		$provider = 'sparkpost';

		$etsp = EmailTemplateSenderProvider::on('bk')->where('template_name', $type)->first();

		if (isset($etsp)) {
			$provider = $etsp->provider;
		}
		
		// Sendgrid ip address warmup 
		// if ($provider == "sendgrid") {
		// 	$today = Carbon::today()->subHours(2);
		// 	$day = 1;
		// 	if (Cache::has('sendgrid_ip_warmup_schedules_day_tracker')) {
		// 		$cache = Cache::get('sendgrid_ip_warmup_schedules_day_tracker');
		// 		if ($cache['today'] == $today) {
		// 			$qry = SendgridIpWarmupSchedule::where('day', $cache['day'])->first();

		// 			if (isset($qry) && $qry->volume > $qry->sent) {
		// 				DB::table('sendgrid_ip_warmup_schedules')->where('day', $cache['day'])->increment('sent');
		// 			}else{
		// 				$provider = "sparkpost";
		// 			}
		// 		}else{
		// 			$arr = array();
		// 			$arr['day'] = $cache['day'] + 1;
		// 			$arr['today'] = $today;

		// 			Cache::forever('sendgrid_ip_warmup_schedules_day_tracker', $arr);
		// 			DB::table('sendgrid_ip_warmup_schedules')->where('day', $arr['day'])->increment('sent');
		// 		}

		// 	}else{
		// 		$arr = array();
		// 		$arr['day'] = $day;
		// 		$arr['today'] = $today;

		// 		Cache::forever('sendgrid_ip_warmup_schedules_day_tracker', $arr);
		// 		DB::table('sendgrid_ip_warmup_schedules')->where('day', $arr['day'])->increment('sent');
		// 	}
		// }

		switch ($provider) {
			case 'sparkpost_direct':
				$this->sendThroughSparkpost($type, $email_arr, $params, $reply_email, $attachments, $bcc_address, true, $subject, $ab_test_id);
				break;

			case 'sparkpost':
				$this->sendThroughSparkpost($type, $email_arr, $params, $reply_email, $attachments, $bcc_address, NULL, $subject, $ab_test_id);
				break;

			case 'sendgrid':
				$reply_email = str_replace("plexuss.com", "plexuss.net", $reply_email);
				(isset($etsp->from_name)) ? $fromName = $etsp->from_name : $fromName = "Plexuss";
				$params['email'] = $email_arr['email'];
				$this->sendThroughSendGrid($type, $email_arr, $params, $reply_email, $attachments, $bcc_address, $fromName);
				break;

			case 'mandrill':
				$this->sendThroughMandrill($type, $email_arr, $params, $reply_email, $attachments, $bcc_address);
				break;
			
			default:
				# code...
				break;
		}

		// $this->sendThroughSes($type, $email_arr, $params, $reply_email, $attachments, $bcc_address);

	}

	private function sendThroughSparkpost($type, $email_arr, $params, $reply_email, $attachments = NULL, $bcc_address = NULL, $sparkpost_direct = NULL, $subject = NULL, $ab_test_id = NULL){
        
		$spm = new SparkpostModel($type, $sparkpost_direct);

		if ($this->template_name == '') {
			$this->template_name = $type;
		}

		$response = $spm->sendTemplate($email_arr, $params, $reply_email, $attachments, $bcc_address, $subject, $ab_test_id);

		return $response;

	}


    private function sendThroughSendGrid($type, $email_arr, $params, $reply_email, $attachments = NULL, $bcc_address = NULL, $fromName = NULL){
        $sg = new SendGridModel($type);

        if ($this->template_name == '') {
            $this->template_name = $type;
        }

        $response = $sg->sendTemplate($email_arr, $params, $reply_email, $attachments, $bcc_address, $fromName);

        return $response;
    }

	private function sendThroughSes($type, $email_arr, $params, $reply_email, $attachments = NULL, $bcc_address = NULL){

		$spm = new SesModel($type);

		if ($this->template_name == '') {
			$this->template_name = $type;
		}

		$response = $spm->sendTemplate($email_arr, $params, $reply_email, $attachments, $bcc_address);
	}

	/**
	 * Send an email using mandrill 
	 *
	 * @param  type        template name
	 * @param  email_arr   receiver's email information
	 * @param  params      parameters to send along with the email template.
	 * @return null
	 */
	private function sendThroughMandrill($type, $email_arr, $params, $reply_email, $attachments = NULL, $bcc_address = NULL){

		$md = new MandrillModel($type);

		if ($this->template_name == '') {
			$this->template_name = $type;
		}

		try{
			$response = $md->sendTemplate($email_arr, $params, $reply_email, $attachments, $bcc_address);

			$ml = new MandrillLog();
			$ml->template_name = $this->template_name;
			$ml->response = json_encode($response);
			$ml->email = $email_arr['email'];
			$ml->params = json_encode($params);

			$ml->save();

			$this->template_name = '';

		}catch (Mandrill_Error $e) {

			$str = (String) $e;

			$ml = new MandrillLog();
			$ml->template_name = $this->template_name;
			$ml->response = $str;
			$ml->email = $email_arr['email'];
			$ml->params = json_encode($params);

			$ml->save();

			$this->template_name = '';

			//continue;
			// Mandrill errors are thrown as exceptions
		    echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
			throw $e;
		}
	}

    /*
     *
     * onboardingEmailSend
     *
     */
    public function onboardingEmailSend($email_address, $params){
        $this->template_name = 'onboarding_signup';
        $reply_email = 'support@plexuss.com';

        $email_arr = array('email' => $email_address,
            'name' => $params['FNAME'],
            'type' =>'to');

        $this->sendMandrillEmail('onboarding_signup', $email_arr, $params, $reply_email, NULL, NULL);

        return "success";
    }
}
