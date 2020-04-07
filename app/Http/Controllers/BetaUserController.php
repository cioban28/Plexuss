<?php

namespace App\Http\Controllers;

use Request, DB;
use Carbon\Carbon;
use App\User, App\CarePackageNotifyMe, App\RevenueReport, App\DatesModel;
use Illuminate\Support\Facades\Cache;

class BetaUserController extends Controller
{
	public function testTime(){
		$now= Carbon::now();

		dd($now);
	}
    /**
	 * Returns list of beta users when url 'http://plexuss.com/betausers?t=7bkfqr6h0y' is used.
	 */
	public function getBetaUsers(){



		$term = strtolower( Request::get( 't' ) );

		if ($term != "j0sdofi94mkas131" && $term != "eh12y3i1yn8411hjk23" ) {
			return View( 'betapublic.homepage.homepage' );
		}



		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['betaUsers'] = DB::connection('bk')->table('users as U')
		->select('U.id', 'fname', 'lname', 'email', 'city', 'state', 'profile_percent', 'U.phone', 'majors.name as major' , 'U.created_at', 'countries.country_name',
					'is_student', 'is_intl_student', 'is_alumni', 'is_parent', 'is_counselor', 'in_college', 'college_grad_year', 'hs_grad_year')
		->leftJoin('countries', 'U.country_id', '=', 'countries.id')
		->leftJoin('objectives', 'U.id', '=', 'objectives.user_id')
		->leftJoin('majors', 'objectives.major_id', '=', 'majors.id')
		->orderBy('U.id', 'DESC')
		->limit(1000)
		->get();

		$data['show_ldy'] = false;

		if ($term == "eh12y3i1yn8411hjk23") {
			$data['show_ldy'] = true;
		}

		$now = Carbon::now();
		$data['total_today_count'] = User::on('bk')->where('created_at', '>', $now->today())->count();

		$data['total_today_ldy_count'] = User::on('bk')->where('created_at', '>', $now->today())->where('is_ldy', 1)->count();

		$data['total_yesterday_count'] = User::on('bk')->where('created_at','<', $now->today())
										->where('created_at', '>=', $now->yesterday())->count();
		$data['total_yesterday_ldy_count'] = User::on('bk')->where('created_at','<', $now->today())
										->where('created_at', '>=', $now->yesterday())->where('is_ldy', 1)->count();

		$monday = Carbon::parse('last sunday 11:59:59 pm');
		$sunday = Carbon::parse('this sunday 11:59:59 pm');
		
		$data['this_week_count'] = User::on('bk')->where('created_at', '>', $monday)
										->where('created_at','<=', $sunday)->count();

		$data['this_week_ldy_count'] = User::on('bk')->where('created_at', '>', $monday)
										->where('created_at','<=', $sunday)->where('is_ldy', 1)->count();
		
		$data['this_month_count'] = User::on('bk')->where('created_at','>=', Carbon::now()->startOfMonth())
										->where('created_at', '<=', Carbon::now()->endOfMonth())->count();

		$data['this_month_ldy_count'] = User::on('bk')->where('created_at','>=', Carbon::now()->startOfMonth())
										->where('created_at', '<=', Carbon::now()->endOfMonth())->where('is_ldy', 1)->count();

		$first_day_last_month =  new Carbon('first day of last month 12:00 am');

		
		$data['last_month_count'] = User::on('bk')->where('created_at','<', Carbon::now()->startOfMonth())
										->where('created_at', '>=', $first_day_last_month)->count();

		$data['last_month_ldy_count'] = User::on('bk')->where('created_at','<', Carbon::now()->startOfMonth())
										->where('created_at', '>=', $first_day_last_month)->where('is_ldy', 1)->count();

		$total_users_count = User::on('bk')->select(DB::raw('count(*) as cnt'))->first();
		$data['total_users_count'] = $total_users_count->cnt;

		$total_users_ldy_count = User::on('bk')->select(DB::raw('count(*) as cnt'))->where('is_ldy', 1)->first();
		$data['total_users_ldy_count'] = $total_users_ldy_count->cnt;


		$data['advertisingSubmits'] = DB::connection('bk')->table('college_prep')->select('id', 'company', 'title', 'email', 'phone', 'notes', 'created_at')->get();
		
		$data['careersSubmits'] = DB::connection('bk')->table('careers-submit')->select('id', 'position', 'fname', 'lname', 'email', 'phone', 'zipcode', 'school', 'grade_level', 'counselor', 'gpa', 'camid', 'specid', 'pixel', 'created_at')->get();

		$data['collegeSubmissions'] = DB::connection('bk')->table('college_submission')->select('id', 'company', 'contact', 'title', 'email' , 'phone' , 'notes', 'created_at')->get();

		$data['contactus'] = DB::connection('bk')->table('contact_us')->select('id', 'fname', 'lname', 'email', 'phone', 'company', 'tell_us_more', 'created_at')->get();

		$data['scholarshipSubmission'] = DB::connection('bk')->table('scholarship_submission')->select('id', 'scholarship_title', 'contact', 'phone', 'fax', 'email', 'address', 'address2', 'city', 'state', 'zip', 'deadline', 'number_of_awards', 'max_amount', 'website', 'scholarship_description', 'created_at')->get();

		$ccp = CarePackageNotifyMe::all();

		$ccpArr = array();

		foreach ($ccp as $key) {
			$tmp = array();
			$tmp['email'] = $key->email;
			$tmp['ip'] = $key->ip;
			$tmp['user_id'] = $key->user_id;
			$tmp['created_at'] = $key->created_at;

			$ccpArr[] = $tmp;
		}
		

		$data['carepackage_signups'] = $ccpArr;

		return View( 'betausers.betausers', $data);
	}

	// public function revenueReport(){

	// 	$term = strtolower( Request::get( 't' ) );

	// 	if ($term != "jkdh31knj87y3hkyy21") {
	// 		return redirect("/");
	// 	}

	// 	if (Cache::has(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob')) {
	// 		$data = Cache::get(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob');
	// 	}else{
	// 		$data = array();
	// 		// $data = $this->runRevenueReportCronJob();
	// 	}

	// 	return View('betausers.report', $data);
	// }

