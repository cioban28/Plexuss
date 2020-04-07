<?php

namespace App\Http\Controllers;

use DB, Carbon\Carbon, Queue, AWS, Request;
use App\InternalCollegeContactTemplate, App\InternalCollegeContactInfoLog, App\InternalCollegeContactPlexussEmail, App\SparkpostModel;
use App\InternalCollegeContactInfo, App\EdxEmailLog, App\EdxTemplate, App\UsersIdsForEmailsLog, App\UsersPortalEmailEffortLog, App\UsersPortalEmailEffortCouldNotSendLog;
use App\RevenueOrganization, App\DistributionClient, App\AdRedirectCampaign, App\Recruitment, App\PortalNotification, App\AorCollege, App\CollegeOverviewImages;
use App\User, App\College, App\PartnerEmailLog, App\PartnerEmailTemplate, App\EmailSuppressionList, App\SendGridModel, App\RoleBaseEmail, App\UsersInvite, App\AdClick, App\UsersClusterLog, App\CappexPossibleMatch, App\UsersWithoutDob, App\NrccuaUser, App\OrganizationPortal, App\RecruitmentTag, App\OrganizationPortalUser, App\PrescreenedUser, App\RecruitmentVerifiedHS, App\RecruitmentVerifiedApp, App\Objective, App\RecruitmentConvert, App\RecruitmentRevenueOrgRelation, App\TrackingPage, App\ZipCodes, App\UsersPortalEmailEffortLogsDateId, App\PartnerEmailLogsDateId, App\Priority, App\State, App\EmailTemplateSenderProvider, App\EmailLogicHelper, App\EmailTemplateGrouping, App\CollegesDataFromApi, App\CollegesDataFromApisMapping;



use App\RevenueSchoolsMatching, App\TrackingPageId, App\RevenueEabStudent, App\RevenueNrccuaStudent, App\TrackingPageLog,App\NrccuaQueue, App\EmailClickLog, App\Country, App\EmailOpenLog, App\OrganizationBranch, App\NrccuaNearbyState, App\Score, App\PickACollegeView, App\ConfirmToken;

use App\Http\Controllers\MandrillAutomationController, App\Http\Controllers\DistributionController;
use App\Http\Controllers\CollegeRecommendationController, App\Http\Controllers\TwilioController;
use	App\Http\Controllers\ScholarshipsController;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use PHPMailer, SimpleXMLElement;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;
use App\Console\Commands\AddToEmailSuppressionList;
use App\Console\Commands\PostAutoPortalEmail, App\Console\Commands\AddRecommendationsToSupportAccount;

use App\CollegeLogoUploadLog, App\DistributionClientFieldMapping, App\DistributionClientValueMapping, App\UsersIpLocation;
use Intervention\Image\ImageManagerStatic as Image;

use App\Jobs\EmailQueueProcess,  App\Jobs\EmailSingleSend;

class UtilityController extends Controller
{

	public function emailInternalCollegesLiveChat(){
		$template_id = 1;
		$sender_email = "emma.johnson@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesScholarship(){
		$template_id = 2;
		$sender_email = "michelle.harris@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesTexting(){
		$template_id = 3;
		$sender_email = "michelle@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesCalling(){
		$template_id = 4;
		$sender_email = "steven.ball@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesChatBot(){
		$template_id = 5;
		$sender_email = "liam.jones@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesRecruitment(){
		$template_id = 6;
		$sender_email = "james.lee@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesTCPA(){
		$template_id = 7;
		$sender_email = "ben.garcia@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesVirtualCollegeApplicationSystem(){
		$template_id = 8;
		$sender_email = "charlotte.thompson@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesFBMarketing(){
		$template_id = 9;
		$sender_email = "michael.clark@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesAdvertising(){
		$template_id = 10;
		$sender_email = "alex.miller@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesCRM(){
		$template_id = 11;
		$sender_email = "emily.lopez@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesHigherEducationEngineeringServices(){
		$template_id = 12;
		$sender_email = "olivia.lewis@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesDataScienceAndRegressionAnalysis(){
		$template_id = 13;
		$sender_email = "aria.anderson@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesVCs(){
		$template_id = 26;
		$sender_email = "jacob@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesPresidents(){
		$template_id = 27;
		$sender_email = "nancy@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailIntlStudentLeads(){
		$template_id = 37;
		$sender_email = "jacob.wright@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailIntlAgentWebinar(){
		$template_id = 38;
		$sender_email = "nancy@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailIntlNavitas(){
		$template_id = 39;
		$sender_email = "nancy@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalPlexussEmailForAnalytics(){

		$template_id = 51;
		$sender_email = 'james.lee@plexuss.com';

	    $qry =  DB::connection('bk')->table('internal_college_contact_info as icci')
	    								  ->leftjoin('internal_college_contact_info_logs as iccil', function($q) use ($template_id){
	    								  			$q->on('icci.id', '=', 'iccil.icci_id')
	    								  			  ->where('iccil.template_id', '=', $template_id);
	    								  })
	    								  ->join('colleges as c', 'c.id', '=', 'icci.college_id')
	    								  ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'icci.college_id')
	    								  ->whereIn('icci.type', array('admissions_enrollmen', 'recruitment_related'))
	    								  ->whereNull('iccil.icci_id')
	    								  ->where('icci.unsub', 0)
	    								  ->whereRaw("icci.college_id not in 
												    (select college_id
												    from priority p
												    join colleges c on p.college_id = c.id
												    where aor_id is null)")
	    								  ->take(10)
	    								  ->select('fname', 'email', 'icci.id', 'c.school_name', 'c.id as college_id', 'cr.plexuss as rank',
	    										   'c.logo_url')
	    								  ->orderBy(DB::raw("RAND()"))
	    								  ->get();

	    $sm = new SparkpostModel('test_template');

	    $send_email_qry = InternalCollegeContactPlexussEmail::on('bk')
    														->where('email', $sender_email)
    														->first();
    	$sent_email_cnt = 0;
	   	foreach ($qry as $key) {

	   		isset($key->logo_url) ? $key->logo_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->logo_url : $key->logo_url = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png";
    		
    		// $key->email= 'anthony.shayesteh@plexuss.com';
    		// $key->email= 'jp.novin@plexuss.com';
    		// $key->email  = 'jacqueline.lee@plexuss.com'	;
    		// $key->email = 'nic.nuyten@plexuss.com';
    		$is_suppressed = $sm->isSupressed($key->email);

    		$is_organization = User::on('bk')->where(function($q){
    										   		$q->orWhere('is_organization', '=', DB::raw("1"))
    										   		  ->orWhere('is_agency', '=', DB::raw("1"));
    										   })
    										   ->where('email', $key->email)
    										   ->first();

    		if ((isset($is_suppressed) && $is_suppressed == true) || isset($is_organization)) {

    			$icci_temp = InternalCollegeContactInfo::find($key->id);

    			$icci_temp->unsub = 1;
    			$icci_temp->save();

    			$log = new InternalCollegeContactInfoLog;
	    		
	    		$log->sent_email_id = $send_email_qry->id;	
	    		$log->icci_id = $key->id;
	    		$log->template_id = $template_id;
	    		$log->save();

    			continue;
    		}

    		if ($key->rank <=100) {
    			$template_id = 51;
    		}elseif ($key->rank > 100 && $key->rank < 500) {
    			$template_id = 52;
    		}else{
    			$template_id = 53;
    		}

    		$icct =  InternalCollegeContactTemplate::find($template_id);

			$data = array();
			$data['host']      = env('MAIL_HOST');
			$data['username']  = $send_email_qry->email;
			$data['pass'] 	   = env('UNIVERSAL_EMAIL_PASS');
			$data['from_name'] = $send_email_qry->name;
			$data['to_email']  = $key->email;
			// $data['to_email']  = 'anthony.shayesteh@plexuss.com';
			$data['to_name']   = ucwords($key->fname);
			$data['subject']   = $icct->subject;
			$data['school_name'] = '';
			$data['slug']      = '';
			$data['num_of_students'] = '';
			isset($key->school_name) ? $data['school_name'] = $key->school_name : null;
			isset($key->slug) ? $data['slug'] = $key->slug : null;
			isset($key->num_of_students) ? $data['num_of_students'] = $key->num_of_students : null;

			if ( $data['num_of_students'] < 1000) {
				$data['num_of_students'] = 1020;
			}
			
			$body = (isset($key->fname) && !empty($key->fname)) ? '<p><span style="font-size: 12pt; font-family: \'Calibri\';"><span style="color: #222222;">'.$icct->intro_str.' '. ucwords($key->fname) . ", </span></span></p><p></p>" : '<p><span style="font-size: 12pt; font-family: \'Calibri\';"><span style="color: #222222;">Hello there, </span></span></p><p></p>';

			$template_content = str_replace("{{email}}", $data['to_email'], $icct->template);
			$template_content = str_replace("*|FNAME|*", $data['to_name'], $template_content);
			$template_content = str_replace("{{school_name}}", $data['school_name'], $template_content);
			$template_content = str_replace("{{num_of_students}}", number_format($data['num_of_students']), $template_content);
			$template_content = str_replace("{{slug}}", $data['slug'], $template_content);
			$template_content = str_replace("{{NAME}}", ucwords(strtolower($key->fname)), $template_content);
			$template_content = str_replace("{{COLLEGELOGO}}", $key->logo_url, $template_content);
			$template_content = str_replace("{{email}}", $key->email, $template_content);

			$data['subject'] = str_replace("{{num_of_students}}", number_format($data['num_of_students']), $data['subject']);

			$data['body'] = $template_content;	

			$result = $this->sendSMTPEmail($data);

    		$log = new InternalCollegeContactInfoLog;
    		
    		$log->sent_email_id = $send_email_qry->id;	
    		$log->icci_id = $key->id;

    		// Just save all three template as one template
    		$template_id = 51;
    		$log->template_id = $template_id;
    		$log->save();

    		$sent_email_cnt++;
	    }

	    return "Number of emails sent: ". $sent_email_cnt;
	}

	public function partnerEmailCronJob($rand, $cron_type){

		$time_now = Carbon::now()->toTimeString();
				
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailCronJob_'.$rand)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailCronJob_'.$rand);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		$now 			   = Carbon::today();
		
		$first_day_of_week = $now->subDays(7);
		$tomorrow 		   = Carbon::tomorrow(); 
		$seven_days_ago = Carbon::today()->subDays(7);

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailCronJob_'.$rand, 'in_progress', 40);

		$cnt = DB::connection('bk')->table('partner_email_logs as pel')
									 ->leftjoin('partner_email_templates as pet', 'pel.template_id', '=', 'pet.id')
									 ->select('pel.template_id', DB::raw('COUNT(pel.id) as cnt'), 'pet.max_num_of_sent_email_daily',
											  'pet.template_name')
									 ->where('pel.created_at', '>=', Carbon::today())
									 ->where('pel.created_at', '<', Carbon::tomorrow())
									 ->groupBy('pel.template_id')
									 ->get();

		$template_suppresion_ids = array();
		if (isset($cnt) && !empty($cnt)) {
			foreach ($cnt as $key) {

				if ($key->cnt >= $key->max_num_of_sent_email_daily && isset($key->template_id)) {
					$template_suppresion_ids[] = $key->template_id;
				}
			}
		}

		$pel = PartnerEmailTemplate::on('bk')->orderBy(DB::raw('RAND()'))
											   ->where('id', $rand);
	
		if (!empty($template_suppresion_ids)) {
			$pel = $pel->whereNotIn('id', $template_suppresion_ids);
		}

		$pel = $pel->first();
		
		if (!isset($pel) || empty($pel)) {
			return "all partner emails has reached max";
		}
		
		// $pel->template_name = 'topuniversities_grad_school_ver_b';
		// $pel->template_name = 'topuniversities_mba_ver_b';

		$company 	    = $pel->company;
		$template_name  = $pel->template_name;
		$template_id    = $pel->id;
		$take           = $pel->take;
		$contact_method = $pel->contact_method;
		$start_time     = $pel->start_time;
		$end_time     	= $pel->end_time;

		if (isset($start_time) && isset($end_time)) {

			$can_i_run = false;
			if ($time_now >= $start_time && $time_now <= $end_time) {
				$can_i_run = true;
			}

			if ($can_i_run == false) {
				return "Can't run this at this time";
			}
		}
		
		$group_by_uid = true;			

		$qry = DB::connection('bk')->table('users as u')
									 ->whereRaw("not exists (Select 1 from email_suppression_lists esl where esl.uid = u.id and uid is not null)")
									 ->where('u.is_organization', 0)
								     ->where('u.is_agency', 0)
								     ->where('u.is_ldy', 0)
								     ->where('u.is_alumni', 0)
								     ->where('u.is_parent', 0)
								     ->where('u.is_counselor', 0)
								     ->where('u.is_university_rep', 0)
									 ->where('u.is_aor', 0)

									 ->where('u.email', 'NOT LIKE', '%els.edu%')
									 ->where('u.email', 'NOT LIKE', '%shorelight%')
									 ->where('u.id', '!=', 1024184)

									 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
									 
									 ->orderBy(DB::raw("rand()"))
									 ->take($take)
									 ->select('u.id', 'u.fname', 'u.email');

		if ($template_name == "plexuss_owl_scholarship_update_r2" || $template_name == "into_study_uk_2" ||
			$template_name == "plexuss_cappex_scholarship_update_r1" || $template_name == "openclassromms_email_r2" ||
			$template_name == "springboard_direct_v2") {
			$qry = $qry->whereRaw("not exists(
									select
										1
									from
										ad_clicks
									where
										company = '".$company."'
									and user_id = u.id
									AND user_id is not null
								)");
		}else{
			$qry = $qry->whereRaw("not exists(
									select
										1
									from
										ad_clicks
									where
										company = '".$company."'
									and pixel_tracked = 1
									and user_id = u.id
									AND user_id is not null
								)");
		}

		// Study Portal query retry every 4 days instead of 7
		if ($template_name == "studyportals_email_r2" || $template_name == "studyportal_email3") {
			$qry = $qry->where(DB::raw("datediff(current_date() , u.created_at)"), '>', 3)
			           ->whereRaw("not exists (
												Select
													1
												from
													partner_email_logs eel
												where
													u.id = eel.user_id
												and eel.user_id is not NULL
												and 
											 ( eel.id >= (
													Select
														min(pel_id)
													from
														partner_email_logs_date_ids peldi
													where
														date(timestamp) >= date_sub(current_date , interval 4 day)
													)
												OR (eel.template_id = 19 and
											    eel.id >= (
													Select
														min(pel_id)
													from
														partner_email_logs_date_ids peldi
													where
														date(timestamp) >= date_sub(current_date , interval 5 day)
													)
											   )
											 )
											)");
		}elseif($template_name == "plexuss_owl_scholarship_update_r2" || $template_name == "into_study_uk_2" ||
				$template_name == "plexuss_cappex_scholarship_update_r1" || $template_name == "openclassromms_email_r2") {
			$qry = $qry->whereRaw("not exists 
									(Select 1 from partner_email_logs eel
									where u.id = eel.user_id 
									and eel.user_id is not NULL
									and eel.id >= (Select min(pel_id)
										 from partner_email_logs_date_ids peldi
										 where date(timestamp) >= date_sub(current_date, interval 4 day))
									)");
		}else{
			$qry = $qry->whereRaw("not exists 
									(Select 1 from partner_email_logs eel
									where u.id = eel.user_id 
									and eel.user_id is not NULL
									and eel.id >= (Select min(pel_id)
										 from partner_email_logs_date_ids peldi
										 where date(timestamp) >= date_sub(current_date, interval 7 day))
									)");
		}

		// Place filter for qs companies
		if ($template_name == 'topuniversities_mba' 			  || $template_name == 'topuniversities_grad_school' || 
			$template_name == 'topuniversities_grad_school_ver_b' || $template_name == 'topuniversities_mba_ver_b' ) {
			$qry = $qry->where(function($q){
					$q->orWhere('u.country_id', '=', DB::raw(1))
					  ->orWhere('u.country_id', '=', DB::raw(2))
					  ->orWhere('u.country_id', '=', DB::raw(140));
			});
		}

		// USA and Canada
		if ($template_name == 'qs_scholarships_template1_copy_04' || $template_name == 'plexuss_qs_email_template_update_r1') {
			$qry = $qry->where(function($q){
					$q->orWhere('u.country_id', '=', DB::raw(1))
					  ->orWhere('u.country_id', '=', DB::raw(2));
			});
		}

		// India
		if ($template_name == 'plexuss_qs_email_template_update_india') {
			$qry = $qry->where('u.country_id', '=', 99);
		}

		// Europe
		if ($template_name == 'plexuss_qs_email_template_update_europe') {
			$qry = $qry->join('countries as c', 'c.id', '=', 'u.country_id')
					   ->where('c.continent_code', 'EU');
		}

		// Latin America
		if ($template_name == 'plexuss_qs_email_template_update_america') {
			$qry = $qry->join('countries as c', 'c.id', '=', 'u.country_id')
					   ->where('c.continent_code', 'SA');
		}
		
		// edx
		if ($template_name == "users_invite_edx_free_courses_top_universities_nr") {
			$qry = $qry->where('u.country_id', '!=', 1);
		}

		// Conditions for Springboard
		if ($template_name == 'springboard_first') {
			$qry = $qry->where('u.country_id', 1)
					   ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')
					   ->whereNull('cc.id'); 
		}

		// Music Institute query
		if ($template_name == "music_institute_v1") {
			$qry = NULL;
			$qry = DB::connection('bk')->table('objectives as ob')
										 ->join('users as u', 'u.id', '=', 'ob.user_id')
										 ->join('countries as c', 'c.id', '=', 'u.country_id')
										 // ->whereRaw("major_id in 
											// 		(
											// 		    Select distinct m.id
											// 		    from major_mapping mm
											// 		    join majors m on mm.plexuss_major = m.id
											// 		    where client_major in 
											// 		    (select id from major_mapping_ids where name like '%music%')
											// 		)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	ad_clicks
																where
																	company = '".$company."'
																and pixel_tracked = 1
																AND user_id is not null
															)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	partner_email_logs
																where
																	template_id = '".$rand."'
																AND	created_at >= '".$first_day_of_week."'
																AND created_at < '".$tomorrow."'
																AND user_id is not null
															)")
										 ->whereIn('u.country_id', array(32, 114, 99))
										 ->where('u.is_organization', 0)
										 ->where('u.is_university_rep', 0)
										 
										 ->where('u.is_counselor', 0)
										 ->where('u.is_aor', 0)
										 ->where('u.email', 'NOT LIKE', '%els.edu%')
										 ->where('u.email', 'NOT LIKE', '%shorelight%')
										 ->where('u.id', '!=', 1024184)
									     ->where('u.is_ldy', 0)
										 ->leftjoin('partner_email_logs as eel', function($q) use ($template_id){
									 			$q->on('u.id', '=', 'eel.user_id')
									 			  ->on('eel.template_id', '=', DB::raw($template_id));
										 })
										 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

									     ->whereNull('esl.id')
									     ->groupBy('u.id')
										 ->orderBy(DB::raw("rand()"))
										 ->take($take)
										 ->select('u.id', 'u.fname', 'u.lname', 'u.email', DB::raw("if(c.id = 114, 'South Korea', c.country_name) as country_name"));
		}

		// Eddy GCU and CALU conditions
		if ($template_name == 'gcu_eddy_click' || $template_name == 'calu_eddy_click') {
			$qry = $qry->where('u.country_id', 1);
		}

		// Eddy CALU Conditions
		if ($template_name == 'calu_eddy_click') {
			$qry = $qry->leftjoin('objectives as ob', 'ob.user_id', '=', 'u.id')
						   ->leftjoin('majors as mj', 'ob.major_id', '=', 'mj.id')
						   ->leftjoin('departments as dp', function($q){
						   			$q->on('dp.id', '=', 'mj.department_id');
						   			$q->on('dp.id', '!=', DB::raw(9));
						   			$q->on('dp.id', '!=', DB::raw(16));
						   			$q->on('dp.id', '!=', DB::raw(36));
						   });
		}

		// Exampal query
		if ($template_name == "exampal_v1" || $template_name == "exampal_v2") {
			$qry = NULL;
			$qry = DB::connection('bk')->table('users as u')
										 ->whereRaw("u.id in 
													(
														select distinct u.id
														from users u
														left join objectives o on u.id = o.user_id
														where country_id in 
														(1, 2, 15, 23,
														73, 96, 103, 104,
														108, 114, 120, 126,
														143, 153, 187, 192,
														210, 225, 226
														#, 99, 171, 32, 100
														)
														and (o.degree_type in (4, 5) or u.in_college = 1)
															and is_plexuss = 0
														and is_organization = 0
														and is_ldy = 0
														and is_university_rep = 0
														and is_aor = 0
														and is_university_rep = 0
													#	and (major_id is null or major_id in 
													#		(Select id from majors where department_id = 37)
													#	)
														and email like '%@%.%'
													)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	ad_clicks
																where
																	company = '".$company."'
																AND user_id is not null
															)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	partner_email_logs
																where
																	template_id = '".$rand."'
																AND	created_at >= '".$first_day_of_week."'
																AND created_at < '".$tomorrow."'
																AND user_id is not null
															)")
										 ->select('u.id', 'u.fname', 'u.lname', 'u.email')
										 ->leftjoin('partner_email_logs as eel', function($q) use ($template_id){
									 			$q->on('u.id', '=', 'eel.user_id')
									 			  ->on('eel.template_id', '=', DB::raw($template_id));
										 })
										 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

									     ->whereNull('esl.id')
									     ->groupBy('u.id')
										 ->orderBy(DB::raw("rand()"))
										 ->take($take);
		}

		// Cornell College Query
		if ($template_name == 'cornell_college_v1' || $template_name == 'cornell_college_v2') {
			$qry = NULL;
			$qry = DB::connection('bk')->table('users as u')
										 ->select('u.id', 'u.fname', 'u.lname', 'u.email')
										 ->whereRaw("(u.in_college = 0 or u.in_college is null)
														and u.is_plexuss = 0
														and u.is_organization = 0
														and u.is_ldy = 0
														and u.is_university_rep = 0
														and u.is_aor = 0
														and u.is_university_rep = 0
														and (
														    (u.country_id = 99)
														    or 
														    (((u.state in ('CO', 'Colorado')) or (u.city like '%chicago%') and u.state in ('IL', 'Illinois'))
														        and u.country_id = 1 and u.id not in (Select user_id from country_conflicts)
														    )
														)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	ad_clicks
																where
																	company = '".$company."'
																and pixel_tracked = 1
																AND user_id is not null
															)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	partner_email_logs
																where
																	template_id = '".$rand."'
																AND	created_at >= '".$first_day_of_week."'
																AND created_at < '".$tomorrow."'
																AND user_id is not null
															)")
										 ->leftjoin('partner_email_logs as eel', function($q) use ($template_id){
									 			$q->on('u.id', '=', 'eel.user_id')
									 			  ->on('eel.template_id', '=', DB::raw($template_id));
										 })
										 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

									     ->whereNull('esl.id')
									     ->groupBy('u.id')
										 ->orderBy(DB::raw("rand()"))
										 ->take($take);
		}

		// Magoosh query
		if ($template_name == 'magoosh_email_r4') {
			$qry = NULL;
			$qry = DB::connection('bk')->table('users as u')
										 ->join('countries as c', 'c.id', '=', 'u.country_id')

										 ->where('c.is_english_primary', 0)
										 ->where('u.is_organization', 0)
										 ->where('u.is_university_rep', 0)
										 
										 ->where('u.is_counselor', 0)
										 ->where('u.is_aor', 0)
										 ->where('u.email', 'NOT LIKE', '%els.edu%')
										 ->where('u.email', 'NOT LIKE', '%shorelight%')
										 ->where('u.id', '!=', 1024184)
										 ->where('u.is_ldy', 0)

										 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
										 ->where(DB::raw("datediff(current_date() , u.created_at)"), '>', 14)
										 ->select('u.id', 'u.fname', 'u.lname', 'u.email')
										 ->whereRaw("not exists 
												(Select user_id	
												 from scores 
												 where scores.user_id = u.id
												 and (toefl_ibt_total > 65 or toefl_total > 15 or ielts_total >= 5)
											)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	ad_clicks
																where
																	company = '".$company."'
																and pixel_tracked = 1
																AND user_id is not null
															)")
										 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	partner_email_logs
																where
																	template_id = '".$rand."'
																AND	created_at >= '".$first_day_of_week."'
																AND created_at < '".$tomorrow."'
																AND user_id is not null
															)")
										 ->leftjoin('partner_email_logs as eel', function($q) use ($template_id){
									 			$q->on('u.id', '=', 'eel.user_id')
									 			  ->on('eel.template_id', '=', DB::raw($template_id));
										 })
										 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

									     ->whereNull('esl.id')									     
										 ->orderBy(DB::raw("rand()"))
										 ->take($take);
		}

		// San Diego State University query
		if ($template_name == 'sdsu_email_r2') {
			$qry = $qry->where('u.country_id', '=', 219);
		}

		// USF General Email
		if ($template_name == "usf_email_updated_copy_r1") {
			$qry = $qry->whereRaw("(u.financial_firstyr_affordibility is null 
									or u.financial_firstyr_affordibility in ('10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '5,000 - 10,000', '50,000')) ")
					   ->whereRaw("(u.utm_content like '%master%' OR
									exists(
								    Select
								        user_id
								    from
								        objectives
								    where
								        objectives.user_id = u.id
										and degree_type in (4,5)
									)
								)")
					   ->whereRaw("u.country_id in 
										(1, 32, 140, 226, 163, 105, 48, 170, 111)")
						   ->whereRaw("not exists 
										    (Select user_id from country_conflicts
										     where country_conflicts.user_id = u.id 
										)")
					   ->whereRaw("not exists 
									(Select user_id from objectives
									 where objectives.user_id = u.id 
									 and major_id in (1196,412,5,578,5952,598,697,7,734,740,915,935,936,944,104,105,1077,1125,
										113,114,136,1392,1420,1426,143,1431,144,145,146,1460,1466,1468,152,153,154,155,158,
										160,161,162,163,165,171,179,180,181,185,186,189,196,262,310,361,37,413,469,473,489,577,
										579,581,5953,5954,5955,599,647,661,665,683,695,696,698,699,700,760,762,764,843,919,92,
										937,938,939,940,946,952
								)
								)");
		}

		// USF Program based query
		if ($template_name == "usf_dynamic_program_email") {
			$qry = $qry->whereRaw("(u.financial_firstyr_affordibility is null 
								    or u.financial_firstyr_affordibility in ('10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '5,000 - 10,000', '50,000')) ")
					   ->whereRaw("(u.utm_content like '%master%' OR
								    exists(
								    Select
								        user_id
								    from
								        objectives
								    where
								        objectives.user_id = u.id
								        and degree_type in (4,5)
								    )
								)")
					   ->whereRaw("(u.in_college = 1 or u.in_college is null)")
					   ->whereRaw("u.country_id in 
										(1, 32, 140, 226, 163, 105, 48, 170, 111)")
					   ->whereRaw("not exists 
										    (Select user_id from country_conflicts
										     where country_conflicts.user_id = u.id 
										)")
					   ->whereRaw("exists 
									    (Select user_id from objectives
									     where objectives.user_id = u.id 
									     and major_id in (113,104,136,105,469,114,1460,473,489,
														598,599,683,665,843,
														7,196,186,361,181,189,185,
														935,1392,939,938,936,937,940,
														412,413,37,
														5952,5953,5954,5955,
														936,940,939,938,935,
														944,946,1420,939,1431,740,161,
														740,
														734,
														5,145,146,154,158,152,143,160,161,144,163,165,153,171,1466,179,155,162,180,
														1196,310,760,762,647,1468,1125,764,
														915,919,92,952,
														578,577,579,1077,581,
														697,700,695,1426,696,262,661,698,699
														)
									)")
					   ->addSelect('u.profile_img_loc', 'u.lname');
		}

		// Study Portal query
		if ($template_name == "studyportals_email_r2" || $template_name == "studyportal_email3") {
			$qry = $qry->whereRaw("(
									u.in_college = 1 or
									(year(u.birth_date) <= (year(current_date) - 21) and birth_date != '0000-00-00') or
									utm_content like '%master%' OR
									exists (
								    Select
								        1
								    from
								        objectives
								    where
								        objectives.user_id = u.id
										and degree_type in (4,5)
									)
								)");
			$qry = $qry->whereRaw("(
									`u`.`country_id` in(
										2 ,12 ,13 ,15 ,16 ,17 ,22 ,23 ,29 ,32 ,35 ,44 ,45 ,48 ,52 ,53 ,55 ,56 ,57 ,62 ,63 ,67 ,71 ,72 ,73 ,80 ,81 ,84 ,89 ,96 ,97 ,98 ,99 ,100 ,103 ,104 ,105 ,107 ,108 ,109 ,110 ,114 ,119 ,120 ,125 ,126 ,128 ,131 ,134 ,140 ,142 ,145 ,153 ,154 ,156 ,163 ,164 ,167 ,170 ,171 ,173 ,174 ,176 ,178 ,179 ,187 ,189 ,192 ,193 ,194 ,199 ,207 ,208 ,210 ,213 ,219 ,224 ,225 ,227 ,229 ,232 ,233 ,234 ,235
									)
									or(
										u.country_id in(1 , 226)
										and not exists(
											Select
												1
											from
												country_conflicts cc
											where
												cc.user_id = `u`.`id`
										)
									)
								)");
			// $qry = $qry->whereRaw("(u.financial_firstyr_affordibility is null or u.financial_firstyr_affordibility in ('0', '0.00', '0 - 5,000', ''))")
					   // ->whereRaw("(u.utm_content like '%master%' OR
								// 		exists(
								// 	    Select
								// 	        user_id
								// 	    from
								// 	        objectives
								// 	    where
								// 	        objectives.user_id = u.id
								// 			and degree_type in (4,5)
								// 		)
								// 	)")
					   // ->whereRaw("(u.in_college = 1 or u.in_college is null)");
		}

		// Zeta Texting Query
		if ($template_name == "zeta_texting") {
			$qry = NULL;

			$qry = DB::connection('bk')->table('users as u')
									 ->leftjoin('partner_email_logs as eel', function($q) use ($template_id){
									 			$q->on('u.id', '=', 'eel.user_id')
									 			  ->on('eel.template_id', '=', DB::raw($template_id));
									 })

									 ->where('u.is_organization', 0)
									 ->where('u.is_university_rep', 0)
									 
									 ->where('u.is_counselor', 0)
									 ->where('u.is_aor', 0)
									 ->where('u.email', 'NOT LIKE', '%els.edu%')
									 ->where('u.email', 'NOT LIKE', '%shorelight%')
									 ->where('u.id', '!=', 1024184)
									 ->where('u.is_ldy', 0)

									 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
									 ->where(DB::raw("datediff(current_date() , u.created_at)"), '>', 120)
									 ->whereRaw("u.id not in(
																select distinct
																	user_id
																from
																	partner_email_logs
																where
																	created_at >= '".$first_day_of_week."'
																and created_at < '".$tomorrow."'
																AND user_id is not null
															)")
									 ->groupBy('u.id')
									 ->orderBy(DB::raw("rand()"))
									 ->take($take)
									 ->select('u.id', 'u.fname', 'u.email', 'u.phone');

			$qry = $qry->where('u.country_id', 1)
					   ->whereRaw("not exists 
								    (Select user_id from country_conflicts
								     where country_conflicts.user_id = u.id 
								)")
					   ->whereRaw("(u.txt_opt_in is null or u.txt_opt_in in (0,1))")
					   ->where(DB::raw("length(u.phone)"), ">=", 10);
		}

		// Hult University
		if ($template_name == "plexuss_hult_email_blackhtml") {
			$qry = $qry->whereIn('u.country_id', array(1,2))
					   ->whereRaw("not exists(
										Select
											user_id
										from
											country_conflicts
										where
											country_conflicts.user_id = u.id
									)")
					   ->where('u.fname', 'NOT LIKE', "%test%")
					   ->where('u.lname', 'NOT LIKE', "%test%")
					   ->where('u.email', 'NOT LIKE', "%test%")
					   ->where(DB::raw("year(birth_date)"), "<=", DB::raw("(year(current_date()) - 20)"))
					   ->whereRaw("(
										u.financial_firstyr_affordibility is null
										or u.financial_firstyr_affordibility in(
											'10,000 - 20,000' ,
											'20,000 - 30,000' ,
											'30,000 - 50,000' ,
											'50,000'
										)
									)");
		}

		// EDX Certificate
		if ($template_name == "edx_certificate" || $template_name == "edx_email_certificate") {
			$qry = $qry->whereRaw("(utm_content like '%master%' OR
								    exists(
								    Select
								        user_id
								    from
								        objectives
								    where
								        objectives.user_id = u.id
								        and degree_type in (4,5)
								    )
								)");
		}

		// Oregon University Query
		if ($template_name == "osu_email_r1") {
			$qry = $qry->join('countries as c', 'c.id', '=', 'u.country_id')
					   ->where('c.id', '!=', 1)
			           ->where(function($q){
			           		$q->where(function($q2){
			           			$q2->whereIn('u.financial_firstyr_affordibility', array('5,000 - 10,000' ,
																						'10,000 - 20,000' ,
																						'20,000 - 30,000' ,
																						'30,000 - 50,000' ,
																						'50,000'))
			           			   ->whereIn('c.tier', array(3,4));
			           		})
			           		  ->orWhereIn('c.tier', array(1,2));
			           });
		}

		// Alliant Query
		if ($template_name == "alliant_email_r1") {
			$qry = $qry->whereRaw("(
								    (
								    u.financial_firstyr_affordibility in(
								                '10,000 - 20,000',
								        '20,000 - 30,000' ,
								        '30,000 - 50,000' ,
								        '50,000'
								    ) and u.country_id in (Select id from countries where toggle_passthrough_financials = 1))
								    or u.country_id in (Select id from countries where toggle_passthrough_financials = 0)
								)")
					  ->whereIn("u.country_id", array(2,13,32,81,110,111,171,197,219,229,233));
		}

		// Cappex Scholarship
		if ($template_name == "cappex_scholarship_email_r1" || $template_name == "plexuss_cappex_scholarship_update_r1") {
			$qry = $qry->leftjoin('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
					   ->whereRaw('(
									(coalesce(
										IF(
											u.in_college = 0 ,
											hs_grad_year ,
											college_grad_year
										) ,
										nu.grad_year
									) between 2019 and 2021)
									or coalesce(
										IF(
											u.in_college = 0 ,
											hs_grad_year ,
											college_grad_year
										) ,
										nu.grad_year) is null
									)')
					   ->whereRaw('not exists (select user_id from country_conflicts cc where `u`.`id` = `cc`.`user_id`)')
					   ->where('u.country_id', 1)
					   ->whereRaw('date(u.created_at) < date_sub(current_date , interval 10 day)')
					   ->addSelect(DB::raw("date_format(last_day(current_date), '%M %e, %Y') as cappex_deadline, u.profile_img_loc"));
		}

		// Plexuss Premium query
		if ($template_name == "premium_emails_light_dark_r1" || $template_name == "plexuss_premium_save_r2") {
			// $qry = $qry->where('a', 1);
			$qry = $qry->where(function($q){
						$q->orWhere('u.country_id', '!=', DB::raw(1))
						  ->orWhereNull('u.country_id');
					   })
					   ->addSelect('u.profile_img_loc');
		}

		// Owl Scholarship query
		if ($template_name == "owl_scholarship_email_r1" || $template_name == "plexuss_owl_scholarship_update_r2") {
			$qry = $qry->where(function($q){
						$q->orWhere('u.country_id', '=', DB::raw(1))
						  ->orWhereNull('u.country_id');
					   })
					   ->addSelect('u.profile_img_loc');
		}

		// Into UK query
		if ($template_name == "into_uk_email" || $template_name == "into_study_uk_2") {
			$qry = $qry->whereRaw("(u.in_college = 0 or u.in_college is null)")
                           ->whereRaw("(
                                        (
                                        u.financial_firstyr_affordibility in(
                                            '20,000 - 30,000' ,
                                            '30,000 - 50,000' ,
                                            '50,000'
                                        ) and 
                                        u.country_id in (Select id from countries where toggle_passthrough_financials = 1))
                                        or u.country_id in (Select id from countries where toggle_passthrough_financials = 0)
                                        or u.country_id is null
                                    )")
                           ->whereIn("u.country_id", array(5,19,34,38,58,61,63,100,101,102,104,109,116,118,120,123,131,134,147,149,164,171,176,187,192,209,213,218,219,225,233,238,9,12,14,26,28,32,41,44,48,52,54,60,62,64,69,75,85,87,89,93,95,136,140,146,154,157,167,169,170,175,202,204,221,228,232,234))
                           ->addSelect('u.profile_img_loc');
		}

		// OpenClassrooms
		if ($template_name == "openclassromms_email_r2") {
			$qry = $qry->where('u.country_id', 1)
					   ->where(DB::raw("year(birth_date)"), ">=", DB::raw("(year(current_date()) - 18)"));
		}

		// Update Profile
		if ($template_name == "plexuss_more_info_emails_r1") {
			
			$group_by_uid = false;
			$qry = $qry->where(function($q){
			                $q->orWhereNull('zip')
			                  ->orWhere('zip', '=', DB::raw("''"))
			                  ->orWhereNull('address')
			                  ->orWhere('address', '=', DB::raw("''"))
			                  ->orWhereNull('city')
			                  ->orWhere('city', '=', DB::raw("''"))
			                  ->orWhereNull('state')
			                  ->orWhere('state', '=', DB::raw("''"));
			            });	
		}

		// Study Group 
		if ($template_name == "studygroup_email_study_in_uk") {
			$qry = $qry->where('u.is_plexuss', 0)
						->where('u.is_organization', 0)
						->where('u.is_ldy', 0)
						->where('u.is_university_rep', 0)
						->where('u.is_aor', 0)
						->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
						->leftjoin('countries as c', 'u.country_id', '=', 'c.id')
						->where(function($q){
							$q->orWhere('c.tier', '=', 1)
							  ->orWhere('c.tier', '=', 2);
						})
						->where('u.country_id', '!=', 1)
						->whereRaw("(u.in_college = 0 or u.in_college is null) AND (
																(
																	u.financial_firstyr_affordibility in(
																		'20,000 - 30,000' ,
																		'30,000 - 50,000' ,
																		'50,000'
																	)
																)
															)");

		}

		if ($group_by_uid == true) {
			$qry = $qry->groupBy('u.id');
		}else{
			$qry = $qry->groupBy('u.email');
		}

		if ($cron_type == "first") {
			// $qry = $qry->whereNull('eel.user_id');
		}elseif ($cron_type == "second") {
			$qry = $qry->where('eel.created_at', '<', $seven_days_ago)
			           ->havingRaw('COUNT(eel.id) = 1');
		}

		// $qry = $qry->where('a', 1);
		$qry = $qry->get();
		
		$mac = new MandrillAutomationController();
		
		$sm = new SparkpostModel('test_template');
		$sent_email_cnt = 0;

		foreach ($qry as $key) {

			if (isset($contact_method) && $contact_method == "phone") {
				
				$params = array();
				
				$params['user_id'] = $key->id;
				$params['phone']   = $key->phone;

				$this->sendZetaTextCampaign($params);
			}else{
				$reply_email = 'social@plexuss.com';
				// $key->email = 'anthony.shayesteh@plexuss.com';

				$is_suppressed = $sm->isSupressed($key->email, $key->id);
				if (isset($is_suppressed) && $is_suppressed == true){
					continue;
				}

				$params = array();
				$params['FNAME']   		 = trim(ucwords(strtolower($key->fname)));
				isset($key->lname) 		  ? $params['LNAME']       = ucwords(strtolower($key->lname)) : $params['LNAME'] = '';
				isset($key->country_name) ? $params['COUNTRYNAME'] = $key->country_name : NULL;
				$params['USER_ID'] 		 = $key->id;
	            isset($key->id)           ? $params['HID'] = Crypt::encrypt($key->id) : NULL;
	            $params['NAME']			 = $params['FNAME'] . " " . $params['LNAME'];
	            $params['NAME']			 = trim($params['NAME']);

	            isset($key->profile_img_loc) ? $params['USER_IMAGE_URL'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$key->profile_img_loc : $params['USER_IMAGE_URL'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
	            isset($key->cappex_deadline) ? $params['CAPPEX_DEADLINE'] = $key->cappex_deadline : NULL;

	            if ($template_name == "usf_dynamic_program_email") {	

	            	$params['COLLEGELOGOURL'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/University_of_San_Francisco.png";
	            	$params['COLLEGENAME']    = "University of San Francisco";
	            	$params['COLLEGEADDRESS'] = "2130 Fulton St, San Francisco, CA 94117-1080, United States | (415)-422-5555";

	            	$arr = $this->getUSFProgramRecords($params);
	            	
	            	            	
	            	if ($arr['status'] == 'failed') {
	            		continue;
	            	}
	            	
	            	$params = $arr['params'];
	        	}
	        	$subject = NULL;
	        	isset($params['SUBJECT']) ? $subject = $params['SUBJECT'] : NULL;
				$mac->generalEmailSend($reply_email, $template_name, $params, $key->email, $subject);
			}
			

			$eel = new PartnerEmailLog;
			$eel->user_id = $key->id;
			$eel->template_id = $template_id;

			$eel->save();
			$sent_email_cnt++;
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailCronJob_'.$rand, 'done', 40);

		return "Successfully ran template_id: ". $rand. " num of emails sent: ". $sent_email_cnt;
	}

	private function getUSFProgramRecords($params){
		$arr = array();
		$arr['status'] = "success";
		$arr['params'] = $params;
		$obj = Objective::on('bk')->where('user_id', $params['USER_ID'])
									->selectRaw('major_id')
									->get();

		if (!isset($obj) || empty($obj)) {
			$arr['status'] = 'failed';

			return $arr;
		}

		$qry = DB::connection('bk')->table('revenue_programs as rp')
									 ->join('revenue_programs_selling_point as rpsp', 'rp.id', '=', 'rpsp.rp_id')
									 ->join('ad_redirect_campaigns as arc', 'rp.arc_id', '=', 'arc.id')
									 ->where('rp.college_id', 533)
									 ->where(function($q) use ($obj){
									 	foreach ($obj as $key) {
									 		$q->orWhere('rp.major_ids', 'LIKE', "%".$key->major_id."%");
									 	}
									 	 
									 })
									 ->select('rp.program_name', 'rp.program_type', 'rp.degree_type', 'rp.simple_major_name',
									 		  'rp.url', 'rpsp.selling_point', 'arc_id', 'rp.id as rp_id')
									 ->get();

		if (isset($qry) && !empty($qry)) {
			$utm_source = "program_based";
			$arc_id = NULL;
			$params['SELLING_POINT'] = array();
			foreach ($qry as $key) {
				!isset($arc_id) ? $arc_id = $key->arc_id : NULL;
				if ($arc_id != $key->arc_id) {
					break;
				}
				$params['LOGO_URL']             = env('CURRENT_URL').'adRedirect?company=usf&cid='.$key->arc_id.'&uid='.$params['USER_ID'].'&hid='.$params['HID']."&utm_source=email_program-".$key->rp_id.'_img_logo';
				$params['CTA_URL']             = env('CURRENT_URL').'adRedirect?company=usf&cid='.$key->arc_id.'&uid='.$params['USER_ID'].'&hid='.$params['HID']."&utm_source=email_program-".$key->rp_id.'_cta_interested';
				$params['PROGRAM_URL']             = env('CURRENT_URL').'adRedirect?company=usf&cid='.$key->arc_id.'&uid='.$params['USER_ID'].'&hid='.$params['HID']."&utm_source=email_program-".$key->rp_id.'_txt_program-name';

				$params['SELLING_POINT'][] = $key->selling_point;
				$params['PROGRAM_NAME']    = $key->program_name;
				$params['SUBJECT']		   = "Is University of San Francisco's ".$key->program_name." the right program for you?";
			}
		}

		$arr['params'] = $params;

		return $arr;
	}

	// Send partner emails using user invite users that have clicked or converted.
	public function partnerEmailForUsersInviteCronJob($rand, $cron_type){

		$time_now = Carbon::now()->toTimeString();
				
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailForUsersInviteCronJob_'.$rand)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailForUsersInviteCronJob_'.$rand);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		$now 			   = Carbon::today();
		
		$first_day_of_week = $now->subDays(7);
		$tomorrow 		   = Carbon::tomorrow(); 

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailForUsersInviteCronJob_'.$rand, 'in_progress', 20);

		$cnt = DB::connection('bk')->table('partner_email_logs as pel')
									 ->leftjoin('partner_email_templates as pet', 'pel.template_id', '=', 'pet.id')
									 ->select('pel.template_id', DB::raw('COUNT(pel.id) as cnt'), 'pet.max_num_of_sent_email_daily',
											  'pet.template_name')
									 ->where('pel.created_at', '>=', Carbon::today())
									 ->where('pel.created_at', '<', Carbon::tomorrow())
									 ->groupBy('pel.template_id')
									 ->get();

		$template_suppresion_ids = array();
		if (isset($cnt) && !empty($cnt)) {
			foreach ($cnt as $key) {

				if ($key->cnt >= $key->max_num_of_sent_email_daily && isset($key->template_id)) {
					$template_suppresion_ids[] = $key->template_id;
				}
			}
		}

		$pel = PartnerEmailTemplate::on('bk')->orderBy(DB::raw('RAND()'))
											   ->where('id', $rand);
	
		if (!empty($template_suppresion_ids)) {
			$pel = $pel->whereNotIn('id', $template_suppresion_ids);
		}

		$pel = $pel->first();
		
		if (!isset($pel) || empty($pel)) {
			return "all partner emails has reached max";
		}
		
		// $pel->template_name = 'topuniversities_grad_school_ver_b';
		// $pel->template_name = 'topuniversities_mba_ver_b';

		$company 	   = $pel->company;
		$template_name = $pel->template_name;
		$template_id   = $pel->id;
		$take          = $pel->take;

		$qry = DB::connection('bk')->table('users_invites as u')
								   ->join('ad_clicks as ac', 'u.id', 'ac.user_invite_id')
									 ->leftjoin('partner_email_logs as eel', function($q) use ($template_id){
									 			$q->on('u.id', '=', 'eel.user_invite_id')
									 			  ->on('eel.template_id', '=', DB::raw($template_id));
									 })
									 ->leftjoin('email_suppression_lists as esl', 'esl.uiid', '=', 'u.id')

								     ->whereNull('esl.id')
									 
									 ->where('u.invite_email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
									 ->where(DB::raw("datediff(current_date() , ac.created_at)"), '>', 14)
									 
									 ->whereRaw("u.id not in(
																select distinct
																	user_invite_id
																from
																	ad_clicks
																where
																	company = '".$company."'
																and pixel_tracked = 1
																AND user_invite_id is not NULL
															)")
									 ->whereRaw("u.id not in(
																select distinct
																	user_invite_id
																from
																	partner_email_logs
																where
																	created_at >= '".$first_day_of_week."'
																and created_at < '".$tomorrow."'
																AND user_invite_id is not NULL
															)")
									 ->groupBy('u.id')
									 ->orderBy(DB::raw("rand()"))
									 ->take($take)
									 ->select('u.id', 'u.invite_name as fname', 'u.invite_email as email', 'ac.countryName');

		// Place filter for qs companies
		if ($template_name == 'users_invite_topuniversities_mba_ver_b') {
			$qry = $qry->where(function($q){
							$q->orWhere('ac.countryName', '=', DB::raw("'United States'"))
							  ->orWhere('ac.countryName', '=', DB::raw("'Canada'"))
							  ->orWhere('ac.countryName', '=', DB::raw("'Mexico'"));
					})->where('ac.pixel_tracked', 1);
		}

		// Conditions for Springboard
		if ($template_name == 'users_invite_springboard_first') {
			$qry = $qry->where('ac.countryName', 'United States')
					   ->where('ac.pixel_tracked', 1);
		}

		// Music Institute query
		if ($template_name == "users_invite_music_institute_v1") {
			$qry = $qry->where(function($q){
							$q->orWhere('ac.countryName', '=', DB::raw("'Brazil'"))
							  ->orWhere('ac.countryName', '=', DB::raw("'India'"))
							  ->orWhere('ac.countryName', '=', DB::raw("'Republic of Korea'"));
					});
		}

		// Eddy CALU and GCU query
		if ($template_name == 'users_invite_gcu_eddy_click' || $template_name == 'users_invite_calu_eddy_click') {
			$qry = $qry->where('ac.countryName', 'United States');
		}

		if ($cron_type == "first") {
			$qry = $qry->whereNull('eel.user_id');
		}elseif ($cron_type == "second") {
			$qry = $qry->havingRaw('COUNT(eel.id) = 1');
		}

		$qry = $qry->get();

		$mac = new MandrillAutomationController();
		
		$sm = new SparkpostModel('test_template');
		$sent_email_cnt = 0;

		foreach ($qry as $key) {
			$reply_email = 'social@plexuss.com';
			// $key->email = 'anthony.shayesteh@plexuss.com';

			$is_suppressed = $sm->isSupressed($key->email, NULL, $key->id);
			if (isset($is_suppressed) && $is_suppressed == true){
				continue;
			}

			$params = array();

			$params['FNAME']   		 = isset($key->fname) ? ucwords(strtolower($key->fname)) : 'there';
			$params['LNAME']         = ''; 
			isset($key->countryName) ? $params['COUNTRYNAME'] = $key->countryName : NULL;
			$params['USER_ID'] 		 = $key->id;

			$mac->generalEmailSend($reply_email, $template_name, $params, $key->email);

			$eel = new PartnerEmailLog;
			$eel->user_invite_id = $key->id;
			$eel->template_id 	 = $template_id;

			$eel->save();
			$sent_email_cnt++;
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_partnerEmailForUsersInviteCronJob_'.$rand, 'done', 20);

		return "Successfully ran template_id: ". $rand. " num of emails sent: ". $sent_email_cnt;
	}

	public function edxEmailCronJob($rand){

		$time_now = Carbon::now()->toTimeString();
				
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'in_progress', 60);

		$cnt = EdxEmailLog::on('bk')
							//->where('template_id', $template_id)
							->where('created_at', '>=', Carbon::today())
							->where('created_at', '<',  Carbon::tomorrow())
							->where('template_id', $rand)
							->count();

		$et = EdxTemplate::find($rand);

		$today 	  = Carbon::now();
		$tomorrow = Carbon::tomorrow();

		$fourteen_days_ago = Carbon::today()->subDays(14);
		$sm = new SparkpostModel('test_template');
		$sent_email_cnt = 0;
		switch ($rand) {
			case 3:
				if ($cnt >= $et->max_num_of_sent_email_daily) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'done', 60);
					return "edX template_id: ".$rand." has reached max";
				}
				if ($today->dayOfWeek !== Carbon::MONDAY && $today->dayOfWeek !== Carbon::TUESDAY && $today->dayOfWeek !== Carbon::WEDNESDAY &&
					$today->dayOfWeek !== Carbon::THURSDAY ) {

					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'done', 60);
					return "Can not run template_id: ". $rand." on ". $today->dayOfWeek;
				}

				Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'in_progress', 60);

				$qry = DB::connection('bk')->table('users as u')
											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)										 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')
											 ->where('u.is_ldy', 0)
											 ->where(function($q){
											 		$q->whereNull('u.country_id')
											 		  ->orWhereNotIn('u.country_id', array(1, 2, 15, 156, 226));
											 })

											 ->whereRaw("not exists 
															(Select id from email_suppression_lists esl where esl.uid = u.id)")
											 ->where('u.id', '!=', 1024184)
											 ->whereRaw("(exists 
														(Select user_id from scores s where (ielts_total is null and toefl_total is null and toefl_ibt_total is null) and u.id = s.user_id)
													or not exists 
														(Select user_id from scores s2 where s2.user_id = u.id)
												)")
											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
											 ->whereRaw("not exists (
																select user_id
																from
																	ad_clicks ac
																where company = 'edx'
																and pixel_tracked = 1
																and u.id = ac.user_id
															)")
											 ->whereRaw("not exists (
																select user_id
																from edx_email_logs eel
																where eel.user_id = u.id and template_id = ".$rand."
															)")
											 ->where("u.id", "<", DB::raw("(select min(id) from users where date(created_at) = date_sub(current_date, interval 14 day))"))
											 ->orderBy(DB::raw("rand()"))
											 ->take(20)
											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();



				$mac = new MandrillAutomationController();
				
				foreach ($qry as $key) {

					$is_suppressed = $sm->isSupressed($key->email, $key->id);
					if (isset($is_suppressed) && $is_suppressed == true){
						continue;
					}

					$mac->edXEmail($rand, ucwords(strtolower($key->fname)), $key->email, $key->id);

					$eel = new EdxEmailLog;
					$eel->user_id = $key->id;
					$eel->template_id = $rand;

					$eel->save();
					$sent_email_cnt++;
				}
				break;
			case 2:
				if ($cnt >= $et->max_num_of_sent_email_daily) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'done', 60);
					return "edX template_id: ".$rand." has reached max";
				}
				// if ($today->dayOfWeek !== Carbon::FRIDAY && $today->dayOfWeek !== Carbon::SATURDAY && $today->dayOfWeek !== Carbon::SUNDAY ) {
				// 	return "Can not run template_id: ". $rand." on ". $today->dayOfWeek;
				// }
				$take = 50;
				if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
					$take *= 3;
				}

				Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'in_progress', 60);

				$qry = DB::connection('bk')->table('users as u')
											 ->whereRaw("not exists
														(Select id from email_suppression_lists esl where esl.uid = u.id)")
											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)
											 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')

											 ->where('u.is_ldy', 0)
											 ->where('u.id', '!=', 1024184)
											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
											 ->where('u.id', '<', DB::raw("(select min(id) from users where date(created_at) = date_sub(current_date, interval 14 day))"))
											 ->whereRaw("not exists (
															select user_id
															from
																ad_clicks ac
															where company = 'edx'
															and pixel_tracked = 1
															and u.id = ac.user_id
														)")
											 ->whereRaw("not exists (
															select user_id
															from edx_email_logs eel
															where eel.user_id = u.id and template_id = ".$rand."
														)")

											 ->orderBy(DB::raw("rand()"))
											 ->take($take)

											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();

				$mac = new MandrillAutomationController();
				
				foreach ($qry as $key) {
					// $rand = 5;
					// $key->email = "anthony.shayesteh@plexuss.com";

					$is_suppressed = $sm->isSupressed($key->email, $key->id);
					if (isset($is_suppressed) && $is_suppressed == true){
						continue;
					}

					$mac->edXEmail($rand, ucwords(strtolower($key->fname)), $key->email, $key->id);

					$eel = new EdxEmailLog;
					$eel->user_id = $key->id;
					$eel->template_id = $rand;

					$eel->save();
					$sent_email_cnt++;
				}
				break;
			case 1:
				if ($cnt >= $et->max_num_of_sent_email_daily) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'done', 60);
					return "edX template_id: ".$rand." has reached max";
				}
				if ($today->dayOfWeek !== Carbon::MONDAY && $today->dayOfWeek !== Carbon::TUESDAY && $today->dayOfWeek !== Carbon::WEDNESDAY &&
				 	$today->dayOfWeek !== Carbon::THURSDAY ) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'done', 60);
					return "Can not run template_id: ". $rand." on ". $today->dayOfWeek;
				}

				Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'in_progress', 60);

				$qry = DB::connection('bk')->table('users as u')
											 ->leftjoin('edx_email_logs as eel', function($q) use ($rand){
											 			$q->on('u.id', '=', 'eel.user_id')
											 			  ->on('eel.template_id', '=', DB::raw($rand));
											 })
											 ->whereRaw("not exists
														(Select id from email_suppression_lists esl where esl.uiid = u.id)")
											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)
											 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')
											 ->where('u.is_ldy', 0)

											 ->where('u.id', '!=', 1024184)	
											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
											 ->where(DB::raw("datediff(current_date() , birth_date) / 365"), '<', 18)
											 
											 ->where('u.country_id', 1)
											 ->where('u.id', '<', DB::raw("(select min(id) from users where date(created_at) = date_sub(current_date, interval 14 day))"))
											 ->whereRaw("not exists (
															select user_id
															from edx_email_logs eel
															where eel.user_id = u.id and template_id = ".$rand."
														)")
											 ->whereRaw("not exists (
															select user_id
															from
																ad_clicks ac
															where company = 'edx'
															and pixel_tracked = 1
															and u.id = ac.user_id
														)")
											 ->orderBy(DB::raw("rand()"))
											 ->take(10)
											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();

				$mac = new MandrillAutomationController();
				
				foreach ($qry as $key) {

					$is_suppressed = $sm->isSupressed($key->email, $key->id);
					if (isset($is_suppressed) && $is_suppressed == true){
						continue;
					}

					$mac->edXEmail($rand, ucwords(strtolower($key->fname)), $key->email, $key->id);

					$eel = new EdxEmailLog;
					$eel->user_id = $key->id;
					$eel->template_id = $rand;

					$eel->save();
					$sent_email_cnt++;
				}
				break;
			default:
				# code...
				break;
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob'.$rand, 'done', 60);

		return "Successfully ran template_id: ". $rand. " num of emails sent: ". $sent_email_cnt;
	}

	////// Repeat edxEmailCronJob
	public function edxEmailCronJobRepeat($rand, $repeat_cnt){

		$time_now = Carbon::now()->toTimeString();
				
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'in_progress', 60);

		$cnt = EdxEmailLog::on('bk')
							//->where('template_id', $template_id)
							->where('created_at', '>=', Carbon::today())
							->where('created_at', '<',  Carbon::tomorrow())
							->where('template_id', $rand)
							->count();

		$et = EdxTemplate::find($rand);

		$today    = Carbon::now();
		$tomorrow = Carbon::tomorrow();

		$sm = new SparkpostModel('test_template');
		$fourteen_days_ago = Carbon::today()->subDays(14);
		$seven_days_ago = Carbon::today()->subDays(7);

		$sent_email_cnt = 0;
		switch ($rand) {
			case 3:
				if ($cnt >= $et->max_num_of_sent_email_daily) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'done', 60);
					return "Repeat edX template_id: ".$rand." has reached max";
				}
				if ($today->dayOfWeek !== Carbon::MONDAY && $today->dayOfWeek !== Carbon::TUESDAY && $today->dayOfWeek !== Carbon::WEDNESDAY &&
					$today->dayOfWeek !== Carbon::THURSDAY ) {

					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'done', 60);
					return "Can not run template_id: ". $rand." on ". $today->dayOfWeek;
				}

				Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'in_progress', 60);

				$qry = DB::connection('bk')->table('users as u')
											 ->join('scores as s', 'u.id', '=', 's.user_id')
											 ->leftjoin('edx_email_logs as eel', function($q) use ($rand){
											 			$q->on('u.id', '=', 'eel.user_id')
											 			  ->on('eel.template_id', '=', DB::raw($rand));
											 })
											 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

								             ->whereNull('esl.id')
											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)
											 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')
											 ->where('u.is_ldy', 0)

											 ->where(function($q){
											 		$q->whereNull('u.country_id')
											 		  ->orWhereNotIn('u.country_id', array(1, 2, 15, 156, 226));
											 })

											 ->where('u.id', '!=', 1024184)
											 ->where(function($q){
											 		$q->where(function($innerQ){
											 			$innerQ->whereNull('s.ielts_total')
											 				   ->whereNull('s.toefl_total')
											 				   ->whereNull('s.toefl_ibt_total');
											 		})->orWhereNull('s.id');

											 })

											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
											 ->where('u.created_at', '<', $fourteen_days_ago)
											 ->where('eel.created_at', '<', $seven_days_ago)
											 ->whereRaw("u.id not in(
																		select distinct
																			user_id
																		from
																			ad_clicks
																		where
																			company = 'edx'
																		and pixel_tracked = 1
																		AND user_id is not NULL
																	)")

											 ->whereRaw("u.id not in(
																		select distinct
																			user_id
																		from
																			edx_email_logs
																		where
																		    user_id is not NULL
																		AND template_id >= '".$rand."'
																		AND created_at >= '".$today."'
																		AND created_at <= '".$tomorrow."'
																	)")

											 ->havingRaw('COUNT(eel.id) = '. $repeat_cnt)
											 ->groupBy('u.id')
											 ->orderBy(DB::raw("rand()"))
											 ->take(20)

											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();

				$mac = new MandrillAutomationController();
				
				foreach ($qry as $key) {

					$is_suppressed = $sm->isSupressed($key->email, $key->id);
					if (isset($is_suppressed) && $is_suppressed == true){
						continue;
					}
					
					$mac->edXEmail($rand, ucwords(strtolower($key->fname)), $key->email, $key->id);

					$eel = new EdxEmailLog;
					$eel->user_id = $key->id;
					$eel->template_id = $rand;

					$eel->save();
					$sent_email_cnt++;
				}
				break;
			case 2:
				if ($cnt >= $et->max_num_of_sent_email_daily) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'done', 60);
					return "Repeat edX template_id: ".$rand." has reached max";
				}
				// if ($today->dayOfWeek !== Carbon::FRIDAY && $today->dayOfWeek !== Carbon::SATURDAY && $today->dayOfWeek !== Carbon::SUNDAY ) {
				// 	return "Can not run template_id: ". $rand." on ". $today->dayOfWeek;
				// }
				$take = 100;
				if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
					$take *= 3;
				}

				Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'in_progress', 60);

				$qry = DB::connection('bk')->table('users as u')
											 ->leftjoin('edx_email_logs as eel', function($q) use ($rand){
											 			$q->on('u.id', '=', 'eel.user_id')
											 			  ->on('eel.template_id', '=', DB::raw($rand));
											 })
											 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

								             ->whereNull('esl.id')
											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)
											 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')
											 ->where('u.is_ldy', 0)

											 ->where('u.id', '!=', 1024184)
											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
											 ->where('u.created_at', '<', $fourteen_days_ago)
											 ->whereRaw("u.id not in(
																		select distinct
																			user_id
																		from
																			ad_clicks
																		where
																			company = 'edx'
																		and pixel_tracked = 1
																		AND user_id is not NULL
																	)")

											 // ->whereNull('eel.user_id')
											 ->where('eel.created_at', '<', $seven_days_ago)
											 ->groupBy('u.id')
											 ->orderBy(DB::raw("rand()"))
											 ->havingRaw('COUNT(eel.id) = '. $repeat_cnt)
											 ->take($take)

											 ->whereRaw("u.id not in(
																		select distinct
																			user_id
																		from
																			edx_email_logs
																		where
																		    user_id is not NULL
																		AND template_id >= '".$rand."'
																		AND created_at >= '".$today."'
																		AND created_at <= '".$tomorrow."'
																	)")

											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();

				$mac = new MandrillAutomationController();
				
				foreach ($qry as $key) {
					// $rand = 5;
					// $key->email = "anthony.shayesteh@plexuss.com";

					$is_suppressed = $sm->isSupressed($key->email, $key->id);
					if (isset($is_suppressed) && $is_suppressed == true){
						continue;
					}

					$mac->edXEmail($rand, ucwords(strtolower($key->fname)), $key->email, $key->id);
					// dd(39012310931);
					$eel = new EdxEmailLog;
					$eel->user_id = $key->id;
					$eel->template_id = $rand;

					$eel->save();
					$sent_email_cnt++;
				}
				break;
			case 1:
				if ($cnt >= $et->max_num_of_sent_email_daily) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'done', 60);
					return "Repeat edX template_id: ".$rand." has reached max";
				}
				if ($today->dayOfWeek !== Carbon::MONDAY && $today->dayOfWeek !== Carbon::TUESDAY && $today->dayOfWeek !== Carbon::WEDNESDAY &&
				 	$today->dayOfWeek !== Carbon::THURSDAY ) {
					Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'done', 60);
					return "Can not run template_id: ". $rand." on ". $today->dayOfWeek;
				}

				Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'in_progress', 60);

				$qry = DB::connection('bk')->table('users as u')
											 ->leftjoin('edx_email_logs as eel', function($q) use ($rand){
											 			$q->on('u.id', '=', 'eel.user_id')
											 			  ->on('eel.template_id', '=', DB::raw($rand));
											 })
											 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

								             ->whereNull('esl.id')
											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)
											 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')
											 ->where('u.is_ldy', 0)

											 ->where('u.id', '!=', 1024184)	
											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
											 ->where(DB::raw("datediff(current_date() , birth_date) / 365"), '<', 18)
											 
											 ->where('u.country_id', 1)
											 ->where('u.created_at', '<', $fourteen_days_ago)
											 ->where('eel.created_at', '<', $seven_days_ago)
											 ->havingRaw('COUNT(eel.id) = '. $repeat_cnt)
											 ->groupBy('u.id')
											 ->orderBy(DB::raw("rand()"))
											 ->take(10)

											 ->whereRaw("u.id not in(
																		select distinct
																			user_id
																		from
																			edx_email_logs
																		where
																		    user_id is not NULL
																		AND template_id >= '".$rand."'
																		AND created_at >= '".$today."'
																		AND created_at <= '".$tomorrow."'
																	)")

											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();


				$mac = new MandrillAutomationController();
				
				foreach ($qry as $key) {

					$is_suppressed = $sm->isSupressed($key->email, $key->id);
					if (isset($is_suppressed) && $is_suppressed == true){
						continue;
					}

					$mac->edXEmail($rand, ucwords(strtolower($key->fname)), $key->email, $key->id);

					$eel = new EdxEmailLog;
					$eel->user_id = $key->id;
					$eel->template_id = $rand;

					$eel->save();
					$sent_email_cnt++;
				}
				break;
			default:
				# code...
				break;
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_edxEmailCronJob_Repeat_'.$rand, 'done', 60);

		return "Successfully ran template_id: ". $rand. " num of emails sent: ". $sent_email_cnt;
	}
	////// Repeat edxEmailCronJob

	public function edxUserInviteListCronJon($cron_type = NULL){

		$cron_name = 'is_cron_edxUserInviteListCronJon';

		if (isset($cron_type)) {
			$cron_name  .= $cron_type;
		}

		if (Cache::has( env('ENVIRONMENT') .'_'.$cron_name )) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. $cron_name);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. $cron_name, 'in_progress', 20);

		// $cnt = EdxEmailLog::on('bk')->where('created_at', '>=', Carbon::today())
		// 						   	->where('created_at', '<', Carbon::tomorrow())
		// 						   	->whereNull('template_id')
		// 						   	->whereNotNull('user_invite_id')
		// 						    ->count();

		// if ($cnt >= 250000) {
		// 	return "Max number of emails have reached!";
		// }	
    	
    	$time_now = Carbon::now()->toTimeString();
    	$take = 900;
		if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
			$take *= 2;
		}

		$today 	  = Carbon::today();
		$tomorrow = Carbon::tomorrow();

		$qry = DB::connection('bk')->table('users_invites as u')
								   ->join('users as user', 'u.user_id', '=', 'user.id')
								   ->join('countries as c', 'c.id', '=', 'user.country_id')

								   ->where('c.is_gdpr', 0)
								   ->where('u.invite_email', 'NOT LIKE', '%els.edu%')
								   ->where('u.invite_email', 'NOT LIKE', '%shorelight%')
								   ->whereRaw(" not exists (Select id from email_suppression_lists esl where esl.uiid = u.id)")
								   ->where('u.is_dup', 0)
								   ->where('u.invite_email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
								   ->whereRaw(" not exists (
														    select user_invite_id
														    from ad_clicks as ac 
														    where company = 'edx'
																AND pixel_tracked = 1
														    AND ac.user_invite_id = u.id
														)")
								   ->whereRaw(" not exists (
														select user_invite_id
														from edx_email_logs as eel3
														where eel3.id > 
															(select min(id)
															from edx_email_logs 
															where date(created_at) = date_sub(current_date(), interval 14 day)) #obtain earliest id of earliest record within past 14 days
														and eel3.user_invite_id = u.id
												)")
    							   ->orderBy(DB::raw("rand()"))
    							   
    							   ->select('u.invite_name as name', 'u.invite_email as email', 'u.id as user_invite_id')
								   ->take($take);

		$qry = $qry->get();
	
		$mac = new MandrillAutomationController();
		$template_id = 4;
		$sm = new SparkpostModel('test_template');

		$cnt = 0;
		foreach ($qry as $key) {

			if (isset($key->name) && !empty($key->name)) {
				$name = ucwords(strtolower($key->name));
			}else{
				$name = "there";
			}
			// $key->email = "anthony.shayesteh@plexuss.com";

			$is_suppressed = $sm->isSupressed($key->email, NULL, $key->user_invite_id);
			if (isset($is_suppressed) && $is_suppressed == true){
				continue;
			}
			
			$mac->edXEmail($template_id, $name, $key->email, -1, $key->user_invite_id);
	
			$eel = new EdxEmailLog;
			$eel->user_invite_id = $key->user_invite_id;

			$eel->save();
			$cnt++;
		}

    	Cache::put( env('ENVIRONMENT') .'_'. $cron_name, 'done', 20);

    	return $cnt." number of emails sent";
	}

	public function usersOneappScholarshipEmail(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'usersOneappScholarshipEmail')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'usersOneappScholarshipEmail');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	$template_id = 4;

    	$cnt = EdxEmailLog::on('bk')
							//->where('template_id', $template_id)
							->where('created_at', '>=', Carbon::today())
							->where('created_at', '<',  Carbon::tomorrow())
							->where('template_id', $template_id)
							->count();

		$et = EdxTemplate::find($template_id);

		if ($cnt >= $et->max_num_of_sent_email_daily) {
			return "usersOneappScholarshipEmail: ".$template_id." has reached max";
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'usersOneappScholarshipEmail', 'in_progress', 60);

		$qry = DB::connection('bk')->table('users as u')
											 ->leftjoin('edx_email_logs as eel', function($q) use ($template_id){
											 			$q->on('u.id', '=', 'eel.user_id')
											 			  ->on('eel.template_id', '=', DB::raw($template_id));
											 })

											 ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')
								             ->whereNull('esl.id')

											 ->whereIn('u.financial_firstyr_affordibility', array('20,000 - 30,000', '30,000 - 50,000', '50,000'))

											 ->whereRaw("u.id not in(
																	select
																		user_id
																	from
																		users_submitted_app)")
											 ->whereRaw("u.id not in(
																	select
																		user_id
																	from
																		users_custom_questions
																	where
																		application_state = 'submit'
																)")

											 ->where('u.country_id', '!=', 1)
											 ->where('u.is_ldy', 0)
											 ->where(DB::raw("datediff(current_date() , u.created_at)"), '>', 28)
											 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')

											 ->where('u.is_organization', 0)
											 ->where('u.is_university_rep', 0)
											 ->where('u.is_ldy', 0)
											 
											 ->where('u.is_counselor', 0)
											 ->where('u.is_aor', 0)
											 ->where('u.email', 'NOT LIKE', '%els.edu%')
											 ->where('u.email', 'NOT LIKE', '%shorelight%')

											 ->whereNull('eel.user_id')
											 ->groupBy('u.id')

											 ->take(1)

											 ->select('u.id', 'u.fname', 'u.email')
											 ->get();

		$mac = new MandrillAutomationController();

		$sm = new SparkpostModel('test_template');
				
		foreach ($qry as $key) {

			$is_suppressed = $sm->isSupressed($key->email, $key->id);
			if (isset($is_suppressed) && $is_suppressed == true){
				continue;
			}

			$attachments = array();
			$tmp = array();
			$tmp['type'] = $et->type;
			$tmp['name'] = $et->name;
			$tmp['data'] = $et->data;

			$attachments[] = $tmp;

			$mac->usersOneappScholarshipEmail(ucwords(strtolower($key->fname)), $key->email, $attachments);

			$eel = new EdxEmailLog;
			$eel->user_id = $key->id;
			$eel->template_id = $template_id;

			$eel->save();
		}


    	Cache::put( env('ENVIRONMENT') .'_'. 'usersOneappScholarshipEmail', 'done', 60);
	}

	public function emailInternalCollegesSecondaryEmail(){

		$num_of_emails_sent = 0;

		$qry = InternalCollegeContactTemplate::on('bk')->where('nature', 'second')
														 ->where('active', 1)
														 ->get();
		if (isset($qry)) {
			foreach ($qry as $key) {
				$iccpe = InternalCollegeContactPlexussEmail::on('bk')->where('id', $key->sender_email_id)->first();

				$num_of_emails_sent += $this->emailInternalColleges($key->id, $iccpe->email, $key->nature);
			}

			return $num_of_emails_sent . " emails have been sent";
		}
	}

	public function emailInternalCollegesThirdEmail(){

		$num_of_emails_sent = 0;

		$qry = InternalCollegeContactTemplate::on('bk')->where('nature', 'third')
														 ->where('active', 1)
														 ->get();
		if (isset($qry)) {													 
			foreach ($qry as $key) {
				$iccpe = InternalCollegeContactPlexussEmail::on('bk')->where('id', $key->sender_email_id)->first();

				$num_of_emails_sent += $this->emailInternalColleges($key->id, $iccpe->email, $key->nature);
			}

			return $num_of_emails_sent . " emails have been sent";
		}
	}

	public function customForSchoolsEnrollmentDepartmentCron(){

		$template_id = 45;
		$sender_email = "james.lee@plexuss.com";
		$nature = 'customForSchoolsEnrollmentDepartment';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function emailInternalCollegesSEEDFunding(){
		$template_id = 47;
		$sender_email = "jacob@plexuss.com";
		$nature = 'first';

		return $this->emailInternalColleges($template_id, $sender_email, $nature);
	}

	public function uploadMissingFBImages(){

		$user = User::whereNotNull('fb_id')
					->where(function($q){
							$q->whereNull('profile_img_loc')
							  ->orWhere('profile_img_loc', '=', DB::raw("''"));
					})
					->take(100)
					->orderBy('id', 'DESC')
					->get();

		foreach ($user as $key) {
			$url = 'https://graph.facebook.com/'.$key->fb_id.'/picture?height=500&width=500&redirect=0';
		    $client = new Client(['base_uri' => 'http://httpbin.org']);
		    
		    try {
		        
		        $response = $client->request('GET', $url);
		        $response = json_decode($response->getBody()->getContents(), true);

		        $url = $response['data']['url'];

		        $temp_url = $this->getStringBetween($url, "/", "?");
		        $id = $key->id * 76312;
		        $file_name = $id. '_'. substr(strrchr($temp_url, "/"), 1);
		        $file_name = str_replace(" ", "_", $file_name);

		        $img_data = file_get_contents($url);
		        $base64 = base64_encode($img_data);

		        $input = array();
		        $input['file_name'] = $file_name;
		        $input['image']     = $base64;
		        $input['user_id']   = Crypt::encrypt($key->id);
		        $input['bucket_url']= 'asset.plexuss.com/users/images';

		        $public_path = $this->uploadBase64Img($input);

		        $key->profile_img_loc = $public_path;
		        $key->save();

		    } catch (\Exception $e) {
		        $key->profile_img_loc = 'default.png';
		        $key->save();
		    }
		}	
	}

	public function iOSappInvite(){

		$cnt = UsersIdsForEmailsLog::on('bk')->where('created_at', '>=', Carbon::today())
											   ->where('created_at', '<', Carbon::tomorrow())
											   ->count();

		if ($cnt >= 20000) {
			return "Max number of emails have reached!";
		}

		$qry = DB::connection('bk')->table('users_ids_for_emails as uife')
								   ->join(DB::raw("(Select id , email from users) as u"), 'uife.user_id', '=', 'u.id')
								   ->leftjoin('users_ids_for_emails_logs as uifel', 'uife.id', '=', 'uifel.uife_id')
								   ->leftjoin(DB::raw("(
														select
															user_id
														from
															users_ids_for_emails uife
														join users_ids_for_emails_logs uifel on uife.id = uifel.uife_id
														where
															date(uifel.created_at) > date_sub(current_date() , interval 20 day)
													) as emailed_recently"), 'emailed_recently.user_id', '=', 'uife.user_id')
								   ->whereNull('emailed_recently.user_id')
								   ->whereNull('uifel.uife_id')
								   ->where('email', '!=', 'none')
								   ->orderBy(DB::raw("RAND()"))
								   ->take(50)
								   ->select('uife.id as uife_id', 'email', 'u.id as user_id')
								   ->get();


		$template_name = 'users_plexuss_mobile_app_annoucement';
		$reply_email = 'social@plexuss.com';
		$params = array();

		$mac = new MandrillAutomationController();

		foreach ($qry as $key) {
			
			// $key->email = "anthony.shayesteh@plexuss.com";

			$mac->generalEmailSend($reply_email, $template_name, null, $key->email);

			$uifel = new UsersIdsForEmailsLog;

			$uifel->uife_id = $key->uife_id;
			$uifel->save();
			
		}

		return "success";
	}

	public function agentOneADay(){
		$template_id = 48;
		$reply_email = "nancy@plexuss.com";
		$template_name = "agencies_one_lead_a_day_non_registered";

		$today = Carbon::now();
		$take  = 10;
		if ($today->dayOfWeek == Carbon::SATURDAY && $today->dayOfWeek == Carbon::SUNDAY) {
			return "This cron doesnt run in weekends";
		}

		$qry =  DB::connection('bk')->table('internal_college_contact_info as icci')
    								  ->leftjoin('internal_college_contact_info_logs as iccil', function($q) use ($template_id){
    								  			$q->on('icci.id', '=', 'iccil.icci_id')
    								  			  ->where('iccil.template_id', '=', $template_id);
    								  })
    								  ->where('icci.type', 'agency')
    								  ->whereNull('iccil.icci_id')
    								  ->where('icci.unsub', 0)
    								  ->take($take)
    								  ->select('fname', 'email', 'icci.id', 'country_id')
    								  ->get();

    	$sm = new SparkpostModel('test_template');

	    foreach ($qry as $key) {
	    	$is_suppressed = $sm->isSupressed($key->email);

    		$is_organization = User::on('bk')->where(function($q){
    										   		$q->orWhere('is_organization', '=', DB::raw("1"))
    										   		  ->orWhere('is_agency', '=', DB::raw("1"));
    										   })
    										   ->where('email', $key->email)
    										   ->first();

    		if ((isset($is_suppressed) && $is_suppressed == true) || isset($is_organization)) {

    			$icci_temp = InternalCollegeContactInfo::find($key->id);

    			$icci_temp->unsub = 1;
    			$icci_temp->save();

    			$log = new InternalCollegeContactInfoLog;
	    		
	    		$log->sent_email_id = $send_email_qry->id;	
	    		$log->icci_id = $key->id;
	    		$log->template_id = $template_id;
	    		$log->save();

    			continue;
    		}
    		

    		$tmp = DB::connection('bk')->table('users as u')
    									 ->join('users_custom_questions as ucq', 'u.id', '=', 'ucq.user_id')
    									 ->join('countries as c', 'u.country_id', '=', 'c.id')
    									 ->join('objectives as o', 'o.user_id', '=', 'u.id')
    									 ->join('majors as m', 'm.id', '=', 'o.major_id')

    									 ->whereIn('ucq.application_state', array('sponsor', 'declaration', 'uploads', 'additional_info',
    																			  'essay', 'courses', 'clubs', 'family', 'colleges'))
    									 ->whereNotNull('u.profile_img_loc')
    									 ->where('u.profile_img_loc', '!=', 'default.png')
    									 ->where('u.country_id', $key->country_id)
    								     ->orderBy(DB::raw("RAND()"))

    								     ->select('u.fname', 'u.lname', 'c.country_name', 'u.profile_img_loc', 'u.financial_firstyr_affordibility',
    								     		  DB::raw("IF(u.in_college = 0, u.hs_grad_year, u.college_grad_year) as grad_year"), 
    								     		  DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(m.name SEPARATOR ', '), ',', 4) as `major_name`")
    								     		  )
    								     ->first();

    		if (isset($tmp)) {
    			$params = array();

    			$params['PROFILEPIC'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$tmp->profile_img_loc;
				$params['FNAME'] = ucwords(strtolower(substr($tmp->fname, 0, 2). "*****"));
				$params['LNAME'] = ucwords(strtolower(substr($tmp->lname, 0, 2). "*****"));
				$params['GRADYEAR'] = $tmp->grad_year;

				$params['COUNTRY'] = $tmp->country_name;
				$params['FINANCIAL'] = $tmp->financial_firstyr_affordibility;
				$params['MAJORS'] = $tmp->major_name;

				// $key->email = 'anthony.shayesteh@plexuss.com';
				
    			$mda = new MandrillAutomationController();
	    		$mda->generalEmailSend($reply_email, $template_name, $params, $key->email);

	    		$log = new InternalCollegeContactInfoLog;
			    		
	    		$log->sent_email_id = 17;	
	    		$log->icci_id = $key->id;
	    		$log->template_id = $template_id;
	    		$log->save();
    		}
	    }

	    return $take. " number of emails have been sent";
	}

	public function releaseB2B(){
		$icct = InternalCollegeContactTemplate::where('name', 'B2B Press Release')
											  ->first();

		if (isset($icct) && $icct->active = 1) {
			if (Cache::has( env('ENVIRONMENT') .'_'. 'is_releaseB2B')) {
    		
	    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_releaseB2B');

	    		if ($cron == 'in_progress') {
	    			return "a cron is already running";
	    		}
	    	}
	    	Cache::put( env('ENVIRONMENT') .'_'. 'is_releaseB2B', 'in_progress', 60);
			$mac = new MandrillAutomationController();
			$response = $mac->sendPressRelease();
			Cache::put( env('ENVIRONMENT') .'_'. 'is_releaseB2B', 'done', 60);
			return $response;
		}
		return 'failed';
	}

	public function freemiumEmail(){

		$template_id = 50;
		$template_name = 'colleges_student_organically_inquired';
		$reply_email = 'james.lee@plexuss.com';

		$qry =  DB::connection('bk')->table('internal_college_contact_info as icci')
	    								  ->leftjoin('internal_college_contact_info_logs as iccil', function($q) use ($template_id){
	    								  			$q->on('icci.id', '=', 'iccil.icci_id')
	    								  			  ->where('iccil.template_id', '=', $template_id);
	    								  })
	    								  ->leftjoin('priority as p', function($q){
	    								  		$q->on('p.college_id', '=', 'icci.college_id')
	    								  		  ->on('p.active', '=', DB::raw("1"));
	    								  })
	    								  ->whereNull('p.id')
	    								  ->whereNull('iccil.icci_id')
	    								  ->where(function($q) {
	    								  	$q->orWhere('icci.type', '=', DB::raw("'admissions_enrollmen'"))
	    								  	  ->orWhere('icci.type', '=', DB::raw("'recruitment_related'"));
	    								  })
	    								  ->where('icci.unsub', 0)
	    								  ->take(10)
	    								  ->select('icci.id', 'fname', 'email')
	    								  ->groupBy('icci.id')
	    								  ->get();

	    foreach ($qry as $key) {
	    	$params = array();
	    	$params['FNAME'] = $key->fname;
	    	// $key->email = "anthony.shayesteh@plexuss.com";

	    	$mac = new MandrillAutomationController();
	    	$mac->generalEmailSend($reply_email, $template_name, $params, $key->email);

	    	$log = new InternalCollegeContactInfoLog;
		    		
    		$log->sent_email_id = 5;	
    		$log->icci_id = $key->id;
    		$log->template_id = $template_id;
    		$log->save();
	    }
	}

	public function autoPortalEmail($template_name, $ro_name, $forced_college_id = NULL){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_autoPortalEmail_'.$ro_name)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_autoPortalEmail_'.$ro_name);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_autoPortalEmail_'.$ro_name, 'in_progress', 10);

    	$time_now = Carbon::now()->toTimeString();

    	$this_ro = RevenueOrganization::on('bk')->where('name', $ro_name)->first();

    	$take = 200;
    	if ($this_ro->name == 'shorelight') {
			$take = 500;
		}
		if ($this_ro->name == 'edx') {
			$take = 400;
		}
    	
		if ($time_now >= "00:00:01" && $time_now <= "05:00:00") {
			$take *= 3;
		}

		if ($this_ro->name == 'eddy_reg') {
			$take = 200;
		}

		if ($this_ro->name == 'study_group') {
			$take = 10;
		}

		if ($this_ro->name == 'usf') {
			$take = 5;
		}
		
		if ($this_ro->name == 'nrccua') {
			$take = 900;
		}

		if ($this_ro->name == 'benedictine') {
			$take = 30;
		}

		if ( $this_ro->name == 'eddy_click' || $this_ro->name == 'qs_mba' || $this_ro->name == 'qs_grad'){
			$take = 600;
			if (isset($forced_college_id) && $forced_college_id == 105) {
				$take = 600;
			}
		}

		$today 	  = Carbon::today();
		$tomorrow = Carbon::tomorrow();
		$two_days_ago = Carbon::today()->subDays(2);

		// $template_arr = array('school_viewed_you', 'recommendation', 'school_want_to_recruit_you');
		// $rand = rand(0, count($template_arr) - 1);
		// $template_name = $template_arr[$rand];

		// $users = UsersPortalEmailEffortLog::on('bk')->where('created_at', '>=', $today)
		// 											->where('created_at', '<', $tomorrow)
		// 											->pluck('user_id');

		$qry = DB::connection('bk')->table('users as u')
								   // ->leftjoin('email_suppression_lists as esl', 'esl.uid', '=', 'u.id')

								   // ->whereNull('esl.id')
								   // ->whereNull('sent_recently_tbl.user_id')
								   ->whereRaw("not exists (Select 1 from email_suppression_lists esl where esl.uid = u.id and uid is not null)")
								   ->orderBy(DB::raw("RAND()"))
								   
								   ->where('u.is_organization', 0)
								   ->where('u.is_agency', 0)
								   ->where('u.is_ldy', 0)
								   ->where('u.is_alumni', 0)
								   ->where('u.is_parent', 0)
								   ->where('u.is_counselor', 0)
								   ->where('u.is_university_rep', 0)

								   ->groupBy('u.id')
								   ->select('u.id as uid', 'u.fname', 'u.lname', 'u.email', 'u.profile_img_loc', 'u.country_id')
								   ->where('u.created_at', '<=', $two_days_ago);
								   // ->whereRaw('u.id not in (select distinct user_id from users_portal_email_effort_logs as upeel where created_at >= "'.$today.'" and created_at < "'.$tomorrow.'")');
		
		// Temporarily send emails to these organization after we've sent 3 days ago
		if ($this_ro->name == 'eddy_reg' || $this_ro->name == 'eddy_click' || $this_ro->name == 'qs_mba' || 
			$this_ro->name == 'qs_grad' || $this_ro->name == "study_group" || $this_ro->name == "benedictine") {
			$qry = $qry->whereRaw("not exists 
									(Select 1
									 from users_portal_email_effort_logs upeel
									 where id >
										(Select min(upeel_id)
										 from users_portal_email_effort_logs_date_ids
										 where date(timestamp) >= date_sub(current_date(), interval 3 day))
										and upeel.user_id = u.id
										and template_name ='".$template_name."')");

		}else{
			$qry = $qry->whereRaw("not exists 
									(Select 1
									 from users_portal_email_effort_logs upeel
									 where id >
										(Select min(upeel_id)
										 from users_portal_email_effort_logs_date_ids
										 where date(timestamp) >= date_sub(current_date(), interval 7 day))
										and upeel.user_id = u.id)");
		}


		if (isset($this_ro)) {
			$res = $this->addCustomFiltersForRevenueOrgs($qry, $this_ro->name);
			$qry = $res['qry'];
		}

		$qry = $qry->take($take);
		
		// if (isset($users) && !empty($users)) {
		// 	$qry = $qry->whereNotIn('u.id', $users);
		// }
		
		$qry = $qry->get();
		
		$sm = new SparkpostModel('test_template');
		$cnt = 0;
		foreach ($qry as $key) {
			$is_suppressed = $sm->isSupressed($key->email, $key->uid);
			if (isset($is_suppressed) && $is_suppressed == true){ 
				continue;
			}
			Queue::push( new PostAutoPortalEmail($key, $template_name, $ro_name, $this_ro, $forced_college_id));
			// $this->autoPortalEmailQueuePart($key, $template_name, $ro_name, $this_ro, $forced_college_id);
			$cnt++;
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_autoPortalEmail_'.$ro_name, 'done', 10);

		return "Ran ". $template_name. " for ". $cnt. " users"	;
	}

	public function autoPortalEmailQueuePart($key, $template_name, $ro_name, $this_ro, $forced_college_id){
		switch ($template_name) {
			case 'school_viewed_you':
				$innerQ = DB::connection('bk')->table('notification_topnavs as nt')
										 ->join('colleges as c', 'c.school_name', '=', 'nt.name')
										 ->where('nt.command', 1)
										 ->where('nt.msg', 'viewed your profile')
										 ->where('nt.type_id', $key->uid)
										 ->groupBy('nt.id')
										 ->pluck('c.id as college_id');
			break;

			case 'recommendation':
				$innerQ = DB::connection('bk')->table('portal_notifications as pn')
										 ->join('colleges as c', 'c.id', '=', 'pn.school_id')
										 ->where('pn.user_id', $key->uid)
										 ->groupBy('pn.school_id')
										 ->pluck('pn.school_id as college_id');
			break;

			case 'school_want_to_recruit_you':
				$innerQ = DB::connection('bk')->table('recruitment as r')
										 ->join('colleges as c', 'c.id', '=', 'r.college_id')
										 ->where('r.user_id', $key->uid)
										 ->groupBy('r.college_id')
										 ->pluck('r.college_id');
			break;
		}

		// select 3 random colleges from each one of revenue organizations to be selected later to send to user.
		// making every partner equal chance of getting an email.

		// 4/10/18 UPDATE
		// Only do the following if you are not sending a forced_college_id
		if (!isset($forced_college_id)) {
			$ro = DB::connection('bk')->table('aor as a')
										->join('aor_colleges as ac', 'a.id', '=', 'ac.aor_id')
										->join('revenue_organizations as ro', 'ro.aor_id', '=', 'a.id')
										->where('ro.active', 1)
										->groupBy('ro.name')
										->select('ro.name', DB::raw("if(length(group_concat(college_id order by rand())) - length(replace(group_concat(college_id order by rand()), ',', '')) <= 2
											, group_concat(college_id order by rand()) #display all if less than 3 schools
											, substring_index(group_concat(college_id order by rand()), ',', 3)) #randomly pick 3 schools
											as 'schools'"), 'ac.*');

			$ro = $ro->get();

			$ac_college_ids = array();
			$tmp_colleges_str = '';
			foreach ($ro as $k) {
				$tmp_colleges_str .= $k->schools.",";
			}

			if ($tmp_colleges_str != '') {
				$ac_college_ids = explode(",", $tmp_colleges_str);
				unset($ac_college_ids[count($ac_college_ids) - 1]);
			}
		}

		$ac = AorCollege::on('bk')->orderBy(DB::raw("RAND()"))
						 		  ->join('colleges as c', 'c.id', '=', 'aor_colleges.college_id')
						 		  ->join('revenue_organizations as ro', function($q){
						 		  		$q->on('ro.aor_id', '=', 'aor_colleges.aor_id')
						 		  		  ->on('ro.active', '=', DB::raw("1"));
						 		  })
						 		  ->leftjoin('college_overview_images as coi', 'c.id', '=', 'coi.college_id')
						 		  ->leftjoin('colleges_admissions as ca', 'ca.college_id', '=', 'c.id')
						 		  ->leftjoin('colleges_tuition as ct', 'ct.college_id', '=', 'c.id')
						 		  ->leftjoin('colleges_financial_aid as cfa', 'cfa.college_id', '=', 'c.id')

						 		  ->leftjoin('organization_branches as ob', 'ob.school_id', '=', 'c.id')
						 		  ->leftjoin('organization_branch_permissions as obp', 'ob.id', '=', 'obp.organization_branch_id')
						 		  
						 		  ->select('c.id as college_id', 'c.logo_url', 'c.school_name', 'c.slug', 'c.address', 
						 		  		   'c.undergrad_enroll_1112', 'c.average_freshman_gpa', 'c.overview_content',
						 		  		   'c.city', 'c.state', 'c.zip', 'c.general_phone as phone', 'c.student_body_total',
						 		  		   'ca.deadline', 'ca.admissions_total', 'ca.applicants_total', 

						 		  		   'ct.*',
						 		  		   'obp.user_id',
						 				   'aor_colleges.aor_id', 
						 				   'coi.url as img_url',
						 				   'cfa.*',
						 				   'ro.id as ro_id', 'ro.has_filter', 'ro.name as ro_company')
						 		  // ->where('ro.id', '=', 6)
						 		  ->groupBy('c.id');

		if (isset($forced_college_id)) {
			$ac = $ac->where('c.id', $forced_college_id);
		}
		if (isset($this_ro)) {
			$ac = $ac->where('ro.id', '=', $this_ro->id);
		}

		if (isset($innerQ) && !empty($innerQ)) {
			$ac = $ac->whereNotIn('c.id', $innerQ);
		}

		$aor_id_arr = array();

		// if you are US, choose the following aor_ids
		if (isset($key->country_id) && $key->country_id == 1) {
			$aor_id_arr[] = 7;
			// $aor_id_arr[] = 8;
			$aor_id_arr[] = 9;
			$aor_id_arr[] = 10;
		}

		// if you are US, Mexico, Canada choose the following aor_ids
		if (isset($key->country_id) && ($key->country_id == 1 || $key->country_id == 2 || $key->country_id == 140)) {
			$aor_id_arr[] = 12;	
			$aor_id_arr[] = 13;
		}

		// if you are NOT in US, Mexico, Canada choose the following aor_ids

		$tmp_ro = RevenueOrganization::on('rds1')->where('name', $ro_name)->first();
		
		if (isset($tmp_ro->aor_id)) {
			$aor_id_arr = array();
			$aor_id_arr[] = $tmp_ro->aor_id;
		}elseif (isset($key->country_id) && ($key->country_id != 1 && $key->country_id != 2 && $key->country_id != 140)) {
			$aor_id_arr[] = 3;
			$aor_id_arr[] = 11;
		}

		if (!empty($aor_id_arr)) {
			$ac = $ac->where(function($q) use($aor_id_arr){
							foreach ($aor_id_arr as $k => $v) {
								$q->orWhere('aor_colleges.aor_id', '=', DB::raw($v));
							}
				  });
		}

		// Select one of the colleges randomly selected earlier from active revenue organizations.
		if (isset($ac_college_ids) && !empty($ac_college_ids)) {
			$ac = $ac->whereIn('c.id', $ac_college_ids);
		}
		$ac = $ac->first();
		$cnt = 0;

		if (isset($ac)) {
			$user_id = isset($ac->user_id) ? $ac->user_id : 1145069;

			$key->fname = ucwords(strtolower($key->fname));
			$key->lname = ucwords(strtolower($key->lname));
			// $this->customdd($input);
			// $this->customdd("---------------<br/>");
			// $this->customdd($url);
			// exit();

			if (isset($ac->has_filter) && $ac->has_filter == 1) {
				
				// $key->uid = 1114687;
				$crc = new CollegeRecommendationController();
				$filter = $crc->findCollegesForThisUserOnGetStarted($key->uid, $ac->aor_id, $ac->college_id);

				if (empty($filter)) {
					return;
				}

				$rnd = rand(0, count($filter) - 1);
				$tmp_college = College::find($filter[$rnd]);

				$ac->college_id 		   = $tmp_college->id;
				$ac->logo_url   		   = $tmp_college->logo_url;
				$ac->school_name 		   = $tmp_college->school_name;
				$ac->slug 		 		   = $tmp_college->slug;

				$ac->address 	 		   = $tmp_college->address;
				$ac->undergrad_enroll_1112 = $tmp_college->undergrad_enroll_1112;
				$ac->average_freshman_gpa  = $tmp_college->average_freshman_gpa;
				$ac->overview_content 	   = $tmp_college->overview_content;
				$ac->city 		 		   = $tmp_college->city;
				$ac->state 		 		   = $tmp_college->state;
				$ac->zip 				   = $tmp_college->zip;
				$ac->phone 		 		   = $tmp_college->general_phone;
				$ac->student_body_total    = $tmp_college->student_body_total;
			}

			$input = array();
			$input['user_id']    = $key->uid;
			$input['college_id'] = $ac->college_id;
			$input['ro_id']      = $ac->ro_id;

			$url = $this->getRevenueOrganizationLinksForEmails($input, 'email_'.$template_name, $key->uid);
			$url = $url['url'];

			switch ($template_name) {
				case 'school_viewed_you':

					// $ntn = new NotificationController();
					// $ntn->create( $ac->school_name, 'user', 1, null, $user_id , $key->uid, null, 1);

					// $key->email = 'anthony.shayesteh@plexuss.com';

					$params = array();
					$params['FNAME'] 		  = $key->fname;
					$params['LNAME'] 		  = $key->lname;
					$params['PROFILEPIC']	  = isset($key->profile_img_loc) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$key->profile_img_loc : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
					$params['COLLEGEIMAGE']   = (isset($ac->img_url) && !empty($ac->img_url)) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$ac->img_url : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/no-image-default.png';
					$params['COLLEGELOGOURL'] = isset($ac->logo_url) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$ac->logo_url : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
					$params['COLLINK'] 		  = $url;
					
					$params['COLLEGENAME'] 	  	 = $ac->school_name;
					$params['COLLEGEADDRESS'] 	 = $ac->address.', '.$ac->city.', '.$ac->state.' '.$ac->zip. ' | '.$ac->phone;
					$params['ADMISSIONDEADLINE'] = (isset($ac->deadline) && !empty($ac->deadline)) ? $ac->deadline : 'N/A';

					$acceptance_rate = ($ac->applicants_total != 0) ? round(($ac->admissions_total / $ac->applicants_total) * 100) : 'N/A';
					$params['ACCEPTANCERATE'] 	 = $acceptance_rate;
					$params['TUITION'] 			 = number_format(round($ac->tuition_avg_in_state_ftug + $ac->books_supplies_1213 + $ac->room_board_on_campus_1213 + $ac->other_expenses_on_campus_1213));
					$params['GPA'] 				 = isset($ac->average_freshman_gpa) ? $ac->average_freshman_gpa : 'N/A';
					$params['TOTALBODYSIZE'] 	 = isset($ac->student_body_total) ? number_format($ac->student_body_total) : 'N/A';
					$params['UNDERGRADBODYSIZE'] = isset($ac->undergrad_enroll_1112) ? number_format($ac->undergrad_enroll_1112) : 'N/A';

					$email_template_name = 'users_a_college_viewed_your_profile';
					$mac = new MandrillAutomationController();
					$mac->generalEmailSend('support@plexuss.com', $email_template_name, $params, $key->email);
					
					$upeel = new UsersPortalEmailEffortLog;
					$arr   = array();
					$arr['user_id'] 	  = $key->uid;
					$arr['template_name'] = $email_template_name;
					$arr['ro_id'] 		  = $ac->ro_id;
					$arr['company'] 	  = $ac->ro_company;
					$update = $upeel->saveLog($arr);
					
					$cnt = 1;
					break;
				case 'recommendation':

					$attr = array('user_id' => $key->uid, 'school_id' => $ac->college_id);
					$val  = array('user_id' => $key->uid, 'school_id' => $ac->college_id, 'is_recommend' => 1, 
						'is_higher_rank_recommend' => 1, 'is_top_75_percentile_recommend' => 1, 
						'is_lower_tuition_recommend' => 0, 'recommend_based_on_college_id' => -1);

					$tmp = PortalNotification::updateOrCreate($attr, $val);

					// $key->email = 'anthony.shayesteh@plexuss.com';
					$params = array();
					$params['FNAME'] 		  = $key->fname;
					$params['LNAME'] 		  = $key->lname;
					$params['PROFILEPIC']	  = isset($key->profile_img_loc) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$key->profile_img_loc : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
					$params['COLLLOGO1']	  = isset($ac->logo_url) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$ac->logo_url : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
					$params['COLLINK1']		  = $url;
					$params['COLLNAME1'] 	  = $ac->school_name;
					$params['COLLLOC1'] 	  = $ac->address.', '.$ac->city.', '.$ac->state.' '.$ac->zip. ' | '.$ac->phone;

					$email_template_name = 'users_recommended_by_plexuss';
					$mac = new MandrillAutomationController();
					$mac->generalEmailSend('support@plexuss.com', $email_template_name, $params, $key->email);
					
					$upeel = new UsersPortalEmailEffortLog;
					$arr   = array();
					$arr['user_id'] = $key->uid;
					$arr['template_name'] = $email_template_name;
					$arr['ro_id'] 		  = $ac->ro_id;
					$arr['company'] 	  = $ac->ro_company;
					$update = $upeel->saveLog($arr);

					$cnt = 1;
					break;
				case 'school_want_to_recruit_you':

					$attr = array('user_id' => $key->uid, 'college_id' => $ac->college_id, 'aor_id' =>$ac->aor_id );

					$val = array('user_id' => $key->uid, 'college_id' => $ac->college_id,
								 'user_recruit' => 0, 'college_recruit' => 1,
								 'reputation' => 0,  'location' => 0, 'tuition' => 0,
								 'program_offered' => 0, 'athletic' => 0, 'onlineCourse' => 0,
								 'religion' => 0, 'campus_life' => 0, 'status' => 1, 
								 'type' => 'auto_recommendation', 'aor_id' =>$ac->aor_id);

					$tmp = Recruitment::updateOrCreate($attr, $val);

					////*************Add Notification to the user *********************///////
					$ntn = new NotificationController();
					$ntn->create( $ac->school_name, 'user', 3, null, $key->uid , $user_id);	

					// $key->email = 'anthony.shayesteh@plexuss.com';

					$params = array();
					$params['FNAME'] 		  = $key->fname;
					$params['LNAME'] 		  = $key->lname;
					$params['PROFILEPIC']	  = isset($key->profile_img_loc) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$key->profile_img_loc : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
					$params['COLLEGELOGO']	  = isset($ac->logo_url) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$ac->logo_url : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
					$params['COLLEGENAME'] 	  = $ac->school_name;
					$params['COLLINK'] 		  = $url;

					$start = strpos($ac->overview_content, '<p>');
					$end = strpos($ac->overview_content, '</p>', $start);
					$paragraph = substr($ac->overview_content, $start, $end-$start+4);
					$params['COLLEGEDESC'] = $paragraph;

					$getPins = DB::table('lists')->where('custom_college', '=', $ac->college_id)->orderBy('rank_num', 'asc')->first();
					if (isset($getPins)) {
						
						$email_template_name = 'users_a_college_wants_to_recuit_you';
						$params['RANKTITLE'] = $getPins->title;
						$params['RANKIMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/'.$getPins->image;
						$params['RANKNUM']   = $getPins->rank_num;
                        $params['COLLEGEADDRESS']    = $ac->address.', '.$ac->city.', '.$ac->state.' '.$ac->zip. ' | '.$ac->phone;

					}else{

						$email_template_name = 'users_a_college_wants_to_recuit_you_2';
						$params['PERCENTRECEIVEDAID']  = $ac->undergrad_grant_pct;
						$params['AVGFINANCIALAID'] 	   = number_format($ac->undergrad_grant_avg_amt);
						$params['PERCENTRECEIVEDLOAN'] = $ac->undergrad_loan_pct;
						$params['AVGFINANCIALLOAN']    = number_format($ac->undergrad_loan_avg_amt);
						$params['OUTOFSTATEAIDGIVEN']  = number_format($ac->undergrad_aid_avg_amt + $ac->undergrad_loan_avg_amt);
						$params['COLLEGEIMAGE']   = (isset($ac->img_url) && !empty($ac->img_url)) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/'.$ac->img_url : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/no-image-default.png';
						$params['COLLEGELOGOURL'] = isset($ac->logo_url) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$ac->logo_url : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';
						
						
						$params['COLLEGENAME'] 	  	 = $ac->school_name;
						$params['COLLEGEADDRESS'] 	 = $ac->address.', '.$ac->city.', '.$ac->state.' '.$ac->zip. ' | '.$ac->phone;
					}
					
					$mac = new MandrillAutomationController();
					$mac->generalEmailSend('support@plexuss.com', $email_template_name, $params, $key->email);
					// $this->customdd($email_template_name);
					// $this->customdd("-------------<br/>");
					// dd(6653651265312);
					$upeel = new UsersPortalEmailEffortLog;
					$arr   = array();
					$arr['user_id'] = $key->uid;
					$arr['template_name'] = $email_template_name;
					$arr['ro_id'] 		  = $ac->ro_id;
					$arr['company'] 	  = $ac->ro_company;
					$update = $upeel->saveLog($arr);

					$cnt = 1;
					break;
			}
		}

		return $cnt;
	}

	// Send the recommendation for revenue_school_matching where email_sent is zero
	public function sendRecommendationForRevenueSchoolMatching(){
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_sendRecommendationForRevenueSchoolMatching')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_sendRecommendationForRevenueSchoolMatching');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put(env('ENVIRONMENT') .'_'. 'is_cron_sendRecommendationForRevenueSchoolMatching', 'in_progress', 10);

		$ro_name 	   = "nrccua";
		$template_name = "recommendation";
		$this_ro 	   = RevenueOrganization::on('bk')->where('name', $ro_name)->first();

    	$take = 50;

    	$today 		 	= Carbon::today()->toDateString();
    	$three_days_ago = Carbon::today()->subDays(3)->toDateString();

    	$qry = DB::connection('bk')->table('revenue_schools_matching as rsm')
    								 ->join('users as u', 'u.id', '=', 'rsm.user_id')
    								 ->where('rsm.email_sent', 0)
    								 ->where('rsm.is_uploaded', 1)
    								 ->whereNotNull('rsm.utm_source')
    								 ->whereRaw("not exists(
																SELECT
																	1
																FROM
																	distribution_responses as dr
																JOIN distribution_clients as dc ON dc.id = dr.dc_id
																and dr.ro_id = 1
																where
																	dr.user_id = rsm.user_id
																and dr.manual = 0
																and dc.college_id = rsm.college_id
															)")
    								 ->whereRaw("not exists(
																Select
																	1
																from
																	recruitment r
																where
																	user_recruit = 1
																and r.user_id = rsm.user_id
																and r.college_id = rsm.college_id
															)")
    								 ->whereRaw("not exists(
																Select
																	1
																from
																	revenue_schools_matching rsm2
																WHERE
																	email_sent = 1
																and date(updated_at) BETWEEN '".$three_days_ago."'
																and '".$today."'
																and rsm.user_id = rsm2.user_id
															)")
    								 ->groupBy('u.id')
    								 ->orderBy(DB::raw("RAND()"))
    								 ->select('u.id as uid', 'u.fname', 'u.lname', 'u.email', 'u.profile_img_loc', 'u.country_id', 'rsm.college_id', 'rsm.id as rsm_id')
    								 ->take($take)
    								 ->get();

    	$sm = new SparkpostModel('test_template');
		$cnt = 0;

    	foreach ($qry as $key) {
    		$is_suppressed = $sm->isSupressed($key->email, $key->uid);
			if (isset($is_suppressed) && $is_suppressed == true){ 
				continue;
			}

			$rsm = RevenueSchoolsMatching::find($key->rsm_id);
			if ($rsm->email_sent == 1) {
				continue;
			}
			// $tmp_cnt = 0;
			// $tmp_cnt =  $this->autoPortalEmailQueuePart($key, $template_name, $ro_name, $this_ro, $key->college_id);
			// $cnt = $cnt + $tmp_cnt;
    		
    		Queue::push( new PostAutoPortalEmail($key, $template_name, $ro_name, $this_ro, $key->college_id));

    		$rsm->email_sent = 1;
    		$rsm->save();

    		$cnt++;
    	}
    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_sendRecommendationForRevenueSchoolMatching', 'done', 10);

    	return "Ran ". $template_name. " for ". $cnt. " users"	;
	}

	public function getRevenueOrganizationLinksForEmails($input, $utm_source = null, $user_id = NULL){
	
		$ro = RevenueOrganization::find($input['ro_id']);
		$ret = array();

		(!isset($utm_source)) ? $utm_source = 'get_started' : NULL;

		switch ($ro->type) {
			case 'post':
				// $dc = new DistributionController();
				// $dc->postInquiriesWithQueue($input['ro_id'], $input['college_id'], $input['user_id']);
				// Queue::push( new PostInquiriesThroughDistributionClient($input['ro_id'], $input['college_id'], $input['user_id']));
				if (isset($user_id)) {
					$user = User::on('rds1')->find($user_id);
					if (!isset($user->profile_percent) || $user->profile_percent < 30) {
						$ret['url']  = env('CURRENT_URL').'get_started?utm_source='.$utm_source."&college_id=".$input['college_id'].'&ro_id='.$input['ro_id'];
						$ret['status'] = 'success';
					}else{
						$college = College::on('rds1')->find($input['college_id']);

						$ret['url']  = env('CURRENT_URL').'college/'.$college->slug.'?utm_source='.$utm_source.'&ro_id='.$input['ro_id'];
						$ret['status'] = 'success';
					}
				}else{
					$ret['url']  = env('CURRENT_URL').'next-steps/?ro_id='.$input['ro_id'].'&college_id='.$input['college_id'];
					$ret['status'] = 'success';
				}

				break;

			case 'linkout':
				$this_dc = DistributionClient::on('bk')->where('ro_id', $input['ro_id'])
														 ->where('college_id', $input['college_id'])
														 ->first();
				$dc = new DistributionController();
				$str = $dc->generateLinkoutUrl($this_dc->id, $input['user_id']);

				$ret['type'] = $ro->type;
				$ret['url']  = env('CURRENT_URL').'adRedirect?company='.$ro->name.'&utm_source='.$utm_source.'&uid='.$input['user_id'].'&cid=-1&url='.urlencode($str);

                if (isset($input['user_id'])) {
                    $hashed_user_id = Crypt::encrypt($input['user_id']);

                    $ret['url'] .= '&hid=' . $hashed_user_id;
                }

				isset($input['college_id']) ? $ret['url'] = $ret['url'] . '&college_id='.$input['college_id'] : NULL;

				$ret['status'] = 'success';
				break;

			case 'click':
				$arc =  AdRedirectCampaign::on('bk')->where('ro_id', $input['ro_id'])
													  ->where('college_id', $input['college_id'])
													  ->first();

				if (!isset($arc) || empty($arc)) {
					$arc =  AdRedirectCampaign::on('bk')->where('ro_id', $input['ro_id'])
													  ->first();
				}

				$ret['type'] = $ro->type;
				$ret['url']  = env('CURRENT_URL').'adRedirect?company='.$arc->company.'&utm_source='.$utm_source.'&cid='.$arc->id;

				if (isset($input['user_id'])) {
					$ret['url'] .= '&uid='.$input['user_id'];
				}
                if (isset($input['user_id'])) {
                    $hashed_user_id = Crypt::encrypt($input['user_id']);

                    $ret['url'] .= '&hid=' . $hashed_user_id;
                }

				isset($input['college_id']) ? $ret['url'] = $ret['url'] . '&college_id='.$input['college_id'] : NULL;

				$ret['status'] = 'success';
				break;
		}

		return $ret;
	}
	
    private function emailInternalColleges($template_id, $sender_email, $nature){

    	if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_running_emailInternalColleges_'. $template_id. '_'. $sender_email)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_running_emailInternalColleges_'. $template_id. '_'. $sender_email);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	$send_email_qry = InternalCollegeContactPlexussEmail::on('bk')
    														->where('email', $sender_email)
    														->first();


    	$cnt = InternalCollegeContactInfoLog::on('bk')
    										//->where('template_id', $template_id)
    										->where('created_at', '>=', Carbon::today())
    										->where('created_at', '<',  Carbon::tomorrow())
    										->where('sent_email_id', $send_email_qry->id)
    										->count();

    	$today = Carbon::now();
    	$sm = new SparkpostModel('test_template');

    	$sent_email_cnt = 0;

    	if ($cnt < $send_email_qry->max_num_of_sent_email_daily && $today->dayOfWeek !== Carbon::SATURDAY && 
    		($today->toTimeString() >= '06:30:00' && $today->toTimeString() <= '15:00:00')) {

    		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_running_emailInternalColleges_'. $template_id. '_'. $sender_email, 'in_progress', 60);

    		$icct =  InternalCollegeContactTemplate::find($template_id);

    		if (isset($nature) && $nature == 'first') {
    			$qry  = $this->firstEmailQry($template_id, $icct);
    		}else if (isset($nature) && $nature == 'second') {
    			$qry  = $this->secondEmailQry($template_id, $icct);
    		}else if (isset($nature) && $nature == 'third') {
    			$qry  = $this->secondEmailQry($template_id, $icct);
    		}else if (isset($nature) && $nature == 'customForSchoolsEnrollmentDepartment') {
    			$qry  = $this->customForSchoolsEnrollmentDepartment($template_id, $icct);
    		}
	    		    	
	    	foreach ($qry as $key) {

	    		// $key->email= 'anthony.shayesteh@plexuss.com';
	    		// $key->email= 'jp.novin@plexuss.com';
	    		$is_suppressed = $sm->isSupressed($key->email);

	    		$is_organization = User::on('bk')->where(function($q){
	    										   		$q->orWhere('is_organization', '=', DB::raw("1"))
	    										   		  ->orWhere('is_agency', '=', DB::raw("1"));
	    										   })
	    										   ->where('email', $key->email)
	    										   ->first();

	    		if ((isset($is_suppressed) && $is_suppressed == true) || isset($is_organization)) {

	    			$icci_temp = InternalCollegeContactInfo::find($key->id);

	    			$icci_temp->unsub = 1;
	    			$icci_temp->save();

	    			$log = new InternalCollegeContactInfoLog;
		    		
		    		$log->sent_email_id = $send_email_qry->id;	
		    		$log->icci_id = $key->id;
		    		$log->template_id = $template_id;
		    		$log->save();

	    			continue;
	    		}

	    		$rand = rand(1, 10);

	    		if($rand < 5){
	    			$data = array();
	    			$data['host']      = env('MAIL_HOST');
	    			$data['username']  = $send_email_qry->email;
	    			$data['pass'] 	   = env('UNIVERSAL_EMAIL_PASS');
	    			$data['from_name'] = $send_email_qry->name;
	    			$data['to_email']  = $key->email;
	    			// $data['to_email']  = 'anthony.shayesteh@plexuss.com';
	    			$data['to_name']   = ucwords($key->fname);
	    			$data['subject']   = $icct->subject;
	    			$data['school_name'] = '';
	    			$data['slug']      = '';
	    			$data['num_of_students'] = '';
	    			isset($key->school_name) ? $data['school_name'] = $key->school_name : null;
	    			isset($key->slug) ? $data['slug'] = $key->slug : null;
	    			isset($key->num_of_students) ? $data['num_of_students'] = $key->num_of_students : null;

	    			if ( $data['num_of_students'] < 1000) {
	    				$data['num_of_students'] = 1020;
	    			}
	    			
	    			$body = (isset($key->fname) && !empty($key->fname)) ? '<p><span style="font-size: 12pt; font-family: \'Calibri\';"><span style="color: #222222;">'.$icct->intro_str.' '. ucwords($key->fname) . ", </span></span></p><p></p>" : '<p><span style="font-size: 12pt; font-family: \'Calibri\';"><span style="color: #222222;">Hello there, </span></span></p><p></p>';

	    			$template_content = str_replace("{{email}}", $data['to_email'], $icct->template);
	    			$template_content = str_replace("*|FNAME|*", $data['to_name'], $template_content);
	    			$template_content = str_replace("{{school_name}}", $data['school_name'], $template_content);
	    			$template_content = str_replace("{{num_of_students}}", number_format($data['num_of_students']), $template_content);
	    			$template_content = str_replace("{{slug}}", $data['slug'], $template_content);

	    			$data['subject'] = str_replace("{{num_of_students}}", number_format($data['num_of_students']), $data['subject']);

	    			if (isset($nature) && $nature == 'first') {
	    				$data['body'] = $body. $icct->template;
	    				if (isset($send_email_qry->signature)) {
	    					$data['body'] .= $send_email_qry->signature; 
	    				}

	    			}else if (isset($nature) && $nature == 'second') {
	    				$data['body'] = $template_content;

	    			}else if (isset($nature) && $nature == 'third') {				
	    				$data['body'] = $template_content;

	    			}else if (isset($nature) && $nature == 'customForSchoolsEnrollmentDepartment') {
	    				$data['body'] = $body. $template_content;	
	    			}

	    			$result = $this->sendSMTPEmail($data);

		    		$log = new InternalCollegeContactInfoLog;
		    		
		    		$log->sent_email_id = $send_email_qry->id;	
		    		$log->icci_id = $key->id;
		    		$log->template_id = $template_id;
		    		$log->save();

		    		$sent_email_cnt++;
	    		}
	    	}

	    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_running_emailInternalColleges_'. $template_id. '_'. $sender_email, 'done', 60);
    	}

    	return $sent_email_cnt;
    }

    private function firstEmailQry($template_id, $icct){

    	$qry =  DB::connection('bk')->table('internal_college_contact_info as icci')
	    								  ->leftjoin('internal_college_contact_info_logs as iccil', function($q) use ($template_id){
	    								  			$q->on('icci.id', '=', 'iccil.icci_id')
	    								  			  ->where('iccil.template_id', '=', $template_id);
	    								  })
	    								  ->where('icci.template_id', $template_id)
	    								  ->whereNull('iccil.icci_id')
	    								  ->where('icci.unsub', 0)
	    								  ->take(10)
	    								  ->select('fname', 'email', 'icci.id')
	    								  ->get();

	    return $qry;
    }

    private function secondEmailQry($template_id, $icct){

    	$qry =  DB::connection('bk')->table(DB::raw("(select
																`fname` ,
																`email` ,
																`icci`.`id`,
																iccil.template_id
														from
																`internal_college_contact_info` as `icci`
														join `internal_college_contact_info_logs` as `iccil` on `icci`.`id` = `iccil`.`icci_id`
														where `icci`.`unsub` = 0        
														and `icci`.`template_id` = ".$icct->related_template_id."  #anchor template
														and datediff(date(date_sub(current_timestamp(), interval 7 hour)), date(iccil.created_at)) >= 7) as tbl_orig"))

	    								  ->leftjoin('internal_college_contact_info_logs as iccil', function($q) use ($template_id){
	    								  			$q->on('tbl_orig.id', '=', 'iccil.icci_id')
	    								  			  ->where('iccil.template_id', '=', $template_id);
	    								  })

	    								  ->whereNull('iccil.icci_id')
	    								  ->take(10)
	    								  ->select('fname', 'email', 'tbl_orig.id');

	   	$qry = $qry->get();

	    return $qry;
    }

    private function customForSchoolsEnrollmentDepartment($template_id, $icct){
    	$qry =  DB::connection('bk')->table('internal_college_contact_info as icci')
	    								  ->leftjoin('internal_college_contact_info_logs as iccil', function($q) use ($template_id){
	    								  			$q->on('icci.id', '=', 'iccil.icci_id')
	    								  			  ->where('iccil.template_id', '=', $template_id);
	    								  })
	    								  ->join('colleges as c', 'c.id', '=', 'icci.college_id')
	    								  
	    								  ->where('icci.type', 'admissions_enrollmen')
	    								  ->whereNull('iccil.icci_id')
	    								  ->where('icci.unsub', 0)
	    								  ->take(10)
	    								  ->select('fname', 'email', 'icci.id', 'c.school_name', 'c.id as college_id',
	    										   DB::raw('CONCAT("https://plexuss.com/college/",`c`.`slug`) as slug '))
	    								  ->get();

	    foreach ($qry as $key) {
	    	
	    	$num_of_students = DB::connection('bk')->table('recruitment as r')
	    											 ->join('colleges as c', 'c.id', '=', 'r.college_id')
	    											 ->where('r.user_recruit', 1)
	    											 ->where('r.college_recruit', 0)
	    											 ->where('r.status', 1)
	    											 ->where('c.id', $key->college_id)
	    											 ->count();

	    	$key->num_of_students = $num_of_students;
	    }

	    return $qry;
    }

    private function sendSMTPEmail($data){

    	$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $data['host'];  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $data['username'];                 // SMTP username
		$mail->Password = $data['pass'];                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		$mail->setFrom($data['username'], $data['from_name']);
		$mail->addAddress($data['to_email'], $data['to_name']);     // Add a recipient
		// $mail->addAddress('ellen@example.com');               // Name is optional
		$mail->addReplyTo($data['username'], $data['from_name']);
		// $mail->addCC('cc@example.com');
		// $mail->addBCC('bcc@example.com');

		// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $data['subject'];
		$mail->Body    = $data['body'];
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		$ret = array();

		if(!$mail->send()) {
			$ret['status'] = "failed";
			$ret['error']  = $mail->ErrorInfo;
		} else {
		    $ret['status'] = "success";
		}


		return response()->json($ret);
    }

    public function resendCappedEmails(){

    	$take = 10000;
    	$qry = DB::table('mandrill_logs')
    							     // ->where(function($q){
    							     // 		$q->orWhere('template_name', '=', DB::raw("'users-edx-free-courses-top-universities-nr'"))
    							     // 		  ->orWhere('template_name', '=', DB::raw("'users-edx-free-courses-top-universities'"));
    							     // })
    							     ->where('response', 'LIKE', "%Exceed Sending Limit (monthly)%")
    							     ->where('id', '>=', 82056043)
    							     ->take($take);

    	if (Cache::has(env('ENVIRONMENT').'_resendCappedEmails_offset')) {
    		$offset = Cache::get(env('ENVIRONMENT').'_resendCappedEmails_offset');
    		$qry = $qry->skip($offset);
    	}

    	$qry = $qry->get();

    	if (isset($offset)) {
    		$offset = $offset + $take;
    	}else{
    		$offset = $take;
    	}

    	Cache::put(env('ENVIRONMENT').'_resendCappedEmails_offset', $offset, 60);

    	$sm = new SparkpostModel(true);

    	if (isset($qry)) {
    		foreach ($qry as $key) {
	    		// $key->email = "reza.shayesteh@gmail.com";
	    		$message = $this->obj(json_decode($key->params));

	    		$sm->sendCappedEmails($message, $key->email, $key->template_name);
	    	}

	    	return "success";
    	}
    	return "failed";
    }

    public function resendCappedEmailsOffset(){
    	$offset = Cache::get(env('ENVIRONMENT').'_resendCappedEmails_offset');
    	return $offset;
    }

    public function setResendCappedEmailsOffset($offset){
    	Cache::put(env('ENVIRONMENT').'_resendCappedEmails_offset', $offset, 60);
    }

    // Insert users for profile completion
	public function procInsertUsersProfileCompletion(){
		$qry = DB::statement("call proc_insert_users_profile_completion();");
		return "success";
	}


	public function addToEmailSuppressionQueue($type, $email_key){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_addToEmailSuppressionQueue_'.$type.'_'.$email_key)) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_addToEmailSuppressionQueue_'.$type.'_'.$email_key);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_addToEmailSuppressionQueue_'.$type.'_'.$email_key, 'in_progress', 10);


		if (Cache::has( env('ENVIRONMENT') .'_'. 'page_addToEmailSuppressionQueue_'.$type."_".$email_key)) {
    		$page  = Cache::get( env('ENVIRONMENT') .'_'. 'page_addToEmailSuppressionQueue_'.$type."_".$email_key);
    		$page += 1;

    		Cache::forever( env('ENVIRONMENT') .'_'. 'page_addToEmailSuppressionQueue_'.$type."_".$email_key, $page);
       	}else{
       		$page = 1;
       		Cache::forever( env('ENVIRONMENT') .'_'. 'page_addToEmailSuppressionQueue_'.$type."_".$email_key, $page);
       	}

		$today = Carbon::today()->toDateString();

		// $today = "2018-10-20";
		// $page  = 1;

		$url = "https://api.sparkpost.com/api/v1/suppression-list?page=".$page."&to=".$today."&from=2014-12-20&types=".$type."&per_page=100";
		$client = new Client(['base_uri' => 'http://httpbin.org']);
		
		try {
			$response = $client->request('GET', $url, [
			    'headers' => [
			        'Content-Type'       => 'application/json',
			        'Accept'             => 'application/json',
			        'Authorization'      => $email_key
			    ]
			]);
			$result = json_decode($response->getBody()->getContents(), true)['results'];
		} catch (\Exception $e) {
			return 'false';
		}

		if (empty($result)) {
			return "No more suppression users";
		}
		
		foreach ($result as $key) {

			if (empty($key['description'])) {
				$key['description'] =  'Coming from '.' addToEmailSuppressionQueue ' . $type;
			}
			
			$attr = array();
			$attr['recipient'] 		   = $key['recipient'];
			$attr['description'] 	   = $key['description'];
			$attr['source'] 		   = $key['source'];
			$attr['type'] 			   = $key['type'];
			$attr['non_transactional'] = 1;

			$val = $attr;

			Queue::push( new AddToEmailSuppressionList($attr, $val));
			// $update = EmailSuppressionList::updateOrCreate($attr, $val);
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_addToEmailSuppressionQueue_'.$type.'_'.$email_key, 'done', 10);

		return "success";
	}

	/* importSendGridSuppression
	 * 
	 * This method imports blocks bounce invalid emails and spam complaints from sendgrid and 
	 * add them to email_suppression_lists table.
	 */
	public function importSendGridSuppression(){
		$sg = new SendGridModel('test');
		$cnt = $sg->importSendGridSuppression();

		return $cnt;
	}


	/* cleanUpUserInvitesTable
	 * 
	 * This method cleans up users invite for duplications.
	 * 
	 */
	public function cleanUpUserInvitesTable(){
		
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_cron_cleanUpUserInvitesTable')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_cron_cleanUpUserInvitesTable');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_cleanUpUserInvitesTable', 'in_progress', 10);

		$rbe = new RoleBaseEmail;
		$qry = DB::connection('bk')->table('users_invites')
								   ->where('source', '!=', 'Mobile App')
								   ->where('is_dup', 0)
								   ->groupBy('invite_email')
								   ->having('cnt', '>', 1)
								   ->orderBy('cnt', 'DESC')
								   ->select(DB::raw("count(invite_email) as cnt"), 'invite_email as email')
								   ->orderBy(DB::raw("RAND()"))
								   ->take(1000)
								   ->get();

		foreach ($qry as $key) {
			$is_role_base = $rbe->isRoleBase($key->email);
			if ($is_role_base) {
				UsersInvite::where('invite_email', $key->email)->delete();
				continue;
			}

			$inner = UsersInvite::where('invite_email', $key->email)->get();

			$check = false;
			foreach ($inner as $k) {
				if (!isset($temp_k)) {
					$temp_k = $k;
					continue;
				}
				$ac = AdClick::on('bk')->where('user_invite_id', $k->id)->first();
				if (!isset($ac)) {
					$k->is_dup = 1;
					$k->save();
				}elseif(!$check){
					$temp_k->is_dup = 1;
					$temp_k->save();

					$temp_k = $k;
					$check = true;
				}
			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_cron_cleanUpUserInvitesTable', 'done', 10);

		return "success";
	}

	public function runClustersForAllUsers(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_runClustersForAllUsers')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_runClustersForAllUsers');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_runClustersForAllUsers', 'in_progress', 10);

		$ucl = new UsersClusterLog;
		$qry = DB::connection('bk')->table('users as u')
									 ->leftjoin('users_cluster_logs as ucl', 'u.id', '=', 'ucl.user_id')
									 ->whereNull('ucl.id')
									 ->take(500)
									 ->select('u.id as user_id')
									 ->orderBy(DB::raw("RAND()"))
									 ->get();
		$cnt = 0;
		foreach ($qry as $key) {
			$update = $ucl->updateCluster($key->user_id);
			if ($update == true) {
				$cnt++;
			}else{
				Cache::put( env('ENVIRONMENT') .'_'. 'is_runClustersForAllUsers', 'done', 10);
				return "user_id problem: ". $key->user_id;
			}
		}
		Cache::put( env('ENVIRONMENT') .'_'. 'is_runClustersForAllUsers', 'done', 10);
		return "cnt: ". $cnt;
	}

	public function getBirthdayUsingAccurateAppend(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_getBirthdayUsingAccurateAppend')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_getBirthdayUsingAccurateAppend');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_getBirthdayUsingAccurateAppend', 'in_progress', 10);


		$client = new Client(['base_uri' => 'http://httpbin.org']);
		$url    =  'https://api.accurateappend.com/Services/V2/AppendDob/15152115-8c3d-4985-ac56-b5df029ec044/';


		$qry = DB::connection('bk')->table('cappex_possible_matches as cpm')
									 ->where('cpm.error_log', '{"field_name":["b_year","b_month","b_date"],"possible_fields":[[],[],[]]}')

								  ->leftjoin('users_without_dob as uwb', 'uwb.user_id', '=', 'cpm.user_id')	

								  ->whereNull('uwb.id')
								  ->where('cpm.sent', 0)
								  ->groupBy('cpm.user_id')
								  ->orderBy(DB::raw("RAND()"))
								  ->select('cpm.*')
								  ->take(10)
								  ->get();

		$cnt = 0;
		foreach ($qry as $key) {
		  	$user = User::on('bk')->where('id', $key->user_id)->first();

		  	if (!isset($user->fname) || !isset($user->lname) || !isset($user->address) || !isset($user->city) || 
		  		!isset($user->state) || !isset($user->zip)) {
		  		
		  		$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id);
				UsersWithoutDob::updateOrCreate($attr, $val);

		  		continue;
		  	}
		  	$params = array();
		  	$params['firstname'] = $user->fname;
		  	$params['lastname']  = $user->lname;
		  	$params['address']   = urlencode($user->address);
		  	$params['city']      = $user->city;
		  	$params['state']     = $user->state;
		  	$params['postalcode']= $user->zip;

		  	// $params['firstname'] = 'reza';
		  	// $params['lastname']  = 'shayesteh';
		  	// $params['address']   = urlencode('1180 Saranap Ave apt 102');
		  	// $params['city']      = 'Walnut Creek';
		  	// $params['state']     = 'CA';
		  	// $params['postalcode']= '94595';
		  	$response = $client->request('GET', $url, [
		        'query' => $params]);
			
			$ret = $response->getBody()->getContents();

			$ret = json_decode($ret);
			if (isset($ret->Dob)) {
				$dob = $ret->Dob;
				$dob = $dob->Dob;
				$birth_date = substr($dob, 0, 4) . '-'. substr($dob, 4, 2). '-'. substr($dob, 6, 2);
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'birth_date' => $birth_date);
				NrccuaUser::updateOrCreate($attr, $val);

				$del = CappexPossibleMatch::where('user_id', $key->user_id)->where('sent', 0)->delete();

				$cnt++;
			}else{
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id);
				UsersWithoutDob::updateOrCreate($attr, $val);
			}
		}	

		Cache::put( env('ENVIRONMENT') .'_'. 'is_getBirthdayUsingAccurateAppend', 'done', 10);
		return "Number of matches found: ". $cnt;					  
	}


	// Append user info using Accurate Append
	public function getAddressUsingAccurateAppend(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_getAddressUsingAccurateAppend')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_getAddressUsingAccurateAppend');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_getAddressUsingAccurateAppend', 'in_progress', 10);

		$two_days_ago = Carbon::now()->subDays(2);

		$client = new Client(['base_uri' => 'http://httpbin.org']);
		$url    =  'https://api.accurateappend.com/Services/V2/ReverseEmail/15152115-8c3d-4985-ac56-b5df029ec044/';

		$qry = DB::connection('bk')->table('users as u')
									 // ->join('scores as s', 'u.id', '=', 's.user_id')
									 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
									 ->leftjoin('users_without_dob as uwb', function($q){
									 	$q->on('uwb.user_id', '=', 'u.id');
									 	$q->on('uwb.address', '=', DB::raw(1));
									 })	

									 ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')

									 ->whereNull('cc.id')
								     ->whereNull('uwb.id')
									 ->where(function($q){
									 	$q->WhereNull('u.address')
									 	  ->WhereNull('nu.address');
									 })
									 // ->where(function($q){
									 // 	$q->orWhereNotNull('s.hs_gpa')
									 // 	  ->orWhereNotNull('s.overall_gpa')
									 // 	  ->orWhereNotNull('s.weighted_gpa');
									 // })
									 ->where('u.email', '!=', 'none')
									 ->where('u.is_organization', 0)
									 ->where('u.is_university_rep', 0)
									 
									 ->where('u.is_counselor', 0)
									 ->where('u.is_aor', 0)
									 // ->where('u.email', 'NOT LIKE', '%els.edu%')
									 // ->where('u.email', 'NOT LIKE', '%shorelight%')
									 ->where('u.id', '!=', 1024184)
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', 1)

									 ->where('u.created_at', '<', $two_days_ago)
									 ->groupBy('u.id')
									 ->orderBy('u.created_at', 'DESC')
									 ->orderBy(DB::raw("RAND()"))
									 ->take(100)
									 ->select('u.id as user_id', 'u.fname', 'u.lname', 'u.email', 'u.created_at')
									 ->get();

		$cnt = 0;
		$error_cnt = 0;
		$not_found_cnt = 0;

		foreach ($qry as $key) {

		  	$params = array();
		  	$params['firstname'] = $key->fname;
		  	$params['lastname']  = $key->lname;
		  	$params['emailaddress'] = $key->email; 
		  	// $params['firstname'] = "reza";
		  	// $params['lastname']  = "shayesteh";
		  	// $params['emailaddress'] = "reza.shayesteh@gmail.com"; 
		  	try{
			  	$response = $client->request('GET', $url, [
			        'query' => $params]);
				
				$ret = $response->getBody()->getContents();

				$ret = json_decode($ret);
			} catch (\Exception $e) {
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'response' => json_encode($e), "accurateappend" => 1);
				UsersWithoutDob::updateOrCreate($attr, $val);
				$error_cnt++;
				continue;
			}
	
			if (isset($ret->Records[0]) && !empty($ret->Records[0])) {

				// $this->customdd($ret);
				// exit();
				$records = $ret->Records[0];
				$address = $records->Address;
				$city    = $records->City;
				$state   = $records->State;
				$zip     = $records->PostalCode;


				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => ucwords(strtolower($address)), 
							  'city' => ucwords(strtolower($city)), 'state' => $state, 'zip' => $zip, 'is_manual' => 1);

				$nrccua_tmp = NrccuaUser::where('user_id', $key->user_id)->first();
				if (isset($nrccua_tmp)) {
					isset($nrccua_tmp->gender) ? $val['gender'] = strtolower($nrccua_tmp->gender) : null;
					isset($nrccua_tmp->birth_date)  ? $val['birth_date']  = $nrccua_tmp->birth_date : null;
					$del_nrccua = NrccuaUser::where('user_id', $key->user_id)->delete();
				}
				
				NrccuaUser::updateOrCreate($attr, $val);

				$cnt++;
			}else{
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'response' => json_encode($ret), 'accurateappend' => 1);
				UsersWithoutDob::updateOrCreate($attr, $val);
				$not_found_cnt++;
			}
		}
		Cache::put( env('ENVIRONMENT') .'_'. 'is_getAddressUsingAccurateAppend', 'done', 10);
		return "Number of matches found: ". $cnt. " Number of error_cnt: ". $error_cnt. " Number of not_found_cnt: ". $not_found_cnt;	
	}

	// Append user info using Ebureau
	public function getAddressUsingEbureau(){
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_getAddressUsingEbureau')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_getAddressUsingEbureau');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_getAddressUsingEbureau', 'in_progress', 30);

		$two_days_ago = Carbon::now()->subDays(2);

		$client = new Client(['base_uri' => 'http://httpbin.org']);
		$url    =  'https://factory.ebureau.com/production/';

		$qry = DB::connection('bk')->table('users as u')
									 // ->join('scores as s', 'u.id', '=', 's.user_id')
									 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
									 ->leftjoin('users_without_dob as uwb', function($q){
									 	$q->on('uwb.user_id', '=', 'u.id');
									 	$q->on('uwb.address', '=', DB::raw(1));
									 })	

									 ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')

									 ->whereNull('cc.id')
								     ->whereNull('uwb.id')
									 ->where(function($q){
									 	$q->WhereNull('u.address')
									 	  ->WhereNull('nu.address');
									 })
									 // ->where(function($q){
									 // 	$q->orWhereNotNull('s.hs_gpa')
									 // 	  ->orWhereNotNull('s.overall_gpa')
									 // 	  ->orWhereNotNull('s.weighted_gpa');
									 // })
									 ->where('u.email', '!=', 'none')
									 ->where('u.is_organization', 0)
									 ->where('u.is_university_rep', 0)
									 
									 ->where('u.is_counselor', 0)
									 ->where('u.is_aor', 0)
									 ->where('u.email', 'NOT LIKE', '%els.edu%')
									 ->where('u.email', 'NOT LIKE', '%shorelight%')
									 ->where('u.email', 'NOT LIKE', '%test%')
									 ->where('u.id', '!=', 1024184)
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', 1)

									 ->where('u.created_at', '<', $two_days_ago)
									 ->groupBy('u.email')
									 // ->orderBy('u.created_at', 'DESC')
									 ->orderBy(DB::raw("RAND()"))
									 ->take(10)
									 ->select('u.id as user_id', 'u.fname', 'u.lname', 'u.email', 'u.phone', 'u.created_at',
											   'u.city', 'u.state', 'u.zip')
									 ->get();

		$cnt = 0;
		$error_cnt = 0;
		$not_found_cnt = 0;

		$tp = new TrackingPage;

		foreach ($qry as $key) {

			$params = array();
			$params['login'] = array();
			
			$tmp = array();
			$tmp['userid']   = env('EBUREAU_UN');
			$tmp['password'] = env('EBUREAU_PASS');
			$tmp['realtime'] = 1;
			$tmp['protocol'] = 1;
			$tmp['timeout']  = 10;
			$tmp['sid']      = 'xtech:append:entity:1';
			$tmp['output']   = 'full';

			$params['login'] = $tmp;

			$params['transaction'] = array();
			$params['transaction']['input'] = array();
			$params['transaction']['input']['account'] = array();

			$tmp = array();
			$tmp['account'] = $key->user_id;
			$params['transaction']['input']['account'] = $tmp;

			$params['transaction']['input']['data'] = array();
			$tmp = array();
			$tmp['first'] = $key->fname;
			$tmp['last']  = $key->lname;
			$tmp['email']  = $key->email;
			isset($key->city) ? $tmp['city'] = $key->city : NULL;
			isset($key->state) ? $tmp['state'] = $key->state : NULL;
			isset($key->zip) ? $tmp['zip'] = $key->zip : NULL;

			if (!isset($tmp['zip'])) {
				$tp_tmp = $tp->getLastLogForThisUserId($key->user_id);
				if (isset($tp_tmp)) {
					$tmp_ip_lookup = $this->iplookup($tp_tmp->ip);
					
					isset($tmp_ip_lookup['cityName']) ? $tmp['city'] = $tmp_ip_lookup['cityName'] : NULL;
					isset($tmp_ip_lookup['stateAbbr']) ? $tmp['state'] = $tmp_ip_lookup['stateAbbr'] : NULL;
					isset($tmp_ip_lookup['cityAbbr']) ? $tmp['zip'] = $tmp_ip_lookup['cityAbbr']: NULL;
				}
			}

			if (isset($key->phone)) {
				$key->phone = preg_replace('/\s+/', '', $key->phone);
				if (isset($key->phone) && !empty($key->phone)) {
					$tmp['phone'] = $key->phone;
				}
			}

		    // $tmp['first']  = "reza";
			// $tmp['last']   = "shayesteh";
			// $tmp['email']  = "reza.shayesteh@gmail.com"; 

		  	$params['transaction']['input']['data'] = $tmp;

		  	// creating object of SimpleXMLElement
			$xml_data = new SimpleXMLElement('<?xml version="1.0"?><root></root>');

			// function call to convert array to xml
			$this->array_to_xml($params,$xml_data);
			$ret = $xml_data->asXML();

		  	try{

				$response = $client->post($url, ['body' => $ret]);

				$ret_resp = (string)$response->getBody();
				$xml  = simplexml_load_string($ret_resp, "SimpleXMLElement", LIBXML_NOCDATA);
				$json = json_encode($xml);
				$res  = json_decode($json,TRUE);
				// $ret_resp = json_decode($ret_resp);
				
			} catch (\Exception $e) {
				// $this->customdd($e);
				// exit();

				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'ebureau_response' => json_encode($e), "ebureau" => 1);
				UsersWithoutDob::updateOrCreate($attr, $val);
				$error_cnt++;
				continue;
			}

			if (isset($res['transaction']['output']) && !empty($res['transaction']['output'])) {

				$user = User::on('bk')
				            ->where('id', $key->user_id)
				            ->select('state')
				            ->first();
				
				$output = $res['transaction']['output'];

				if (!isset($output['address'][0])) {

					$records = $output['address'];
					$address = $records['address'];
					$city    = $records['city'];
					$state   = $records['state'];
					$zip     = isset($records['zip']) ? substr($records['zip'], 0, 5) : NULL;

				}else{
					for ($i=0; $i < count($output['address']); $i++) { 

						$records = $output['address'][$i];
						
						if (!isset($records['address']) || !isset($records['state']) || 
							!isset($records['city']) || !isset($records['city'])) {
							continue;
						}
						

						$address = $records['address'];
						$city    = $records['city'];
						$state   = $records['state'];
						$zip     = isset($records['zip']) ? substr($records['zip'], 0, 5) : NULL;

						break;
					}		
				}
				
				if (isset($user->state)) {
					for ($i=1; $i < count($output['address']); $i++) { 
						$records = $output['address'][$i];

						$state   = $records['state'];
						$state   = $state[$i];

						if ($state == $user->state) {
							$address = $records['address'];
							$city    = $records['city'];
							$state   = $records['state'];
							$zip     = isset($records['zip']) ? substr($records['zip'], 0, 5) : NULL;

							// dd($address . " ---- ". $city.  " ---- ". $state . " ---- ". $user_id);
							break;
						}
					}		
				}



				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => ucwords(strtolower($address)), 
							  'city' => ucwords(strtolower($city)), 'state' => $state, 'zip' => $zip, 'is_manual' => 1);

				// $this->customdd($val);
				// exit();
				$nrccua_tmp = NrccuaUser::where('user_id', $key->user_id)->first();
				if (isset($nrccua_tmp)) {
					isset($nrccua_tmp->gender) ? $val['gender'] = strtolower($nrccua_tmp->gender) : null;
					isset($nrccua_tmp->birth_date)  ? $val['birth_date']  = $nrccua_tmp->birth_date : null;
					$del_nrccua = NrccuaUser::where('user_id', $key->user_id)->delete();
				}
				
				NrccuaUser::updateOrCreate($attr, $val);


				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'ebureau_response' => json_encode($res), 
					'ebureau' => -1);
				UsersWithoutDob::updateOrCreate($attr, $val);

				$cnt++;
			}else{
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'ebureau_response' => json_encode($res), 'ebureau' => 1);
				UsersWithoutDob::updateOrCreate($attr, $val);
				$not_found_cnt++;
			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_getAddressUsingEbureau', 'done', 30);
		return "Number of matches found: ". $cnt. " Number of error_cnt: ". $error_cnt. " Number of not_found_cnt: ". $not_found_cnt;	
	}

	public function ebureauXMLParser(){
		// $xml = \XmlParser::load(storage_path('xmlFiles/ebureau_3_16_test.xml'));
		// $user = $xml->parse([
		//     'output' => ['uses' => 'transaction.output.address[rank,first,last,address,city,state,zip]'],
		//     'status' => ['uses' => 'transaction.input.account[fileid,account,serial]'],
		//     'test'   => ['uses' => 'transaction.output.address.rank'],
		// ]);
		$str = file_get_contents(storage_path('xmlFiles/ebureau_list_3_21_raw_file.xml'));
		$xml=simplexml_load_string($str);
		// dd(31231);
		$cnt = 0;
		foreach ($xml as $key) {

			if ($cnt < 10000) {
				$cnt++;
				continue;
			}
			if ($cnt == 15000) {
				break;
			}
			$user_id = (array)($key->input->account->account);
			$user_id = $user_id[0];
			if (isset($key->output->address)) {

				$user = User::on('bk')->where('id', $user_id)->first();

				$address = $key->output->address->address;
				$address = ucwords(strtolower($address[0]));

				$city    = $key->output->address->city;
				$city    = ucwords(strtolower($city[0]));

				$state   = $key->output->address->state;
				$state   = $state[0];

				$zip     = $key->output->address->zip;
				$zip     = substr($zip[0], 0, 5);

				if (isset($user->state)) {
					for ($i=1; $i < count($key->output->address->address); $i++) { 
						$state   = $key->output->address->state;
						$state   = $state[$i];

						if ($state == $user->state) {
							$address = $key->output->address->address;
							$address = ucwords(strtolower($address[$i]));

							$city    = $key->output->address->city;
							$city    = ucwords(strtolower($city[$i]));

							$state   = $key->output->address->state;
							$state   = $state[$i];

							$zip     = $key->output->address->zip;
							$zip     = substr($zip[$i], 0, 5);

							// dd($address . " ---- ". $city.  " ---- ". $state . " ---- ". $user_id);
							break;
						}
					}		
				}

				$attr = array('user_id' => $user_id);
				$val  = array('user_id' => $user_id, 'address' => $address, 'city' => $city, 
							  'state' => $state, 'zip' => $zip, 'is_manual' => 1);


				// $nrccua_tmp = NrccuaUser::where('user_id', $key->user_id)
				// 						->whereNull('address')
				//                         ->first();
				// if (isset($nrccua_tmp)) {
				// 	isset($nrccua_tmp->gender) ? $val['gender'] = strtolower($nrccua_tmp->gender) : null;
				// 	isset($nrccua_tmp->birth_date)  ? $val['birth_date']  = $nrccua_tmp->birth_date : null;
				// 	$del_nrccua = NrccuaUser::where('user_id', $key->user_id)->delete();
				// }


				NrccuaUser::updateOrCreate($attr, $val);

				$uwd = UsersWithoutDob::where('user_id', $user_id)->first();
				$uwd->ebureau = -1;
				$uwd->save();				
			}else{
				$uwd = UsersWithoutDob::where('user_id', $user_id)->first();
				$uwd->ebureau = 1;
				$uwd->save();
			}
			$cnt++;
			// print_r($cnt."<br>");
		}

		return "success";
	}

	// Add new keypath users to Keypath portal on support
	public function addKeypathUsersToSupportAccount(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_addKeypathUsersToSupportAccount')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_addKeypathUsersToSupportAccount');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_addKeypathUsersToSupportAccount', 'in_progress', 100);
		
		$qry = DB::connection('bk')->table('users as u')
									 ->join('recruitment as r', 'r.user_id', '=', 'u.id')
									 ->whereIn('u.financial_firstyr_affordibility', array('5,000 - 10,000', '10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '50,000'))
									 ->where('u.is_plexuss', 0)
									 ->where('u.is_organization', 0)
									 ->where('u.is_ldy', 0)
									 ->where('u.is_university_rep', 0)
									 ->where('u.is_aor', 0)
									 ->where('u.in_college', 1)
									 ->where('u.email', 'not like', DB::raw("'%test%' "))
									 ->where('u.fname', 'not like', DB::raw("'%test%' "))
									 ->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
									 ->whereRaw("u.id in 
												(Select user_id 
												 from objectives o
												 where 
													(major_id in (1359, 1362, 1375, 1395, 1396, 1426, 1454, 1464, 1465, 1361, 1369, 1420, 1467, 385, 395, 460, 1465, 1374, 1362, 1375, 249, 257, 1374, 1465, 37, 39, 41, 758, 1366, 
													 1414, 1418, 1419, 1359, 1429, 1432, 1454, 1364, 183, 207, 209, 1422) or 
													 major_id in (Select id from majors where department_id = 9)
											 	 and degree_type in (4,5))
												 and o.user_id = u.id
											)")
									 ->whereRaw("u.id not in (SELECT r.user_id FROM recruitment as r JOIN recruitment_tags as rt ON(r.user_id = rt.user_id and rt.org_portal_id = 297) WHERE r.college_id = 7916)")
									 ->groupBy('u.id')
									 ->orderBy('u.id', 'DESC')
									 ->take(100)
									 ->select('u.id')
									 ->get();


		foreach ($qry as $key) {

			$rec_qry = Recruitment::on('bk')->where('user_id', $key->id)
											  ->where('college_id', 7916)
											  ->first();

			if (!isset($rec_qry)) {
				$attr = array('user_id' => $key->id, 'college_id' => 7916);
				$val  = array('user_id' => $key->id, 'college_id' => 7916, 'user_recruit' => 1, 'college_recruit' => 0, 
							  'status' => 1, 'type' => 'manual_keypath', 'email_sent' => 1);
				$rec  =  Recruitment::updateOrCreate($attr, $val);
			}else{
				Recruitment::where('id', $rec_qry->id)
  									   ->update(array('email_sent' => 1, 'user_recruit' => 1, 'college_recruit' => 0, 
  									   				  'status' => 1, 'type' => DB::raw("CONCAT(type, ' manual_keypath')")));
			}

			$rt_qry  = RecruitmentTag::where('user_id', $key->id)
								     ->where('college_id', 7916)
								     ->where('aor_id', -1)
								     ->where('org_portal_id', -1)
								     ->where('aor_portal_id', -1)
								     ->first();

			$pu_qry = PrescreenedUser::where('user_id', $key->id)
									 ->where('college_id', 7916)
									 ->whereNull('aor_id')
									 ->whereNull('org_portal_id')
									 ->whereNull('aor_portal_id')
									 ->delete();

			$rva_qry = RecruitmentVerifiedApp::where('user_id', $key->id)
									 ->where('college_id', 7916)
									 ->whereNull('aor_id')
									 ->whereNull('org_portal_id')
									 ->whereNull('aor_portal_id')
									 ->delete();

			$rvh_qry = RecruitmentVerifiedHS::where('user_id', $key->id)
											 ->where('college_id', 7916)
											 ->whereNull('aor_id')
											 ->whereNull('org_portal_id')
											 ->whereNull('aor_portal_id')
											 ->delete();
			if (isset($rt_qry)) {
				$rt_qry->aor_id 	   = NULL;
				$rt_qry->org_portal_id = 297;
				$rt_qry->aor_portal_id = NULL;

				$rt_qry->save();
			}else{
				$rt_qry = new RecruitmentTag;

				$rt_qry->user_id 	   = $key->id;
				$rt_qry->college_id    = 7916;
				$rt_qry->aor_id        = -1;
				$rt_qry->org_portal_id = 297;
				$rt_qry->aor_portal_id = -1;

				$rt_qry->save();
			}
		}
		Cache::put( env('ENVIRONMENT') .'_'. 'is_addKeypathUsersToSupportAccount', 'done', 100);
		return "success";
	}

	public function testTemplateEmail() {

        $reply_email = 'social@plexuss.com';
        $template_name = 'qs_scholarships_template1_copy_04';
        // $email = 'ajay.a@mitlag.com';
        $email = "anthony.shayesteh@plexuss.com";
        $params = array('fname' => 'Full', 'lname' => 'Name', 'email' => $email);
        $mda = new MandrillAutomationController();
        $mda->generalEmailSend($reply_email, $template_name, $params, $email);
    
    }

    // Distribute inquiries to college reps evenly.
    public function distributeInquiriesToAgents(){
    	$college_id = 6731;
    	$org_branch_id = 254;
    	$arr = array(292, 293, 294, 295, 296, 298 );

    	$qry = Recruitment::on('bk')->where('college_id', $college_id)->select('user_id')->get();

    	$arr_cnt = count($arr);

    	$counter = 0;
    	foreach ($qry as $key) {
    		if ($counter > $arr_cnt -1) {
    			$counter = 0;
    		}

    		//print_r("user_id ". $key->user_id. " college_id 6731 org_portal_id ". $arr[$counter] . "<br>");

    		$attr = array("user_id" => $key->user_id, "college_id" => $college_id, "org_portal_id" => $arr[$counter]);
    		$val  = array("user_id" => $key->user_id, "college_id" => $college_id, "org_portal_id" => $arr[$counter]);

    		RecruitmentTag::updateOrCreate($attr, $val);
    		$counter++;
    	}

    	return "success";
    }

    // Send text messages for Zeta
    public function sendZetaTextCampaign($input = NULL){
    	$from_phone = "+12137846254";
    	$msg = "You recently requested information about furthering your education on Plexuss.com . If you like to speak with one of our live college representatives, call us at +1 (213) 784-6254";

    	$tc = new TwilioController();

    	// $input = array();
    	// $input['phone'] = "+13105980347";
    	// $input['user_id'] = 93;

		$data = array();

		$data['from'] = $from_phone;
		$data['to']   = $input['phone'];
		$data['msg']  = $msg;
		$data['campaign_id'] = NULL;
		$data['user_id'] = 463;
		$data['receiver_user_id'] = $input['user_id'];
		$data['smsBy'] = "Plexuss";

		$tc->sendSingleSms($data);

		return "success";
    }

    // Adding the converted users to their respected portals on Support
    public function addUsersToConvertedBucketInSupportAccount($ro_type, $lead_type = NULL){
    	
    	if (!isset($ro_type)) {
    		return "something is wrong here";
    	}
    	
    	$take    = 500;
    	
    	if (Cache::has( env('ENVIRONMENT') .'_'. 'addUsersToConvertedBucketInSupportAccount')) {
    		$cache_arr = Cache::get( env('ENVIRONMENT') .'_'. 'addUsersToConvertedBucketInSupportAccount');
    		$portals = $cache_arr['portals'];
    		$admin_users = $cache_arr['admin_users'];
    	}else{
    		$portal_qry = OrganizationPortal::on('bk')->where('org_branch_id', 1)->get();
	    	$portals = array();
	    	foreach ($portal_qry as $key) {
	    		$portals[strtolower($key->name)] = $key->id;
	    	}
	    	$admin_qry	 = DB::connection('bk')->table('organization_branch_permissions as obp')
	    										 ->join('users as u', 'u.id', '=', 'obp.user_id')
	    										 ->where('obp.organization_branch_id', 1)
	    										 ->where('u.is_plexuss', 1)
	    										 ->select('u.id as user_id')
	    										 ->get();
	    	$admin_users = array();
	    	foreach ($admin_qry as $key) {
	    		$admin_users[] = $key->user_id;
	    	}
	    	$cache_arr = array();
	    	$cache_arr['portals'] = $portals;
	    	$cache_arr['admin_users'] = $admin_users;
	    	Cache::put( env('ENVIRONMENT') .'_'. 'addUsersToConvertedBucketInSupportAccount', $cache_arr, 180);
    	}
    	
    	$qry = DB::connection('bk')->table('revenue_organizations as ro')
    								 ->select('ro.id as ro_id', 'ro.name as ro_name', 'u.id as user_id', 'ro.conversion_on')
    								 ->groupBy('ro_name', 'u.id')
    								 ->where('ro.active', 1)
    								 ->where('ro.id', '!=', 1)
    								 ->where('ro.id', '!=', 38);
    	switch ($ro_type) {
    		case 'click':
    			$qry = $qry->join('ad_redirect_campaigns as arc', 'ro.id', '=', 'arc.ro_id')
    			           ->join('ad_clicks as ac', 'ac.company', '=', 'arc.company')
    					   ->join('users as u', 'u.id', '=', 'ac.user_id')
    					   ->addSelect('ac.id as converted_id', 'ac.paid_client');
    			if (isset($lead_type) && $lead_type == "inquiries") {
    				$qry = $qry->where('ac.pixel_tracked', 0)
    						   ->where('ac.paid_client', 0);
    			}else{
    				$qry = $qry->where('ac.pixel_tracked', 1);
    			}
    			break;
			case 'post':
    			$qry = $qry->join('distribution_responses as ac', 'ac.ro_id', '=', 'ro.id')
    					   ->join('users as u', 'u.id', '=', 'ac.user_id')
    					   ->where('ac.success', 1)
    					   ->addSelect('ac.id as converted_id');
    			break;    		
    		default:
    			exit();
    			break;
    	}

    	// Exclude edx and if recruitment is there, don't run it for inquiries
    	if (isset($lead_type) && $lead_type == "inquiries") {
    		$qry = $qry->where('ro.id', '!=', 6)
    				   ->whereNull('r.id');
    	}

    	$qry = $qry->leftjoin('recruitment as r', function($q){
							 		$q->on('r.user_id', '=', 'u.id');
							 		$q->on('r.college_id', '=', DB::raw(7916));
							 		$q->on('r.ro_id', '=', 'ro.id');
							 })
    			   ->leftjoin('recruitment_revenue_org_relations as rror', function($q){
							 		$q->on('rror.rec_id', '=', 'r.id');
							 		$q->on('rror.related_id', '=', 'ac.id');
							 		$q->on('rror.ro_id', '=', 'ro.id');
							 })
				   ->WhereNull('rror.id')
    			   // ->whereNull('r.id')
    	           ->orderBy('ro.id', 'DESC')
    			   ->orderBy('converted_id', 'ASC')
    			   ->take($take)
    			   ->get();
    	$cnt = 0;
    	
    	foreach ($qry as $key) {
    		// Check to see wheather this is an inquiry or a conversion based on where the client says it's a conversion.
    		if (!isset($lead_type)) {
    			switch ($key->conversion_on) {
    				case 'paid_client':
    					if (isset($key->paid_client) && $key->paid_client == 0) {
    						$lead_type = "inquiries";
    					}
    					break;

    				case 'pixel_tracked':
    					if (isset($key->pixel_tracked) && $key->pixel_tracked == 0) {
    						$lead_type = "inquiries";
    					}
    					break;
    				
    				default:
    					# code...
    					break;
    			}
    		}

    		if (isset($portals[strtolower($key->ro_name)])) {
    			$portal_id = $portals[$key->ro_name];
    		}else{
    			/// Create a new portal
    			$attr = array('org_branch_id' => 1, 'name' => $key->ro_name, 'ro_id' => $key->ro_id);
    			$val  = array('org_branch_id' => 1, 'name' => $key->ro_name, 'ro_id' => $key->ro_id);
    			$op = OrganizationPortal::updateOrCreate($attr, $val);
    			$portal_id = $op->id;
    			$portals[$key->ro_name] = $portal_id;
    			Cache::forget( env('ENVIRONMENT') .'_'. 'addUsersToConvertedBucketInSupportAccount');
    			foreach ($admin_users as $k => $v) {
					$attr = array( 'org_portal_id' => $portal_id, 'user_id' => $v);
					$val  = array( 'org_portal_id' => $portal_id, 'user_id' => $v);
					$opu = OrganizationPortalUser::updateOrCreate($attr, $val);
				}
    		}
    		
    		$rec_qry = Recruitment::on('bk')->where('user_id', $key->user_id)
											  ->where('college_id', 7916)
											  ->where('ro_id', $key->ro_id)
											  ->first();
			if (!isset($rec_qry)) {
				$attr = array('user_id' => $key->user_id, 'college_id' => 7916, 'ro_id' => $key->ro_id);
				(isset($key->paid_client) && $key->paid_client == 1) ? $arr['applied'] = 1 : NULL;
				$val  = array('user_id' => $key->user_id, 'college_id' => 7916, 'user_recruit' => 0, 'college_recruit' => 0, 
							  'status' => 1, 'type' => 'manual_convert', 'email_sent' => 1, 'ro_id' => $key->ro_id);
				
				if (isset($lead_type) && $lead_type == "inquiries") {
					$val['user_recruit'] = 1;
				}
				
				$rec  =  Recruitment::updateOrCreate($attr, $val);
				$rec_id = $rec->id;
			}else{
				$arr = array('email_sent' => 1, 'user_recruit' => 0, 'college_recruit' => 0, 
			   				 'ro_id' => $key->ro_id, 'status' => 1);
				if (!(strpos($rec_qry->type, 'manual_convert') !== true)) {
					$arr['type'] = DB::raw("CONCAT(type, ' manual_convert')");
				}
				(isset($key->paid_client) && $key->paid_client == 1) ? $arr['applied'] = 1 : NULL;
				if (isset($lead_type) && $lead_type == "inquiries") {
					$arr['user_recruit'] = 1;
				}
				Recruitment::where('id', $rec_qry->id)
  									   ->update($arr);
  				$rec_id = $rec_qry->id;
			}
			
			$attr = array('user_id' => $key->user_id, 'college_id' => 7916, 'org_portal_id' => $portal_id);
			$val  = array('user_id' => $key->user_id, 'college_id' => 7916, 'org_portal_id' => $portal_id);
			RecruitmentTag::updateOrCreate($attr, $val);
			
            // Add to RecruitmentConverts if lead_type is not set.
            if (!isset($lead_type)) {
    			$attr = array('rec_id' => $rec_id, 'converted_id' => $key->converted_id);
    			$val  = array('rec_id' => $rec_id, 'converted_id' => $key->converted_id);
    			RecruitmentConvert::updateOrCreate($attr, $val);
            }
			
			$attr = array('related_id' => $key->converted_id, 'ro_id' => $key->ro_id);
			$val  = array('rec_id' => $rec_id, 'related_id' => $key->converted_id, 'ro_id' => $key->ro_id);

			RecruitmentRevenueOrgRelation::updateOrCreate($attr, $val);
			
			$pu_qry = PrescreenedUser::where('user_id', $key->user_id)
									 ->where('college_id', 7916)
									 ->whereNull('aor_id')
									 ->whereNull('org_portal_id')
									 ->whereNull('aor_portal_id')
									 ->delete();
			
			$rva_qry = RecruitmentVerifiedApp::where('user_id', $key->user_id)
									 ->where('college_id', 7916)
									 ->whereNull('aor_id')
									 ->whereNull('org_portal_id')
									 ->whereNull('aor_portal_id')
									 ->delete();

			$rvh_qry = RecruitmentVerifiedHS::where('user_id', $key->user_id)
											 ->where('college_id', 7916)
											 ->whereNull('aor_id')
											 ->whereNull('org_portal_id')
											 ->whereNull('aor_portal_id')
											 ->delete();
			$cnt++;
    	}
    
    	return "Number of inquiries moved: ". $cnt;
    }

    public function addRecommendationsToSupportAccountCronJob(){
    	if (Cache::has( env('ENVIRONMENT') .'_'. 'addRecommendationsToSupportAccountCronJob')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'addRecommendationsToSupportAccountCronJob');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		Cache::put( env('ENVIRONMENT') .'_'. 'addRecommendationsToSupportAccountCronJob', 'in_progress', 30);

		Queue::push( new AddRecommendationsToSupportAccount());

		Cache::put( env('ENVIRONMENT') .'_'. 'addRecommendationsToSupportAccountCronJob', 'done', 30);

		return "done";
    }

    // This queue method adds recommendations to revenue org 
    public function queueAddRecommendationsToSupportAccount(){
    	
    	$main_qry = DB::connection('bk')->table('revenue_organizations as ro')
    									->join('organization_portals as op', 'ro.id', '=', 'op.ro_id')
    								 ->select('ro.id as ro_id', 'ro.name as ro_name', 'ro.num_of_rec', 'op.id as portal_id')
    								 ->groupBy('ro_name')
    								 ->where(function($q){
    								 	$q->orWhere('ro.active', '=', DB::raw(1));
    								 	$q->orWhere('ro.id', '=', DB::raw(10));
    								 	$q->orWhere('ro.id', '=', DB::raw(31));
    								 })
    								 ->where('ro.id', '!=', 1)
    								 ->where('ro.id', '!=', 6)
    								 ->get();

    	
    	$cnt = 0;

    	foreach ($main_qry as $key) {
    		
    		$qry = DB::connection('bk')->table('users as u')
    								   ->select('u.id as user_id')
    								   ->whereNotNull('u.phone')
                                       ->where('u.phone', '!=', ' ');

    		$res = $this->addCustomFiltersForRevenueOrgs($qry, $key->ro_name);
    		if (isset($res['status']) && $res['status'] == "success") {
    			$qry = $res['qry'];
    		}else{
    			continue;
    		}

    		// Keypath orderby
    		if ($key->ro_id == 10) {
    			$qry = $qry->orderByRaw("case when (u.country_id = 5 OR u.country_id= 63 OR u.country_id= 68 OR u.country_id= 102 OR  u.country_id= 123 OR  u.country_id= 147 OR  u.country_id= 158 OR  u.country_id= 159 OR  u.country_id= 218) then 1 else 2 end");
    		}else{
    			$qry = $qry->orderBy(DB::raw("RAND()")); 
    		}

    		$qry = $qry->leftjoin('recruitment as r', function($q) use($key){
							 		$q->on('r.user_id', '=', 'u.id');
							 		$q->on('r.college_id', '=', DB::raw(7916));
							 		$q->on('r.ro_id', '=', DB::raw($key->ro_id));
							 })
    			   ->leftjoin('recruitment_revenue_org_relations as rror', function($q) use($key){
							 		$q->on('rror.rec_id', '=', 'r.id');
							 		$q->on('rror.ro_id', '=', DB::raw($key->ro_id));
							 })
				   ->WhereNull('rror.id')
    			   ->whereNull('r.id')
    	           ->where('u.email', '!=', 'none')
				   ->where('u.is_organization', 0)
				   ->where('u.is_university_rep', 0)
				 
				   ->where('u.is_counselor', 0)
				   ->where('u.is_aor', 0)
				   ->where('u.email', 'NOT LIKE', '%els.edu%')
				   ->where('u.email', 'NOT LIKE', '%shorelight%')
				   ->where('u.email', 'NOT LIKE', '%test%')
				   ->where('u.id', '!=', 1024184)
				   ->where('u.is_ldy', 0)
				 
    	           ->take($key->num_of_rec)
    			   ->get();

    		foreach ($qry as $k) {
	    		$rec_qry = Recruitment::on('bk')->where('user_id', $k->user_id)
												  ->where('college_id', 7916)
												  ->where('ro_id', $key->ro_id)
												  ->first();
				if (!isset($rec_qry)) {
					$attr = array('user_id' => $k->user_id, 'college_id' => 7916, 'ro_id' => $key->ro_id);
					
					$val  = array('user_id' => $k->user_id, 'college_id' => 7916, 'user_recruit' => 1, 'college_recruit' => 0, 
								  'status' => 1, 'type' => 'manual_recommendations', 'email_sent' => 1, 'ro_id' => $key->ro_id);
					
					$rec  =  Recruitment::updateOrCreate($attr, $val);
					$rec_id = $rec->id;
				}else{
					$arr = array('email_sent' => 1, 'user_recruit' => 0, 'college_recruit' => 0, 
				   				 'ro_id' => $key->ro_id, 'status' => 1);
					if (!(strpos($rec_qry->type, 'manual_recommendations') !== true)) {
						$arr['type'] = DB::raw("CONCAT(type, ' manual_recommendations')");
					}
					
					Recruitment::where('id', $rec_qry->id)
	  									   ->update($arr);
	  				$rec_id = $rec_qry->id;
				}
				
				$attr = array('user_id' => $k->user_id, 'college_id' => 7916, 'org_portal_id' => $key->portal_id);
				$val  = array('user_id' => $k->user_id, 'college_id' => 7916, 'org_portal_id' => $key->portal_id);
				RecruitmentTag::updateOrCreate($attr, $val);
				
				
				$attr = array('rec_id' => $rec_id, 'ro_id' => $key->ro_id);
				$val  = array('rec_id' => $rec_id, 'ro_id' => $key->ro_id);

				RecruitmentRevenueOrgRelation::updateOrCreate($attr, $val);
				
				$pu_qry = PrescreenedUser::where('user_id', $k->user_id)
										 ->where('college_id', 7916)
										 ->whereNull('aor_id')
										 ->whereNull('org_portal_id')
										 ->whereNull('aor_portal_id')
										 ->delete();
				
				$rva_qry = RecruitmentVerifiedApp::where('user_id', $k->user_id)
										 ->where('college_id', 7916)
										 ->whereNull('aor_id')
										 ->whereNull('org_portal_id')
										 ->whereNull('aor_portal_id')
										 ->delete();

				$rvh_qry = RecruitmentVerifiedHS::where('user_id', $k->user_id)
												 ->where('college_id', 7916)
												 ->whereNull('aor_id')
												 ->whereNull('org_portal_id')
												 ->whereNull('aor_portal_id')
												 ->delete();
			}

			$cnt++;
    	}
    
    	return "Number of revenue_organizations recommendation added: ". $cnt;
    }


    public function addRoId(){
    	$qry = RecruitmentRevenueOrgRelation::whereNull('ro_id')->get();

    	foreach ($qry as $key) {
    		$tmp_qry = Recruitment::on('bk')->where('id', $key->rec_id)->select('ro_id')->first();
    		$key->ro_id = $tmp_qry->ro_id;
    		$key->save();

    		// dd(131231);
    	}
    }

    // Append users for white pages
    public function getAddressUsingWhitepages(){
    	if (Cache::has( env('ENVIRONMENT') .'_'. 'is_getAddressUsingWhitepages')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_getAddressUsingWhitepages');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_getAddressUsingWhitepages', 'in_progress', 30);

		$two_days_ago = Carbon::now()->subDays(2);

		$client = new Client(['base_uri' => 'http://httpbin.org']);
		$url    =  'https://proapi.whitepages.com/3.0/person';

		$qry = DB::connection('bk')->table('users as u')
									 // ->join('scores as s', 'u.id', '=', 's.user_id')
									 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'u.id')
									 ->join('users_without_dob as uwb', function($q){
									 	$q->on('uwb.user_id', '=', 'u.id');
									 	$q->on('uwb.address', '=', DB::raw(1));
									 	$q->on('uwb.ebureau', '=', DB::raw(1));
									 })	

									 ->leftjoin('country_conflicts as cc', 'cc.user_id', '=', 'u.id')

									 ->whereNull('cc.id')
									 ->where(function($q){
									 	$q->WhereNull('u.address')
									 	  ->WhereNull('nu.address');
									 })
									 // ->where(function($q){
									 // 	$q->orWhereNotNull('s.hs_gpa')
									 // 	  ->orWhereNotNull('s.overall_gpa')
									 // 	  ->orWhereNotNull('s.weighted_gpa');
									 // })
									 ->where('u.email', '!=', 'none')
									 ->where('u.is_organization', 0)
									 ->where('u.is_university_rep', 0)
									 
									 ->where('u.is_counselor', 0)
									 ->where('u.is_aor', 0)
									 ->where('u.email', 'NOT LIKE', '%els.edu%')
									 ->where('u.email', 'NOT LIKE', '%shorelight%')
									 ->where('u.email', 'NOT LIKE', '%test%')
									 ->where('u.id', '!=', 1024184)
									 ->where('u.is_ldy', 0)
									 ->where('u.country_id', 1)

									 ->where('uwb.whitepages', 0)
									 // ->where('u.id', 399950)

									 ->where('uwb.created_at', '<', $two_days_ago)
									 ->groupBy('u.email')
									 // ->orderBy('u.created_at', 'DESC')
									 ->orderBy(DB::raw("RAND()"))
									 ->take(11)
									 ->select('uwb.id as uwb_id', 'u.id as user_id', 'u.fname', 'u.lname', 'u.email', 'u.phone', 'u.created_at',
											   'u.city', 'u.state', 'u.zip')
									 ->get();

		$cnt = 0;
		$error_cnt = 0;
		$not_found_cnt = 0;

		foreach ($qry as $key) {

			$tp = TrackingPage::on('bk-log')->where('user_id', $key->user_id)->first();

			if (!isset($tp->ip)) {
				continue;
			}
			$arr = $this->iplookup($tp->ip);

			isset($arr['cityName'])  ? $city   = $arr['cityName']  : $city = '';
			isset($arr['cityAbbr'])  ? $zip    = $arr['cityAbbr']  : $zip = '';
			isset($arr['stateAbbr']) ? $state  = $arr['stateAbbr'] : $state = '';

			$ret = array();

			$ret['api_key'] 		     = '3045b573fd3445249983a2963dd565c5';
			$ret['name']    			 = ucwords(strtolower($key->fname)) . ' ' .  ucwords(strtolower($key->lname));
			$ret['address.city'] 		 = $city;
			$ret['address.postal_code']  = $zip;
			$ret['address.state_code']   = $state;
			$ret['address.country_code'] = 'US'; 

			$ret = http_build_query($ret);
			$url = $url."?".$ret;

			// dd($url);
			// $response = $client->request('GET', $url);
			// $ret_resp = json_decode($response->getBody()->getContents(), true);

			// dd($ret_resp);
		  	try{

				$response = $client->request('GET', $url);
				$ret_resp = json_decode($response->getBody()->getContents(), true);

				// $xml  = simplexml_load_string($ret_resp, "SimpleXMLElement", LIBXML_NOCDATA);
				// $json = json_encode($xml);
				// $res  = json_decode($json,TRUE);

				// $this->customdd($ret_resp);			
				// exit();
			} catch (\Exception $e) {
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'whitepages_response' => json_encode($e), "whitepages" => 1);
				UsersWithoutDob::updateOrCreate($attr, $val);
				$error_cnt++;
				continue;
			}

			if (isset($ret_resp['person'][0]['found_at_address'])){
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 
							  'address' => ucwords(strtolower($ret_resp['person'][0]['found_at_address']['street_line_1'])), 
							  'city' => ucwords(strtolower($ret_resp['person'][0]['found_at_address']['city'])), 'state' => $state, 
							  'zip' => $ret_resp['person'][0]['found_at_address']['postal_code'], 'is_manual' => 1);

				isset($ret_resp['person'][0]['phones'][0]['phone_number']) ? $val['phone'] = $ret_resp['person'][0]['phones'][0]['phone_number'] : NULL;
				if (isset($ret_resp['person'][0]['gender'])) {
					if ($ret_resp['person'][0]['gender'] == 'Male') {
						$val['gender'] = 'm';
					}else{
						$val['gender'] = 'f';
					}
				}
				// $this->customdd($val);
				// exit();
				$nrccua_tmp = NrccuaUser::where('user_id', $key->user_id)->first();
				if (isset($nrccua_tmp)) {
					isset($nrccua_tmp->gender) ? $val['gender'] = strtolower($nrccua_tmp->gender) : null;
					isset($nrccua_tmp->birth_date)  ? $val['birth_date']  = $nrccua_tmp->birth_date : null;
					$del_nrccua = NrccuaUser::where('user_id', $key->user_id)->delete();
				}
				
				NrccuaUser::updateOrCreate($attr, $val);


				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'whitepages_response' => json_encode($ret_resp), 
					'whitepages' => -1);
				UsersWithoutDob::updateOrCreate($attr, $val);

				$cnt++;
			}else{
				$attr = array('user_id' => $key->user_id);
				$val  = array('user_id' => $key->user_id, 'address' => 1, 'whitepages_response' => json_encode($ret_resp), 'whitepages' => 1);
				UsersWithoutDob::updateOrCreate($attr, $val);
				$not_found_cnt++;
			}
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_getAddressUsingWhitepages', 'done', 30);
		return "Number of matches found: ". $cnt. " Number of error_cnt: ". $error_cnt. " Number of not_found_cnt: ". $not_found_cnt;	
    }

    public function autoUploadLogoCollege(){
		
		$take = 10;

		$colleges = College::on('rds1')->select('id','school_name', 'slug', 'logo_url')
										   ->where(function($q){
										   		$q->orWhereNull('logo_url')
										   		  ->orWhere('logo_url', '=', DB::raw("''"));
										   })
										   ->where('verified', 1)
										   ->orderByRaw("RAND()")
										   ->take($take)
										   ->get();

		try 
		{
			

			if(isset($colleges) && !empty($colleges))
			{
				foreach ($colleges as $college) 
				{
						
						$url = "https://api.cognitive.microsoft.com/bing/v7.0/images/search";
						$client = new Client(['base_uri' => 'http://httpbin.org']);
						$query = $college->school_name.' logo';
						
						try {
							$response = $client->request('GET', $url, ['headers' => [
						        'Ocp-Apim-Subscription-Key'	=> '6df34635ac4f4b42a9392a4036dea783'],
						        'query' => [
								'q' => $query,
							    'count' => '20',
							    'offset' => '0',
							    'mkt' => 'en-us',
							    'safeSearch' => 'Moderate',
							    'size' => 'Medium']]);
						
							$imgQuery = json_decode($response->getBody()->getContents(), true)['value'];

							if(is_array($imgQuery) && !empty($imgQuery))
							{		
								$check = false;
								for ($i=0; $i < count($imgQuery) ; $i++) { 
									if ($check == true) {
										break;
									}
									if($this->remote_file_exists($imgQuery[$i]['contentUrl'])){
									    $img_data = file_get_contents($imgQuery[$i]['contentUrl']);
									    $check = true;
									}else{
										$check = false;
									}
									
								}
						        $base64 = base64_encode($img_data);
			
        						$imageDir = storage_path().'/img/';        						

        						$filename = $college->id. "_". basename($imgQuery[0]['contentUrl']);
        						// $filename = $college->id. "_". $college->slug;

						        $filePath =  $this->saveBase64ImagePng($base64, $imageDir, $filename);


        							$bucket_url = 'college/logos/'.$filename; 
									$s3 = AWS::createClient('s3');
									$s3->putObject(array(
										'ACL' => 'public-read',
										'Bucket' => 'asset.plexuss.com',
										'Key' => $bucket_url,
										'SourceFile' => $filePath,
									));
									
									$collegeUpd = College::where('id',$college->id)->first();
									$collegeUpd->logo_url = $filename;
									$collegeUpd->save();

									$uploadLog = new CollegeLogoUploadLog();
									$uploadLog->college_id = $college->id;
									$uploadLog->logo_url = $filename;
									$uploadLog->url_found = $imgQuery[0]['contentUrl'];
									$uploadLog->save();

        							unlink($filePath);
									
									echo 'Logo '.$filename.' Uploaded to college '.$college->school_name. "<br/>";
								
							}
						} catch (\Exception $e) {
							print_r("Bad 1");
						}
				}
			}
		} catch (\Exception $e) {
			print_r("Bad 2");
		}
	}

	public function autoUploadCollegeOverviewImage(){
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_autoUploadCollegeOverviewImage')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_autoUploadCollegeOverviewImage');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_autoUploadCollegeOverviewImage', 'in_progress', 30);

		$take = 10;

		$arr = array('[school] campus pictures', '[school] pictures', '[school] photos', '[school] pics', 'photos of [school]');

		$colleges = DB::connection('rds1')->table('colleges as c')
										  ->leftjoin('countries as country', 'c.country_id', 'country.id')
										  ->leftjoin('college_overview_images as coi', function($q){
										  			$q->on('c.id', '=', 'coi.college_id');
										  			$q->on('coi.is_video', '=', DB::raw(0));
										  			$q->on('coi.is_tour', '=', DB::raw(0));
										  			$q->on('coi.is_youtube', '=', DB::raw(1));
										  })
										  ->whereNull('coi.id')
										  ->where('c.verified', 1)
										  ->orderByRaw("RAND()")
										  ->select('c.school_name', 'c.state', 'country.country_name as country_name', 'c.id as college_id')
										  ->take(10)
										  ->get();

		$ret = array();
		try {
			if(isset($colleges) && !empty($colleges)){
				foreach ($colleges as $college) 
				{
					
					$url = "https://api.cognitive.microsoft.com/bing/v7.0/images/search";
					$client = new Client(['base_uri' => 'http://httpbin.org']);
					
					$str = $arr[rand(0, count($arr) - 1)];
					if ($college->country_name == "United States") {
						$query = str_replace("[school]", $college->school_name. ' in '. $college->state, $str);
					}else{
						$query = str_replace("[school]", $college->school_name. ' in '. $college->country_name, $str);	
					}
					
					try {
						$response = $client->request('GET', $url, ['headers' => [
					        'Ocp-Apim-Subscription-Key'	=> '6df34635ac4f4b42a9392a4036dea783'],
					        'query' => [
							'q' => $query,
						    'count' => '20',
						    'offset' => '0',
						    'mkt' => 'en-us',
						    'safeSearch' => 'Moderate',
						    'licence' => 'public',
						    'size' => 'large']]);
					
						$imgQuery = json_decode($response->getBody()->getContents(), true)['value'];

						if(is_array($imgQuery) && !empty($imgQuery)){	
							
							$counter = 0;
							for ($i=0; $i < count($imgQuery) ; $i++) { 
								if ($counter == 5) {
									break;
								}
								if($this->remote_file_exists($imgQuery[$i]['contentUrl'])){
								    $img_data = file_get_contents($imgQuery[$i]['contentUrl']);
								}else{
									continue;
								}
								
							
						        $base64 = base64_encode($img_data);
			
	    						$imageDir = storage_path().'/img/';        						

	    						$filename = $college->college_id. "_". str_replace(" ", "_", $college->school_name) . "_". basename($imgQuery[$i]['contentUrl']);
	    						// $filename = $college->id. "_". $college->slug;

						        $originalFilePath =  $this->saveBase64ImagePng($base64, $imageDir, "original_" . $filename);
						        list($width, $height) = getimagesize($originalFilePath);

						        if ($width < 831 || $height < 381) {
						        	continue;
						        }

						        $image_resize = Image::make($originalFilePath); 
						                
							    $image_resize->resize(831, 381);
							    $filePath = storage_path().'/img/' .$filename;
							    $image_resize->save($filePath);


						        $image_resize = Image::make($originalFilePath); 
						                
							    $image_resize->resize(420, 300);
							    $filePath_carousel = storage_path().'/img/carousel/' .$filename;
							    $image_resize->save($filePath_carousel);

								$bucket_url = 'college/overview_images/'.$filename; 
								$s3 = AWS::createClient('s3');
								$s3->putObject(array(
									'ACL' => 'public-read',
									'Bucket' => 'asset.plexuss.com',
									'Key' => $bucket_url,
									'SourceFile' => $filePath,
								));


								$bucket_url = 'college/overview_images/carousel_images/'.$filename; 
								$s3 = AWS::createClient('s3');
								$s3->putObject(array(
									'ACL' => 'public-read',
									'Bucket' => 'asset.plexuss.com',
									'Key' => $bucket_url,
									'SourceFile' => $filePath_carousel,
								));
								
								$collegeUpd = new CollegeOverviewImages;
								$collegeUpd->url = $filename;
								$collegeUpd->college_id = $college->college_id;
								$collegeUpd->save();

								$uploadLog = new CollegeLogoUploadLog();
								$uploadLog->college_id = $college->college_id;
								$uploadLog->logo_url = $filename;
								$uploadLog->url_found = $imgQuery[$i]['contentUrl'];
								$uploadLog->type = "overview_img";
								$uploadLog->save();

								unlink($originalFilePath);
								unlink($filePath);
								unlink($filePath_carousel);

								$counter++;
							}
							
							// echo 'Logo '.$filename.' Uploaded to college '.$college->school_name. "<br/>";
							$ret[] = $college->school_name;
						}
					} catch (\Exception $e) {
						print_r("Bad 1");
					}
				}
			}
		} catch (\Exception $e) {
			print_r("Bad 2");
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_autoUploadCollegeOverviewImage', 'done', 30);

		return json_encode($ret);
	}

	public function setUsersPortalEmailEffortLogsDateId(){

		$today = Carbon::today();

		$check_for_tracking_id = TrackingPageId::on('bk-log')->where('date', $today->toDateString())->first();

		if (isset($check_for_tracking_id)) {
			return "nope";
		}

		$qry = UsersPortalEmailEffortLog::on('rds1')->where('created_at', '>=', $today)->first();

		$attr = array('date' => $today->toDateString(), 'timestamp' => $today, 'upeel_id' => $qry->id);
		$val  = array('date' => $today->toDateString(), 'timestamp' => $today, 'upeel_id' => $qry->id);

		UsersPortalEmailEffortLogsDateId::updateOrCreate($attr, $val);


		$qry = PartnerEmailLog::on('rds1')->where('created_at', '>=', $today)->first();

		$attr = array('date' => $today->toDateString(), 'timestamp' => $today, 'pel_id' => $qry->id);
		$val  = array('date' => $today->toDateString(), 'timestamp' => $today, 'pel_id' => $qry->id);

		PartnerEmailLogsDateId::updateOrCreate($attr, $val);	


		$qry = TrackingPage::on('bk-log')->where('created_at', '>=', $today)->first();

		$has_tracking_id = TrackingPageId::on('bk-log')->where('timestamp', $today)->first();

		if (!isset($has_tracking_id)) {
			$tmp = new TrackingPageId;
			$tmp->setConnection('log');

			$tmp->tp_id = $qry->id;
			$tmp->timestamp = $today;
			$tmp->date = $today->toDateString();

			$tmp->save();
		}
		
		return "success";
	}

	public function getListForInfoGroup(){
		$qry = DB::connection('rds1')->table('users_without_dob as uwd')
									 ->leftjoin('nrccua_users as nu', 'nu.user_id', '=', 'uwd.user_id')
									 ->leftjoin('users as u', 'u.id', '=', 'uwd.user_id')
									 ->whereRaw('(u.address is null or u.address= "")')
									 ->whereRaw('(nu.address is null or nu.address= "")')
									 ->where('uwd.ebureau', 1)
									 ->whereNotNull('u.id')
									 ->groupBy('u.id')
									 ->where('u.id', '>', 1216267)
									 ->select('u.id as user_id', 'u.fname', 'u.lname', 'u.email', 'u.state', 'u.zip', 'u.city')
									 ->get();

		$zc = new ZipCodes;
		$tp = new TrackingPage;

		foreach ($qry as $key) {
			if (isset($key->zip) && !isset($key->city) && !isset($key->state)) {
				$tmp = $zc->getCityStateByZip($key->zip);
	
				if (isset($tmp)) {
					$key->state = $tmp->StateAbbr;
					$key->city  = $tmp->CityName;
				}
			}

			if (!isset($key->zip) && !isset($key->city) && !isset($key->state)) {

				$tmp = $tp->getLastLogForThisUserId($key->user_id);

				if (isset($tmp)) {

					$ip = $this->iplookup($tmp->ip);
					if (isset($ip)) {
						$key->state = $ip['stateAbbr'];
						$key->city  = $ip['cityName'];
						$key->zip   = $ip['cityAbbr'];
					}
					
				}
			}

			print_r($key->user_id.";". $key->fname.";". $key->lname.";". $key->email.";". $key->city.";". $key->state.";".  $key->zip." <br/> ");
		}

	}

	public function fillValueMappingNrccua(){

		$arr = array(734,735,736,737,738,739,740,741,742,743,744,745,746,747,748,749,750,751,752,753,754,755,756,757);
		
		foreach ($arr as $key => $value) {
			$qry = DistributionClientFieldMapping::on('rds1')->where('dc_id', $value)->get();

			$gender_id = NULL;
			$isInterestedInOnlineEducation_id = NULL;
			$currentSchoolIpedId_id = NULL;
			$interestedSchoolIpedId_id = NULL;
			$globalContactOptIn_id = NULL;
			$utmCampaign_id = NULL;
			$userType_id = NULL;
			

			foreach ($qry as $k) {
				if ($k->client_field_name == 'gender') {
					$gender_id = $k->id;
				}

				if ($k->client_field_name == 'isInterestedInOnlineEducation') {
					$isInterestedInOnlineEducation_id = $k->id;
				}

				if ($k->client_field_name == 'currentSchoolIpedId') {
					$currentSchoolIpedId_id = $k->id;
				}

				if ($k->client_field_name == 'interestedSchoolIpedId') {
					$interestedSchoolIpedId_id = $k->id;
				}

				if ($k->client_field_name == 'userType') {
					$userType_id = $k->id;
				}

				if ($k->client_field_name == 'utmCampaign') {
					$utmCampaign_id = $k->id;
				}

				if ($k->client_field_name == 'globalContactOptIn') {
					$globalContactOptIn_id = $k->id;
				}
			}

			$final_arr = array();

			$final_arr[] = $gender_id;
			$final_arr[] = $gender_id;

			$final_arr[] = $isInterestedInOnlineEducation_id;
			$final_arr[] = $isInterestedInOnlineEducation_id;
			$final_arr[] = $isInterestedInOnlineEducation_id;

			$final_arr[] = $currentSchoolIpedId_id;
			$final_arr[] = $interestedSchoolIpedId_id;

			$final_arr[] = $globalContactOptIn_id;
			$final_arr[] = $utmCampaign_id;
			$final_arr[] = $userType_id;


			// $qry2 = DistributionClientValueMapping::where('dc_id', $value)->get();

			// $cnt = 0;
			// foreach ($qry2 as $t) {
			// 	$t->dcfm_id = $final_arr[$cnt];
			// 	$t->save();
			// 	$cnt++;
			// }
		}	
	}

	public function fillValueMappingCappex(){

		$arr = array(758, 759, 760, 761, 762, 763);
		
		foreach ($arr as $key => $value) {
			$qry = DistributionClientFieldMapping::on('rds1')->where('dc_id', $value)->get();

			$AFID_id = NULL;
			$CID_id = NULL;
			$gender_id = NULL;
			$hs_grad_month_id = NULL;
			$studentType_id = NULL;
			$college_considering_id = NULL;
			$Test_id = NULL;
			

			foreach ($qry as $k) {
				if ($k->client_field_name == 'AFID') {
					$AFID_id = $k->id;
				}

				if ($k->client_field_name == 'CID') {
					$CID_id = $k->id;
				}

				if ($k->client_field_name == 'gender') {
					$gender_id = $k->id;
				}

				if ($k->client_field_name == 'hs_grad_month') {
					$hs_grad_month_id = $k->id;
				}

				if ($k->client_field_name == 'studentType') {
					$studentType_id = $k->id;
				}

				if ($k->client_field_name == 'college_considering') {
					$college_considering_id = $k->id;
				}

				if ($k->client_field_name == 'Test') {
					$Test_id = $k->id;
				}

				

				
			}

			$final_arr = array();

			$final_arr[] = $AFID_id;
			$final_arr[] = $CID_id;

			$final_arr[] = $gender_id;
			$final_arr[] = $gender_id;

			$final_arr[] = $hs_grad_month_id;
			$final_arr[] = $studentType_id;

			$final_arr[] = $college_considering_id;
			$final_arr[] = $Test_id;


			$qry2 = DistributionClientValueMapping::where('dc_id', $value)->get();

			$cnt = 0;
			foreach ($qry2 as $t) {
				$t->dcfm_id = $final_arr[$cnt];
				$t->save();
				$cnt++;
			}
		}	
	}

	public function fixUsersState(){

		$qry = DB::connection('rds1')->table('users as u')
									 ->orWhere('u.state', 'LIKE', '%Alabama%' )
									->orWhere('u.state', 'LIKE', '%Alaska%' )
									->orWhere('u.state', 'LIKE', '%Arizona%' )
									->orWhere('u.state', 'LIKE', '%Arkansas%' )
									->orWhere('u.state', 'LIKE', '%California%' )
									->orWhere('u.state', 'LIKE', '%Colorado%' )
									->orWhere('u.state', 'LIKE', '%Connecticut%' )
									->orWhere('u.state', 'LIKE', '%Delaware%' )
									->orWhere('u.state', 'LIKE', '%District of Columbia%' )
									->orWhere('u.state', 'LIKE', '%Florida%' )
									->orWhere('u.state', 'LIKE', '%Georgia%' )
									->orWhere('u.state', 'LIKE', '%Hawaii%' )
									->orWhere('u.state', 'LIKE', '%Idaho%' )
									->orWhere('u.state', 'LIKE', '%Illinois%' )
									->orWhere('u.state', 'LIKE', '%Indiana%' )
									->orWhere('u.state', 'LIKE', '%Iowa%' )
									->orWhere('u.state', 'LIKE', '%Kansas%' )
									->orWhere('u.state', 'LIKE', '%Kentucky%' )
									->orWhere('u.state', 'LIKE', '%Louisiana%' )
									->orWhere('u.state', 'LIKE', '%Maine%' )
									->orWhere('u.state', 'LIKE', '%Maryland%' )
									->orWhere('u.state', 'LIKE', '%Massachusetts%' )
									->orWhere('u.state', 'LIKE', '%Michigan%' )
									->orWhere('u.state', 'LIKE', '%Minnesota%' )
									->orWhere('u.state', 'LIKE', '%Mississippi%' )
									->orWhere('u.state', 'LIKE', '%Missouri%' )
									->orWhere('u.state', 'LIKE', '%Montana%' )
									->orWhere('u.state', 'LIKE', '%Nebraska%' )
									->orWhere('u.state', 'LIKE', '%Nevada%' )
									->orWhere('u.state', 'LIKE', '%New Hampshire%' )
									->orWhere('u.state', 'LIKE', '%New Jersey%' )
									->orWhere('u.state', 'LIKE', '%New Mexico%' )
									->orWhere('u.state', 'LIKE', '%New York%' )
									->orWhere('u.state', 'LIKE', '%North Carolina%' )
									->orWhere('u.state', 'LIKE', '%North Dakota%' )
									->orWhere('u.state', 'LIKE', '%Ohio%' )
									->orWhere('u.state', 'LIKE', '%Oklahoma%' )
									->orWhere('u.state', 'LIKE', '%Oregon%' )
									->orWhere('u.state', 'LIKE', '%Pennsylvania%' )
									->orWhere('u.state', 'LIKE', '%Rhode Island%' )
									->orWhere('u.state', 'LIKE', '%South Carolina%' )
									->orWhere('u.state', 'LIKE', '%South Dakota%' )
									->orWhere('u.state', 'LIKE', '%Tennessee%' )
									->orWhere('u.state', 'LIKE', '%Texas%' )
									->orWhere('u.state', 'LIKE', '%Utah%' )
									->orWhere('u.state', 'LIKE', '%Vermont%' )
									->orWhere('u.state', 'LIKE', '%Virginia%' )
									->orWhere('u.state', 'LIKE', '%Washington%' )
									->orWhere('u.state', 'LIKE', '%West Virginia%' )
									->orWhere('u.state', 'LIKE', '%Wisconsin%' )
									->orWhere('u.state', 'LIKE', '%Wyoming%' )
									->orWhere('u.state', 'LIKE', '%Puerto Rico%' )
									->select('u.id as user_id', 'u.state')
									->get();

		foreach ($qry as $key) {
	
			$state = State::on('rds1')->where('state_name', 'LIKE', "%". $key->state . "%")
									  ->first();

			if (!isset($state)) {
				continue;
			}

			$user = User::find($key->user_id);
			$user->state = 	$state->state_abbr;
			$user->save();			
		}
	}


	public function populateUsersIpLocations(){
		
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_populateUsersIpLocations')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_populateUsersIpLocations');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_populateUsersIpLocations', 'in_progress', 10);	

    	$qry = DB::connection('bk')->table('users as u')
    								 ->whereRaw('not exists(
													Select
														1
													from
														users_ip_locations as uil
													where
														uil.user_id = u.id
												)')
    								 ->select('u.id as user_id', 'u.created_at', 'u.updated_at')
    								 // ->where('u.country_id', 1)
    								 // ->where('u.created_at', '>=',  '2018-05-01 00:00:00')
    								 ->orderBy(DB::raw("RAND()"))
    								 ->take(10000)
    								 ->get();

    	foreach ($qry as $key) {
    		$dt1 = Carbon::parse($key->created_at);
			$dt1 = $dt1->toDateString();

			$dt = Carbon::parse($key->created_at);
			$dt = $dt->addDay(1);
			$dt = $dt->toDateString();

			$tpid = TrackingPageId::on('bk-log')->where('date', $dt1)->first();
			
			if (!isset($tpid)) {
				$attr = array();
				$attr['user_id']  = $key->user_id; 
				$attr['ip'] 	  	   = NULL;
				$attr['ip_state'] 	   = NULL;
				$attr['ip_city']  	   = NULL;
				$attr['ip_zip']   	   = NULL;
				$attr['ip_state_id']   = NULL;
				$attr['ip_country_id'] = NULL;

				UsersIpLocation::updateOrCreate($attr, $attr);

				continue;
			}

			$start_date = $tpid->tp_id;

			$tpid2 = TrackingPageId::on('bk-log')->where('date', $dt)->first();
			if (isset($tpid2)) {
				$end_date = $tpid2->tp_id;
			}else{
				$end_date = TrackingPage::on('bk-log')->orderBy('id', 'desc')->first();
				$end_date = $end_date->id;
			}

			$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $key->user_id)
												 ->first();
			
			if (!isset($sub_qry)) {
				$dt1 = Carbon::parse($key->updated_at);
				$dt1 = $dt1->toDateString();
				
				$dt = Carbon::parse($key->updated_at);
				$dt = $dt->addDay(1);
				$dt = $dt->toDateString();

				$tpid = TrackingPageId::on('bk-log')->where('date', $dt1)->first();
				$start_date = $tpid->tp_id;

				$tpid2 = TrackingPageId::on('bk-log')->where('date', $dt)->first();
				if (isset($tpid2)) {
					$end_date = $tpid2->tp_id;
				}else{
					$end_date = TrackingPage::on('bk-log')->orderBy('id', 'desc')->first();
					$end_date = $end_date->id;
				}

				$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
													 ->where('id', '<=', $end_date)
													 ->where('user_id', $key->user_id)
													 ->first();
			}

			if (isset($sub_qry)) {
				$iplookup = $this->iplookup($sub_qry->ip);

				$attr = array();
				$attr['user_id']  = $key->user_id; 
				$attr['ip'] 	  = $sub_qry->ip;
				$attr['ip_state'] = $iplookup['stateAbbr'];
				$attr['ip_city']  = $iplookup['cityName'];
				$attr['ip_zip']   = $iplookup['cityAbbr'];

				$state = State::on('bk')->where('state_abbr', $iplookup['stateAbbr'])
										->select('id')
										->first();

				$country = Country::on('bk')->where('country_code', $iplookup['countryAbbr'])
				                           ->select('id')
											->first();						
				$attr['ip_state_id']   = isset($state->id)   ? $state->id   : NULL;
				$attr['ip_country_id'] = isset($country->id) ? $country->id : NULL;
				
			}else{

				$attr = array();
				$attr['user_id']  	   = $key->user_id; 
				$attr['ip'] 	  	   = NULL;
				$attr['ip_state'] 	   = NULL;
				$attr['ip_city']  	   = NULL;
				$attr['ip_zip']   	   = NULL;
				$attr['ip_state_id']   = NULL;
				$attr['ip_country_id'] = NULL;
			}

			UsersIpLocation::updateOrCreate($attr, $attr);
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_populateUsersIpLocations', 'done', 7);
	}

	public function fixIpLocation(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_fixIpLocation')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_fixIpLocation');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_fixIpLocation', 'in_progress', 10);	

		$qry = UsersIpLocation::whereNull('ip_country_id')
							  ->whereNotNull('ip_state')
							  // ->whereNotNull('ip_city')
							  ->take(10000)
							  ->get();

		foreach ($qry as $key) {
			$iplookup = $this->iplookup($key->ip);
			// dd($iplookup);

			$state = State::on('bk')->where('state_abbr', $iplookup['stateAbbr'])
										->select('id')
										->first();

			$country = Country::on('bk')->where('country_code', $iplookup['countryAbbr'])
			                           ->select('id')
										->first();			

			$key->ip_state_id   = isset($state->id)   ? $state->id   : NULL;
			$key->ip_country_id = isset($country->id) ? $country->id : NULL;

			$key->save();
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'is_fixIpLocation', 'done', 7);
	}

	/**
	 * updateHelperTableUnsub
	 * This method updates email_logic_helper with current unsubscribed users
	 *
	 * @return true or false
	 */
	public function updateHelperTableUnsub(){

		DB::statement("update email_logic_helper elh
						set is_unsubscribed = 1
						where exists
						(
						       Select
						           1
						       from
						           email_suppression_lists esl
						       where
						           esl.uid = elh.user_id
						       and uid is not null
						   )
						and elh.is_unsubscribed = 0");
	}

	/**
	 * updateIsCompleteProfile
	 * set is_complete_profile to 1, then assign template attribution (profile_flow_converted_source) based on last click template
	 *
	 */
	public function updateIsCompleteProfile(){

		DB::statement("update email_logic_helper elh
						set is_complete_profile = 1
						where exists 
						(
							Select 1
							from users u
							join scores s on u.id = s.user_id ##
							join objectives o on u.id = o.user_id
							where country_id = 1
							and not exists (select user_id from country_conflicts where user_id = u.id)
							and is_ldy = 0
							and email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
							and email not like '%test%'
							and fname not like '%test'
							and email not like '%nrccua%'
							and address is not null
							and zip is not null
							and city is not null
							and gender in ('m', 'f')
							and country_id = 1
							and coalesce(hs_gpa, overall_gpa, weighted_gpa) is not null
							and is_plexuss = 0
							and u.id = elh.user_id
						)
						and is_complete_profile = 0
						and is_international = 0;");

		DB::statement("update email_logic_helper elh
						join (select max(id), user_id, template_id 
							from email_click_logs ecl
							where exists
								(Select 1 from email_template_grouping etg where category_id = 1 and ecl.template_id = etg.template_id)
							group by user_id
						) click_logs_tbl on elh.user_id = click_logs_tbl.user_id
						set profile_flow_converted_source = click_logs_tbl.template_id
						where profile_flow_last_template > 2
						and profile_flow_assignment is not null
						and is_complete_profile = 1
						and is_international = 0
						and profile_flow_converted_source is null;");
	}

	/**
	 * updateIsCompleteProfileIntl for international users
	 * set is_complete_profile to 1, then assign template attribution (profile_flow_converted_source) based on last click template
	 *
	 */
	public function updateIsCompleteProfileIntl(){
		DB::statement("###add complete profile and attribution international
						update email_logic_helper elh
						set is_complete_profile = 1
						where exists 
						(
							Select 1
							from users u
							join scores s on u.id = s.user_id ##
							join objectives o on u.id = o.user_id
							where (country_id != 1 or (country_id = 1 
								and exists (select user_id from country_conflicts where user_id = u.id)))
							and is_ldy = 0
							and email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
							and email not like '%test%'
							and fname not like '%test'
							and email not like '%nrccua%'
							and address is not null
							and zip is not null
							and city is not null
							and gender in ('m', 'f')
							and coalesce(hs_gpa, overall_gpa, weighted_gpa) is not null
							and is_plexuss = 0
							and u.id = elh.user_id
							and u.planned_start_term is not null
							and u.planned_start_yr is not null
							and u.financial_firstyr_affordibility is not null
						)
						and is_complete_profile = 0
						and is_international = 1;");

		DB::statement("update email_logic_helper elh
						join (select max(id), user_id, template_id 
							from email_click_logs ecl
							where exists
								(Select 1 from email_template_grouping etg where category_id = 1 and ecl.template_id = etg.template_id)
							group by user_id
						) click_logs_tbl on elh.user_id = click_logs_tbl.user_id
						set profile_flow_converted_source = click_logs_tbl.template_id
						where profile_flow_last_template > 2
						and profile_flow_assignment is not null
						and is_complete_profile = 1
						and profile_flow_converted_source is null
						and is_international = 1;");
	}

	public function assignProfileFlowAssignment(){
		DB::statement("update email_logic_helper
						set profile_flow_assignment = '2b'
						, profile_flow_last_time_sent = date_sub(current_timestamp, interval 8 hour)
						, profile_flow_start = date(date_sub(current_timestamp, interval 8 hour))
						where is_duplicate = 0
						and is_unsubscribed = 0
						and is_complete_profile = 0
						and is_international = 0
						and timezone_adjustment is not null
						and profile_flow_assignment is null;");
	}

	public function assignProfileFlowAssignmentIntl(){
		DB::statement("update email_logic_helper
						set profile_flow_assignment = ELT(.5 + RAND() * 4, 'i1a', 'i1b', 'i2a', 'i2b')
						, profile_flow_last_time_sent = date_sub(current_timestamp, interval 8 hour)
						, profile_flow_start = date(date_sub(current_timestamp, interval 8 hour))
						where is_duplicate = 0
						and is_unsubscribed = 0
						and is_complete_profile = 0
						and is_international = 1
						and timezone_adjustment is not null
						and profile_flow_assignment is null;");
	}
	
	/**
	 * isUserDuplicate
	 * checks the duplicate users for  the day, and if they are dup it would set it on the helper table.
	 *
	 */
	public function isUserDuplicate(){

		$today = Carbon::today();

		$user = User::on('bk')
					->where('created_at', '>=', $today)
					->select('id')
					->get();

		$str1 = '( ';
		$str2 = '( ';
		foreach ($user as $key) {
			// $user_id_arr .= $key->id.",";
			$str1 .= 'id = '.$key->id." OR ";
			$str2 .= 'u1.id = '.$key->id." OR ";
		}

		$str1 = rtrim($str1, "OR ");
		$str2 = rtrim($str2, "OR ");

		$str1 .= ')';
		$str2 .= ')';

		$qry_str = "Select u1.id,
					if(u1.id in 
					  (Select max(u2.id)
					   from users u2
					   where is_ldy = 0
					   and is_plexuss = 0 
					   and email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
					   and email not like '%test%'
					   and fname not like '%test'
					   and email not like '%nrccua%'
					   and ".$str1."
						 group by email)
					, 0, 1) as 'is_duplicate'
					from users u1
					where is_ldy = 0
					and is_plexuss = 0 
					and email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
					and email not like '%test%'
					and fname not like '%test'
					and email not like '%nrccua%'
					and ".$str2.";";

		$qry = DB::connection('bk')->select($qry_str);

		foreach ($qry as $key) {
			if ($key->is_duplicate == 1) {
				$attr = array('user_id' => $key->id);
				$val  = array('user_id' => $key->id, 'is_duplicate' => $key->is_duplicate);

				EmailLogicHelper::updateOrCreate($attr, $val);
			}
		}
	}

	public function  updateTimeZoneQueries(){

		DB::statement("update email_logic_helper elh
						join users u on elh.user_id = u.id
						join countries c on u.country_id = c.id
						set elh.timezone_adjustment = c.timezone_adjustment
						where elh.timezone_adjustment is null
						and is_international = 1;");
		DB::statement("update email_logic_helper elh
						join users u on u.id = elh.user_id
						join states s on u.state = s.state_abbr
						set elh.timezone_adjustment = s.timezone_adjustment
						where is_international = 0
						and elh.timezone_adjustment is null
						and u.country_id = 1
						and length(u.state) = 2;");
		DB::statement("update email_logic_helper elh
						join users_ip_locations uil on elh.user_id = uil.user_id
						join states s on uil.ip_state_id = s.id
						set elh.timezone_adjustment = s.timezone_adjustment
						where is_international = 0
						and elh.timezone_adjustment is null
						and uil.ip_country_id = 1;");
		DB::statement("update email_logic_helper
						set timezone_adjustment = 3, unknown_timezone = 1
						where timezone_adjustment is null
						and is_international = 0
						and date(created_at) < date_sub(current_date, interval 4 day);
						#international
						update email_logic_helper
						set timezone_adjustment = 8, unknown_timezone = 1
						where timezone_adjustment is null
						and is_international = 1
						and date(created_at) < date_sub(current_date, interval 4 day);");
	}

	public function addUsersToEmailLogicHelper(){

		$qry_str = "Select u.id
						, if(country_id = 1 and u.id and not exists (select 1 from country_conflicts cc where cc.user_id = u.id), 0, 1) as 'is_international'
						from users u 
						join (
							SELECT id
							FROM users 
							order by id desc
							limit 25000 ) as u_recent on u.id = u_recent.id
						WHERE
							not exists (
								Select
									1
								from email_logic_helper elh
								where elh.user_id = u.id
						)
						and is_ldy = 0
						and is_plexuss = 0
						and is_organization = 0
						and is_ldy = 0
						and is_university_rep = 0
						and is_aor = 0
						and is_university_rep = 0
						and email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'";

		$qry = DB::connection('bk')->select($qry_str);

		foreach ($qry as $key) {
			$attr = array('user_id' => $key->id);
			$val  = array('user_id' => $key->id, 'is_international' => $key->is_international);

			EmailLogicHelper::updateOrCreate($attr, $val);
		}

	}
	/**
	 * setLoggedInUserTrackingLog
	 * This method sets the everyday tracking of each user_id that has joined us.
	 *
	 * @return true or false
	 */
	public function setLoggedInUserTrackingLog(){

		$main_qry = TrackingPageId::on('bk-log')
		                          ->orderby('date', 'asc')
		                          ->whereBetween('date', ['2015-02-14', '2015-02-24'])
		                          ->get();


		foreach ($main_qry as $key) {

			$start_date = $key->tp_id;
			
		    $dt = Carbon::parse($key->date);
			$dt = $dt->addDay(1);
			$dt = $dt->toDateString();

			$sub_qry = TrackingPageId::on('bk-log')
		                          ->where('date', $dt)
		                          ->first()->tp_id;

		    if (!isset($sub_qry)) {
		    	$end_date = TrackingPage::on('bk-log')
		    							->first()->id;
		    }else{
		    	$end_date = $sub_qry;
		    }

		    $tp = TrackingPage::on('bk-log')
		                      ->whereBetween('id', [$start_date, $end_date])
		                      ->where('user_id', '!=', 0)
		                      ->groupBy('user_id')
		                      ->select('ip', DB::raw("count(*) as cnt"), 'user_id')
		                      ->get();

		    foreach ($tp as $k) {
		    	$arr = $this->iplookup($k->ip);
		    	$attr = array('ip' => $k->ip, 'user_id' => $k->user_id, 'city' => $arr['cityName'], 'state' => $arr['stateAbbr'],
		    	              'zip' => $arr['cityAbbr'], 'country' => $arr['countryAbbr'], 'cnt' =>$k->cnt, 'tpi_id' => $key->id, 
		    	              'tpt_id' => 1);

		    	TrackingPageLog::on('log')->updateOrCreate($attr, $attr);
		    }

		    $tp = TrackingPage::on('bk-log')
		                      ->whereBetween('id', [$start_date, $end_date])
		                      ->where('user_id', 0)
		                      ->groupBy('ip')
		                      ->select('ip', DB::raw("count(*) as cnt"), 'user_id')
		                      ->get();

		    foreach ($tp as $k) {
		    	$arr = $this->iplookup($k->ip);
		    	$attr = array('ip' => $k->ip, 'user_id' => $k->user_id, 'city' => $arr['cityName'], 'state' => $arr['stateAbbr'],
		    	              'zip' => $arr['cityAbbr'], 'country' => $arr['countryAbbr'], 'cnt' =>$k->cnt, 'tpi_id' => $key->id, 
		    	              'tpt_id' => 2);

		    	TrackingPageLog::on('log')->updateOrCreate($attr, $attr);
		    }
		    

		    // dd($start_date. '  === '. $end_date);
		}
	}

	public function getNrccuaClicks($source = NULL){
		$url = "https://api.sparkpost.com/api/v1/message-events/?events=click";
        $client = new Client(['base_uri' => 'http://httpbin.org']);
        // $esl    = new EmailSuppressionList;

        $s_key = env('SPARKPOST_KEY');
        if ($source == 2) {
        	$s_key = env('SPARKPOST_DIRECT_KEY');
        }
        try {
            $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => $s_key,
                            'Accept'        => 'application/json'
                            ]
                        ]);
            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['results'])) {
            	foreach ($result['results'] as $key) {
    
            		if (($key['template_id'] != "users-a-college-viewed-your-profile" && $key['template_id'] != "users-recommended-by-plexuss" && $key['template_id'] != "users-a-college-wants-to-recuit-you" && 
            			$key['template_id'] != "users-a-college-wants-to-recuit-you-2") || 
            			(strpos($key['target_link_url'], 'adRedirect') !== false)) {
            			continue;
            		}else{
            			
            			$user = User::on('bk')->where('email', $key['rcpt_to'])
            								  ->select('id')
            			                      ->first();

            			if (!isset($user)) {
            				continue;
            			}
            			$slug = substr(str_replace("https://plexuss.com/college/", "", $key['target_link_url']), 0, strpos(str_replace("https://plexuss.com/college/", "", $key['target_link_url']), "/"));

            			$college = College::on('rds1')->where('slug', $slug)->first();
            			// $this->customdd("*********<br/>");
            			// $this->customdd($user->id);
            			if (!isset($college->id)) {
            				$college_name = str_replace(" is interested to recruit you.", "", $key['subject']);
            				$college = College::on('rds1')->where('school_name', $college_name)
            											  ->where('verified', 1)
            				                              ->first();
            				// $this->customdd($college_name);
            				// $this->customdd("^^^^^^^^^^^<br/>");
            				// dd($key);
            				// exit();
            				if (!isset($college)) {
            					// $url = "https://plexuss.com/get_started/?utm_source=email_school_viewed_you&college_id=263";
            					$url     = $key['target_link_url'];
            					$college = substr($url, strpos($url, "college_id=") + 11);
            					$college = College::on('rds1')->find($college);
            					// dd($college);
            				}
            				if (!isset($college)) {
            					continue;
            				}
            				
            				// $this->customdd($college);
            				// dd($key);
            				// exit();
            			}
            			$dc = DistributionClient::on('rds1')->where('ro_id', 1)
            											    ->where('college_id', $college->id)
            											    ->first();

            			if (!isset($dc)) {
            				continue;
            			}
            			// $this->customdd($college->id);
            			// $this->customdd("=========<br/>");
            			$test = NrccuaQueue::on('rds1')->where('ro_id', 1)
            										   ->where('college_id', $college->id)
            										   ->where('user_id', $user->id)
            										   ->first();

            									   
            			if (isset($test)) {
            				continue;
            			}
            			$attr = array('ro_id' => 1, 'college_id' => $college->id, 'user_id' => $user->id, 'manual' => 1);
            			NrccuaQueue::updateOrCreate($attr, $attr);
            			// dd($key);
            			// // $college_name = str_replace("https://plexuss.com/college/", "", $key['target_link_url']);
            			// dd($college_name);
            		}
            	}
            	// dd($result['results']);
            }
        }catch (\Exception $e) {
            return false;
            // return "something bad happened";
        }
	}

	public function getEmailClicks($source = NULL){
		$url = "https://api.sparkpost.com/api/v1/message-events/?events=click";
        $client = new Client(['base_uri' => 'http://httpbin.org']);
        // $esl    = new EmailSuppressionList;

        $s_key = env('SPARKPOST_KEY');
        if ($source == 2) {
        	$s_key = env('SPARKPOST_DIRECT_KEY');
        }
        try {
            $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => $s_key,
                            'Accept'        => 'application/json'
                            ]
                        ]);
            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['results'])) {
            	$cnt = 0;
            	foreach ($result['results'] as $key) {
            		// if ($cnt == 22) {
            		// 	dd($key);
            		// }
            		// $cnt++;
            		$user = User::on('bk')->where('email', $key['rcpt_to'])
            								->select('id')
            								->first();

            		if(!isset($user)){
            			continue;
            		}
            		if (!isset($key['geo_ip'])) {
            			$key['geo_ip'] = array();
            			$key['geo_ip']['city'] = "";
            			$key['geo_ip']['country'] = "";
            			$key['geo_ip']['latitude'] = "";
            			$key['geo_ip']['longitude'] = "";
            		}

            		$valid   	= $this->isEmailClickValid($key['target_link_url']);
            		$url_seg 	= $this->getEmailClickCompany($key['target_link_url']);

            		$company 	= NULL;
            		$college_id = NULL;
            		$ro_id 		= NULL;

            		$utm_source = NULL;
            		$utm_medium = NULL;
            		$utm_content= NULL;

            		(isset($url_seg['company'])) 	? $company = $url_seg['company'] : NULL;
            		(isset($url_seg['college_id'])) ? $college_id = $url_seg['college_id'] : NULL;
            		(isset($url_seg['ro_id'])) 		? $ro_id = $url_seg['ro_id'] : NULL;

            		(isset($url_seg['utm_source'])) ? $utm_source = $url_seg['utm_source'] : NULL;
            		(isset($url_seg['utm_medium'])) ? $utm_medium = $url_seg['utm_medium'] : NULL;
            		(isset($url_seg['utm_content']))? $utm_content = $url_seg['utm_content'] : NULL;

            		$click_date = $this->convertUTCtoPST($key['timestamp']);

            		$this->setLastClickLastOpen($user->id, "click", $click_date);

            		$attr = array('email' => $key['rcpt_to'], 'user_id' => $user->id, 'template_id' => $key['template_id'], 'url' => $key['target_link_url']);

            		$val  = array('email' => $key['rcpt_to'], 'user_id' => $user->id, 'template_id' => $key['template_id'], 'geo_city' => $key['geo_ip']['city'], 'geo_country' => $key['geo_ip']['country'], 'geo_latitude' => $key['geo_ip']['latitude'], 'geo_longitude' => $key['geo_ip']['longitude'], 'click_date' => $click_date, 'url' => $key['target_link_url'], 'valid' => $valid, 'company' => $company,  
            			'ro_id' => $ro_id,  'college_id' => $college_id, 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_content' => $utm_content, 'user_agent' => $key['user_agent'], 
            			'response' =>json_encode($key));

            		EmailClickLog::updateOrCreate($attr, $val);
            	}
            }
        }catch (\Exception $e) {
            return false;
            // return "something bad happened";
        }

        return false;
	}

	public function getEmailOpens($source = NULL){

		$url = "https://api.sparkpost.com/api/v1/message-events/?events=open";
        $client = new Client(['base_uri' => 'http://httpbin.org']);
        // $esl    = new EmailSuppressionList;

        $s_key = env('SPARKPOST_KEY');
        if ($source == 2) {
        	$s_key = env('SPARKPOST_DIRECT_KEY');
        }
        try {
            $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => $s_key,
                            'Accept'        => 'application/json'
                            ]
                        ]);
            $result = json_decode($response->getBody()->getContents(), true);
            // dd($result);
            if (isset($result['results'])) {
            	$cnt = 0;
            	foreach ($result['results'] as $key) {
            		// if ($cnt == 22) {
            			// dd($key);
            		// }
            		// $cnt++;
            		$user = User::on('bk')->where('email', $key['rcpt_to'])
            								->select('id')
            								->first();

            		if(!isset($user)){
            			continue;
            		}
            		if (!isset($key['geo_ip'])) {
            			$key['geo_ip'] = array();
            			$key['geo_ip']['city'] = "";
            			$key['geo_ip']['country'] = "";
            			$key['geo_ip']['latitude'] = "";
            			$key['geo_ip']['longitude'] = "";
            		}

            		$valid = 0;
            		$open_date = $this->convertUTCtoPST($key['timestamp']);

            		$this->setLastClickLastOpen($user->id, "open", $open_date);

            		$attr = array('email' => $key['rcpt_to'], 'user_id' => $user->id, 'template_id' => $key['template_id'], 'ip_address' => $key['ip_address']);

            		$val  = array('email' => $key['rcpt_to'], 'user_id' => $user->id, 'template_id' => $key['template_id'], 'geo_city' => $key['geo_ip']['city'], 'geo_country' => $key['geo_ip']['country'], 'geo_latitude' => $key['geo_ip']['latitude'], 'geo_longitude' => $key['geo_ip']['longitude'], 'open_date' => $open_date, 'valid' => $valid, 'subject' => $key['subject'], 'user_agent' => $key['user_agent'], 'response' =>json_encode($key), 'ip_address' => $key['ip_address'], 'sending_ip' => $key['sending_ip']);

            		EmailOpenLog::updateOrCreate($attr, $val);            		
            	}
            }
        }catch (\Exception $e) {
            return false;
            // return "something bad happened";
        }

        return false;
	}

	private function setLastClickLastOpen($user_id, $type, $dt){

		$elh = EmailLogicHelper::on('bk')->where('user_id', $user_id)
									     ->select('id', 'last_open', 'last_click')
									     ->first();

		if (!isset($elh)) {
			$attr = array();
			$val  = array();

			$attr['user_id'] = $user_id;
			$val['user_id']  = $user_id;

			switch ($type) {
				case 'open':
					$val['last_open']  = $dt;	
					break;
				
				case 'click':
					$val['last_click'] = $dt;
					break;
				default:
					# code...
					break;
			}

			EmailLogicHelper::updateOrCreate($attr, $val);
		}else{
			switch ($type) {
				case 'open':
					if (!isset($elh->last_open)) {
						$update = EmailLogicHelper::where('id', $elh->id)									         
									              ->update(['last_open' => $dt]);
						// $elh->last_open = $dt;
						// $elh->save();
					}else{
						$old_time = Carbon::parse($elh->last_open);
						$new_time = Carbon::parse($dt);
						if ($new_time->gt($old_time)) {
							$update = EmailLogicHelper::where('id', $elh->id)									         
									              ->update(['last_open' => $dt]);
							// $elh->last_open = $dt;
							// $elh->save();
						}

					}
					break;
				
				case 'click':
					if (!isset($elh->last_click)) {
						$update = EmailLogicHelper::where('id', $elh->id)									         
									              ->update(['last_click' => $dt]);
						// $elh->last_click = $dt;
						// $elh->save();
					}else{
						$old_time = Carbon::parse($elh->last_click);
						$new_time = Carbon::parse($dt);
						if ($new_time->gt($old_time)) {
							$update = EmailLogicHelper::where('id', $elh->id)									         
									              ->update(['last_click' => $dt]);
							// $elh->last_click = $dt;
							// $elh->save();
						}
					}
					break;
				default:
					# code...
					break;
			}
		}

		return "success";
	}

	private function isEmailClickValid($url){

		$valid = 1;
		if (strpos($url, 'https://plexuss.com/unsubscribe/') !== FALSE){
			$valid = 0;

			return $valid;
		}

		if (strpos($url, 'https://plexuss.com/') !== FALSE){
			$valid = 1;
		}else{
			$valid = 0;
		}

		return $valid;
	}

	private function getEmailClickCompany($url){
		
		$url_seg = array();
		$str = explode("?", $url);
		if(strpos($url, 'https://plexuss.com/college') !== FALSE) {
			$slug = $this->getStringBetween($url, 'https://plexuss.com/college/', '?');
		
			$college = College::on('rds1')->where('slug', $slug)->first();
			if (isset($college)) {
				$dc = DistributionClient::on('rds1')->where('college_id', $college->id)
				    										->orderBy('ro_id')
				    										->whereNotNull('ro_id')
				    										->first();
				    $url_seg['college_id'] = $college->id;						
		    	if (isset($dc)) {
		    		$ro = RevenueOrganization::on('rds1')->find($dc->ro_id);
					$url_seg['company'] = $ro->name;
					$url_seg['ro_id']   = $dc->ro_id;
		    	}
			}
		}elseif (isset($str[1])) {
			$inner_str = explode("&", $str[1]);

			for ($i=0; $i <count($inner_str) ; $i++) { 
				if (isset($inner_str[$i]) && strpos($inner_str[$i], 'company=') !== FALSE){
					$ret_arr = explode("=", $inner_str[$i]);

				    isset($ret_arr[1]) ? $url_seg['company'] = $ret_arr[1] : NULL;
				}

				if (isset($inner_str[$i]) && strpos($inner_str[$i], 'utm_source=') !== FALSE){
					$ret_arr = explode("=", $inner_str[$i]);

				    isset($ret_arr[1]) ? $url_seg['utm_source'] = $ret_arr[1] : NULL;
				}

				if (isset($inner_str[$i]) && strpos($inner_str[$i], 'utm_medium=') !== FALSE){
					$ret_arr = explode("=", $inner_str[$i]);

				    isset($ret_arr[1]) ? $url_seg['utm_medium'] = $ret_arr[1] : NULL;
				}

				if (isset($inner_str[$i]) && strpos($inner_str[$i], 'utm_content=') !== FALSE){
					$ret_arr = explode("=", $inner_str[$i]);

				    isset($ret_arr[1]) ? $url_seg['utm_content'] = $ret_arr[1] : NULL;
				}


				if (isset($inner_str[$i]) && strpos($inner_str[$i], 'ro_id=') !== FALSE){
					$ret_arr = explode("=", $inner_str[$i]);
					
					isset($ret_arr[1]) ? $ro_id = $ret_arr[1] : $ro_id = NULL;

					if (isset($ro_id)) {
						$url_seg['ro_id'] = $ro_id;
						if (!isset($url_seg['company'])) {
							
							$ro = RevenueOrganization::on('rds1')->find($ro_id);
							$url_seg['company'] = $ro->name;
						}
					}
				}

				if (isset($inner_str[$i]) && strpos($inner_str[$i], 'college_id=') !== FALSE){
					$ret_arr = explode("=", $inner_str[$i]);

				    isset($ret_arr[1]) ? $url_seg['college_id'] = $ret_arr[1] : NULL;

				    if (!isset($url_seg['company']) && isset($url_seg['college_id']) && 
				    	strpos($url, 'https://plexuss.com/get_started') !== FALSE) {

				    	$dc = DistributionClient::on('rds1')->where('college_id', $url_seg['college_id'])
				    										->orderBy('ro_id')
				    										->whereNotNull('ro_id')
				    										->first();
				    	if (isset($dc)) {
				    		$ro = RevenueOrganization::on('rds1')->find($dc->ro_id);
							$url_seg['company'] = $ro->name;
							$url_seg['ro_id']   = $dc->ro_id;
				    	}

				    }
				}
			}
		}
		
		return $url_seg;
	}

	private function convertUTCtoPST($click_date){

		$dt = Carbon::parse($click_date);
		$dt->subHours('7');

		return $dt->toDateTimeString();
	}

	public function emailGateway(){

		$input = Request::all();

		try {

			$email_num  = $input['en'];
			$user_id    = Crypt::decrypt($input['uid']);
			isset($input['cid']) ? $college_id 	   = $input['cid'] : NULL;
			isset($input['sid']) ? $scholarship_id = $input['sid'] : NULL;
			
		} catch (Exception $e) {
			return "Bad inputs";
		}
		
		$ac = new AjaxController();

		switch ($email_num) {
			case 16:
				$input = array();
				$input['user_id'] = $user_id;
				$input['is_seen'] = 1;
            	$input['is_seen_recruit'] = 1;
            	$input['email_sent'] = 1;
				$tmp = $ac->saveUserRecruitMe($college_id, true, $input);
				$college = College::on('bk')->find($college_id);

				$url = 'college/'. $college->slug."/?applynowmodal=1";
				break;			
			case 17:
				$input = array();
				$input['user_id'] = $user_id;
				$input['is_seen'] = 1;
            	$input['is_seen_recruit'] = 1;
            	$input['email_sent'] = 1;
				$tmp = $ac->saveUserRecruitMe($college_id, true, $input);
				$college = College::on('bk')->find($college_id);

				$url = 'college/'. $college->slug."/?applynowmodal=1";
				break;
			case 18:
				$input = array();
				$input['user_id'] = $user_id;
				$input['is_seen'] = 1;
            	$input['is_seen_recruit'] = 1;
            	$input['email_sent'] = 1;
				$tmp = $ac->saveUserRecruitMe($college_id, true, $input);
				$college = College::on('bk')->find($college_id);

				$url = 'college/'. $college->slug."/?applynowmodal=1";
				break;
			case 19:
				$input = array();
				$input['user_id'] 		   = $user_id;
				$input['is_seen'] 		   = 1;
            	$input['is_seen_recruit']  = 1;
            	$input['email_sent'] 	   = 1;
            	
            	$input['user_recruit'] 	   = 0;
            	$input['college_recruit']  = 1;
				$tmp = $ac->saveUserRecruitMe($college_id, true, $input);
				$college = College::on('bk')->find($college_id);

				$url = 'portal/collegesrecruityou';
				break;
			case 20:
				$input = array();
				$input['user_id'] = $user_id;
				$input['is_seen'] = 1;
            	$input['is_seen_recruit'] = 1;
            	$input['email_sent'] = 1;

            	$college_id = explode(",", $college_id);
            	foreach ($college_id as $key => $value) {
            		$tmp = $ac->saveUserRecruitMe($value, true, $input);
					$college = College::on('bk')->find($value);
            	}
				$url = 'college/'. $college->slug."/?applynowmodal=1";
				break;
			case 21:
				$input = array();
				$input['user_id'] = $user_id;
				$input['is_seen'] = 1;
            	$input['is_seen_recruit'] = 1;
            	$input['email_sent'] = 1;
				$tmp = $ac->saveUserRecruitMe($college_id, true, $input);
				$college = College::on('bk')->find($college_id);

				$url = 'college/'. $college->slug."/?applynowmodal=1";
				break;	
			case 22:
				$input = array();
				$input['user_id'] = $user_id;
				$input['is_seen'] = 1;
            	$input['is_seen_recruit'] = 1;
            	$input['email_sent'] = 1;
				$tmp = $ac->saveUserRecruitMe($college_id, true, $input);
				$college = College::on('bk')->find($college_id);

				$url = 'college/'. $college->slug."/?applynowmodal=1";
				break;
			case 23:
				$college = College::on('bk')->find($college_id);
				$ntn = new NotificationController();
				$ntn->create( $college->school_name, 'user', 1, null, 463 , $user_id);	

				$url = 'portal/collegesviewedprofile';
				break;
			case 24:
				
				$sc = new ScholarshipsController();
				$scholarship_id = explode(",", $scholarship_id);
				foreach ($scholarship_id as $key => $value) {
					$arr = array();	
					$arr['user_id'] = $user_id;
					$arr['status']  = 'finish';
					$arr['scholarship'] = $value;

					$sc->queueScholarship($arr);
				}

				$url = 'portal/scholarships';
				break;
			case 25:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/recommendationlist';
				break;
			case 26:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/collegesviewedprofile';
				break;
			case 27:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/recommendationlist';
				break;
			case 28:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/collegesrecruityou';
				break;	
			case 29:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/recommendationlist';
				break;
			case 30:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/recommendationlist';
				break;
			case 31:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/collegesrecruityou';
				break;
			case 32:
				$college_id_arr = explode(",", $college_id);

				foreach ($college_id_arr as $key => $value) {
					$input = array();
					$input['user_id'] = $user_id;
					$input['is_seen'] = 1;
	            	$input['is_seen_recruit'] = 1;
	            	$input['email_sent'] = 1;
					$tmp = $ac->saveUserRecruitMe($value, true, $input);
				}
		
				$url = 'portal/collegesviewedprofile';
				break;
			case 33:
				$sc = new ScholarshipsController();
				$scholarship_id = explode(",", $scholarship_id);
				foreach ($scholarship_id as $key => $value) {
					$arr = array();	
					$arr['user_id'] = $user_id;
					$arr['status']  = 'finish';
					$arr['scholarship'] = $value;

					$sc->queueScholarship($arr);
				}

				$url = 'portal/scholarships';
				break;
			default:
				# code...
				break;

			}

		return redirect( env('CURRENT_URL').$url );
	}

	public function userEngagementEmailProcess($type){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'userEngagementEmailProcess_'.$type)) {

    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'userEngagementEmailProcess_'.$type);

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'userEngagementEmailProcess_'.$type, 'in_progress', 10);

		$take = 500;

		$last_thirty_days = Carbon::now()->subDays(30);
		$last_thirty_days = $last_thirty_days->toDateString();
		
		$upeel_id = UsersPortalEmailEffortLogsDateId::on('bk')->where('date', $last_thirty_days)->first()->upeel_id;

		// $qry = DB::connection('bk')
		// 		 ->table('users as u')
		// 	     // ->where('fname', 'NOT LIKE', '%test%')
		// 	     // ->where('lname', 'NOT LIKE', '%test%')
		// 	     // ->where('email', 'NOT LIKE', '%test%')
 		// 		     ->select('u.id as user_id', 'u.country_id', 'u.email')
 		// 		     // ->where('u.id', 1288407)
		// 	     // ->where('u.id', 1288510)
 		// 		     // ->where('u.id', 1289624)
			     
		// 	     ->whereIn('u.id', array( 789 ))
		// 	     // ->whereIn('u.id', array(127,139,147,789, 1111462, 1155059, 1283604, 1293474 ))
		// 	     ->take($take)
		// 	     ->get();


		$qry = $this->userEngagementEmailProcessMainQuery($type);
		
		$sent_email_cnt = 0;
		foreach ($qry as $key) {

			$complete_profile = $this->engagementEmailCheckups('complete_profile', $key->user_id);
			// $complete_profile = true;

			$templates_sent = UsersPortalEmailEffortLog::on('bk')
													   ->where('user_id', $key->user_id)
													   ->whereNotNull('params')
													   ->groupBy('template_name')
													   ->pluck('template_name');
									   
			if ($complete_profile) {
				// For now skip the complete profile users
				// continue;

				/// get school ids that we have to exclude
				$upeel = UsersPortalEmailEffortLog::on('bk')
												  ->where('user_id', $key->user_id)
												  ->whereNotNull('params')
												  ->get();

				$exclude_school_ids = array();

				foreach ($upeel as $k) {
					$tmp = json_decode($k->params);

					$i = 1;
					foreach ($tmp as $k2 => $val2) {
						if($k2 == 'SCHOOL'.$i.'_ID'){
							$exclude_school_ids[] = $val2;
						}
						$i++;
					}
				}
				/// Exclude schoolids end here.

				/// has user selected over 5 schools?
				// $selected_over_5 = $this->engagementEmailCheckups('selected_over_5', $key->user_id);

				$ro = RevenueOrganization::on('bk')->orderBy('priority', 'ASC')
												   ->where('active', 1)
												   ->get();
				
				$start_date = '2015-06-01';
				$end_date   = Carbon::tomorrow()->toDateString();
				
				$bc = new BetaUserController;	
				$ro_to_send = NULL;
				foreach ($ro as $k) {

					$params = array();
					
					$ro_to_send = NULL;	
					$check = false;

					if (isset($k->org_branch_id)) {
						$ob = OrganizationBranch::on('bk')
												->where('id', $k->org_branch_id);

						if (isset($exclude_school_ids)) {
							$ob = $ob->whereNotIn('school_id', $exclude_school_ids);
						}

						$ob = $ob->first();

						if (!isset($ob)) {
							continue;
						}
						$check = true;
					}elseif (isset($k->aor_id)) {
						$ac = AorCollege::on('bk')
										->where('aor_id', $k->aor_id);

						if (isset($exclude_school_ids)) {
							$ac = $ac->whereNotIn('college_id', $exclude_school_ids);
						}

						$ac = $ac->first();

						if (!isset($ac)) {
							continue;
						}
						$check = true;
					}

					if (!$check) {
						continue;
					}

					$tmp_qry = DB::connection('bk')->table('users as u')
											       ->where('u.id', $key->user_id);

					$tmp_qry = $this->addCustomFiltersForRevenueOrgs($tmp_qry, $k->name);

					if ($tmp_qry['status'] != "success") {
						continue;
					}

					
					$tmp_qry = $tmp_qry['qry'];
					$tmp_qry = $tmp_qry->first();
					
					if (!isset($tmp_qry)) {
						continue;
					}

					$cnt = $bc->getIndividualCompanyRev($k->name, $start_date, $end_date, $key->user_id);

					if ($cnt <= 0) {
						$ro_to_send = $k;
					}

					if (isset($ro_to_send)) {
						$template_to_send = EmailTemplateSenderProvider::on('bk')
															   ->whereBetween('email_num', [16,34])
															   ->orderBy('email_num');

						$template_to_send = $template_to_send->select('email_num', 'template_name')
			                                     // ->where('email_num', 20)
		                                         ->whereNotIn('template_name', $templates_sent)
			                                     ->first();
			            
			            $params = array();
			            
						$params = $this->getParamsForSpecificEmail($template_to_send->email_num, $key->user_id, $ro_to_send);
						
						// $params = $this->getParamsForSpecificEmail(9, $key->user_id, $ro_id);
						// dd($params);
						// can't send this email.
						if ($params['status'] == 'failed') {
							continue;
						}else{
							break;
						}
					}
				}
				// dd($ro_to_send);
				// dd($ro_to_send);
				// No. Client to choose for this user.
				// if (!isset($ro_to_send)) {
				// 	continue;
				// }
			}else{
				$template_to_send = EmailTemplateSenderProvider::on('bk')
															   ->whereBetween('email_num', [3,15])
															   ->where('email_num', '!=', 2)
															   ->whereNotNull('email_num')			
															   ->orderBy('email_num');
				if ($key->country_id == 1) {
					$template_to_send = $template_to_send->where('email_num', '!=', 13)
										                 ->where('email_num', '!=', 14)	
										                 ->where('email_num', '!=', 15)
										                 ->where('is_international', 0);		   
				}else{
					$template_to_send = $template_to_send->where('is_international', 1);
				}
			
				$template_to_send = $template_to_send->select('email_num', 'template_name', 'ab_test_id');

				if (isset($templates_sent) && !empty($templates_sent)) {
					$template_to_send = $template_to_send->whereNotIn('template_name', $templates_sent);
				}			
			                                         
				$template_to_send = $template_to_send->first();
				
				// No more emails to send for this user.
				if (!isset($template_to_send)) {
					continue;
				}

				$params = array();
				$params = $this->getParamsForSpecificEmail($template_to_send->email_num, $key->user_id);
				// $params = $this->getParamsForSpecificEmail(9, $key->user_id, $ro_id);
				// $this->customdd($template_to_send->email_num);

				// can't send this email.
				// if ($params['status'] == 'failed') {
				// 	continue;
				// }
			}

			if (empty($params) || $params['status'] == "failed") {	

				$upeel 				  = new UsersPortalEmailEffortLog;
				$upeel->user_id 	  = $key->user_id;
				$upeel->template_name = $template_to_send->template_name;
				$upeel->params 		  = json_encode($params);

				$upeel->save();

				$upeecnsl 			  	  = new UsersPortalEmailEffortCouldNotSendLog;
				$upeecnsl->user_id 	  	  = $key->user_id;
				$upeecnsl->template_name  = $template_to_send->template_name;
				$upeecnsl->params 		  = json_encode($params);
				$upeecnsl->upeel_id 	  = $upeel->id;

				$upeecnsl->save();				 

				continue;
			}
			

			// Check if another cron job has already sent an email if so move on to the next one.
			if ($template_to_send->email_num <= 15) {
				$elh = EmailLogicHelper::on('bk')->where('user_id',  $key->user_id)
									  ->select('profile_flow_last_time_sent')
									  ->first();

				if (isset($elh->profile_flow_last_time_sent) && !empty($elh->profile_flow_last_time_sent)) {
					$today = Carbon::now()->toDateString();
					$profile_flow_last_time_sent = Carbon::parse($elh->profile_flow_last_time_sent)->toDateString();

					if ($today == $profile_flow_last_time_sent) {
						continue;
					}
				}
			}else{

				$elh = EmailLogicHelper::on('bk')->where('user_id',  $key->user_id)
									  ->select('choose_last_time_sent')
									  ->first();

				if (isset($elh->choose_last_time_sent) && !empty($elh->choose_last_time_sent)) {
					$today = Carbon::now()->toDateString();
					$choose_last_time_sent = Carbon::parse($elh->choose_last_time_sent)->toDateString();

					if ($today == $choose_last_time_sent) {
						continue;
					}
				}
			}
			
			// Send the email
			$reply_email = "support@plexuss.com";
			$eqp = new EmailQueueProcess($reply_email, $template_to_send->template_name, 
										 $params, $key->email, $key->user_id, NULL, $template_to_send->ab_test_id, 
										 $template_to_send->email_num);
			$eqp->handle();
			$sent_email_cnt++;
		}

		Cache::put( env('ENVIRONMENT') .'_'. 'userEngagementEmailProcess_'.$type, 'done', 10);

		return "num of emails sent: ".$sent_email_cnt;
	}

	private function userEngagementEmailProcessMainQuery($type){
		switch ($type) {
			case 'us_non_complete':
				$qry = DB::connection('bk')
				         ->select("Select 
							`u`.`id` as `user_id` ,
							`u`.`country_id` ,
							`u`.`email`
						from users u
						join email_logic_helper elh on u.id = elh.user_id
						where elh.is_duplicate = 0
						and elh.is_unsubscribed = 0
						and elh.is_inactive = 0
						and elh.is_complete_profile = 0
						and not exists 
							(Select 1 from profile_completion_user_ids pcui
							 where pcui.user_id = u.id)
						and
						#send to user by checking the current template they're in and the last time they received a profile flow related email
						Case when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 3
											and profile_flow_last_template in (2,4,6,8,10,12,14)
											and profile_flow_assignment in ('1a', '1b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 4
											and profile_flow_last_template in (3,5,7,9,11,13)
											and profile_flow_assignment in ('1a', '1b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 2
											and profile_flow_last_template in (2,5,8,11,14)
											and profile_flow_assignment in ('2a', '2b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 2
											and profile_flow_last_template in (3,6,9,12,15)
											and profile_flow_assignment in ('2a', '2b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 3
											and profile_flow_last_template in (4,7,10,13)
											and profile_flow_assignment in ('2a', '2b') then 1	
								 else 0
								END = 1
						and Case when profile_flow_assignment in ('1a', '2a')
										 and (hour(current_timestamp) - 8 + if(hour(current_timestamp) between 0 and 7, 24, 0) + timezone_adjustment) = 15 then 1
								when profile_flow_assignment in ('1b', '2b')
										 and (hour(current_timestamp) - 8 + if(hour(current_timestamp) between 0 and 7, 24, 0) + timezone_adjustment) = 17 then 1
								else 0
							 END = 1
						order by RAND();");
				break;
			case 'us_complete':
				$qry = DB::connection('bk')
				         ->select("Select 
							`u`.`id` as `user_id` ,
							`u`.`country_id` ,
							`u`.`email`
						from users u
						join email_logic_helper elh on u.id = elh.user_id
						where elh.is_duplicate = 0
						and elh.is_unsubscribed = 0
						and elh.is_inactive = 0
						and elh.is_complete_profile = 1
						and
						#send to user by checking their local time and how long it's been since they received a choose template
						Case when datediff(date_sub(current_timestamp, interval 8 hour), elh.choose_last_time_sent) = 3
											and choose_assignment in ('ca3', 'cb3') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.choose_last_time_sent) = 4
											and choose_assignment in ('ca4', 'cb4') then 1
								 else 0
								END = 1

						and Case when choose_assignment in ('ca3', 'ca4')
										 and (hour(current_timestamp) - 8 + if(hour(current_timestamp) between 0 and 7, 24, 0) + timezone_adjustment) = 15 then 1
								when choose_assignment in ('cb3', 'cb4')
										 and (hour(current_timestamp) - 8 + if(hour(current_timestamp) between 0 and 7, 24, 0) + timezone_adjustment) = 17 then 1
								else 0
							 END = 1
						order by RAND();");
				break;
			case 'non_us_non_complete':
				$qry = DB::connection('bk')
				         ->select("Select 
							`u`.`id` as `user_id` ,
							`u`.`country_id` ,
							`u`.`email`
						from users u
						join email_logic_helper elh on u.id = elh.user_id
						where elh.is_duplicate = 0
						and elh.is_unsubscribed = 0
						and elh.is_inactive = 0
						and elh.is_complete_profile = 0
						and not exists 
							(Select 1 from profile_completion_user_ids pcui
							 where pcui.user_id = u.id)
						and
						#send to user by checking the current template they're in and the last time they received a profile flow related email
						#2x per week: i1a and i2b
						Case when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 3
											and profile_flow_last_template in (2,4,6,8,10,12,14)
											and profile_flow_assignment in ('i1a', 'i1b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 4
											and profile_flow_last_template in (3,5,7,9,11,13)
											and profile_flow_assignment in ('i1a', 'i1b') then 1

						#3x per week: i2a and i2b
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 2
											and profile_flow_last_template in (2,5,8,11,14)
											and profile_flow_assignment in ('i2a', 'i2b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 2
											and profile_flow_last_template in (3,6,9,12,15)
											and profile_flow_assignment in ('i2a', 'i2b') then 1
								 when datediff(date_sub(current_timestamp, interval 8 hour), elh.profile_flow_last_time_sent) = 3
											and profile_flow_last_template in (4,7,10,13)
											and profile_flow_assignment in ('i2a', 'i2b') then 1	
								 else 0
								END = 1

						#10 AM for i1a and i2a; 5 pm for i1b and i2b
						and Case when profile_flow_assignment in ('i1a', 'i2a')
										 and (hour(current_timestamp) - 8 + if(hour(current_timestamp) between 0 and 7, 24, 0) + timezone_adjustment) = 10 then 1
								when profile_flow_assignment in ('i1b', 'i2b')
										 and (hour(current_timestamp) - 8 + if(hour(current_timestamp) between 0 and 7, 24, 0) + timezone_adjustment) = 17 then 1
								else 0
							 END = 1
						order by RAND();");
				break;
			default:
				# code...
				break;
		}

		return $qry;
	}

	public function getParamsForSpecificEmail($email_num, $user_id, $ro = NULL){

		// $etsp = EmailTemplateSenderProvider::on('bk')
		//                                ->where('email_num', $email_num)
		//                                ->select('id')
		//                                ->first();
		$params = array();

		switch ($email_num) {
			case 1:
				$user = User::find($user_id);

				if ($user->email_confirmed == 1) {
					$params['status'] = 'failed';
					return $params;
				}

				$confirmation = str_random( 20 );

				$token = new ConfirmToken( array( 'token' => $confirmation ) );
				$user->confirmtoken()->save( $token );

				$params['CTA_LINK'] = env('CURRENT_URL').'confirmemail/'.$confirmation;
				$params['status']   = "success";
				break;
			case 2:
				$params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				$params['status']   = "success";
				break;
			case 3:
				$user = User::on('bk')->where('id', $user_id)
									  ->select('address', 'city', 'country_id')
									  ->first();

				$params['valid_email'] 	 = true;
				$params['valid_address'] = true;

				if (!isset($user->address) || !isset($user->city) || !isset($user->country_id) || 
					empty($user->address)  || empty($user->city)  || empty($user->country_id) ) {
					$params['valid_address'] = false;
				}

				$obj = Objective::on('bk')->where('user_id', $user_id)
										  ->select('major_id')
				                          ->first();

				$params['valid_major'] = true;
				if (!isset($obj->major_id)) {
					$params['valid_major'] = false;
				}

				$rec = Recruitment::on('bk')->where('user_id', $user_id)
											->where('user_recruit', 1)
											->select('id')
											->first();

				$params['valid_college'] = true;
				if (!isset($rec->id)) {
					$params['valid_college'] = false;
				}
				
				$params['CTA_LINK'] 	   = env('CURRENT_URL').'get_started/';
				$params['CTA_UPDATE_INFO'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=update-info';

				$params['status']   = "success";
				break;	
			case 4:
				
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'international-resources/main';
				$params['CTA_LINK'] 			 = env('CURRENT_URL').'get_started/';

				$params['IMG_RESEARCH_COLLEGES'] = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=research-colleges';
				$params['IMG_DISCOVER_MAJOR']    = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=discover-major';
				$params['IMG_FIND_SCHOLARSHIPS'] = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=find-scholarships';
				$params['IMG_EXPLORE_EVENTS']	= env('CURRENT_URL').'get_started?utm_medium=img&utm_content=explore-events';

				$params['IMG_READ_ESSAYS'] 	     = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=read-essays';
				$params['IMG_COMPARE_COLLEGES']  = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=compare-colleges';
				$params['IMG_USE_PORTAL'] 	     = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=use-portal';
				$params['CTA_EXPLORE'] 			 = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=explore';

				break;
			case 5:
				
				$params['CTA_WORLD_COLLEGES'] 	 = env('CURRENT_URL').'college?utm_medium=cta&utm_content=find-by-location';
				$params['CTA_MAJOR_COLLEGES'] 	 = env('CURRENT_URL').'college-majors?utm_medium=cta&utm_content=find-by-major';
				$params['CTA_RANKING_COLLEGES']  = env('CURRENT_URL').'ranking?utm_medium=cta&utm_content=find-by-ranking';
				$params['CTA_CATEGORY_COLLEGES'] = env('CURRENT_URL').'ranking/categories?utm_medium=cta&utm_content=find-by-category';

				$params['status']   = "success";
				break;
			case 6:
				
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'scholarships/';
				$params['IMG_GRAD_CAP'] = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=grad-cap';
				$params['CTA_APPLY_TO_SCHOLARSHIPS'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=apply-to-scholarships';
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				break;
			case 7:

				$params['IMG_GROUP_SINGLE_PHONE'] = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=group-single-phone';
				$params['CTA_EXPLORE_MAJORS'] 	  = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=explore-majors';

				// $params['CTA_FINISH_PROFILE'] = env('CURRENT_URL').'get_started/';
				
				$params['status']   = "success";
				// $params['CTA_DISCIVER_MAJOR'] = env('CURRENT_URL').'college-majors';
				break;
			case 8:
				$params['IMG_APPLE_VS_MUFFIN']  = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=apple-vs-muffin';
				$params['CTA_COMPARE_COLLEGES'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=compare-colleges';
				
				
				$params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				$params['status']   = "success";
				break;
			case 9:

				$user = User::on('bk')->where('id', $user_id)
									  ->select('id', 'country_id', 'state', 'created_at')
									  ->first();

				if ($user->country_id == 1) {
					$nearby_states = $this->getNrccuaNearbyStates($user);

					$qry = DB::connection('bk')->table('aor_colleges as ac')
										   ->join('colleges as c', 'ac.college_id', '=', 'c.id')
										   ->leftjoin('college_overview_images as coi', function($join)
											{
											    $join->on('c.id', '=', 'coi.college_id');
											    $join->on('coi.url', '!=', DB::raw('""'));
											    $join->on('coi.is_video', '=', DB::raw(0));
											    $join->on('coi.is_tour', '=', DB::raw(0));
											})
										   ->where('ac.aor_id', 7)
										   // ->where('ac.active', 1)
										   ->take(4)
										   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
										   			'c.id as college_id',
										   	DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
										   ->groupBy('c.id')
										   ->orderBy('ac.active', 'DESC')
										   ->orderBy(DB::raw("RAND()"));

					if (isset($nearby_states) && !empty($nearby_states)) {
						$qry = $qry->whereIn('c.state', $nearby_states);
					}
					$qry = $qry->get();

					if (empty($qry[0])) {
						$params['status'] = 'failed';
						return $params;
					}

					$i = 1;
					foreach ($qry as $key) {

						if (isset($key->img_url) && $key->img_url != "") {
							$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
						}else{
							$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
						}
						
						$params['SCHOOL'.$i.'_ID']      = $key->college_id;
						$params['SCHOOL'.$i.'_LOGO']    = $key->logo_url;
						$params['SCHOOL'.$i.'_NAME']	= $key->school_name;
						$params['SCHOOL'.$i.'_ADDRESS'] = $key->city. ', '. $key->state;

						$params['COL_IMG_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-img&utm_content='.$key->college_id;
						$params['COL_LOGO_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-logo&utm_content='.$key->college_id;
						$params['COL_NAME_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-name&utm_content='.$key->college_id;

						$i++;
					}
				}else{
					$qry = DB::connection('bk')->table('aor_colleges as ac')
										   ->join('colleges as c', 'ac.college_id', '=', 'c.id')
										   ->leftjoin('college_overview_images as coi', function($join)
											{
											    $join->on('c.id', '=', 'coi.college_id');
											    $join->on('coi.url', '!=', DB::raw('""'));
											    $join->on('coi.is_video', '=', DB::raw(0));
											    $join->on('coi.is_tour', '=', DB::raw(0));
											})
										   ->join('revenue_organizations as ro', 'ro.aor_id', 'ac.aor_id')

										   ->whereIn('ro.aor_id', [28,30])

										   ->take(4)
										   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
										   			'c.id as college_id',
										   	DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
										   ->groupBy('c.id')
										   ->orderBy(DB::raw("RAND()"))
										   ->get();

					$i = 1;
					foreach ($qry as $key) {

						if (isset($key->img_url) && $key->img_url != "") {
							$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
						}else{
							$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
						}
						
						$params['SCHOOL'.$i.'_ID']      = $key->college_id;
						$params['SCHOOL'.$i.'_LOGO']    = $key->logo_url;
						$params['SCHOOL'.$i.'_NAME']	= $key->school_name;
						$params['SCHOOL'.$i.'_ADDRESS'] = $key->city. ', '. $key->state;

						$params['COL_IMG_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-img&utm_content='.$key->college_id;
						$params['COL_LOGO_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-logo&utm_content='.$key->college_id;
						$params['COL_NAME_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-name&utm_content='.$key->college_id;
						$i++;
					}

				}
				

				$params['ro_id']	= 1;
				$params['company']	= 'nrccua';
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				$params['CTA_YES']  = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=yes';
				
				break;
			case 10:
				
				$params['CTA_MANAGE_PORTAL'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=manage-portal';
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				break;			
			case 11:
				

				$user = User::on('bk')->where('id', $user_id)
									  ->select('id', 'country_id', 'state', 'created_at')
									  ->first();

				$nearby_states = $this->getNrccuaNearbyStates($user);

				if ($user->country_id == 1) {

					$nearby_states = $this->getNrccuaNearbyStates($user);
					$qry = DB::connection('bk')->table('aor_colleges as ac')
										   ->join('colleges as c', 'ac.college_id', '=', 'c.id')
										   ->leftjoin('college_overview_images as coi', function($join)
											{
											    $join->on('c.id', '=', 'coi.college_id');
											    $join->on('coi.url', '!=', DB::raw('""'));
											    $join->on('coi.is_video', '=', DB::raw(0));
											    $join->on('coi.is_tour', '=', DB::raw(0));
											})
										   ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
										   ->where('ac.aor_id', 7)
										   // ->where('ac.active', 1)
										   ->take(4)
										   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
										   			'cr.plexuss as rank', 'c.id as college_id',
										   	DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
										   ->groupBy('c.id')
										   ->orderBy('ac.active', 'DESC')
										   ->orderBy(DB::raw("RAND()"));

					if (isset($nearby_states) && !empty($nearby_states)) {
						$qry = $qry->whereIn('c.state', $nearby_states);
					}
				}else{
					$qry = DB::connection('bk')->table('aor_colleges as ac')
										   ->join('colleges as c', 'ac.college_id', '=', 'c.id')
										   ->leftjoin('college_overview_images as coi', function($join)
											{
											    $join->on('c.id', '=', 'coi.college_id');
											    $join->on('coi.url', '!=', DB::raw('""'));
											    $join->on('coi.is_video', '=', DB::raw(0));
											    $join->on('coi.is_tour', '=', DB::raw(0));
											})
										   ->join('revenue_organizations as ro', 'ro.aor_id', 'ac.aor_id')

										   ->whereIn('ro.aor_id', [28,30])

										   ->take(4)
										   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
										   			'c.id as college_id',
										   	DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
										   ->groupBy('c.id')
										   ->orderBy(DB::raw("RAND()"));
				}

				$exclude_school_ids = $this->getExcludeColleges($user_id);

				if (isset($exclude_school_ids)) {
					$qry = $qry->whereNotIn('c.id', $exclude_school_ids);
				}
				
				$qry = $qry->get();

				$i = 1;
				foreach ($qry as $key) {

					if (isset($key->img_url) && $key->img_url != "") {
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
					}else{
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
					}
					
					$params['SCHOOL'.$i.'_ID']      = $key->college_id;
					$params['SCHOOL'.$i.'_LOGO']    = $key->logo_url;
					$params['SCHOOL'.$i.'_NAME']	= $key->school_name;
					$params['SCHOOL'.$i.'_ADDRESS'] = $key->city. ', '. $key->state;
					$params['SCHOOL'.$i.'_RANK']	= isset($key->rank) ? '#'.$key->rank : 'N/A';

					$params['COL_IMG_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-img&utm_content='.$key->college_id;
					$params['COL_LOGO_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-logo&utm_content='.$key->college_id;
					$params['COL_NAME_'.$i] = env('CURRENT_URL').'get_started?utm_medium=col-name&utm_content='.$key->college_id;

					$i++;
				}

				$params['CTA_YES'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=yes';

				$params['ro_id']	= 1;
				$params['company']	= 'nrccua';
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				break;			
			case 12:
				
				$qry = DB::connection('bk')->table('scholarship_verified as sv')
										   ->join('scholarship_providers as sp', 'sv.provider_id', '=', 'sp.id')
										   ->join('revenue_organizations as ro', 'ro.id', '=', 'sv.ro_id')
										   ->select('ro.id as ro_id', 'ro.name as company', 'sv.max_amount',
										   		    'sp.company_name as provider_name', 
										   			'sv.scholarship_title', 'sv.deadline', 'sv.website')
										   ->take(3)
										   ->get();

				$i = 1;
				foreach ($qry as $key) {
					$params['SCHOLARSHIP'.$i.'_NAME']     = $key->scholarship_title;
					$params['SCHOLARSHIP'.$i.'_PROVIDER'] = $key->provider_name;
					$params['SCHOLARSHIP'.$i.'_AMOUNT']   = $key->max_amount;
					$params['SCHOLARSHIP'.$i.'_DEADLINE'] = $key->deadline;
					$params['SCHOLARSHIP'.$i.'_RO_ID'] 	  = $key->ro_id;
					$params['SCHOLARSHIP'.$i.'_COMPANY']  = $key->company;

					$i++;
				}

				$params['CTA_YES']  = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=yes';
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';			
				break;	
			case 13:
				
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';	
				$params['IMG_US_VISA'] = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=us-visa';	
				$params['CTA_LEARN_MORE'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=learn-more';	
				
				break;
			case 14:
				
				
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				$params['IMG_DIVERSE_GROUP_HUG'] = env('CURRENT_URL').'get_started?utm_medium=img&utm_content=diverse-group-hug';
				$params['CTA_LEARN_MORE'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=learn-more';

				break;
			case 15:
				
				$params['CTA_COMPLETE_PROFILE'] = env('CURRENT_URL').'get_started?utm_medium=cta&utm_content=complete-profile';
				$params['status']   = "success";
				// $params['CTA_LINK'] = env('CURRENT_URL').'get_started/';
				break;
			case 16:
				
				$params = $this->getValuesForChoose1EmailsPortalPart($params, $user_id, $ro);
				
				if ($params['status'] == 'failed') {
					return $params;
				}

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;
				break;
			case 17:
				
				$params = $this->getValuesForChoose1EmailsPortalPart($params, $user_id, $ro);
				
				if ($params['status'] == 'failed') {
					return $params;
				}
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;
				break;
			case 18:
				
				$params = $this->getValuesForChoose1EmailsPortalPart($params, $user_id, $ro, NULL, true);
				
				if ($params['status'] == 'failed') {
					return $params;
				}
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;
				break;
			case 19:
				
				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id')
				                     ->join('college_programs as cp', 'cp.college_id', '=', 'c.id')
									 ->join('objectives as o', function($join) use($user_id){
										$join->on('cp.degree_type', '=', 'o.degree_type')
										     // ->on('cp.major_id', '=', 'o.major_id')
											 
											 ->where('o.user_id', '=', $user_id);
								 	 })
								 	 ->join('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								 	 ->join('majors as m', 'm.id', '=', 'o.major_id')
								 	 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city', 'dt.display_name', 			  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->take(1)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				

				foreach ($eligible_colleges as $key) {
					$params['SCHOOL1_ID']		 	  = $key->college_id;
					$params['DEGREE_DISPLAY_NAME']	  = $key->display_name;
					$params['SCHOOL_LOGO']	 	 	  = $key->logo_url;
					$params['SCHOOL_NAME']	 	 	  = $key->school_name;
					$params['COUNTRY_CODE']  	 	  = $key->country_code;	
					$params['CITY_COUNTRY_CODE'] 	  = $key->city.', '.$key->country_code;
				}
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;				
				break;
			case 20:
				
				$qry = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$qry = $qry->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						       ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$qry = $qry->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						       ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}

				$qry = $qry->leftjoin('college_overview_images as coi', function($join)
							{
							    $join->on('c.id', '=', 'coi.college_id');
							    $join->on('coi.url', '!=', DB::raw('""'));
							    $join->on('coi.is_video', '=', DB::raw(0));
							    $join->on('coi.is_tour', '=', DB::raw(0));
							})
						   ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')

						   ->where('ro.id', $ro->id)

						   ->take(3)
						   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
									'cr.plexuss as rank', 'c.id as college_id',
									DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
						   ->groupBy('c.id')
						   ->orderBy(DB::raw("RAND()"));

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$qry = $qry->whereNotIn('c.id', $exclude_school_ids);
				}

				$qry = $qry->get();

				if (empty($qry[0])) {
					$params['status'] = 'failed';
					return $params;
				}

				$college_id_arr = array();
				$i = 1;
				foreach ($qry as $key) {

					$college_id_arr[] = $key->college_id;
					if (isset($key->img_url) && $key->img_url != "") {
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
					}else{
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
					}
					
					$params['SCHOOL'.$i.'_ID']      = $key->college_id;
					$params['SCHOOL'.$i.'_LOGO']    = $key->logo_url;
					$params['SCHOOL'.$i.'_NAME']	= $key->school_name;
					$params['SCHOOL'.$i.'_ADDRESS'] = $key->city. ', '. $key->state;
					$params['SCHOOL'.$i.'_RANK']	= isset($key->rank) ? '#'.$key->rank : 'N/A';

					$i++;
				}
				
				$college_id_str = implode(",", $college_id_arr);

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 21:

				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id')
				                     ->join('college_programs as cp', 'cp.college_id', '=', 'c.id')
									 ->join('objectives as o', function($join) use($user_id){
										$join->on('cp.degree_type', '=', 'o.degree_type')
										     // ->on('cp.major_id', '=', 'o.major_id')
											 
											 ->where('o.user_id', '=', $user_id);
								 	 })
								 	 ->join('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								 	 ->join('majors as m', 'm.id', '=', 'o.major_id')
								 	 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city', 'dt.display_name', 			  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->take(1)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				

				foreach ($eligible_colleges as $key) {
					$params['SCHOOL1_ID']		 	  = $key->college_id;
					$params['DEGREE_DISPLAY_NAME']	  = $key->display_name;
					$params['SCHOOL_LOGO']	 	 	  = $key->logo_url;
					$params['SCHOOL_NAME']	 	 	  = $key->school_name;
					$params['COUNTRY_CODE']  	 	  = $key->country_code;	
					$params['CITY_COUNTRY_CODE'] 	  = $key->city.', '.$key->country_code;
				}
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";
				$uid 				= Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;
				break;	
			case 22:
				
				$user = User::on('bk')->where('id', $user_id)
									  ->select('id', 'financial_firstyr_affordibility')
									  ->first();

				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id')
				                     ->join('college_programs as cp', 'cp.college_id', '=', 'c.id')
									 ->join('objectives as o', function($join) use($user_id){
										$join->on('cp.degree_type', '=', 'o.degree_type')
										     // ->on('cp.major_id', '=', 'o.major_id')
											 
											 ->where('o.user_id', '=', $user_id);
								 	 })
								 	 ->join('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								 	 ->join('majors as m', 'm.id', '=', 'o.major_id')
								 	 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city', 'dt.display_name', 			  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->take(1)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				

				foreach ($eligible_colleges as $key) {
					$params['SCHOOL1_ID']		 	  = $key->college_id;
					$params['DEGREE_DISPLAY_NAME']	  = $key->display_name;
					$params['SCHOOL_LOGO']	 	 	  = $key->logo_url;
					$params['SCHOOL_NAME']	 	 	  = $key->school_name;
					$params['COUNTRY_CODE']  	 	  = $key->country_code;	
					$params['CITY_COUNTRY_CODE'] 	  = $key->city.', '.$key->country_code;
				}
				$params['FINANCIAL_AVAILABILITY'] = isset($user->financial_firstyr_affordibility) ? $user->financial_firstyr_affordibility : NULL;

				if (isset($params['FINANCIAL_AVAILABILITY'])) {
					$params['FINANCIAL_AVAILABILITY'] = str_replace("- ", "- $", $params['FINANCIAL_AVAILABILITY']);
					$params['FINANCIAL_AVAILABILITY'] = '$'.$params['FINANCIAL_AVAILABILITY'];
				}

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid 				= Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;
				break;	
			case 23:
				$user = User::on('bk')->where('id', $user_id)
									  ->select('id', 'interested_school_type')
									  ->first();

				if ($user->interested_school_type == 0 || !isset($user->interested_school_type)) {
					$params['status'] = 'failed';
					return $params;
				}

				$params = $this->getValuesForChoose1EmailsPortalPart($params, $user_id, $ro, true);
				
				if ($params['status'] == 'failed') {
					return $params;
				}
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";

				if ($user->interested_school_type == 1) {
					$params['SCHOOL_TYPE'] = 'Online-Only';
				}else{
					$params['SCHOOL_TYPE'] = 'Online and On Campus';	
				}
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$params['SCHOOL1_ID'].'&en='.$email_num;
				break;
			case 24:
				
				$qry = DB::connection('bk')->table('scholarship_verified as sv')
										   ->join('scholarship_providers as sp', 'sv.provider_id', '=', 'sp.id')
										   ->join('revenue_organizations as ro', 'ro.id', '=', 'sv.ro_id')
										   ->select('ro.id as ro_id', 'ro.name as company', 'sv.max_amount',
										   		    'sp.company_name as provider_name', 
										   			'sv.scholarship_title', 'sv.deadline', 'sv.website', 
										   			'sv.id as scholarship_id')
										   ->take(3)
										   ->get();

				$i = 1;
				$scholarship_id_arr = array();				
				foreach ($qry as $key) {
					$scholarship_id_arr[] = $key->scholarship_id;
					$params['SCHOLARSHIP'.$i.'_ID']       = $key->scholarship_id;
					$params['SCHOLARSHIP'.$i.'_NAME']     = $key->scholarship_title;
					$params['SCHOLARSHIP'.$i.'_PROVIDER'] = $key->provider_name;
					$params['SCHOLARSHIP'.$i.'_AMOUNT']   = $key->max_amount;
					$params['SCHOLARSHIP'.$i.'_DEADLINE'] = $key->deadline;
					$params['SCHOLARSHIP'.$i.'_RO_ID'] 	  = $key->ro_id;
					$params['SCHOLARSHIP'.$i.'_COMPANY']  = $key->company;

					$i++;
				}
				$scholarship_id_str = implode(",", $scholarship_id_arr);
				$uid = Crypt::encrypt($user_id);

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&sid='.$scholarship_id_str.'&en='.$email_num;
				break;
			case 25:
				
				$qry = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$qry = $qry->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						       ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$qry = $qry->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						       ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}

				$qry = $qry->leftjoin('college_overview_images as coi', function($join)
							{
							    $join->on('c.id', '=', 'coi.college_id');
							    $join->on('coi.url', '!=', DB::raw('""'));
							    $join->on('coi.is_video', '=', DB::raw(0));
							    $join->on('coi.is_tour', '=', DB::raw(0));
							})
						   ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')

						   ->where('ro.id', $ro->id)

						   ->take(5)
						   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
						   			'c.general_phone as phone', 'c.address', 'c.zip',
									'cr.plexuss as rank', 'c.id as college_id',
									DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
						   ->groupBy('c.id')
						   ->orderBy(DB::raw("RAND()"));

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$qry = $qry->whereNotIn('c.id', $exclude_school_ids);
				}

				$qry = $qry->get();

				if (empty($qry[0])) {
					$params['status'] = 'failed';
					return $params;
				}

				$college_id_arr = array();
				$i = 1;
				foreach ($qry as $key) {

					$college_id_arr[] = $key->college_id;
					if (isset($key->img_url) && $key->img_url != "") {
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
					}else{
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
					}
					
					$params['SCHOOL'.$i.'_ID']         = $key->college_id;
					$params['SCHOOL'.$i.'_LOGO']       = $key->logo_url;
					$params['SCHOOL'.$i.'_NAME']	   = $key->school_name;
					$params['SCHOOL'.$i.'_ADDRESS']    = $key->address . ", ". $key->city. ", ".$key->state. ", ".$key->zip;
					$params['SCHOOL'.$i.'_RANK']	   = isset($key->rank) ? '#'.$key->rank : 'N/A';
					$params['SCHOOL'.$i.'_CITY_STATE'] = $key->city. ", ".$key->state;
					$params['SCHOOL'.$i.'_PHONE']	   = $key->phone;
					$i++;
				}
				
				$college_id_str 	= implode(",", $college_id_arr);
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 26:
				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                                   ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}

				$eligible_colleges = $eligible_colleges->join('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
											 ->join('colleges_admissions as ca' , 'ca.college_id', '=', 'c.id')
											 ->join('colleges_tuition as ct', 'ct.college_id', '=', 'c.id')
											 ->join('college_programs as cp', 'cp.college_id', '=', 'c.id')
											 
											 ->join('objectives as o', function($join) use($user_id){
												$join->on('cp.major_id', '=', 'o.major_id')
													 // ->on('cp.degree_type', '=', 'o.degree_type')
													 ->where('o.user_id', '=', $user_id);
										 	 })
										 	 ->join('majors as m', 'm.id', '=', 'o.major_id')
										 	 ->leftjoin('college_overview_images as coi', function($join)
													{
													    $join->on('c.id', '=', 'coi.college_id');
													    $join->on('coi.url', '!=', DB::raw('""'));
													    $join->on('coi.is_video', '=', DB::raw(0));
													    $join->on('coi.is_tour', '=', DB::raw(0));
													})

										 	 ->where('ro.id', $ro->id)
											 
										 	 ->orderBy('cr.plexuss', 'asc')
											 ->select('m.name as major_name', 'ca.sat_read_75', 'ca.sat_write_75', 
											 	      'ca.sat_math_75', 'c.id as college_id', 'c.address', 'c.city', 'c.state', 
													  'c.zip', 'c.school_name as school_name', 'cr.plexuss as rank', 
													  'ca.act_composite_75 as act', 'ct.tuition_avg_in_state_ftug as inStateTuition','ct.tuition_avg_out_state_ftug as outStateTuition', 
													  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
													THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
													ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'), 'coi.url as img_url', 'c.general_phone as phone', 'c.id as college_id')
											 
											 ->groupBy('c.id')
											 ->take(4);

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();
				
				
				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}

				$college_id_arr = array();
				$i = 1;
				foreach ($eligible_colleges as $key) {
					$college_id_arr[] = $key->college_id;

					$params['SCHOOL'.$i.'_LOGO']       = $key->logo_url;
					$params['SCHOOL'.$i.'_ID']			 = $key->college_id;
					$params['SCHOOL'.$i.'_NAME']	     = $key->school_name;
					$params['SCHOOL'.$i.'_ADDRESS']		 = $key->address . ", ". $key->city. ", ".$key->state. ", ".$key->zip;
					$params['SCHOOL'.$i.'_PHONE']		 = $key->phone;
					$params['SCHOOL'.$i.'_CITY_STATE']	 = $key->city. ", ".$key->state;
					
					if (isset($key->img_url) && $key->img_url != "") {
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
					}else{
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
					}

					$i++;
				}
				$college_id_str = implode(",", $college_id_arr);

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 27:
				
				$user = User::on('bk')->where('id', $user_id)
									  ->select('id', 'interested_school_type')
									  ->first();

				if ($user->interested_school_type == 0 || !isset($user->interested_school_type)) {
					$params['status'] = 'failed';
					return $params;
				}

				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                                   ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
											 
				// Is online school.
				$eligible_colleges = $eligible_colleges->where('c.is_online', 1);
				

				$eligible_colleges = $eligible_colleges->join('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
											 ->join('colleges_admissions as ca' , 'ca.college_id', '=', 'c.id')
											 ->join('colleges_tuition as ct', 'ct.college_id', '=', 'c.id')
											 
										 	 ->leftjoin('college_overview_images as coi', function($join)
													{
													    $join->on('c.id', '=', 'coi.college_id');
													    $join->on('coi.url', '!=', DB::raw('""'));
													    $join->on('coi.is_video', '=', DB::raw(0));
													    $join->on('coi.is_tour', '=', DB::raw(0));
													})

										 	 ->where('ro.id', $ro->id)
											 
										 	 ->orderBy('cr.plexuss', 'asc')
											 ->select('ca.sat_read_75', 'ca.sat_write_75', 
											 	      'ca.sat_math_75', 'c.id as college_id', 'c.address', 'c.city', 'c.state', 
													  'c.zip', 'c.school_name as school_name', 'cr.plexuss as rank', 
													  'ca.act_composite_75 as act', 'ct.tuition_avg_in_state_ftug as inStateTuition','ct.tuition_avg_out_state_ftug as outStateTuition', 
													  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
													THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
													ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'), 'coi.url as img_url', 'c.general_phone as phone', 'c.id as college_id')
											 
											 ->groupBy('c.id')
											 ->take(4);

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();
				
				
				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				
				$college_id_arr = array();
				$i = 1;
				foreach ($eligible_colleges as $key) {
					$college_id_arr[] = $key->college_id;

					$params['SCHOOL'.$i.'_LOGO']         = $key->logo_url;
					$params['SCHOOL'.$i.'_ID']			 = $key->college_id;
					$params['SCHOOL'.$i.'_NAME']	     = $key->school_name;
					$params['SCHOOL'.$i.'_ADDRESS']		 = $key->address . ", ". $key->city. ", ".$key->state. ", ".$key->zip;
					$params['SCHOOL'.$i.'_PHONE']		 = $key->phone;
					$params['SCHOOL'.$i.'_CITY_STATE']	 = $key->city. ", ".$key->state;
					
					if (isset($key->img_url) && $key->img_url != "") {
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
					}else{
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
					}

					$i++;
				}
				$college_id_str = implode(",", $college_id_arr);

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";

				if ($user->interested_school_type == 1) {
					$params['SCHOOL_TYPE'] = 'Online-Only';
				}else{
					$params['SCHOOL_TYPE'] = 'Online and On Campus';	
				}
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 28:
				
				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id') 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city', 			  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->take(6)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				
				$college_id_arr = array();
				$i = 1;
				foreach ($eligible_colleges as $key) {
					$college_id_arr[] = $key->college_id;

					$params['SCHOOL'.$i.'_LOGO']         = $key->logo_url;
					$params['SCHOOL'.$i.'_ID']			 = $key->college_id;
					$params['SCHOOL'.$i.'_NAME']	     = $key->school_name;
					$params['SCHOOL'.$i.'_CITY_COUNTRY'] = $key->city.', '.$key->country_code;
					$params['COUNTRY_CODE']  	 	     = $key->country_code;	

					$i++;
				}

				$college_id_str = implode(",", $college_id_arr);
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 29:
				$locationArr = $this->iplookup($user_id);

				if (!isset($locationArr['latitude']) || empty($locationArr['latitude'])) {
					$locationArr['latitude'] = '37.910078';
					$locationArr['longitude'] = '-122.065182';
				}

				$query = '( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) ) AS distance, ';

				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id') 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city', DB::raw($query),			  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->orderBy('distance', 'ASC')
									 ->take(6)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				
				$college_id_arr = array();
				$i = 1;
				foreach ($eligible_colleges as $key) {
					$college_id_arr[] = $key->college_id;
					
					$params['SCHOOL'.$i.'_LOGO']         = $key->logo_url;
					$params['SCHOOL'.$i.'_ID']			 = $key->college_id;
					$params['SCHOOL'.$i.'_NAME']	     = $key->school_name;
					$params['SCHOOL'.$i.'_CITY_COUNTRY'] = $key->city.', '.$key->country_code;
					$params['COUNTRY_CODE']  	 	     = $key->country_code;	

					$i++;
				}

				$college_id_str = implode(",", $college_id_arr);
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']  	= "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 30:
				
				$user = User::on('bk')->where('id', $user_id)
									  ->select('id', 'financial_firstyr_affordibility')
									  ->first();

				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id') 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city',		  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->take(6)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				
				$college_id_arr = array();
				$i = 1;
				foreach ($eligible_colleges as $key) {
					$college_id_arr[] = $key->college_id;
					
					$params['SCHOOL'.$i.'_LOGO']         = $key->logo_url;
					$params['SCHOOL'.$i.'_ID']			 = $key->college_id;
					$params['SCHOOL'.$i.'_NAME']	     = $key->school_name;
					$params['SCHOOL'.$i.'_CITY_COUNTRY'] = $key->city.', '.$key->country_code;
					$params['COUNTRY_CODE']  	 	     = $key->country_code;	

					$i++;
				}

				$college_id_str = implode(",", $college_id_arr);


				$params['FINANCIAL_AVAILABILITY'] = isset($user->financial_firstyr_affordibility) ? $user->financial_firstyr_affordibility : NULL;

				if (isset($params['FINANCIAL_AVAILABILITY'])) {
					$params['FINANCIAL_AVAILABILITY'] = str_replace("- ", "- $", $params['FINANCIAL_AVAILABILITY']);
					$params['FINANCIAL_AVAILABILITY'] = '$'.$params['FINANCIAL_AVAILABILITY'];
				}

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid 				= Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 31:
				
				$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$eligible_colleges = $eligible_colleges
					                     ->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						                 ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$eligible_colleges = $eligible_colleges
					                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}
										

				$eligible_colleges = $eligible_colleges
									 ->join('countries as ct', 'c.country_id', '=', 'ct.id')
				                     ->join('college_programs as cp', 'cp.college_id', '=', 'c.id')
									 ->join('objectives as o', function($join) use($user_id){
										$join->on('cp.degree_type', '=', 'o.degree_type')
										     // ->on('cp.major_id', '=', 'o.major_id')
											 
											 ->where('o.user_id', '=', $user_id);
								 	 })
								 	 ->join('degree_type as dt', 'dt.id', '=', 'o.degree_type')
								 	 ->join('majors as m', 'm.id', '=', 'o.major_id')
								 	 
								 	 ->where('ro.id', $ro->id)
									 
									 ->select('c.school_name as school_name', 'ct.country_code', 
									 	      'c.id as college_id', 'c.city', 'dt.display_name', 			  
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
									 
									 ->take(6)
									 ->groupBy('c.id');

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
				}

				$eligible_colleges = $eligible_colleges->get();

				if (empty($eligible_colleges[0])) {
					$params['status'] = 'failed';
					return $params;
				}
				
				$college_id_arr = array();
				$i = 1;
				foreach ($eligible_colleges as $key) {
					$college_id_arr[] = $key->college_id;
					
					$params['SCHOOL'.$i.'_LOGO']         = $key->logo_url;
					$params['SCHOOL'.$i.'_ID']			 = $key->college_id;
					$params['SCHOOL'.$i.'_NAME']	     = $key->school_name;
					$params['SCHOOL'.$i.'_CITY_COUNTRY'] = $key->city.', '.$key->country_code;
					$params['COUNTRY_CODE']  	 	     = $key->country_code;	
					$params['DEGREE_DISPLAY_NAME']	  	 = $key->display_name;

					$i++;
				}

				$college_id_str = implode(",", $college_id_arr);
				
				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$uid 				= Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;	
				break;
			case 32:
				
				$qry = DB::connection('bk')->table('revenue_organizations as ro');

				if (isset($ro->aor_id)) {
					$qry = $qry->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
						       ->join('colleges as c', 'c.id', '=', 'ac.college_id');
				}else{
					$qry = $qry->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
						       ->join('colleges as c', 'c.id', '=', 'ob.school_id');
				}

				$qry = $qry->leftjoin('college_overview_images as coi', function($join)
							{
							    $join->on('c.id', '=', 'coi.college_id');
							    $join->on('coi.url', '!=', DB::raw('""'));
							    $join->on('coi.is_video', '=', DB::raw(0));
							    $join->on('coi.is_tour', '=', DB::raw(0));
							})
						   ->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')

						   ->where('ro.id', $ro->id)

						   ->take(5)
						   ->select('c.school_name', 'c.city', 'c.state', 'coi.url as img_url', 
									'cr.plexuss as rank', 'c.id as college_id',
									DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'))
						   ->groupBy('c.id')
						   ->orderBy(DB::raw("RAND()"));

				$exclude_school_ids = $this->getExcludeColleges($user_id);
				
				if (isset($exclude_school_ids)) {
					$qry = $qry->whereNotIn('c.id', $exclude_school_ids);
				}

				$qry = $qry->get();

				if (empty($qry[0])) {
					$params['status'] = 'failed';
					return $params;
				}

				$college_id_arr = array();
				$i = 1;
				foreach ($qry as $key) {

					$college_id_arr[] = $key->college_id;
					if (isset($key->img_url) && $key->img_url != "") {
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
					}else{
						$params['SCHOOL'.$i.'_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
					}
					
					$params['SCHOOL'.$i.'_ID']      = $key->college_id;
					$params['SCHOOL'.$i.'_LOGO']    = $key->logo_url;
					$params['SCHOOL'.$i.'_NAME']	= $key->school_name;
					$params['SCHOOL'.$i.'_ADDRESS'] = $key->city. ', '. $key->state;
					$params['SCHOOL'.$i.'_RANK']	= isset($key->rank) ? '#'.$key->rank : 'N/A';

					$i++;
				}
				
				$college_id_str = implode(",", $college_id_arr);

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   			 = "success";
				$uid = Crypt::encrypt($user_id);
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&cid='.$college_id_str.'&en='.$email_num;
				break;
			case 33:
				
				$qry = DB::connection('bk')->table('scholarship_verified as sv')
										   ->join('scholarship_providers as sp', 'sv.provider_id', '=', 'sp.id')
										   ->join('revenue_organizations as ro', 'ro.id', '=', 'sv.ro_id')
										   ->select('ro.id as ro_id', 'ro.name as company', 'sv.max_amount',
										   		    'sp.company_name as provider_name', 
										   			'sv.scholarship_title', 'sv.deadline', 'sv.website', 
										   			'sv.id as scholarship_id')
										   ->take(3)
										   ->get();

				$i = 1;
				$scholarship_id_arr = array();				
				foreach ($qry as $key) {
					$scholarship_id_arr[] = $key->scholarship_id;
					$params['SCHOLARSHIP'.$i.'_ID']       = $key->scholarship_id;
					$params['SCHOLARSHIP'.$i.'_NAME']     = $key->scholarship_title;
					$params['SCHOLARSHIP'.$i.'_PROVIDER'] = $key->provider_name;
					$params['SCHOLARSHIP'.$i.'_AMOUNT']   = $key->max_amount;
					$params['SCHOLARSHIP'.$i.'_DEADLINE'] = $key->deadline;
					$params['SCHOLARSHIP'.$i.'_RO_ID'] 	  = $key->ro_id;
					$params['SCHOLARSHIP'.$i.'_COMPANY']  = $key->company;

					$i++;
				}
				$scholarship_id_str = implode(",", $scholarship_id_arr);
				$uid = Crypt::encrypt($user_id);

				$params['ro_id']    = $ro->id;
				$params['company']  = $ro->name;
				$params['status']   = "success";
				$params['CTA_LINK'] = env('CURRENT_URL').'emailGateway/?uid='.$uid.'&sid='.$scholarship_id_str.'&en='.$email_num;
				break;
			default:
				# code...
				break;
		}

		return $params;
	}

	public function setCollegeDataForApis(){

		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_setCollegeDataForApis')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_setCollegeDataForApis');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_setCollegeDataForApis', 'in_progress', 40);

		$url = 'https://api.data.gov/ed/collegescorecard//v1/schools?api_key=9Y6Pj3vOK5uCfSSkRO0Bw6Wqh3zxI3HfQjreTALT&fields=id,school.name,id,school.city,school.state_fips,school.state,school.zip,school.school_url,school.price_calculator_url,school.locale,school.institutional_characteristics.level,school.ownership,school.alias,location.lon,location.lat,latest.student.enrollment.all,school.religious_affiliation,latest.completion.completion_rate_4yr_150nt,school.accreditor,latest.cost.tuition.in_state,latest.cost.tuition.out_of_state,latest.cost.net_price.private.by_income_level.0-30000,latest.cost.net_price.public.by_income_level.0-30000,latest.cost.net_price.private.by_income_level.30001-48000,latest.cost.net_price.public.by_income_level.30001-48000,latest.cost.net_price.private.by_income_level.48001-75000,latest.cost.net_price.public.by_income_level.48001-75000,latest.cost.net_price.private.by_income_level.75001-110000,latest.cost.net_price.public.by_income_level.75001-110000,latest.cost.net_price.private.by_income_level.110001-plus,latest.cost.net_price.public.by_income_level.110001-plus,school.open_admissions_policy,latest.admissions.admission_rate.overall,latest.admissions.sat_scores.75th_percentile.critical_reading,latest.admissions.sat_scores.25th_percentile.critical_reading,latest.admissions.sat_scores.75th_percentile.writing,latest.admissions.sat_scores.25th_percentile.writing,latest.admissions.sat_scores.75th_percentile.math,latest.admissions.sat_scores.25th_percentile.math,latest.admissions.act_scores.75th_percentile.cumulative,latest.admissions.act_scores.25th_percentile.cumulative,latest.admissions.act_scores.75th_percentile.english,latest.admissions.act_scores.25th_percentile.english,latest.admissions.act_scores.75th_percentile.math,latest.admissions.act_scores.25th_percentile.math,latest.admissions.act_scores.75th_percentile.writing,latest.admissions.act_scores.25th_percentile.writing,latest.aid.pell_grant_rate,latest.aid.federal_loan_rate&per_page=100&page=';

		if (Cache::has( env('ENVIRONMENT') .'_'. 'setCollegeDataForApis_page_num')) {
			$page_num = Cache::get( env('ENVIRONMENT') .'_'. 'setCollegeDataForApis_page_num');

			Cache::put( env('ENVIRONMENT') .'_'. 'setCollegeDataForApis_page_num', $page_num + 1, 1440);
		}else{
			$page_num = 0;
			Cache::put( env('ENVIRONMENT') .'_'. 'setCollegeDataForApis_page_num', $page_num + 1, 1440);
		}

		$url .= $page_num;

	    $client = new Client(['base_uri' => 'http://httpbin.org']);

	    $cdfam = CollegesDataFromApisMapping::on('bk')->get();

	    $arr = array();
	    foreach ($cdfam as $key) {
	    	$arr[$key->api_column] = $key->plex_column;
	    }

	    try {
	        
	        $response = $client->request('GET', $url);
	        $response = json_decode($response->getBody()->getContents(), true);

	        if (empty($response['results'])) {
	        	Cache::put( env('ENVIRONMENT') .'_'. 'setCollegeDataForApis_page_num', 0, 1440);
	        }

	        foreach ($response['results'] as $k => $v) {

	        	$attr = array();
	        	$val  = array();

	        	foreach ($v as $key => $value) {
	        		if ($key == "id") {
		        		$attr["ipeds_id"] = $value;
		        		$val["ipeds_id"]  = $value;
		        		continue;
		        	}

		        	if ($arr[$key] == "graduation_rate_4_year_150" || $arr[$key] == "percent_admitted" || 
		        		$arr[$key] == "undergrad_grant_pct" || $arr[$key] == "undergrad_loan_pct" ) {
		        		$value = round($value*100);

		        		if ($value == 0) {
		        			$value = NULL;
		        		}
		        	}
		        	$val[$arr[$key]] = $value;
	        	}

	        	CollegesDataFromApi::updateOrCreate($attr, $val);
	        }


	    } catch (Exception $e) {
	    	return "failed";
	    }

	    Cache::put( env('ENVIRONMENT') .'_'. 'is_setCollegeDataForApis', 'done', 40);
	    return "ran page num: ". $page_num;
	}

	public function updateCollegeDataForApi(){

		DB::statement("update colleges_data_from_apis cdfa
					join colleges c on c.ipeds_id = cdfa.ipeds_id
					join colleges_tuition ct on c.id = ct.college_id
					join colleges_admissions ca on c.id = ca.college_id
					join colleges_financial_aid cfa on c.id = cfa.college_id
					SET c.school_url = 
						coalesce(if(length(cdfa.school_url) > 3, cdfa.school_url, null), if(length(c.school_url) > 3, c.school_url, null))
					, c.calculator_url =
						coalesce(if(length(cdfa.calculator_url) > 3, cdfa.calculator_url, null), if(length(c.calculator_url) > 3, c.calculator_url, null))
					, c.undergrad_enroll_1112 =
						coalesce(if(cdfa.undergrad_enroll_1112 > 0, cdfa.undergrad_enroll_1112, null), if(c.undergrad_enroll_1112 > 0, c.undergrad_enroll_1112, null))
					, c.graduation_rate_4_year_150 =
						coalesce(if(cdfa.graduation_rate_4_year_150 > 0, cdfa.graduation_rate_4_year_150, null), if(c.graduation_rate_4_year_150 > 0, c.graduation_rate_4_year_150, null))

					, ct.tuition_avg_in_state_ftug =
						coalesce(if(cdfa.tuition_avg_in_state_ftug >  0, cdfa.tuition_avg_in_state_ftug, null), if(ct.tuition_avg_in_state_ftug > 0, ct.tuition_avg_in_state_ftug, null))
					, ct.tuition_avg_out_state_ftug =
						coalesce(if(cdfa.tuition_avg_out_state_ftug >  0, cdfa.tuition_avg_out_state_ftug, null), if(ct.tuition_avg_out_state_ftug > 0, ct.tuition_avg_out_state_ftug, null))
					, ct.private_net_prc_avg_faid_030k =
						coalesce(if(cdfa.private_net_prc_avg_faid_030k >  0, cdfa.private_net_prc_avg_faid_030k, null), if(ct.private_net_prc_avg_faid_030k > 0, ct.private_net_prc_avg_faid_030k, null))
					, ct.public_net_prc_avg_faid_030k =
						coalesce(if(cdfa.public_net_prc_avg_faid_030k >  0, cdfa.public_net_prc_avg_faid_030k, null), if(ct.public_net_prc_avg_faid_030k > 0, ct.public_net_prc_avg_faid_030k, null))
					, ct.private_net_prc_avg_faid_30k48k =
						coalesce(if(cdfa.private_net_prc_avg_faid_30k48k >  0, cdfa.private_net_prc_avg_faid_30k48k, null), if(ct.private_net_prc_avg_faid_30k48k > 0, ct.private_net_prc_avg_faid_30k48k, null))
					, ct.public_net_prc_avg_faid_30k48k =
						coalesce(if(cdfa.public_net_prc_avg_faid_30k48k >  0, cdfa.public_net_prc_avg_faid_30k48k, null), if(ct.public_net_prc_avg_faid_30k48k > 0, ct.public_net_prc_avg_faid_30k48k, null))
					, ct.private_net_prc_avg_faid_48k75k =	
						coalesce(if(cdfa.private_net_prc_avg_faid_48k75k >  0, cdfa.private_net_prc_avg_faid_48k75k, null), if(ct.private_net_prc_avg_faid_48k75k > 0, ct.private_net_prc_avg_faid_48k75k, null))
					, ct.public_net_prc_avg_faid_48k75k =	
						coalesce(if(cdfa.public_net_prc_avg_faid_48k75k >  0, cdfa.public_net_prc_avg_faid_48k75k, null), if(ct.public_net_prc_avg_faid_48k75k > 0, ct.public_net_prc_avg_faid_48k75k, null))
					, ct.private_net_prc_avg_faid_75k110k =	
						coalesce(if(cdfa.private_net_prc_avg_faid_75k110k >  0, cdfa.private_net_prc_avg_faid_75k110k, null), if(ct.private_net_prc_avg_faid_75k110k > 0, ct.private_net_prc_avg_faid_75k110k, null))
					, ct.public_net_prc_avg_faid_75k110k =	
						coalesce(if(cdfa.public_net_prc_avg_faid_75k110k >  0, cdfa.public_net_prc_avg_faid_75k110k, null), if(ct.public_net_prc_avg_faid_75k110k > 0, ct.public_net_prc_avg_faid_75k110k, null))
					, ct.private_net_prc_avg_faid_110kover =	
						coalesce(if(cdfa.private_net_prc_avg_faid_110kover >  0, cdfa.private_net_prc_avg_faid_110kover, null), if(ct.private_net_prc_avg_faid_110kover > 0, ct.private_net_prc_avg_faid_110kover, null))
					, ct.public_net_prc_avg_faid_110kover =
						coalesce(if(cdfa.public_net_prc_avg_faid_110kover >  0, cdfa.public_net_prc_avg_faid_110kover, null), if(ct.public_net_prc_avg_faid_110kover > 0, ct.public_net_prc_avg_faid_110kover, null))

					, ca.sat_read_75 =	
						coalesce(if(cdfa.sat_read_75 >  0, cdfa.sat_read_75, null), if(ca.sat_read_75 > 0, ca.sat_read_75, null))
					, ca.sat_read_25 =
						coalesce(if(cdfa.sat_read_25 >  0, cdfa.sat_read_25, null), if(ca.sat_read_25 > 0, ca.sat_read_25, null))
					, ca.sat_write_75 =
						coalesce(if(cdfa.sat_write_75 >  0, cdfa.sat_write_75, null), if(ca.sat_write_75 > 0, ca.sat_write_75, null))
					, ca.sat_write_25 =	
						coalesce(if(cdfa.sat_write_25 >  0, cdfa.sat_write_25, null), if(ca.sat_write_25 > 0, ca.sat_write_25, null))
					, ca.sat_math_75 =	
						coalesce(if(cdfa.sat_math_75 >  0, cdfa.sat_math_75, null), if(ca.sat_math_75 > 0, ca.sat_math_75, null))
					, ca.sat_math_25 =	
						coalesce(if(cdfa.sat_math_25 >  0, cdfa.sat_math_25, null), if(ca.sat_math_25 > 0, ca.sat_math_25, null))
					, ca.act_composite_75 =	
						coalesce(if(cdfa.act_composite_75 >  0, cdfa.act_composite_75, null), if(ca.act_composite_75 > 0, ca.act_composite_75, null))
					, ca.act_composite_25 =	
						coalesce(if(cdfa.act_composite_25 >  0, cdfa.act_composite_25, null), if(ca.act_composite_25 > 0, ca.act_composite_25, null))
					, ca.act_english_75 =	
						coalesce(if(cdfa.act_english_75 >  0, cdfa.act_english_75, null), if(ca.act_english_75 > 0, ca.act_english_75, null))
					, ca.act_english_25 = 	
						coalesce(if(cdfa.act_english_25 >  0, cdfa.act_english_25, null), if(ca.act_english_25 > 0, ca.act_english_25, null))
					, ca.act_math_75 =
						coalesce(if(cdfa.act_math_75 >  0, cdfa.act_math_75, null), if(ca.act_math_75 > 0, ca.act_math_75, null))
					, ca.act_math_25 =	
						coalesce(if(cdfa.act_math_25 >  0, cdfa.act_math_25, null), if(ca.act_math_25 > 0, ca.act_math_25, null))
					, ca.act_write_75 =	
						coalesce(if(cdfa.act_write_75 >  0, cdfa.act_write_75, null), if(ca.act_write_75 > 0, ca.act_write_75, null))
					, ca.act_write_25 =	
						coalesce(if(cdfa.act_write_25 >  0, cdfa.act_write_25, null), if(ca.act_write_25 > 0, ca.act_write_25, null))
					, cfa.undergrad_grant_pct =	
						coalesce(cdfa.undergrad_grant_pct, cfa.undergrad_grant_pct)
					, cfa.undergrad_loan_pct =
						coalesce(cdfa.undergrad_loan_pct, cfa.undergrad_loan_pct);");
	}

	private function getValuesForChoose1EmailsPortalPart($params, $user_id, $ro, $is_online = NULL, $near_you = NULL){

		$eligible_colleges = DB::connection('bk')->table('revenue_organizations as ro');

		if (isset($ro->aor_id)) {
			$eligible_colleges = $eligible_colleges->join('aor_colleges as ac', 'ro.aor_id', '=', 'ac.aor_id')
				                                   ->join('colleges as c', 'c.id', '=', 'ac.college_id');
		}else{
			$eligible_colleges = $eligible_colleges
			                     ->join('organization_branches as ob', 'ro.org_branch_id', '=', 'ob.id')
				                 ->join('colleges as c', 'c.id', '=', 'ob.school_id');
		}
									 
		if (isset($is_online)) {
			$eligible_colleges = $eligible_colleges->where('c.is_online', 1);
		}

		$eligible_colleges = $eligible_colleges->join('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									 ->join('colleges_admissions as ca' , 'ca.college_id', '=', 'c.id')
									 ->join('colleges_tuition as ct', 'ct.college_id', '=', 'c.id')
									 ->join('college_programs as cp', 'cp.college_id', '=', 'c.id')
									 
									 ->join('objectives as o', function($join) use($user_id){
										$join->on('cp.major_id', '=', 'o.major_id')
											 // ->on('cp.degree_type', '=', 'o.degree_type')
											 ->where('o.user_id', '=', $user_id);
								 	 })
								 	 ->join('majors as m', 'm.id', '=', 'o.major_id')
								 	 ->leftjoin('college_overview_images as coi', function($join)
											{
											    $join->on('c.id', '=', 'coi.college_id');
											    $join->on('coi.url', '!=', DB::raw('""'));
											    $join->on('coi.is_video', '=', DB::raw(0));
											    $join->on('coi.is_tour', '=', DB::raw(0));
											})

								 	 ->where('ro.id', $ro->id)
								 	 
									 ->select('m.name as major_name', 'ca.sat_read_75', 'ca.sat_write_75', 
									 	      'ca.sat_math_75', 'c.id as college_id', 'c.address', 'c.city', 'c.state', 
											  'c.zip', 'c.school_name as school_name', 'cr.plexuss as rank', 
											  'ca.act_composite_75 as act', 'ct.tuition_avg_in_state_ftug as inStateTuition','ct.tuition_avg_out_state_ftug as outStateTuition', 
											  DB::raw('CASE WHEN `c`.`logo_url` IS NULL
											THEN "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png"
											ELSE CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) END as logo_url'), 'coi.url as img_url', 'c.general_phone as phone', 'c.id as college_id')
									 
									 ->groupBy('c.id');

		if (isset($near_you)) {
			
			$locationArr = $this->iplookup($user_id);
			$query = '( 3959 * acos( cos( radians('.$locationArr['latitude'].') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$locationArr['longitude'].') ) + sin( radians('.$locationArr['latitude'].') ) * sin(radians(latitude)) ) ) AS distance';

			$eligible_colleges = $eligible_colleges->addSelect(DB::raw($query))
			                                       ->orderBy('distance', 'ASC');
		}else{
			$eligible_colleges = $eligible_colleges->orderBy('cr.plexuss', 'asc');
		}

		$exclude_school_ids = $this->getExcludeColleges($user_id);
		
		if (isset($exclude_school_ids)) {
			$eligible_colleges = $eligible_colleges->whereNotIn('c.id', $exclude_school_ids);
		}

		$eligible_colleges = $eligible_colleges->get();
		
		
		if (empty($eligible_colleges[0])) {
			$params['status'] = 'failed';
			return $params;
		}
									 
		$usrScores = array();		
		$scoreModel = Score::on('bk')->where('user_id', $user_id)->first();

		if( isset($scoreModel->sat_total) ){
			$usrScores['sat_total'] = $scoreModel->sat_total;
		}else{
			$usrScores['sat_total'] = 'N/A';
		}

		if( isset($scoreModel->act_composite) ){
			$usrScores['act_composite'] = $scoreModel->act_composite;
		}else{
			$usrScores['act_composite'] = 'N/A';
		}

		if( isset($scoreModel->overall_gpa) ){
			$usrScores['overall_gpa'] = $scoreModel->overall_gpa;
		}else{
			$usrScores['overall_gpa'] = 'N/A';
		}

		if( isset($scoreModel->hs_gpa) ){
			$usrScores['hs_gpa'] = $scoreModel->hs_gpa;
		}else{
			$usrScores['hs_gpa'] = 'N/A';
		}

		$latest_school_id = Recruitment::on('bk')
									   ->where('user_id', $user_id)
									   // ->where('user_recruit', 1)
									   ->orderBy('id', 'desc')
									   ->first();
		
		if (!isset($latest_school_id)) {
	   		$latest_school_id = PickACollegeView::on('bk')
	   								->where('user_id', $user_id)
	   								->orderBy('id', 'desc')
	   								->first();
	    }							   
		isset($latest_school_id->college_id) ? $latest_school_id = $latest_school_id->college_id : $latest_school_id = -1;
		
		$input = array();
		$input['user_id'] = $user_id;
		$urc = new UserRecommendationController;
		$rec_funnels =  $urc->recommendationFunnel($eligible_colleges, $usrScores, $latest_school_id, 'major', $input);
		
		if (empty($rec_funnels)) {
			$params['status'] = 'failed';
			return $params;
		}
		
		foreach ($rec_funnels as $key) {
			$params['SCHOOL1_ID']			 = $key->college_id;
			$params['SCHOOL1_NAME']			 = $key->school_name;
			$params['SCHOOL1_ADDRESS']		 = $key->address . ", ". $key->city. ", ".$key->state. ", ".$key->zip;
			$params['SCHOOL1_PHONE']		 = $key->phone;
			$params['SCHOOL1_CITY_STATE']	 = $key->city. ", ".$key->state;
			
			if (isset($key->img_url) && $key->img_url != "") {
				$params['SCHOOL1_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'.$key->img_url;
			}else{
				$params['SCHOOL1_IMAGE'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png';
			}

			$params['SCHOOL1_LOGO']			 = $key->logo_url;
			$params['HAS_HIGHER_RANK']		 = ($key->is_higher_rank_recommend == 1) ? true : false;
			$params['HAS_LOWER_TUITION']	 = ($key->is_lower_tuition_recommend == 1) ? true : false;;
			$params['HAS_HIGHER_PERCENTILE'] = ($key->is_top_75_percentile_recommend == 1) ? true : false;;
			$params['MAJOR_NAME']			 = $key->major_name;

			break;
		}
		
		$params['status'] = "success";

		return $params;
	}

	private function getExcludeColleges($user_id){
		$upeel = UsersPortalEmailEffortLog::on('bk')
										  ->where('user_id', $user_id)
										  ->whereNotNull('params')
										  ->get();

		$exclude_school_ids = array();
		foreach ($upeel as $school) {
			if (isset($school->params)) {
				$tmp = json_decode($school->params);

				$i = 1;
				foreach ($tmp as $key => $value) {
					if($key == 'SCHOOL'.$i.'_ID'){
						$exclude_school_ids[] = $value;
					}
					$i++;
				}
			}
		}
		
		return $exclude_school_ids;
	}

	private function getNrccuaNearbyStates($user){

		$state = NULL;

		if (isset($user->state)) {
			$state = $user->state;
		}else{
			$dt1 = Carbon::parse($user->created_at);
			$dt1 = $dt1->toDateString();

			$dt = Carbon::parse($user->created_at);
			$dt = $dt->addDay(1);
			$dt = $dt->toDateString();

			$tpid = TrackingPageId::on('bk-log')->where('date', $dt1)->first();
			if (!isset($tpid)) {
				return NULL;
			}
			$start_date = $tpid->tp_id;

			$tpid2 = TrackingPageId::on('bk-log')->where('date', $dt)->first();
			if (isset($tpid2)) {
				$end_date = $tpid2->tp_id;
			}else{
				$end_date = TrackingPage::on('bk-log')->orderBy('id', 'desc')->first();
				$end_date = $end_date->id;
			}
			

			$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $user->id)
												 ->first();


			if (isset($sub_qry)) {
				$iplookup = $this->iplookup($sub_qry->ip);
				// dd($iplookup);
				$state = $iplookup['stateAbbr'];
			}
		}
		
		$tmp_qry =  NrccuaNearbyState::on('rds1')
								   ->select('college_state')
								   ->where('user_state', $state)
								   ->get();
											   
		$nearby_states = array();
		
		foreach ($tmp_qry as $k) {
			$nearby_states[] = $k->college_state;
		}
		
		return $nearby_states;
	}

	public function addZipAndDedupEAB(){
		if (Cache::has( env('ENVIRONMENT') .'_'. 'is_addZipAndDedupEAB')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. 'is_addZipAndDedupEAB');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. 'is_addZipAndDedupEAB', 'in_progress', 10);

		$qry = RevenueNrccuaStudent::where('checked_for_zip', 0)
		                           ->get();

		$dc = new DistributionController();
		foreach ($qry as $key) {
			// $cfnd = $dc->checkForNrccuaDuplicate($key->user_id, $key->email);

			// if(isset($cfnd)){
			// 	$key->is_dup = 1;
			// 	$key->save();

			// 	continue;
			// }

			$user = User::on('rds1')->find($key->user_id);

			$dt1 = Carbon::parse($user->created_at);
			$dt1 = $dt1->toDateString();

			$dt = Carbon::parse($user->created_at);
			$dt = $dt->addDay(1);
			$dt = $dt->toDateString();

			$tpid = TrackingPageId::on('bk-log')->where('date', $dt1)->first();
			$start_date = $tpid->tp_id;

			$tpid2 = TrackingPageId::on('bk-log')->where('date', $dt)->first();
			if (isset($tpid2)) {
				$end_date = $tpid2->tp_id;
			}else{
				$end_date = 243361916;
			}
			

			$sub_qry = TrackingPage::on('bk-log')->where('id', '>=', $start_date)
												 ->where('id', '<=', $end_date)
												 ->where('user_id', $key->user_id)
												 ->first();
			if (isset($sub_qry)) {
				$iplookup = $this->iplookup($sub_qry->ip);
				$key->ip = $sub_qry->ip;
				$key->ip_state = $iplookup['stateAbbr'];
				$key->ip_city  = $iplookup['cityName'];
				$key->ip_zip   = $iplookup['cityAbbr'];
			}
			$key->checked_for_zip = 1;

			$key->save();
		}
		
		Cache::put( env('ENVIRONMENT') .'_'. 'is_addZipAndDedupEAB', 'done', 7);
	}

	// Holiday 2018 Emails
	public function sendHolidayEmail(){
		$start_date = Carbon::create(2018, 12, 17, 0, 0, 0);
		$end_date   = Carbon::create(2018, 12, 24, 0, 0, 0);
		$today = Carbon::today();
		$check = false;

		if ($today->gte($start_date) && $today->lte($end_date)) {
			$check = true;
		}

		if (!$check) {
			return  "Can't run this now";
		}
		
		$take  =  100;
		$qry = DB::connection('rds1')->table('users as u')
									 ->select('u.id as user_id', 'u.fname', 'u.lname', 'u.email', 'u.profile_img_loc')
									 ->take($take)
									 ->get();
		
		$template_name  = 'holidays_2018';
		$reply_email    = 'support@plexuss.com';
		foreach ($qry as $key) {
			$params =   array();
			$params['email'] = $key->email;
			$params['NAME']  = ucwords(strtolower($key->fname));
			$params['USER_IMAGE_URL'] = isset($user->profile_img_loc) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$user->profile_img_loc : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png';

			// $key->email = "anthony.shayesteh@plexuss.com";
			// $ess = new EmailSingleSend($reply_email, $template_name, $params, $key->email,  NULL);
			// $ess->handle();

			dd(1231231);
			// EmailSingleSend::dispatch($reply_email, $template_name, $params, $key->email,  NULL)
		}
	}

	// Nowruz link
	public function nowruz(){
		return redirect('https://docs.google.com/forms/d/e/1FAIpQLSdwsjyeQKrzjns4WKqtCVYTltaRRNymv-PfNHfTPXSxTYPmzQ/viewform');
	}

    private function saveBase64ImagePng($base64Image, $imageDir, $fileName){
        $base64Image = trim($base64Image);
        $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);

        $imageData = base64_decode($base64Image);
        //Set image whole path here 
        $filePath = $imageDir . $fileName;


       file_put_contents($filePath, $imageData);

       return $filePath;
    }

    public function addCustomFiltersForRevenueOrgs($qry, $ro_name){
    	
    	$arr = array();
    	$arr['status'] = "failed";

		if ($ro_name == 'nrccua' || $ro_name == 'cappex' || $ro_name == 'eddy_reg' || $ro_name == 'eddy_click') {
			$qry = $qry->where('u.country_id', 1);

			$arr['status'] = "success";

		}elseif ($ro_name == 'qs_mba' || $ro_name == 'qs_grad' ) {
			// $qry = $qry->where(function($q){
			// 		$q->orWhere('u.country_id', '=', DB::raw(1));
			// 		$q->orWhere('u.country_id', '=', DB::raw(2));
			// 		$q->orWhere('u.country_id', '=', DB::raw(140));
			// });

			$qry = $qry->whereRaw("(u.utm_content like '%master%' OR
									    exists(
									    Select
									        user_id
									    from
									        objectives
									    where
									        objectives.user_id = u.id
									        and degree_type in (4,5)
									    )
									)");
			
			$arr['status'] = "success";

		}elseif ($ro_name == 'edx' ) {		
			$qry = $qry->where(function($q){
					// $q->orWhere('u.country_id', '!=', DB::raw(1));
					$q->where(function($q2){
						$q2->where('u.country_id', '!=', DB::raw(2));
						$q2->where('u.country_id', '!=', DB::raw(140));
					})->orWhereNull('u.country_id');
					
			});

			// if ($ro_name == 'edx') {
			// 	$take = $take *2;
			// }
		
			$arr['status'] = "success";

		}elseif ($ro_name == 'shorelight') {		
			$qry = $qry->whereIn('u.country_id', array(2,
													19,
													45,
													96,
													108,
													116,
													140,
													164,
													176,
													187,
													210,
													225,
													114));
			$qry = $qry->orWhereRaw("(
									(u.country_id = 99 and (u.city like 'hyder%' or u.city like 'mumbai%' or u.city like 'banga%' or u.city like '%delhi%' or u.city like 'ahmed%')) #india
									or
									((u.country_id = 179) and (city like '%mosc%' or city like '%pete%')) #russia
									or
									(u.city like 'ho%chi%' and u.country_id = 233) #vietnam
									or
									(u.city like 'sa%pa%l%' and u.country_id = 32) #brazil
									or
									(u.city like '%bogo%' and u.country_id = 48) #colombia
									or
									(u.city like '%lagos%' and u.country_id = 159) #nigeria
								)");

			$arr['status'] = "success";
		}

		// Eddy CALU restriction
		elseif ($ro_name == "eddy_click" && isset($forced_college_id) && $forced_college_id == 6698) {

			$qry = $qry->leftjoin('objectives as ob', 'ob.user_id', '=', 'u.id')
					   ->leftjoin('majors as mj', 'ob.major_id', '=', 'mj.id')
					   ->leftjoin('departments as dp', function($q){
					   			$q->on('dp.id', '=', 'mj.department_id');
					   			$q->on('dp.id', '!=', DB::raw(9));
					   			$q->on('dp.id', '!=', DB::raw(16));
					   			$q->on('dp.id', '!=', DB::raw(36));
					   });

			$arr['status'] = "success";
		}

		// Cornell College
		elseif ($ro_name == "cornellcollege") {
			$qry = $qry->whereRaw("(u.in_college = 0 or u.in_college is null)
													and u.is_plexuss = 0
													and u.is_organization = 0
													and u.is_ldy = 0
													and u.is_university_rep = 0
													and u.is_aor = 0
													and u.is_university_rep = 0
													and (
													    (u.country_id = 99)
													    or 
													    (((u.state in ('CO', 'Colorado')) or (u.city like '%chicago%') and u.state in ('IL', 'Illinois'))
													        and u.country_id = 1 and u.id not in (Select user_id from country_conflicts)
													    )
													)");

			$arr['status'] = "success";
		

		}elseif ($ro_name == "study_group") {		
			// $qry = $qry->where('u.is_plexuss', 0)
			// 			->where('u.is_organization', 0)
			// 			->where('u.is_ldy', 0)
			// 			->where('u.is_university_rep', 0)
			// 			->where('u.is_aor', 0)
			// 			->where('u.email', 'REGEXP', '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$')
			// 			->leftjoin('countries as c', 'u.country_id', '=', 'c.id')
			// 			->where(function($q){
			// 				$q->orWhere('c.tier', '=', 1)
			// 				  ->orWhere('c.tier', '=', 2);
			// 			})
			// 			->where('u.country_id', '!=', 1)
			// 			->whereRaw("(u.in_college = 0 or u.in_college is null) AND (
			// 													(
			// 														u.financial_firstyr_affordibility in(
			// 															'20,000 - 30,000' ,
			// 															'30,000 - 50,000' ,
			// 															'50,000'
			// 														)
			// 													)
			// 												)");

			$qry = $qry->leftjoin('countries as c', 'u.country_id', '=', 'c.id')
			           ->whereRaw('(
								    (
								        (`u`.`financial_firstyr_affordibility` in (
								            "20,000 - 30,000" ,
								            "30,000 - 50,000" ,
								            "50,000"
								        ) or `u`.`financial_firstyr_affordibility` is null)
								        and `c`.`tier` in(3 , 4)
								    )
								    or `c`.`tier` in(1 , 2)
								)');

			$arr['status'] = "success";
		

		}elseif ($ro_name == "usf") {		
			// $qry = $qry->whereRaw("(u.financial_firstyr_affordibility is null 
			// 					or u.financial_firstyr_affordibility in ('10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '5,000 - 10,000', '50,000')) ")
			// 		   ->whereRaw("(u.utm_content like '%master%' OR
			// 						exists(
			// 					    Select
			// 					        user_id
			// 					    from
			// 					        objectives
			// 					    where
			// 					        objectives.user_id = u.id
			// 							and degree_type in (4,5)
			// 						)
			// 					)")
			// 		   ->whereRaw("u.country_id in 
			// 						(1,2,16,23,32,44,48,57,72,73,81,84,98,103,104,105,108,114,140,153,156,163,174,179,199,207,208,210,219,226,233)")
			// 		   ->whereRaw("not exists 
			// 						    (Select user_id from country_conflicts
			// 						     where country_conflicts.user_id = u.id 
			// 						)")
			// 		   ->whereRaw("not exists 
			// 						(Select user_id from objectives
			// 						 where objectives.user_id = u.id 
			// 						 and major_id in (1196,412,5,578,5952,598,697,7,734,740,915,935,936,944,104,105,1077,1125,
			// 							113,114,136,1392,1420,1426,143,1431,144,145,146,1460,1466,1468,152,153,154,155,158,
			// 							160,161,162,163,165,171,179,180,181,185,186,189,196,262,310,361,37,413,469,473,489,577,
			// 							579,581,5953,5954,5955,599,647,661,665,683,695,696,698,699,700,760,762,764,843,919,92,
			// 							937,938,939,940,946,952
			// 					)
			// 					)");

			$qry = $qry->whereRaw("(u.financial_firstyr_affordibility is null 
								or u.financial_firstyr_affordibility in ('10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '5,000 - 10,000', '50,000')) ")
					   ->whereRaw("(u.utm_content like '%master%' OR
									exists(
								    Select
								        user_id
								    from
								        objectives
								    where
								        objectives.user_id = u.id
										and degree_type in (4,5)
									)
								)")
					   ->whereRaw("country_id in 
									(1, 32, 140, 226, 163, 105, 48, 170, 111)");

			$arr['status'] = "success";
		

		}elseif ($ro_name == "intostudy") {		
			// $qry = $qry->whereRaw("(u.in_college = 0 or u.in_college is null)")
			// 		   ->whereRaw("(
			// 					    (
			// 					    u.financial_firstyr_affordibility in(
			// 					        '20,000 - 30,000' ,
			// 					        '30,000 - 50,000' ,
			// 					        '50,000'
			// 					    ) and u.country_id in (Select id from countries where toggle_passthrough_financials = 1))
			// 					    or u.country_id in (Select id from countries where toggle_passthrough_financials = 0)
			// 					    or u.country_id is null
			// 					)")
			// 		   ->whereIn("u.country_id", array(5,19,34,38,58,61,63,100,101,102,104,109,116,118,120,123,131,134,147,149,164,171,176,187,192,209,213,218,219,225,233,238,9,12,14,26,28,32,41,44,48,52,54,60,62,64,69,75,85,87,89,93,95,136,140,146,154,157,167,169,170,175,202,204,221,228,232,234));

			$qry = $qry->whereRaw("(
									u.in_college = 0
									or u.in_college is null
								)")
					   ->whereRaw("(
									(
										(u.financial_firstyr_affordibility in(
											'20,000 - 30,000' ,
											'30,000 - 50,000' ,
											'50,000'
										) or financial_firstyr_affordibility is null)
										and u.country_id in(
											Select
												id
											from
												countries
											where
												toggle_passthrough_financials = 1
										)
									)
									or u.country_id in(
										Select
											id
										from
											countries
										where
											toggle_passthrough_financials = 0
									)
									or u.country_id is null
								)")
					   ->whereRaw("`u`.`country_id` in(
										5 ,19 ,34 ,38 ,58 ,61 ,63 ,100 ,101 ,102 ,104 ,109 ,116 ,118 ,120 ,123 ,131 ,134 ,147 ,149 ,164 ,171 ,176 ,187 ,192 ,209 ,213 ,218 ,219 ,225 ,233 ,238 ,9 ,12 ,14 ,26 ,28 ,32 ,41 ,44 ,48 ,52 ,54 ,60 ,62 ,64 ,69 ,75 ,85 ,87 ,89 ,93 ,95 ,136 ,140 ,146 ,154 ,157 ,167 ,169 ,170 ,175 ,202 ,204 ,221 ,228 ,232 ,234
									)");


			$arr['status'] = "success";
		

		}elseif ($ro_name == "oregonstateuniversity") {		
			// $qry = $qry->join('countries as c', 'c.id', '=', 'u.country_id')
				// 	   ->where('c.id', '!=', 1)
			 //           ->where(function($q){
			 //           		$q->where(function($q2){
			 //           			$q2->whereIn('u.financial_firstyr_affordibility', array('5,000 - 10,000' ,
				// 																		'10,000 - 20,000' ,
				// 																		'20,000 - 30,000' ,
				// 																		'30,000 - 50,000' ,
				// 																		'50,000'))
			 //           			   ->whereIn('c.tier', array(3,4));
			 //           		})
			 //           		  ->orWhereIn('c.tier', array(1,2));
			 //           });

			$qry = $qry->leftjoin('countries as c', 'u.country_id', '=', 'c.id')
			           ->whereRaw('(
								    (
								        (`u`.`financial_firstyr_affordibility` in (
								            "5,000 - 10,000" ,
								            "10,000 - 20,000" ,
								            "20,000 - 30,000" ,
								            "30,000 - 50,000" ,
								            "50,000"
								        ) or financial_firstyr_affordibility is null)
								        and `c`.`tier` in(3 , 4)
								    )
								    or `c`.`tier` in(1 , 2)
								)');

		    $arr['status'] = "success";
		

		}elseif ($ro_name == "alliant") {		
			// $qry = $qry->whereRaw("(
			// 				    (
			// 				    u.financial_firstyr_affordibility in(
			// 				                '10,000 - 20,000',
			// 				        '20,000 - 30,000' ,
			// 				        '30,000 - 50,000' ,
			// 				        '50,000'
			// 				    ) and u.country_id in (Select id from countries where toggle_passthrough_financials = 1))
			// 				    or u.country_id in (Select id from countries where toggle_passthrough_financials = 0)
			// 				)")
			// 	  ->whereIn("u.country_id", array(2,13,32,81,110,111,171,197,219,229,233));

			$qry = $qry->whereRaw("(
									(
										(u.financial_firstyr_affordibility in(
											'10,000 - 20,000' ,
											'20,000 - 30,000' ,
											'30,000 - 50,000' ,
											'50,000'
										) or financial_firstyr_affordibility is null)
										and u.country_id in(
											Select
												id
											from
												countries
											where
												toggle_passthrough_financials = 1
										)
									)
									or u.country_id in(
										Select
											id
										from
											countries
										where
											toggle_passthrough_financials = 0
									)
								)")
					   ->whereRaw("`u`.`country_id` in(
										2 ,13 ,32 ,81 ,110 ,111 ,171 ,197 ,219 ,229 ,233
									)");

			$arr['status'] = "success";

		}elseif ($ro_name == "hult") {	
			// $qry = $qry->whereIn('u.country_id', array(1,2))
			// 		   ->whereRaw("not exists(
			// 							Select
			// 								user_id
			// 							from
			// 								country_conflicts
			// 							where
			// 								country_conflicts.user_id = u.id
			// 						)")
			// 		   ->where('u.fname', 'NOT LIKE', "%test%")
			// 		   ->where('u.lname', 'NOT LIKE', "%test%")
			// 		   ->where('u.email', 'NOT LIKE', "%test%")
			// 		   ->where(DB::raw("year(birth_date)"), "<=", DB::raw("(year(current_date()) - 20)"))
			// 		   ->whereRaw("(
			// 							u.financial_firstyr_affordibility is null
			// 							or u.financial_firstyr_affordibility in(
			// 								'10,000 - 20,000' ,
			// 								'20,000 - 30,000' ,
			// 								'30,000 - 50,000' ,
			// 								'50,000'
			// 							)
			// 						)");

			$qry = $qry->whereRaw("(year(u.birth_date) <= (year(current_date()) - 20) or u.birth_date = '0000-00-00' or u.birth_date is null)")
					   ->whereRaw("(u.financial_firstyr_affordibility is null 
    								or u.financial_firstyr_affordibility in ('10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '50,000'))");

			$arr['status'] = "success";

		}elseif ($ro_name == "sdsu_ali") {	
			$qry = $qry->where('u.country_id', '=', 219);

			$arr['status'] = "success";

		}elseif ($ro_name == "keypath") {	

			// $qry = $qry->where('u.country_id', '!=', 1)
			// 		   ->join('countries as c', 'c.id', '=', 'u.country_id')
			// 		   ->where(function($q){
			// 		   		$q->orWhereIn('c.tier', array(1,2))
			// 		   		  ->orWhereIn('c.id', array('5','63','68','102','123','147','158','159','218'));
			// 		   })
					   
			//            ->join('objectives as ob', 'ob.user_id', '=', 'u.id')
			// 		   ->leftjoin('majors as mj', 'ob.major_id', '=', 'mj.id')
			// 		   ->leftjoin('departments as dp', function($q){
			// 		   			$q->on('dp.id', '=', 'mj.department_id');
			// 		   			$q->on('dp.id', '=', DB::raw(37));
			// 		   })
			// 		   ->whereRaw("(
			// 						u.financial_firstyr_affordibility is null
			// 						or u.financial_firstyr_affordibility in(
			// 							'10,000 - 20,000' ,
			// 							'20,000 - 30,000' ,
			// 							'30,000 - 50,000' ,
			// 							'50,000'
			// 						)
			// 					)");

			$qry = $qry->whereRaw("(utm_content like '%master%' OR
								    exists(
								    Select
								        user_id
								    from
								        objectives
								    where
								        objectives.user_id = u.id
								        and degree_type in (4,5)
								    )
								)");

			$arr['status'] = "success";
		
		}elseif ($ro_name == "gus") {
			$qry = $qry->whereIn('u.country_id', array(32,
													48,
													81,
													114,
													140,
													179,
													199,
													224,
													226,
													13,
													20,
													32,
													48,
													80,
													96,
													100,
													110,
													120,
													131,
													140,
													164,
													173,
													181,
													187,
													200,
													211,
													219,
													224,
													225,
													229,
													233,
													238,
													101,
													179
													))
						->where(DB::raw("year(birth_date)"), ">=", DB::raw("(year(current_date()) - 18)"));

			$arr['status'] = "success";
		
		}elseif ($ro_name == "openClassrooms") {
			$qry = $qry->where('u.country_id', 1)
					   ->where(DB::raw("year(birth_date)"), ">=", DB::raw("(year(current_date()) - 18)"));

			$arr['status'] = "success";
		}

		elseif ($ro_name == "benedictine") {

			$qry = $qry->join('countries as c', 'c.id', '=', 'u.country_id')
                       ->whereIn('c.tier', array(1,2))
					   ->whereRaw("(u.financial_firstyr_affordibility is null 
								or u.financial_firstyr_affordibility in ( '20,000 - 30,000', '30,000 - 50,000', '5,000 - 10,000', '50,000')) ");
			$arr['status'] = "success";
		}
		elseif ($ro_name == "otero") {
			$qry = $qry->where(function($q){
							$q->orWhereNull('u.country_id')
							  ->orWhere('u.country_id', '!=', DB::raw(1));
						})->whereRaw("(exists(
									    Select
									        user_id
									    from
									        objectives
									    where
									        objectives.user_id = u.id
									        and degree_type in (2)
									    )
									)");
			
			$arr['status'] = "success";

		}elseif ($ro_name == "NCSA") {
			$qry = $qry->whereRaw("`u`.`country_id` in(1,2,226,15,156)");
			$arr['status'] = "success";

		}elseif ($ro_name == "universityofarkansas") {
			$qry = $qry->where(function($query){
								$query->where(function($q){
									$q->orWhereNull('u.country_id')
								  		  ->orWhere('u.country_id', '!=', DB::raw(1));
									})
									->orWhere(function($q){
										$q->where('u.country_id', '=', DB::raw(1))
										  ->where('u.in_college', '=', DB::raw(1));
									});

			});
						
			$arr['status'] = "success";
		}


		
		$arr['qry'] = $qry;
		
		return $arr;
    }

    public function uploadUsersPictureWithAUrl($url){

    	$ret = array();	    
	    
	    try {
	    	
	    	$temp_url  =  substr(strrchr($url, "/"), 1);	        
	        $id =(int)($milliseconds = round(microtime(true) * 76312));
	        $file_name = $id. '_'. $temp_url;
	        $file_name = str_replace(" ", "_", $file_name);
	        $img_data = file_get_contents($url);
	        $base64 = base64_encode($img_data);

	        if (!(strpos($file_name, '.png') !== FALSE) && !(strpos($file_name, '.jpg') !== FALSE) && !(strpos($file_name, '.jpeg') !== FALSE)){
	        	$file_name .= ($id *31) .".jpeg";
	        }

	        $input = array();
	        $input['file_name'] = $file_name;
	        $input['image']     = $base64;
	        $input['user_id']   = Crypt::encrypt($id);
	        $input['bucket_url']= 'asset.plexuss.com/users/images';
	        $public_path = $this->uploadBase64Img($input);

	        $ret['status']  = "success";
	        $ret['url']		= $public_path;
	        
	    } catch (\Exception $e) {
	    	$ret['status']  = "failed";
	    }

	    return json_encode($ret);
		
    }
}
