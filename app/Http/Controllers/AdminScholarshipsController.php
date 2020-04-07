<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Carbon\Carbon, DateTime, DateTimeZone;
use Request, DB, Session, Validator;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\CollegeRecommendationFilters, App\Http\Controllers\AjaxController;

use App\ZipCodes, App\Country, App\Department, App\Degree, App\Ethnicity, App\Religion, App\MilitaryAffiliation, App\DistributionClient, App\DistributionResponse, App\AdminText, App\Scholarship, App\Scholarshipcms;

class AdminScholarshipsController extends Controller
{
	 protected $school_name='';
	 protected $school_logo='';

	public function index($type ='',$scholarship_id = 0){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		Session::forget('temp_scholarshipadmin_id');
		//dd($data);
		$data['currentPage'] = 'admin-adv-filtering';
		$data['adminscholarshipPage'] = 'filtering';
		$data['title'] = 'ADMIN';
		$data["scholarship_info"] = '';
		$data["page_type"] = $type;

		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;
		
		$user_id = $data['user_id'];

		$sau = DB::connection('rds1')->table('scholarship_providers as so')
	        							 ->join('scholarship_admin_users as sau', 'sau.scholarship_org_id', '=', 'so.id')
	        							 ->join('scholarship_verified as sv', 'sv.provider_id', '=', 'so.id')
	        							 ->where('sau.user_id', $data['user_id'])
	        						     ->where('sau.active', 1)
	        						     ->select('only_scholarship', 'sv.id as scholarship_id', 'so.id as scholarship_org_id')
	        						     ->get();

		
		$arr = array();	        						     
	    foreach ($sau as $key) {
	    	$arr[] = $key->scholarship_id;	
	   	}   

		$schol_ids = $arr;		

		$schModel = new Scholarshipcms();

		if(in_array($scholarship_id, $schol_ids)){
			//$checkcount = $schModel->checkScholarshipAdminExist($scholarship_id);
			//if($checkcount>0){
				Session::put('temp_scholarshipadmin_id', $scholarship_id);
				$data["scholarship_info"] = $schModel->checkScholarshipAdmin($scholarship_id);
			//}
		}


		$data["scholarships"] = $scholarships = $schModel->getAllScholarshipsadmin($schol_ids);
		//print_r($scholAll);
		
		return View('admin.scholarshipadmin.index', $data);
	}

	public function addScholarshipAdmin (){
		$error = 0;
		$opdata["scholarship_name_error"] ='';
		$opdata["scholarshipsub_name_error"] ='';
		$opdata["amount_error"] ='';
		$opdata["deadline_error"] ='';
		$opdata["description_error"] ='';
		$opdata["success"] ='';
		$opdata["scholarship_id"] =0;

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$user_id = $data['user_id'];

		$schModel = new Scholarshipcms();
		//$checkcount = $schModel->checkScholarshipAdminExist($user_id);

		$input = Request::all();
		$scholarship_name = trim($input['scholarship_title']);
		$scholarshipsub_name = trim($input['scholarship_subtitle']);
		$amount = trim($input['amount']);
		$deadline = trim($input['deadline']);
		$description = $input['description'];
		$scholarship_id = $input['scholarship_id'];

		if($scholarship_name==''){
			$opdata["scholarship_name_error"] = "Please enter scholarship title";
			$error = 1;
		}
		if($scholarshipsub_name==''){
			$opdata["scholarshipsub_name_error"] = "Please enter scholarship subtitle";
			$error = 1;
		}
		if($amount==''){
			$opdata["amount_error"] = "Please enter amount";
			$error = 1;
		}
		if($deadline==''){
			$opdata["deadline_error"] = "Please select deadline";
			$error = 1;
		}
		if($description==''){
			$opdata["description_error"] = "Please enter description";
			$error = 1;
		}

		if($error==0){
			if($scholarship_id ==0){
				$schModel->scholarship_title = $scholarship_name;
				$schModel->scholarshipsub_title = $scholarshipsub_name;
				$schModel->deadline = $deadline;
				$schModel->max_amount = $amount;
				$schModel->description = $description;
				$schModel->submission_id = null;
				$schModel->provider_id = null;
				$schModel->user_id = $user_id;
				$schModel->scholarship_org_id = $data["scholarship_org_id"];
				$schModel->save();
				$opdata["success"] ='success';
				$opdata["operation"] ='add';
				$opdata["scholarship_id"] = $schModel->id;
			}else{
				$schObj['scholarship_title'] = $scholarship_name;
				$schObj['scholarshipsub_title'] = $scholarshipsub_name;
				$schObj['deadline'] = $deadline;
				$schObj['max_amount'] = $amount;
				$schObj['description'] = $description;
				$schObj['scholarship_org_id'] = $data["scholarship_org_id"];
				$schModel->updateOrCreate(['id'=> $scholarship_id], $schObj);
				//$checkid = $schModel->checkScholarshipAdmin($user_id);

				$opdata["success"] ='success'; 
				$opdata["operation"] ='edit';

				$opdata["scholarship_id"] = $scholarship_id;
			}
		}

		return $opdata;
	}