	public function revenueReport(){
		$term = strtolower( Request::get( 't' ) );

		if ($term != "jkdh31knj87y3hkyy21") {
			return redirect("/");
		}

		if (Cache::has(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob')) {
			$data = Cache::get(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob');

		}else{
			$data = array();
			return "No Data Available";
			// $data = $this->runRevenueReportCronJob();
		}

		// $qry = RevenueReport::on('bk-log')->first();
		// $data = json_decode($qry->report);
		$data['today']      = $this->revenueReportDataGathering($data['today']);
		$data['yesterday']  = $this->revenueReportDataGathering($data['yesterday']);
		$data['this_month'] = $this->revenueReportDataGathering($data['this_month']);

		return View('betausers.dailyReport', $data);
	}

	public function newRevenueReport(){
		$term = strtolower( Request::get( 't' ) );

		if ($term != "jkdh31knj87y3hkyy21") {
			return redirect("/");
		}

		if (Cache::has(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob')) {
			$data = Cache::get(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob');

		}else{
			$data = array();
			return "No Data Available";
			// $data = $this->runRevenueReportCronJob();
		}

		// $qry = RevenueReport::on('bk-log')->first();
		// $data = json_decode($qry->report);
		$data['today']      = $this->revenueReportDataGathering($data['today']);
		$data['yesterday']  = $this->revenueReportDataGathering($data['yesterday']);
		$data['this_month'] = $this->revenueReportDataGathering($data['this_month']);

		return View('betausers.dailyReport', $data);
	}

	public function revenueReportDataGathering($report){
		
		$ret    = array();
		$arr    = array();

		$Source = '';
		foreach ($report as $key) {

			if ($key->Source != "--"  && $key->Source !=  "__" && $key->Source !=  "==" && $key->Source !=  "**") {
				if (!isset($ret[$key->Source])) {
					if ($key->Source == "--total") {
						$ret[$Source]['Total_Clicks']	   = $key->Clicks;
						$ret[$Source]['Total_Conversions'] = $key->Conversions;
						$ret[$Source]['Total_Dollar_Value']= $key->Dollar_Value;
					}else{
						$ret[$key->Source] = array();
						$ret[$key->Source]['Clicks']	   = $key->Clicks;
						$ret[$key->Source]['Conversions']  = $key->Conversions;
						$ret[$key->Source]['Dollar_Value'] = $key->Dollar_Value;
					}
					
				}else{
					$this->customdd('Take care of this <br/>');
					dd($key);
				}
				if ($key->Source != "--total") {
					$Source = $key->Source;
				}
			}
		}

		$arr['top'] 		  = array();
		$arr['advanced_paid'] = array();
		$arr['cpe']           = array();
		$arr['num_users']     = array();
		$arr['num_profile']   = array();

		$arr['top']['PLEXUSS Premium'] = $ret['Plex P'];
		$arr['top']['PLEXUSS Premium']['Bold'] = true;
		
		$arr['top']['NRCCUA'] = $ret['nrccua approved'];
		$arr['top']['NRCCUA']['Bold'] = true;

		$tmp = array();
		$tmp['total'] =  array();
		$tmp['total']['Clicks'] 	  = $ret['nrccua approved']['Total_Clicks'];
		$tmp['total']['Conversions']  = $ret['nrccua approved']['Total_Conversions'];
		$tmp['total']['Dollar_Value'] = $ret['nrccua approved']['Total_Dollar_Value'];

		$arr['top']['--total'] = $tmp['total'];
		
		$arr['top']['--manual users']   = $ret['--manual users'];
		$arr['top']['--manual matches'] = $ret['--manual matches'];
		$arr['top']['CAPPEX'] = $ret['cappex approved'];
		$arr['top']['CAPPEX']['Bold'] = true;

		$tmp = array();
		$tmp['total'] =  array();
		$tmp['total']['Clicks'] 	  = $ret['cappex approved']['Total_Clicks'];
		$tmp['total']['Conversions']  = $ret['cappex approved']['Total_Conversions'];
		$tmp['total']['Dollar_Value'] = $ret['cappex approved']['Total_Dollar_Value'];

		$arr['top']['**total'] = $tmp['total'];

		/////
		if (isset($ret['highered approved'])) {
			
			$arr['top']['H ED'] = $ret['highered approved'];
			$arr['top']['H ED']['Bold'] = true;

			if (isset($ret['highered approved']['Total_Clicks']) && isset($ret['highered approved']['Total_Conversions']) && 
				isset($ret['highered approved']['Total_Dollar_Value'])) {
				$tmp = array();
				$tmp['total'] =  array();
				$tmp['total']['Clicks'] 	  = $ret['highered approved']['Total_Clicks'];
				$tmp['total']['Conversions']  = $ret['highered approved']['Total_Conversions'];
				$tmp['total']['Dollar_Value'] = $ret['highered approved']['Total_Dollar_Value'];

				$arr['top']['``total'] = $tmp['total'];
			}
		}
		////
		
		/////
		if (isset($ret['IU approved'])) {
			$arr['top']['IU'] = $ret['IU approved'];
			$arr['top']['IU']['Bold'] = true;

			if (isset($ret['IU approved']['Total_Clicks']) && isset($ret['IU approved']['Total_Conversions']) && 
				isset($ret['IU approved']['Total_Dollar_Value'])) {
				
				$tmp = array();
				$tmp['total'] =  array();
				$tmp['total']['Clicks'] 	  = $ret['IU approved']['Total_Clicks'];
				$tmp['total']['Conversions']  = $ret['IU approved']['Total_Conversions'];
				$tmp['total']['Dollar_Value'] = $ret['IU approved']['Total_Dollar_Value'];

				$arr['top']['__total'] = $tmp['total'];
			}
		}
		////

		isset($ret['COLLEGEX US'])   ? $arr['top']['COLLEGEX US'] = $ret['COLLEGEX US'] : null;
		isset($ret['COLLEGEX INTL']) ? $arr['top']['COLLEGEX INTL'] = $ret['COLLEGEX INTL'] : null;

		isset($ret['COLLEGEX US'])   ? $arr['top']['COLLEGEX US']['Bold'] = true : null;
		isset($ret['COLLEGEX INTL']) ? $arr['top']['COLLEGEX INTL']['Bold'] = true : null;

		$arr['top']['edx'] = $ret['edx'];
		isset($ret['Edvisors SP']) ? $arr['top']['Edvisors SP'] = $ret['Edvisors SP'] : null;
		$arr['top']['Scholar Cappex'] = $ret['CPX$'];
		$arr['top']['Scholar Owl'] 	  = $ret['Scholar Owl'];
		$arr['top']['qs grad'] 		  = $ret['qs grad'];
		$arr['top']['qs mba'] 		  = $ret['qs mba'];
		$arr['top']['Hult'] 		  = $ret['Hult'];
		$arr['top']['Study Portal']   = $ret['Study Portal'];
		$arr['top']['GUS approved']   = $ret['GUS approved'];
		
		// Advanced Paid
		$arr['advanced_paid']['Springboard'] = $ret['springboard'];
		$arr['advanced_paid']['Prodigy'] 	 = $ret['Prodigy'];

		$tmp = array();
		$tmp['advanced_paid'] =  array();
		$tmp['advanced_paid']['Clicks'] 	  = '--';
		$tmp['advanced_paid']['Conversions']  = '--';
		$tmp['advanced_paid']['Dollar_Value'] = '--';

		$arr['advanced_paid']['University of Ark'] = $tmp['advanced_paid'];

		$tmp = array();
		$tmp['advanced_paid'] =  array();
		$tmp['advanced_paid']['Clicks'] 	  = '--';
		$tmp['advanced_paid']['Conversions']  = '--';
		$tmp['advanced_paid']['Dollar_Value'] = '--';

		$arr['advanced_paid']['Otero'] = $tmp['advanced_paid'];

		// Cost Per Enrollment
		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Sallie Mae'] = $tmp['cpe'];

		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Coe College'] = $tmp['cpe'];

		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Saint Michaels'] = $tmp['cpe'];

		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Abilene College'] = $tmp['cpe'];

		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Castleton'] = $tmp['cpe'];

		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Benedictine'] = $tmp['cpe'];

		$tmp = array();
		$tmp['cpe'] =  array();
		$tmp['cpe']['Clicks'] 	  = '--';
		$tmp['cpe']['Conversions']  = '--';
		$tmp['cpe']['Dollar_Value'] = '--';

		$arr['cpe']['Shorelight'] = $tmp['cpe'];

		$arr['cpe']['INTO'] 	   = $ret['into'];
		$arr['cpe']['Study Group'] = $ret['Study Group'];

		// Num of Users
		$arr['num_users']['US Sign Ups'] 	       = $ret['US Sign Ups'];
		$arr['num_users']['Complete Profile'] 	   = $ret['Complete Profile'];
		$arr['num_users']['International Sign Ups']= $ret['International Sign Ups'];
		$arr['num_users']['Total Sign Ups'] 	   = $ret['Total Sign Ups'];

		// Num of Profile
		$arr['num_profile']['US Profile'] 	   = $ret['US Profile'];
		$arr['num_profile']['INTL Profile']    = $ret['INTL Profile'];
		$arr['num_profile']['PassThru US'] 	   = $ret['PassThru US'];
		$arr['num_profile']['PassThru Intl']   = $ret['PassThru Intl'];

		return $arr;
	}

	public function revenueReportMonthly(){

		$term = strtolower( Request::get( 't' ) );

		if ($term != "hgipgap0tjjblrvfn8iyki12131iu9i97316ta") {
			return redirect("/");
		}

		if (Cache::has(env('ENVIRONMENT') .'_'.'runRevenueReportCronJobMonthly')) {
			$data = Cache::get(env('ENVIRONMENT') .'_'.'runRevenueReportCronJobMonthly');
		}else{
			$data = array();
			dd("Cron hasnt ran yet");
			// $data = $this->runRevenueReportCronJobMonthly();
		}

		return View('betausers.reportMonthly', $data);
	}

	private function generateMainRevenueQuery($start_date, $end_date){
		$main_qry = "#edx
				select 'edx' as 'Source'
				,	 (select count(*) as 'Count' from (
						select created_at
						from ad_clicks 
						where company = 'edx'
						and substring_index(utm_source, '_', 1) != 'PLACE'
						group by ip
						having min(created_at)
						order by created_at asc
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."') as 'Clicks'
				, count(*) as 'Conversions', (count(*) * .5) as 'Dollar_Value' from (
					select created_at
					from ad_clicks 
					where company = 'edx'
					and pixel_tracked = 1
					and substring_index(utm_source, '_', 1) != 'PLACE'
					group by ip
					having min(created_at)
					order by created_at asc
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				Select '--', '--', '--', '--'

				union
				select 'nrccua approved', '--', concat(count(*), ' (',
					(select count(*) from (
						(select distinct user_id 
					 from distribution_responses dr 
					 join distribution_clients dc on dr.dc_id = dc.id
					 where dc.ro_id = 1
					 and success = 1
					 and date(dr.created_at) between '".$start_date."' and '".$end_date."')
					) tbl1 
				), ')')
				, count(*) * .61
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 1
				and success = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union
				select '--total', '--', concat(count(*), ' (',
					(select count(*) from (
						(select distinct user_id 
					 from distribution_responses dr 
					 join distribution_clients dc on dr.dc_id = dc.id
					 where dc.ro_id = 1
					 and date(dr.created_at) between '".$start_date."' and '".$end_date."')
					) tbl1 
				), ')')
				, '--'
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'


				union 
				#unique users nrccua
				select '--manual users', '--',  count(distinct user_id), '--'
				FROM
				   distribution_responses
				WHERE
				   ro_id = 1
				AND manual = 0
				AND date(created_at) between '".$start_date."' and '".$end_date."'

				union 
				#unique users nrccua
				select '--manual matches', '--',  count(*), '--'
				FROM
				   distribution_responses
				WHERE
				   ro_id = 1
				AND manual = 0
				AND date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				Select '**', '--', '--', '--'

				union 
				#cappex
				select 'cappex approved', '--',  count(*), count(*) * 0.65
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 2
				and success = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#cappex
				select '--total', '--',  count(distinct user_id, college_id), '--'
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 2
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#GUS
				select 'GUS approved', '--',  count(*), count(*) * 19
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 31
				and success = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#GUS
				select '--total', '--',  count(distinct user_id, college_id), '--'
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 31
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#NCSA
				select 'NCSA approved', '--',  count(*), count(*) * 1
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 38
				and success = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Cappex Scholarships
				select 'CPX$' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'cappex_scholarship'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, Round(count(*) * 0.15)
				, (Round(count(*) * 0.15) * 1) from (
						select created_at
						from ad_clicks 
						where company = 'cappex_scholarship'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Prodigy
				select 'Prodigy' as 'source'
				,	 (select count(*) from (
						select ac.created_at
						from ad_clicks as ac
						join countries as c on c.country_name = ac.countryName
						where ac.company = 'prodigy'
						AND ac.utm_source != 'test_test_test'
						and c.id in (2,32,44,45,48,73,81,82,99,100,105,111,114,131,140,170,179,199,213,226,233)
						group by ac.ip
						having min(ac.created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*) * 4
				, count(*) * 4 from (
						select ac.created_at
						from ad_clicks as ac
						join countries as c on c.country_name = ac.countryName
						where ac.company = 'prodigy'
						AND ac.utm_source != 'test_test_test'
						and c.id in (2,32,44,45,48,73,81,82,99,100,105,111,114,131,140,170,179,199,213,226,233)
						group by ac.ip
						having min(ac.created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				union 
				#keypath
				select 'keypath approved', '--',  count(*), count(*) * dc.price
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 10
				and success = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#keypath
				select '--total', '--',  count(distinct user_id, college_id), '--'
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 10
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#collegeXpress
				select 'COLLEGEX US', '--',  count(*), count(*) * 0.25
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				join users as u on dr.user_id = u.id
				where dc.ro_id = 27
				and dr.success = 1
				and u.country_id = 1
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#collegeXpress
				select 'COLLEGEX INTL', '--',  count(*), count(*) * 0.25
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				join users as u on dr.user_id = u.id
				where dc.ro_id = 27
				and dr.success = 1
				and (u.country_id != 1 OR u.country_id is null)
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union 
				#collegeXpress
				select '--total', '--',  count(distinct user_id), '--'
				from distribution_responses dr 
				join distribution_clients dc on dr.dc_id = dc.id
				where dc.ro_id = 27
				and date(dr.created_at) between '".$start_date."' and '".$end_date."'

				union
				#shorelight
				select 'Shorelight' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'shorelight'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 25 from (
						select created_at
						from ad_clicks 
						where company = 'shorelight'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'


				# union
				# #shorelight
				# select 'shorelight' as 'source'
				# ,	 (select count(*) from (
				# 		select created_at
				# 		from ad_clicks ac
				# 		join (Select id from users where
				# 	(
				# 		country_id in(
				# 			2, 114, 140, 210, 108, 45, 116, 225, 19, 187, 96, 176, 164
				# 		)
				# 	OR
				# 	 (
				# 			(country_id = 99 and (city like 'hyder%' or city like 'mumbai%' or city like 'banga%' or city like '%delhi%' or city like 'ahmed%')) #india
				# 			or
				# 			((country_id = 179) and (city like '%mosc%' or city like '%pete%')) #russia
				# 			or
				# 			(city like 'ho%chi%' and country_id = 233) #vietnam
				# 			or
				# 			(city like 'sa%pa%l%' and country_id = 32) #brazil
				# 			or
				# 			(city like '%bogo%' and country_id = 48) #colombia
				# 			or
				# 			(city like '%lagos%' and country_id = 159) #nigeria
				# 		)
				# 	)
				# ) u on ac.user_id = u.id
				# 		where company = 'shorelight'
				# 		and date(ac.created_at) between '".$start_date."' and '".$end_date."'
				# 		and ac.id not in (57952, 1042386, 1019450)
				# 		group by user_id
				# 		having min(created_at)
				# 		) tbl1)
				# , count(*), count(*) * 25
				# from (
				# 		select created_at
				# 		from ad_clicks ac
				# 		join (Select id from users where
				# 	(
				# 		country_id in(
				# 			2, 114, 140, 210, 108, 45, 116, 225, 19, 187, 96, 176, 164
				# 		)
				# 	OR
				# 	 (
				# 			(country_id = 99 and (city like 'hyder%' or city like 'mumbai%' or city like 'banga%' or city like '%delhi%' or city like 'ahmed%')) #india
				# 			or
				# 			((country_id = 179) and (city like '%mosc%' or city like '%pete%')) #russia
				# 			or
				# 			(city like 'ho%chi%' and country_id = 233) #vietnam
				# 			or
				# 			(city like 'sa%pa%l%' and country_id = 32) #brazil
				# 			or
				# 			(city like '%bogo%' and country_id = 48) #colombia
				# 			or
				# 			(city like '%lagos%' and country_id = 159) #nigeria
				# 		)
				# 	)
				# 	) u on ac.user_id = u.id
				# 		where company = 'shorelight'
				# 		and ac.id not in (57952, 1042386, 1019450)
				# 		and pixel_tracked = 1
				# 		AND utm_source != 'test_test_test'
				# 		group by ac.user_id
				# 		having min(created_at)
				# ) tbl1
				# where date(created_at) between '".$start_date."' and '".$end_date."'

				# union
				# #qs_grad
				# select 'qs grad' as 'source'
				# ,	 (select count(*) from (
				# 		select created_at
				# 		from ad_clicks 
				# 		where company = 'qs_grad'
				# 		and id not in (57952, 1042386, 1019450)
				# 		AND utm_source != 'test_test_test'
				# 		group by ip
				# 		having min(created_at)
				# 		) tbl1
				# 		where date(created_at) between '".$start_date."' and '".$end_date."')
				# , count(*)
				# , count(*) * 15 from (
				# 		select created_at
				# 		from ad_clicks 
				# 		where company = 'qs_grad'
				# 		and pixel_tracked = 1
				# 		and id not in (57952, 1042386, 1019450)
				# 		AND utm_source != 'test_test_test'
				# 		group by ip
				# 		having min(created_at)
				# ) tbl1
				# where date(created_at) between '".$start_date."' and '".$end_date."'

				union
				#qs grad
				select 'qs grad' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'qs_grad'
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 10 from (
						select created_at
						from ad_clicks 
						where company = 'qs_grad'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				union
				#qs mba
				select 'qs mba' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'qs_mba'
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 10 from (
						select created_at
						from ad_clicks 
						where company = 'qs_mba'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				union
				#springboard
				select 'springboard' as 'source', count(*)
				,	'--'
				, count(*) * .615 from (
						select created_at
						from ad_clicks 
						where company = 'springboard'
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				-- UNION
				-- #musician's institute
				-- select 'music_inst' as 'source'
				-- ,	 (select count(*) from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'music_inst'
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- 		) tbl1
				-- 		where date(created_at) between '".$start_date."' and '".$end_date."')
				-- , count(*)
				-- , count(*) * 10 from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'music_inst'
				-- 		and pixel_tracked = 1
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- ) tbl1
				-- where date(created_at) between '".$start_date."' and '".$end_date."'

				-- UNION
				-- #Exampal
				-- select 'exampal' as 'source'
				-- ,	 (select count(*) from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'exampal'
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- 		) tbl1
				-- 		where date(created_at) between '".$start_date."' and '".$end_date."')
				-- , count(*)
				-- , count(*) * 3 from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'exampal'
				-- 		and pixel_tracked = 1
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- ) tbl1
				-- where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Cornell College
				select 'Cornell College' as 'source'
				,	 (select count(*) from (
						select updated_at
						from ad_clicks 
						where company = 'cornellcollege'
						AND utm_source != 'test_test_test'
						group by ip
						having min(updated_at)
						) tbl1
						where date(updated_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 50 from (
						select updated_at
						from ad_clicks 
						where company = 'cornellcollege'
						and paid_client = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(updated_at)
				) tbl1
				where date(updated_at) between '".$start_date."' and '".$end_date."'

				-- UNION
				-- #Magoosh
				-- select 'Magoosh' as 'source'
				-- ,	 (select count(*) from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'magooshielts'
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- 		) tbl1
				-- 		where date(created_at) between '".$start_date."' and '".$end_date."')
				-- , count(*)
				-- , count(*) * 0 from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'magooshielts'
				-- 		and pixel_tracked = 1
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- ) tbl1
				-- where date(created_at) between '".$start_date."' and '".$end_date."'

				-- UNION
				-- #San Diego
				-- select 'San Diego' as 'source', count(*)
				-- ,	'--'
				-- , count(*) * 2 from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'sdsu_ali'
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- ) tbl1
				-- where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Study Group
				select 'Study Group' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks as ac
						where ac.company like 'sg_%'
						AND ac.utm_source != 'test_test_test'
						AND NOT exists 
							(select * from ad_clicks ac_sub
							 where company like 'sg_%' 
							 and countryName = 'United States' 
							 AND utm_source != 's_inquiry'
							 and ac.user_id = ac_sub.user_id)
						group by ac.ip
						having min(ac.created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 10 from (
						select created_at
						from ad_clicks as ac
						where ac.company like 'sg_%'
						and ac.pixel_tracked = 1
						AND ac.utm_source != 'test_test_test'
						AND NOT exists 
							(select * from ad_clicks ac_sub
							 where company like 'sg_%' 
							 and countryName = 'United States' 
							 AND utm_source != 's_inquiry'
							 and ac.user_id = ac_sub.user_id)
						group by ac.ip
						having min(ac.created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				-- UNION
				-- #USF
				-- select 'USF' as 'source'
				-- ,	 (select count(*) from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'usf'
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- 		) tbl1
				-- 		where date(created_at) between '".$start_date."' and '".$end_date."')
				-- , count(*)
				-- , count(*) * 25 from (
				-- 		select created_at
				-- 		from ad_clicks 
				-- 		where company = 'usf'
				-- 		and pixel_tracked = 1
				-- 		AND utm_source != 'test_test_test'
				-- 		group by ip
				-- 		having min(created_at)
				-- ) tbl1
				-- where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Study Portal
				select 'Study Portal' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'studyportals'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, Round(count(*) * 0.3)
				, (Round(count(*) * 0.3) * 0.8) from (
						select created_at
						from ad_clicks 
						where company = 'studyportals'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Hult
				select 'Hult' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'hult'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 50 from (
						select created_at
						from ad_clicks 
						where company = 'hult'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'


				UNION
				#OSU
				select 'OSU' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'oregonstateuniversity'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 25 from (
						select created_at
						from ad_clicks 
						where company = 'oregonstateuniversity'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#intostudy
				select 'into' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company LIKE 'intostudy%'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 25 from (
						select created_at
						from ad_clicks 
						where company LIKE 'intostudy%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Alliant
				select 'Alliant' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company LIKE 'alliant%'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 10 from (
						select created_at
						from ad_clicks 
						where company LIKE 'alliant%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Plexuss Premium
				select 'Plex P' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company LIKE 'plexuss_premium'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, count(*)
				, count(*) * 9.99 from (
						select created_at
						from ad_clicks 
						where company LIKE 'plexuss_premium'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				#Scholarship Owl
				select 'Scholar Owl' as 'source'
				,	 (select count(*) from (
						select created_at
						from ad_clicks 
						where company = 'scholarshipowl'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."')
				, Round(count(*) * 0.2)
				, (Round(count(*) * 0.2) * 1.20) from (
						select created_at
						from ad_clicks 
						where company = 'scholarshipowl'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				Select '__', '--', '--', '--'

				UNION
				#signups
				select 'US Sign Ups', '', count(*), '--'
				from users
				where country_id = 1
				and is_ldy = 0
				and date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				Select 'Complete Profile', '--', count(*),  '--'
				from users u
				join scores s on u.id = s.user_id
				where u.state in
				('AL',	'Alabama',
				'AK',	'Alaska',
				'AZ',	'Arizona',
				'AR',	'Arkansas',
				'CA',	'California',
				'CO',	'Colorado',
				'CT',	'Connecticut',
				'DE',	'Delaware',
				'FL',	'Florida',
				'GA',	'Georgia',
				'HI',	'Hawaii',
				'ID',	'Idaho',
				'IL',	'Illinois',
				'IN',	'Indiana',
				'IA',	'Iowa',
				'KS',	'Kansas',
				'KY',	'Kentucky',
				'LA',	'Louisiana',
				'ME',	'Maine',
				'MD',	'Maryland',
				'MA',	'Massachusetts',
				'MI',	'Michigan',
				'MN',	'Minnesota',
				'MS',	'Mississippi',
				'MO',	'Missouri',
				'MT',	'Montana',
				'NE',	'Nebraska',
				'NV',	'Nevada',
				'NH',	'New Hampshire',
				'NJ',	'New Jersey',
				'NM',	'New Mexico',
				'NY',	'New York',
				'NC',	'North Carolina',
				'ND',	'North Dakota',
				'OH',	'Ohio',
				'OK',	'Oklahoma',
				'OR',	'Oregon',
				'PA',	'Pennsylvania',
				'RI',	'Rhode Island',
				'SC',	'South Carolina',
				'SD',	'South Dakota',
				'TN',	'Tennessee',
				'TX',	'Texas',
				'UT',	'Utah',
				'VT',	'Vermont',
				'VA',	'Virginia',
				'WA',	'Washington',
				'WV',	'West Virginia',
				'WI',	'Wisconsin',
				'WY',	'Wyoming',
				'GE',
				'HA',
				'IO',
				'KA',
				'KE',
				'LO',
				'NO',
				'PE',
				'RH',
				'SO',
				'TE',
				'VE',
				'VI',
				'WE',
				'American Samoa',
				'District of Columbia',
				'Guam',
				'Northern Mariana Islands',
				'Northern Mariana Islands, Commonwealth',
				'Puerto Rico',
				'Puerto Rico, Commonwealth',
				'United States Virgin Islands',
				'AS',
				'DC',
				'GU',
				'MP',
				'PR',
				'VI'
				)
				and is_ldy = 0
				and u.country_id = 1
				and u.address is not null
				and length(u.address) >= 3
				and zip is not null
				and length(city) >= 2
				and u.email REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'
				and u.id not in (Select user_id from country_conflicts)
				and year(birth_date) >= year(current_date()) - 18
				and gender in ('m', 'f')
				and coalesce(hs_gpa, overall_gpa, weighted_gpa) is not null
				and date(u.created_at) between '".$start_date."' and '".$end_date."'
				and email not like '%test%'
				and fname not like '%test'
				and email not like '%nrccua%'


				UNION
				select 'International Sign Ups', '--', count(*), '--'
				from users
				where (country_id != 1 or country_id is null)
				and is_ldy = 0
				and date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				select 'Total Sign Ups', '--', count(*), '--'
				from users
				where is_ldy = 0
				and date(created_at) between '".$start_date."' and '".$end_date."'

				UNION
				Select '==', '--', '--', '--'

				UNION
				select 'US Profile', '--', count(*), '--'
				FROM
					users as u
				JOIN recruitment_tags_cron_jobs as rtcj ON(rtcj.user_id = u.id)
				LEFT JOIN ad_passthroughs as ap ON(u.id = ap.user_id) 
				WHERE
					DATE(u.created_at) != DATE(u.updated_at)
				AND date(u.updated_at) = date(rtcj.updated_at)
				and DATE(u.updated_at) BETWEEN '".$start_date."' and '".$end_date."'
				AND u.country_id = 1
				AND u.profile_percent >=35
				AND ap.id is null
				and u.is_ldy = 0

				UNION
				select 'INTL Profile', '--', count(*), '--'
				FROM
					users as u
				JOIN recruitment_tags_cron_jobs as rtcj ON(rtcj.user_id = u.id)
				LEFT JOIN ad_passthroughs as ap ON(u.id = ap.user_id)
				WHERE
					DATE(u.created_at) != DATE(u.updated_at)
				AND date(u.updated_at) = date(rtcj.updated_at)
				and DATE(u.updated_at) BETWEEN '".$start_date."' and '".$end_date."'
				AND (u.country_id != 1 or u.country_id is null)
				AND u.profile_percent >=35
				AND ap.id is null
				and u.is_ldy = 0

				UNION
				SELECT 'PassThru US', '--', count(DISTINCT u.id), '--'
				FROM
				    ad_passthroughs as ap
				JOIN users as u ON(u.id = ap.user_id)
				JOIN scores as s ON(s.user_id = u.id)
				WHERE
				    DATE(ap.updated_at) = DATE(u.updated_at)
				#AND DATE(ap.updated_at) != DATE(u.created_at)
				AND u.fname is not null 
				AND u.lname is not null
				AND u.email is not null
				AND u.address is not null
				AND u.city is not null
				AND u.state is not null
				AND u.zip is not null
				AND u.gender is not null
				AND u.birth_date is not null
				AND (s.hs_gpa is not null OR s.overall_gpa is not null)
				AND u.country_id = 1
				and u.is_ldy = 0
				AND DATE(u.updated_at) BETWEEN '".$start_date."' and '".$end_date."'

				UNION
				SELECT 'PassThru Intl', '--', count(DISTINCT u.id), '--'
				FROM
				    ad_passthroughs as ap
				JOIN users as u ON(u.id = ap.user_id)
				JOIN scores as s ON(s.user_id = u.id)
				WHERE
				    DATE(ap.updated_at) = DATE(u.updated_at)
				#AND DATE(ap.updated_at) != DATE(u.created_at)
				AND u.fname is not null 
				AND u.lname is not null
				AND u.email is not null
				AND u.address is not null
				AND u.city is not null
				AND u.state is not null
				AND u.zip is not null
				AND u.gender is not null
				AND u.birth_date is not null
				AND (s.hs_gpa is not null OR s.overall_gpa is not null)
				AND (u.country_id != 1 or u.country_id is null)
				and u.is_ldy = 0
				AND DATE(u.updated_at) BETWEEN '".$start_date."' and '".$end_date."'
				;";

		return $main_qry;
	}

	public function runRevenueReportCronJob(){
		
		$time_now = Carbon::now()->toTimeString();

		$start_time = "00:30:00";
		$end_time   = "04:50:00";

		if (isset($start_time) && isset($end_time)) {

			$can_i_run = true;
			if ($time_now >= $start_time && $time_now <= $end_time) {
				$can_i_run = false;
			}

			if ($can_i_run == false) {
				return "Can't run this at this time";
			}
		}

		$is_it_running = DB::connection('rds1')->table('jobs')
											   ->where('payload', 'LIKE', '%RunRevenueReportCronJob%')
											   ->count();

		if ($is_it_running  > 3) {

			DB::table('jobs')->where('payload', 'LIKE', '%RunRevenueReportCronJob%')
							 ->delete()();

			// return "already in queue. Will have to wait";
		}

		// if (Cache::has( env('ENVIRONMENT') .'_'. '__revenueReport')) {
    		
  //   		$cron = Cache::get( env('ENVIRONMENT') .'_'. '__revenueReport');

  //   		if ($cron == 'in_progress') {
  //   			return "a cron is already running";
  //   		}
  //   	}

  //   	Cache::put( env('ENVIRONMENT') .'_'. '__revenueReport', 'in_progress', 80);

		$today 	   = Carbon::today()->toDateString();
		$tomorrow  = Carbon::tomorrow()->toDateString();
		$yesterday = Carbon::yesterday()->toDateString();

		$first_day_of_month = Carbon::now()->startOfMonth()->toDateString();
		$end_of_month = Carbon::now()->endOfMonth()->toDateString();
		
		$today_qry      = DB::connection('rds1')->select($this->generateMainRevenueQuery($today, $tomorrow));
		$yesterday_qry  = DB::connection('rds1')->select($this->generateMainRevenueQuery($yesterday, $yesterday));
		$this_month_qry = DB::connection('rds1')->select($this->generateMainRevenueQuery($first_day_of_month, $end_of_month));

		$data = array();
		$data['currentPage'] = 'report';
		$data['today'] = $today_qry;
		$data['yesterday'] = $yesterday_qry;
		$data['this_month'] = $this_month_qry;

		$today_total = 0;
		$today_clicks = 0;
		foreach ($today_qry as $key) {
			if ($key->Source != "nrccua b" && $key->Dollar_Value != "--") {
				$today_total += $key->Dollar_Value;
			}
			if ($key->Source != "nrccua b" && $key->Clicks != "--") {
				$today_clicks += $key->Clicks;
			}

		}

		$yesterday_total = 0;
		$yesterday_clicks = 0;
		foreach ($yesterday_qry as $key) {
			if ($key->Source != "nrccua b" && $key->Dollar_Value != "--") {
				$yesterday_total += $key->Dollar_Value;
			}
			if ($key->Source != "nrccua b" && $key->Clicks != "--") {
				$yesterday_clicks += $key->Clicks;
			}
		}

		$this_month_total = 0;
		$this_month_clicks = 0;
		foreach ($this_month_qry as $key) {
			if ($key->Source != "nrccua b" && $key->Dollar_Value != "--") {
				$this_month_total += $key->Dollar_Value;
			}
			if ($key->Source != "nrccua b" && $key->Clicks != "--") {
				$this_month_clicks += $key->Clicks;
			}
		}

		$data['today_total'] 	  = number_format($today_total, 2);
		$data['yesterday_total']  = number_format($yesterday_total, 2);
		$data['this_month_total'] = number_format($this_month_total, 2);

		$data['today_clicks'] 	   = number_format($today_clicks);
		$data['yesterday_clicks']  = number_format($yesterday_clicks);
		$data['this_month_clicks'] = number_format($this_month_clicks);
		// $this->customdd($today_qry);

		$appended_today = DB::connection('rds1')->table('users as u')
												->join('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
												->where('nu.updated_at', '>=', $today)
												->where('nu.updated_at', '<=', $tomorrow)
												->whereNotNull('nu.address')
												->whereNotNull('nu.city')
												->whereNotNull('nu.state')
												->where('nu.is_manual', 1)
												->select(DB::raw("count(`nu`.`id`) as cnt"), "u.in_college")
												->groupBy('u.in_college')
												->get();
		$data['appended_today'] 	   = $appended_today;

		$appended_yesterday = DB::connection('rds1')->table('users as u')
												->join('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
												->where('nu.updated_at', '>=', $yesterday)
												->where('nu.updated_at', '<=', $today)
												->whereNotNull('nu.address')
												->whereNotNull('nu.city')
												->whereNotNull('nu.state')
												->where('nu.is_manual', 1)
												->select(DB::raw("count(`nu`.`id`) as cnt"), "u.in_college")
												->groupBy('u.in_college')
												->get();
		$data['appended_yesterday'] 	   = $appended_yesterday;

		$appended_this_month = DB::connection('rds1')->table('users as u')
												->join('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
												->where('nu.updated_at', '>=', $first_day_of_month)
												->where('nu.updated_at', '<=', $end_of_month)
												->whereNotNull('nu.address')
												->whereNotNull('nu.city')
												->whereNotNull('nu.state')
												->where('nu.is_manual', 1)
												->select(DB::raw("count(`nu`.`id`) as cnt"), "u.in_college")
												->groupBy('u.in_college')
												->get();
		$data['appended_this_month'] 	   = $appended_this_month;


		Cache::put(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob', $data, 480);

		Cache::put( env('ENVIRONMENT') .'_'. '__revenueReport', 'done', 30);

		return $data;
	}

	public function getRunRevenueReportCronJob(){
		$tmp = Cache::get(env('ENVIRONMENT') .'_'.'runRevenueReportCronJob');

		return $tmp;
	}

	public function runRevenueReportCronJobMonthly(){

		$is_it_running = DB::connection('rds1')->table('jobs')
											   ->where('payload', 'LIKE', '%runRevenueReportCronJobMonthly%')
											   ->count();

		if ($is_it_running  > 2) {

			DB::table('jobs')->where('payload', 'LIKE', '%runRevenueReportCronJobMonthly%')
							 ->delete()();

			// return "already in queue. Will have to wait";
		}

		
		if (Cache::has( env('ENVIRONMENT') .'_'. '__runRevenueReportCronJobMonthly')) {
    		
    		$cron = Cache::get( env('ENVIRONMENT') .'_'. '__runRevenueReportCronJobMonthly');

    		if ($cron == 'in_progress') {
    			return "a cron is already running";
    		}
    	}

    	Cache::put( env('ENVIRONMENT') .'_'. '__runRevenueReportCronJobMonthly', 'in_progress', 740);

		$first_day_of_a_month_ago = Carbon::now()->subMonth(1)->startOfMonth()->toDateString();
		$end_of_a_month_ago       =  Carbon::now()->subMonth(1)->endOfMonth()->toDateString();

		$first_day_of_two_month_ago = Carbon::now()->subMonth(2)->startOfMonth()->toDateString();
		$end_of_two_month_ago       =  Carbon::now()->subMonth(2)->endOfMonth()->toDateString();

		$first_day_of_three_month_ago = Carbon::now()->subMonth(3)->startOfMonth()->toDateString();
		$end_of_three_month_ago       =  Carbon::now()->subMonth(3)->endOfMonth()->toDateString();
		
		$a_month_ago = Carbon::parse(Carbon::now()->startOfMonth()->subMonth(1))->format('F');
		$two_month_ago = Carbon::parse(Carbon::now()->startOfMonth()->subMonth(2))->format('F');
		$three_month_ago  = Carbon::parse(Carbon::now()->startOfMonth()->subMonth(3))->format('F');


		$today_qry      = DB::connection('rds1')->select($this->generateMainRevenueQuery($first_day_of_a_month_ago, $end_of_a_month_ago));
		$yesterday_qry  = DB::connection('rds1')->select($this->generateMainRevenueQuery($first_day_of_two_month_ago, $end_of_two_month_ago));
		$this_month_qry = DB::connection('rds1')->select($this->generateMainRevenueQuery($first_day_of_three_month_ago, $end_of_three_month_ago));

		$data = array();
		$data['currentPage'] = 'report';

		$data['today'] = $today_qry;
		$data['yesterday'] = $yesterday_qry;
		$data['this_month'] = $this_month_qry;

		$data['a_month_ago']     = $a_month_ago;
		$data['two_month_ago']   = $two_month_ago;
		$data['three_month_ago'] = $three_month_ago;

		$today_total = 0;
		$today_clicks = 0;
		foreach ($today_qry as $key) {
			if ($key->Source != "nrccua b" && $key->Dollar_Value != "--") {
				$today_total += $key->Dollar_Value;
			}
			if ($key->Source != "nrccua b" && $key->Clicks != "--") {
				$today_clicks += $key->Clicks;
			}

		}

		$yesterday_total = 0;
		$yesterday_clicks = 0;
		foreach ($yesterday_qry as $key) {
			if ($key->Source != "nrccua b" && $key->Dollar_Value != "--") {
				$yesterday_total += $key->Dollar_Value;
			}
			if ($key->Source != "nrccua b" && $key->Clicks != "--") {
				$yesterday_clicks += $key->Clicks;
			}
		}

		$this_month_total = 0;
		$this_month_clicks = 0;
		foreach ($this_month_qry as $key) {
			if ($key->Source != "nrccua b" && $key->Dollar_Value != "--") {
				$this_month_total += $key->Dollar_Value;
			}
			if ($key->Source != "nrccua b" && $key->Clicks != "--") {
				$this_month_clicks += $key->Clicks;
			}
		}

		$data['today_total'] 	  = number_format($today_total, 2);
		$data['yesterday_total']  = number_format($yesterday_total, 2);
		$data['this_month_total'] = number_format($this_month_total, 2);

		$data['today_clicks'] 	   = number_format($today_clicks);
		$data['yesterday_clicks']  = number_format($yesterday_clicks);
		$data['this_month_clicks'] = number_format($this_month_clicks);
		// $this->customdd($today_qry);

		$appended_today = DB::connection('bk')->table('users as u')
												->join('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
												->where('nu.updated_at', '>=', $first_day_of_a_month_ago)
												->where('nu.updated_at', '<=', $end_of_a_month_ago)
												->whereNotNull('nu.address')
												->whereNotNull('nu.city')
												->whereNotNull('nu.state')
												->where('nu.is_manual', 1)
												->select(DB::raw("count(`nu`.`id`) as cnt"), "u.in_college")
												->groupBy('u.in_college')
												->get();
		$data['appended_today'] 	   = $appended_today;

		$appended_yesterday = DB::connection('bk')->table('users as u')
												->join('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
												->where('nu.updated_at', '>=', $first_day_of_two_month_ago)
												->where('nu.updated_at', '<=', $end_of_two_month_ago)
												->whereNotNull('nu.address')
												->whereNotNull('nu.city')
												->whereNotNull('nu.state')
												->where('nu.is_manual', 1)
												->select(DB::raw("count(`nu`.`id`) as cnt"), "u.in_college")
												->groupBy('u.in_college')
												->get();
		$data['appended_yesterday'] 	   = $appended_yesterday;

		$appended_this_month = DB::connection('bk')->table('users as u')
												->join('nrccua_users as nu', 'u.id', '=', 'nu.user_id')
												->where('nu.updated_at', '>=', $first_day_of_three_month_ago)
												->where('nu.updated_at', '<=', $end_of_three_month_ago)
												->whereNotNull('nu.address')
												->whereNotNull('nu.city')
												->whereNotNull('nu.state')
												->where('nu.is_manual', 1)
												->select(DB::raw("count(`nu`.`id`) as cnt"), "u.in_college")
												->groupBy('u.in_college')
												->get();
		$data['appended_this_month'] 	   = $appended_this_month;


		Cache::put(env('ENVIRONMENT') .'_'.'runRevenueReportCronJobMonthly', $data, 740);

		Cache::put( env('ENVIRONMENT') .'_'. '__runRevenueReportCronJobMonthly', 'done', 30);

		return $data;
	}

	public function getRevenuePerClient($client_name, $start_date, $end_date){
		// $client_name = "qs_mba";
		// $start_date  = "2018-01-01";
		// $end_date 	 = "2018-02-01";

		if (Cache::has( env('ENVIRONMENT') .'_'.'getRevenuePerClient_'.$client_name. '_'.$start_date. '_'.$end_date)) {
    		$ret = Cache::get( env('ENVIRONMENT') .'_'.'getRevenuePerClient_'.$client_name. '_'.$start_date. '_'.$end_date);
    		return $ret;
    	}
    	
		switch ($client_name) {
			case 'qs_mba':
				$qry = "SELECT count(*) * 15 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'qs_mba'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
				break;

			case 'qs_grad':
				$qry = "SELECT count(*) * 15 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'qs_grad'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
				break;
			
			case 'springboard':
			    $qry = "SELECT count(*) * .615 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'springboard'
						and id not in (57952, 1042386, 1019450)
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
			    break;

			case 'music_inst':
			    $qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'music_inst'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
			    break;

			case 'edx':
			    $qry = "SELECT count(*) * .5 as cnt from (
					select created_at
					from ad_clicks 
					where company = 'edx'
					and pixel_tracked = 1
					and substring_index(utm_source, '_', 1) != 'PLACE'
					group by ip
					having min(created_at)
					order by created_at asc
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'exampal':
			    $qry = "SELECT count(*) * 3 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'exampal'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'cornellcollege':
			    $qry = "SELECT count(*) * 50 as cnt from (
						select updated_at
						from ad_clicks 
						where company = 'cornellcollege'
						and paid_client = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(updated_at)
				) tbl1
				where date(updated_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'magooshielts':
			    $qry = "SELECT count(*) * 0 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'magooshielts'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'sdsu_ali':
			    $qry = "SELECT count(*) * 2 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'sdsu_ali'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'usf':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'usf'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'hult':
			    $qry = "SELECT count(*) * 50 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'hult'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'oregonstateuniversity':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'oregonstateuniversity'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'alliant':
			    $qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'alliant%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'cappex_scholarship':
			    $qry = "SELECT (Round(count(*) * 0.15) * 1) as  cnt from (
						select created_at
						from ad_clicks 
						where company = 'cappex_scholarship'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'plexuss_premium':
			    $qry = "SELECT count(*) * 9.99 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'plexuss_premium'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'intostudy':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'intostudy%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'scholarshipowl':
			    $qry = "SELECT (Round(count(*) * 0.2) * 1.20) as  cnt from (
						select created_at
						from ad_clicks 
						where company = 'scholarshipowl'
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'prodigy':
				$qry = "SELECT (count(*) * 4) as  cnt from (
						select ac.created_at 
						from ad_clicks as ac
						join countries as c on c.country_name = ac.countryName
						where ac.company = 'prodigy'
						AND ac.utm_source != 'test_test_test'
						and c.id in (2,32,44,45,48,73,81,82,99,100,105,111,114,131,140,170,179,199,213,226,233)
						and ac.user_id in (".$user_arr.")
						group by ac.ip
						having min(ac.created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'openClassrooms':
			    $qry = "";
			    break;

			default:
				$qry = "";
				break;
		}

		if (empty($qry)) {
			return 0;
		}

		$ret = DB::connection('rds1')->select($qry);
		
		if (isset($ret[0]->cnt)) {

			Cache::put(env('ENVIRONMENT') .'_'.'getRevenuePerClient_'.$client_name. '_'.$start_date. '_'.$end_date, $ret[0]->cnt, 30);
			return $ret[0]->cnt;
		}
		
		return 0;
	}

	public function getIndividualCompanyRev($company, $start_date, $end_date, $user_arr){

		switch ($company) {
			case 'qs_mba':
				$qry = "SELECT count(*) * 15 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'qs_mba'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
				break;

			case 'qs_grad':
				$qry = "SELECT count(*) * 15 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'qs_grad'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
				break;
			
			case 'springboard':
			    $qry = "SELECT count(*) * .615 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'springboard'
						and id not in (57952, 1042386, 1019450)
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
			    break;

			case 'music_inst':
			    $qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'music_inst'
						and pixel_tracked = 1
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
			    break;

			case 'edx':
			    $qry = "SELECT count(*) * .5 as cnt from (
					select created_at
					from ad_clicks 
					where company = 'edx'
					and pixel_tracked = 1
					and substring_index(utm_source, '_', 1) != 'PLACE'
					and user_id in (".$user_arr.")
					group by ip
					having min(created_at)
					order by created_at asc
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'exampal':
			    $qry = "SELECT count(*) * 3 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'exampal'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'cornellcollege':
			    $qry = "SELECT count(*) * 50 as cnt from (
						select updated_at
						from ad_clicks 
						where company = 'cornellcollege'
						and paid_client = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(updated_at)
				) tbl1
				where date(updated_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'magooshielts':
			    $qry = "SELECT count(*) * 0 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'magooshielts'
						and pixel_tracked = 1
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'sdsu_ali':
			    $qry = "SELECT count(*) * 2 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'sdsu_ali'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'usf':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'usf'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'hult':
			    $qry = "SELECT count(*) * 50 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'hult'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'oregonstateuniversity':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'oregonstateuniversity'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'alliantrfi':
			case 'alliantapp':
			case 'alliant':
			    $qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'alliant%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'cappex_scholarship':
			    $qry = "SELECT (Round(count(*) * 0.15) * 1) as  cnt from (
						select created_at
						from ad_clicks 
						where company = 'cappex_scholarship'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'plexuss_premium':
			    $qry = "SELECT count(*) * 9.99 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'plexuss_premium'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'intostudy_uk':
			case 'intostudy':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'intostudy%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'scholarshipowl':
			    $qry = "SELECT (Round(count(*) * 0.2) * 1.20) as  cnt from (
						select created_at
						from ad_clicks 
						where company = 'scholarshipowl'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'openClassrooms':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'openClassrooms%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
			    break;

			case 'benedictineinternationalapp':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'benedictineinternationalapp'
						and pixel_tracked = 1
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."';";
			    break;

			case 'sg_royalroadsuniversity':
			case 'sg_thecitycollegeofnewyork':
			case 'sg_jamesmadisonuniversity':
			case 'sg_longislanduniversitybrooklyn':
			case 'sg_lipscombuniversity':
			case 'sg_merrimack':
			case 'sg_oglethorpeuniversity':
			case 'sg_rooseveltuniversity':
			case 'sg_texasamniversitycorpuschristi':
			case 'sg_universityofvermont':
			case 'sg_westvirginiauniversity':
			case 'sg_westernwashingtonuniversity':
			case 'sg_wideneruniversity':
			case 'sg_longislanduniversitypost':
			case 'sg_bayloruniversity':
			case 'sg_lancaster_university':
			case 'sg_the_university_of_sheffield':
			case 'sg_durham_university':
			case 'sg_university_of_leicester':
			case 'sg_university_of_leeds':
			case 'sg_university_of_sussex':
			case 'sg_university_of_surrey':
				$qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks as ac
						where ac.company like 'sg_%'
						and ac.pixel_tracked = 1
						AND ac.utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						AND NOT exists 
							(select * from ad_clicks ac_sub
							 where company like 'sg_%' 
							 and countryName = 'United States' 
							 AND utm_source != 's_inquiry'
							 and ac.user_id = ac_sub.user_id)
						group by ac.ip
						having min(ac.created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";

				break;

			case 'studyportals':
				$qry = "SELECT (Round(count(*) * 0.3) * 0.8) as cnt from (
						select created_at
						from ad_clicks 
						where company = 'studyportals'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'update_profile':
				return 0;
				break;

			case 'shorelight':
				$qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'shorelight'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				where date(created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'nrccua':
				$qry = "SELECT count(*) * .61 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 1
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =0
						and date(dr.created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'collegeXpress':
				$qry = "SELECT count(*) * .25 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 27
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =0
						and date(dr.created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'cappex':
				$qry = "SELECT count(*) * 0.65 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 2
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =1
						and date(dr.created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'NCSA':
				$qry = "SELECT count(*) * 1 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 38
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =0
						and date(dr.created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'prodigy':
				$qry = "SELECT (count(*) * 4) as  cnt from (
						select ac.created_at 
						from ad_clicks as ac
						join countries as c on c.country_name = ac.countryName
						where ac.company = 'prodigy'
						AND ac.utm_source != 'test_test_test'
						and c.id in (2,32,44,45,48,73,81,82,99,100,105,111,114,131,140,170,179,199,213,226,233)
						and ac.user_id in (".$user_arr.")
						group by ac.ip
						having min(ac.created_at)
						) tbl1
						where date(created_at) between '".$start_date."' and '".$end_date."'";
				break;

			case 'eddy_reg':
				return 0;
				break;

			case 'openClassrooms':
				return 0;
				break;

			default:
				$qry = "";
				break;
		}

		if (empty($qry)) {
			return 0;
		}

		$ret = DB::connection('rds1')->select($qry);

		if (isset($ret[0]->cnt)) {
			return $ret[0]->cnt;
		}

		return 0;
	}

	public function getIndividualCompanyRevAllTime($company, $user_arr){

		switch ($company) {
			case 'qs_mba':
				$qry = "SELECT count(*) * 15 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'qs_mba'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				;";
				break;

			case 'qs_grad':
				$qry = "SELECT count(*) * 15 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'qs_grad'
						and pixel_tracked = 1
						and id not in (57952, 1042386, 1019450)
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				;";
				break;
			
			case 'springboard':
			    $qry = "SELECT count(*) * .615 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'springboard'
						and id not in (57952, 1042386, 1019450)
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				;";
			    break;

			case 'music_inst':
			    $qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'music_inst'
						and pixel_tracked = 1
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				;";
			    break;

			case 'edx':
			    $qry = "SELECT count(*) * .5 as cnt from (
					select created_at
					from ad_clicks 
					where company = 'edx'
					and pixel_tracked = 1
					and substring_index(utm_source, '_', 1) != 'PLACE'
					and user_id in (".$user_arr.")
					group by ip
					having min(created_at)
					order by created_at asc
				) tbl1
				";
			    break;

			case 'exampal':
			    $qry = "SELECT count(*) * 3 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'exampal'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'cornellcollege':
			    $qry = "SELECT count(*) * 50 as cnt from (
						select updated_at
						from ad_clicks 
						where company = 'cornellcollege'
						and paid_client = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(updated_at)
				) tbl1
				";
			    break;

			case 'magooshielts':
			    $qry = "SELECT count(*) * 0 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'magooshielts'
						and pixel_tracked = 1
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'sdsu_ali':
			    $qry = "SELECT count(*) * 2 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'sdsu_ali'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'usf':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'usf'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'hult':
			    $qry = "SELECT count(*) * 50 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'hult'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'oregonstateuniversity':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'oregonstateuniversity'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'alliantrfi':
			case 'alliantapp':
			case 'alliant':
			    $qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'alliant%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'cappex_scholarship':
			    $qry = "SELECT (Round(count(*) * 0.15) * 1) as  cnt from (
						select created_at
						from ad_clicks 
						where company = 'cappex_scholarship'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'plexuss_premium':
			    $qry = "SELECT count(*) * 9.99 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'plexuss_premium'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'intostudy_uk':
			case 'intostudy':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'intostudy%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'scholarshipowl':
			    $qry = "SELECT (Round(count(*) * 0.2) * 1.20) as  cnt from (
						select created_at
						from ad_clicks 
						where company = 'scholarshipowl'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'openClassrooms':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company LIKE 'openClassrooms%'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
			    break;

			case 'benedictineinternationalapp':
			    $qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'benedictineinternationalapp'
						and pixel_tracked = 1
						and user_id in (".$user_arr.")
						AND utm_source != 'test_test_test'
						group by ip
						having min(created_at)
				) tbl1
				;";
			    break;

			case 'sg_royalroadsuniversity':
			case 'sg_thecitycollegeofnewyork':
			case 'sg_jamesmadisonuniversity':
			case 'sg_longislanduniversitybrooklyn':
			case 'sg_lipscombuniversity':
			case 'sg_merrimack':
			case 'sg_oglethorpeuniversity':
			case 'sg_rooseveltuniversity':
			case 'sg_texasamniversitycorpuschristi':
			case 'sg_universityofvermont':
			case 'sg_westvirginiauniversity':
			case 'sg_westernwashingtonuniversity':
			case 'sg_wideneruniversity':
			case 'sg_longislanduniversitypost':
			case 'sg_bayloruniversity':
			case 'sg_lancaster_university':
			case 'sg_the_university_of_sheffield':
			case 'sg_durham_university':
			case 'sg_university_of_leicester':
			case 'sg_university_of_leeds':
			case 'sg_university_of_sussex':
			case 'sg_university_of_surrey':
				$qry = "SELECT count(*) * 10 as cnt from (
						select created_at
						from ad_clicks as ac
						where ac.company like 'sg_%'
						and ac.pixel_tracked = 1
						AND ac.utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						AND NOT exists 
							(select * from ad_clicks ac_sub
							 where company like 'sg_%' 
							 and countryName = 'United States' 
							 AND utm_source != 's_inquiry'
							 and ac.user_id = ac_sub.user_id)
						group by ac.ip
						having min(ac.created_at)
				) tbl1
				";

				break;

			case 'studyportals':
				$qry = "SELECT (Round(count(*) * 0.3) * 0.8) as cnt from (
						select created_at
						from ad_clicks 
						where company = 'studyportals'
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
				break;

			case 'update_profile':
				return 0;
				break;

			case 'shorelight':
				$qry = "SELECT count(*) * 25 as cnt from (
						select created_at
						from ad_clicks 
						where company = 'shorelight'
						and pixel_tracked = 1
						AND utm_source != 'test_test_test'
						and user_id in (".$user_arr.")
						group by ip
						having min(created_at)
				) tbl1
				";
				break;

			case 'nrccua':
				$qry = "SELECT count(*) * .61 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 1
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =0
						";
				break;

			case 'collegeXpress':
				$qry = "SELECT count(*) * .25 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 27
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =0
						";
				break;

			case 'cappex':
				$qry = "SELECT count(*) * 0.65 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 2
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =1
						";
				break;

			case 'NCSA':
				$qry = "SELECT count(*) * 1 as cnt
						from distribution_responses dr 
						join distribution_clients dc on dr.dc_id = dc.id
						where dc.ro_id = 38
						and success = 1
						and dr.user_id in (".$user_arr.")
						AND dr.manual =0
						";
				break;

			case 'prodigy':
				$qry = "SELECT (count(*) * 4) as  cnt from (
						select ac.created_at
						from ad_clicks as ac
						join countries as c on c.country_name = ac.countryName
						where ac.company = 'prodigy'
						AND ac.utm_source != 'test_test_test'
						and c.id in (2,32,44,45,48,73,81,82,99,100,105,111,114,131,140,170,179,199,213,226,233)
						and ac.user_id in (".$user_arr.")
						group by ac.ip
						having min(ac.created_at)
				) tbl1
				";
				break;


			case 'eddy_reg':
				return 0;
				break;

			case 'openClassrooms':
				return 0;
				break;

			default:
				$qry = "";
				break;
		}

		if (empty($qry)) {
			return 0;
		}

		$ret = DB::connection('rds1')->select($qry);

		if (isset($ret[0]->cnt)) {
			return $ret[0]->cnt;
		}

		return 0;
	}
}