	public function delScholarshipAdmin (){
		$error = 0;

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
		$schol_id = $input['id'];

		$schModel = new Scholarshipcms();
		$schObj["id"] = $schol_id;
		$schObj["user_id"] = $data['user_id'];
		//$schObj["scholarship_org_id"] = $data['scholarship_org_id'];

		if($schol_id=='' || $schol_id==0){
			$error = 1;
		}

		if($error==0){
			$delrst = $schModel->deleteRec($schObj);
			if($delrst==1){
				return "success";
			}
		}
	}

	public function getAjaxFilterSections($section){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();


		if ($section == null) {
			return;
		}


		$scholarship_id = Session::get('temp_scholarshipadmin_id') ? Session::get('temp_scholarshipadmin_id') : 0;
		$user_id = $data['user_id'];

		$schModel = new Scholarshipcms();
		//$checkcount = $schModel->checkScholarshipAdminExist($user_id);
		//if($checkcount >0){
			//$checkid = $schModel->checkScholarshipAdmin($user_id);
			//$scholarship_id = $checkid->id;
		//}



		$org_portal_id = NULL;
		$aor_id = NULL;

		$data['school_name'] = $this->school_name;
		$data['school_logo'] = $this->school_logo;

		$filter_section = 'admin.scholarshipadmin.ajax.'.$section;

		if ($section == 'video') {
			return View($filter_section, $data);
		}

		$crf = new CollegeRecommendationFilters;
		$filters = $crf->getFiltersAndLogs_scholarshipadmin($section, $scholarship_id);

		//exit;
		$ret = array();
		$name = '';
		if (isset($filters)) {
			$temp = array();

			foreach ($filters as $key) {
				if ($name == '') {

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}
				}elseif($name == $key->name){

					if (isset($key->val)) {
						$temp[$key->name][] = $key->val;
					}
					$name = $key->name;
				}else{
					$ret[] = $temp;

					$name = $key->name;

					$temp = array();
					$temp['type'] = $key->type;
					$temp['category'] = $key->category;
					$temp['filter'] = $key->name;
					if (isset($key->val)) {
						$temp[$key->name] = array();
						$temp[$key->name][] = $key->val;
					}

				}
			}

			$ret[] = $temp;
		}

		$data['filters'] = $ret;


		switch ($section) {
			case 'location':
					$zipCodes = new ZipCodes;
					$states = $zipCodes->getAllUsState();
					$countries = Country::all()->toArray();
					$tmp_ctry = array();
					$tmp_ctry[''] = 'Select a country...';
					foreach ($countries as $key => $value) {
						$tmp_ctry[$value['country_name']] = $value['country_name'];
					}

					$data['countries'] = $tmp_ctry;
					$states = $states;
					$cities = array('' => 'Select state first' );
					$data['states'] = $states;
					$data['cities'] = $cities;
				break;

            case 'scores':
                $new_filters = array();
                foreach($data['filters'] as $key){
                    if(isset($key['sat_act'])){
                        foreach($key['sat_act'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'sat'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'satMin_filter';
                                    $temp['satMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '2400'){
                                    $temp = array();
                                    $temp['filter'] = 'satMax_filter';
                                    $temp['satMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                            else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'actMin_filter';
                                    $temp['actMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '36'){
                                    $temp = array();
                                    $temp['filter'] = 'actMax_filter';
                                    $temp['actMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['ielts_toefl'])){
                        foreach($key['ielts_toefl'] as $i){
                            $val = explode(',', $i);
                            if($val[0] == 'ielts'){
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMin_filter';
                                    $temp['ieltsMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '9'){
                                    $temp = array();
                                    $temp['filter'] = 'ieltsMax_filter';
                                    $temp['ieltsMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }else{
                                if ($val[1] != '0'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMin_filter';
                                    $temp['toeflMin_filter'] = array($val[1]);
                                    $new_filters[] = $temp;
                                }
                                if ($val[2] != '120'){
                                    $temp = array();
                                    $temp['filter'] = 'toeflMax_filter';
                                    $temp['toeflMax_filter'] = array($val[2]);
                                    $new_filters[] = $temp;
                                }
                            }
                        }
                    }elseif(isset($key['gpa_filter'])){
                        $val = explode(',', $key['gpa_filter'][0]);
                        if ($val[0] != '0'){
                            $temp = array();
                            $temp['filter'] = 'gpaMin_filter';
                            $temp['gpaMin_filter'] = array($val[0]);
                            $new_filters[] = $temp;
                        }
                        if ($val[1] != '4'){
                            $temp = array();
                            $temp['filter'] = 'gpaMax_filter';
                            $temp['gpaMax_filter'] = array($val[1]);
                            $new_filters[] = $temp;
                        }
                    }
                }
                $data['filters'] = $new_filters;
                break;

			case 'major':
			case 'majorDeptDegree':

				$dep = new Department;
				$useIds = true;
				$dep_name = $dep->getAllDepartments($useIds);

				$departments = $dep_name;

				$majors = array('' => 'Select department first' );
				$data['departments'] = $departments;

				break;

			case 'uploads':
				$tmpArr = $data['filters'];

				if(isset($tmpArr) && !empty($tmpArr[0]) && isset($tmpArr[0]['uploads'])){
					$tmp = array();
					$tmpArr = $data['filters'][0]['uploads'];

					foreach ($tmpArr as $key => $value) {
						$tmp[$value] = true;
					}

					$tmpArr = $tmp;
					$data['filters'][0]['uploads'] = $tmpArr;
				}

				break;

			case 'desiredDegree':

				$degree = new Degree;

				$degree = $degree::all();

				$data['degree'] = $degree;

				$tmp_arr = $data['filters'];

				if(isset($tmp_arr) && !empty($tmp_arr[0])){
					$tmp = array();
					$tmp_arr = $data['filters'][0]['desiredDegree'];

					foreach ($tmp_arr as $key => $value) {
						$tmp[$value] = true;
					}

					$tmp_arr = $tmp;
					$data['filters'][0]['desiredDegree'] = $tmp_arr;
				}

				break;

			case 'demographic':

				//get ethnicity
				if (Cache::has(env('ENVIRONMENT') .'_all_eth')) {
					$eth = Cache::get(env('ENVIRONMENT') .'_all_eth');
				}else{
					$eth = new Ethnicity;
					$eth = $eth->getAllUsEthnicities();
					Cache::put(env('ENVIRONMENT') .'_all_eth', $eth, 120);
				}
				$data['ethnicities'] = $eth;

				//get religion
				if (Cache::has(env('ENVIRONMENT') .'_all_religions')) {
					$religions = Cache::get(env('ENVIRONMENT') .'_all_religions');
				}else{
					$religions = new Religion;
					$religions = $religions->getAllUsReligions();
					Cache::put(env('ENVIRONMENT') .'_all_religions', $religions, 120);
				}
				$data['religions'] = $religions;

				$filters = $data['filters'];
				$arr = array();

				if( !empty($data['filters'][0]) ){
					foreach ($filters as $key) {
						$f = $key['filter'];
						$num = $key[$f];

						if ($key['filter'] == 'include_eth_filter') {
							$arr[$key['filter']] = $num;
							$arr['include_eth_filter_type'] = $key['type'];
						}elseif ($key['filter'] == 'include_rgs_filter') {
							$arr[$key['filter']] = $num;
							$arr['include_rgs_filter_type'] = $key['type'];
						}else{
							$arr[$key['filter']] = $num[0];
						}

					}
				}

				$data['filters'] = $arr;

				break;
			case 'militaryAffiliation':
				$tmpFilters = $data['filters'];
				if (Cache::has(env('ENVIRONMENT') .'_all_militaryAff')) {
					$ma = Cache::get(env('ENVIRONMENT') .'_all_militaryAff');
				}else{
					$ma = new MilitaryAffiliation;
					$ma = $ma->getAll();
					Cache::put(env('ENVIRONMENT') .'_all_militaryAff', $ma, 120);
				}
				$data['militaryAffiliation'] = $ma;
				// $data['filters'][0]['militaryAffiliation'] = $tmpFilters;
				break;
			case 'startDateTerm':
				$dates = array();
				$today = Carbon::today();
				$month = $today->month;
				$yr = $today->year;

				$fall = '';
				$spring = '';
				$dates[''] = 'Select...';
				for ($i = 0; $i < 7; $i++) {
					//if the current month is past fall season already, skip it
					if( $i == 0 ){
						if( $month < 7 ){
							$fall = 'Fall ' . ($yr + $i);
							$dates[$fall] = $fall;
						}
					}else{
						$fall = 'Fall ' . ($yr + $i);
						$dates[$fall] = $fall;
					}

					$spring = 'Spring ' . ($yr + $i);
					$dates[$spring] = $spring;
				}

				$dates = $dates;
				$data['dates'] = $dates;
				break;
			case 'financial':
				$opts = array('0.00','0 - 5,000','5,000 - 10,000','10,000 - 20,000','20,000 - 30,000','30,000 - 50,000', '50,000');
				$fin = array();

				$fin[''] = 'Select...';
				for ($i=0; $i < count($opts); $i++) {
					$tmp = explode('-', $opts[$i]);

					if( count($tmp) > 1 ){
						$fin[$opts[$i]] = '$'.trim($tmp[0]).' - $'.trim($tmp[1]);
					}else{
						if( $tmp[0] == '0.00' ){
							$fin[$opts[$i]] = '$0';
						}elseif( $i == 6 ){
							$fin[$opts[$i]] = '$'.$tmp[0].'+';
						}else{
							$fin[$opts[$i]] = '$'.trim($tmp[0]);
						}
					}
				}

				$data['financial_options'] = $fin;
				break;
			case 'typeofschool':
				break;
			default:
				# code...
				break;
		}

		/*echo "<pre>";
		print_r($data);
		echo "</pre>";*/
		return View($filter_section, $data);
	}


    /*==================================================
	 *========SCHOLARSHIP SECTION BEGINS================
	 *==================================================*/
	public function scholarships(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		//dd($data);

		$data['title'] = 'Plexuss Admin Panel';
		$data['currentPage'] = 'admin';

		$data['admin_title'] = "Add College";
		return View('admin.scholarships', $data);

	}

	/*==================================================
	 *==========SCHOLARSHIP SECTION ENDS================
	 *==================================================*/


}
