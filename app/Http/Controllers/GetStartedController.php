<?php

namespace App\Http\Controllers;

use Request, DB, Session, Queue, Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Crypt;

use App\User, App\Objective, App\Score, App\State, App\Country, App\Highschool, App\College, App\Degree, App\Profession, App\Major, App\GradeConversions;
use App\ZipCodes, App\UsersFinancialFirstyrAffordibilityLog, App\CollegeProgram, App\RevenueOrganization, App\DistributionClient;
use App\AdRedirectCampaign;
use App\Http\Controllers\CollegeRecommendationController;
use App\Http\Controllers\DistributionController, App\Http\Controllers\UtilityController;
use App\Console\Commands\PostNrccuaLead;
use App\Console\Commands\PostInquiriesThroughDistributionClient;
use Jenssegers\Agent\Facades\Agent;
use App\Recruitment;
use App\AdPassthroughs, App\DistributionCustomQuestionUserAnswer;
use App\UsersCustomQuestion, App\NrccuaQueue, App\NrccuaNearbyState, App\UsersAddtlInfo;
ini_set('max_execution_time', 300);

use App\Jobs\PickACollegeProcess;

class GetStartedController extends Controller
{
	
	//index
	public function index($step = null){
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Get Started';
		$data['currentPage'] = 'plex-get-started';
	
		$tmp = array();
		$step_is_complete = false;
		$done = false;

		$user = User::find($data['user_id']);
		$userAI = UsersAddtlInfo::on('rds1')->where('user_id',$data['user_id'])->first();
		$score = DB::connection('rds1')->table('scores')->where('user_id', $data['user_id'])->select('hs_gpa', 'overall_gpa')->get();
		
        $obj = DB::connection('rds1')
                 ->table('objectives as o')
                 ->select('m.name', 'dc.url_slug as category_slug', 'o.major_id', 'o.degree_type')
                 ->join('majors as m', 'm.id', '=', 'major_id')
                 ->join('departments as d', 'd.id', '=', 'm.department_id')
                 ->join('department_categories as dc', 'dc.id', '=', 'd.category_id')
                 ->where('user_id', $data['user_id'])
                 ->first();

		$is_premium = DB::connection('rds1')->table('premium_users')->where('user_id', $data['user_id'])->first();

        if (isset($obj->name) && isset($obj->category_slug)) {
            $data['major_details'] = [
                'name' => $obj->name,
                'category_slug' => $obj->category_slug,
            ];
        }

		$tmp['profile_percent'] = isset($userAI) ? $userAI->get_started_percent : 0;

		for($i = 1; $i <= 6; $i++){
			$str = 'step_'.$i.'_complete';
			$tmp[$str] = false;
		}

		//check if step 1 is complete
		if( isset($user->in_college) && (isset($user->college_grad_year) || isset($user->hs_grad_year)) &&
			($user->is_student == 1 || $user->is_alumni == 1 || $user->is_parent == 1 || $user->is_counselor == 1 || $user->is_university_rep == 1)
			&& (isset($user->email) && !empty($user->email) && $user->email != 'none') ){
			$tmp["step_1_complete"] = true;
			if( $user->is_student == 0 && $user->is_parent == 0 ){
				//using completed_signup to track if user has completed entire get_started process
				$user->completed_signup = 1;
	    		Session::put('userinfo.session_reset', 1);
			}
		}

		//check if step 2 is complete
		// if( (isset($user->planned_start_term) && !empty($user->planned_start_term)) &&
		// 	isset($user->planned_start_yr) && !empty($user->planned_start_yr) ){
        if( isset($score[0]) && ( isset($score[0]->overall_gpa) || isset($score[0]->hs_gpa) ) ){
			$tmp["step_2_complete"] = true;
		}

		if( isset($obj->major_id) && isset($obj->degree_type) ){
			$tmp["step_3_complete"] = true;
		}

		//check if step 4 is complete
		if(isset($user->country_id) && isset($user->address) && 
			isset($user->city) ){
			// if US user, state must be set, else, not US state is not required
			if( $user->country_id == 1 && isset($user->state) ){
				$tmp["step_4_complete"] = true;
			}elseif( $user->country_id != 1 ){
				$tmp["step_4_complete"] = true;
			}
		}

        // step 5 
       	if(isset($user->planned_start_term) && isset($user->planned_start_yr) && isset($user->financial_firstyr_affordibility)) {
                $tmp["step_5_complete"] = true;
       	   }

        // step 6
       if( $user->completed_signup == 1 ){
			$tmp["step_6_complete"] = true;
		}
         
		//check if user has clicked skip or continue to be complete
		if( $user->profile_page_lock_modal == 0 ){
			$tmp["step_7_complete"] = true;
		}

		if( isset($is_premium) ){
			$tmp["step_8_complete"] = true;
		}

		$user->save();

		//if step isn't set, find the last step that hasn't been completed yet
		$tmp = $this->getNextPossibleStep($tmp);


		//if step is null, then calculate the next step user should be on 
		//based on what information they have already filled out
		if( !isset($step) ){
			$data['currentStep'] = $tmp['current_step'];
		}else{
			//if step is set, if user inputs step number larger than the next possible step,
			//then prevent from skipping forward and make current step the next possible step
			//else allow them to go back a step
			if( (int)$step > (int)$tmp['current_step'] ){
				$data['currentStep'] = $tmp['current_step'];
			}else{
				$data['currentStep'] = $step;
			}
		}

		$data['steps_completed'] = json_encode($tmp);
     
		//if completed signup and user has a valid email, redirect to portal
		//else go to next step

        // Don't show scheduler
        if ($data['currentStep'] == 8) {
            $data['currentStep'] = 7;
        }
        
        if($data['currentStep'] == null){
          if(!empty($step))
          {
             $data['currentStep'] = $step;
          }else{
            $data['currentStep'] = 6;
          }
        }
         
        // Force user to go to step 9 if they are going to step 9
        if (isset($step) && $step == 9) {
        	$data['currentStep'] = 9;
		}

		// FOR HIGHER ED, DETERMINE IF WE SHOULD DISPLAY LEADID BCZ THEY REQUIRE IT
		$for_higher_ed = false;
        $user = User::on('rds1')->where('id',$data['user_id'])
									->select(DB::raw("TIMESTAMPDIFF(YEAR, date(birth_date), CURDATE()) as age"), 'country_id')
									->first();

		if (isset($user->country_id) && isset($user->age)) {
			if ($user->country_id == 1 && $user->age >= 24) {
				$for_higher_ed = true;
			}
		}							
		$data['for_higher_ed'] = $for_higher_ed;

		// if TCPA step, check if you should show TCPA or not
		if ($data['currentStep'] == 7) {
			$qry = DB::connection('rds1')
			         ->table('recruitment as r')
			         ->join('colleges as c', 'c.id', '=', 'r.college_id')
			         ->join('distribution_clients as dc', 'dc.college_id', '=', 'c.id')
					->join('distribution_custom_questions as dcqs', 'dcqs.dc_id', '=', 'dc.id')
					->where('r.user_recruit', 1)
					->where('r.user_id', $data['user_id'])
					->first();
			if (!isset($qry)) {
				return redirect('/home');
			}
		}
		$route = 'get_started.step_'.$data['currentStep'];

		return View('get_started.master', $data);
		
	}

	public function getStepStatus() {
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$ret = array();
		$tmp = array();
		$step_is_complete = false;
		$done = false;

		$user = User::find($data['user_id']);
		$score = DB::connection('rds1')->table('scores')->where('user_id', $data['user_id'])->select('hs_gpa', 'overall_gpa')->get();
		
        $obj = DB::connection('rds1')
                 ->table('objectives as o')
                 ->select('m.name', 'dc.url_slug as category_slug', 'o.major_id', 'o.degree_type')
                 ->join('majors as m', 'm.id', '=', 'major_id')
                 ->join('departments as d', 'd.id', '=', 'm.department_id')
                 ->join('department_categories as dc', 'dc.id', '=', 'd.category_id')
                 ->where('user_id', $data['user_id'])
                 ->first();

		$is_premium = DB::connection('rds1')->table('premium_users')->where('user_id', $data['user_id'])->first();

        if (isset($obj->name) && isset($obj->category_slug)) {
            $data['major_details'] = [
                'name' => $obj->name,
                'category_slug' => $obj->category_slug,
            ];
        }

		$tmp['profile_percent'] = $user->profile_percent;

		for($i = 1; $i <= 6; $i++){
			$str = 'step_'.$i.'_complete';
			$tmp[$str] = false;
		}

		//check if step 1 is complete
		if( isset($user->in_college) && (isset($user->college_grad_year) || isset($user->hs_grad_year)) &&
			($user->is_student == 1 || $user->is_alumni == 1 || $user->is_parent == 1 || $user->is_counselor == 1 || $user->is_university_rep == 1)
			&& (isset($user->email) && !empty($user->email) && $user->email != 'none') ){
			$tmp["step_1_complete"] = true;
			if( $user->is_student == 0 && $user->is_parent == 0 ){
				//using completed_signup to track if user has completed entire get_started process
				$user->completed_signup = 1;
	    		Session::put('userinfo.session_reset', 1);
			}
		}

		//check if step 2 is complete
		// if( (isset($user->planned_start_term) && !empty($user->planned_start_term)) &&
		// 	isset($user->planned_start_yr) && !empty($user->planned_start_yr) ){
        if( isset($score[0]) && ( isset($score[0]->overall_gpa) || isset($score[0]->hs_gpa) ) ){
			$tmp["step_2_complete"] = true;
		}

		if( isset($obj->major_id) && isset($obj->degree_type) ){
			$tmp["step_3_complete"] = true;
		}

		//check if step 4 is complete
		if(isset($user->country_id) && isset($user->address) && 
			isset($user->city) ){
			// if US user, state must be set, else, not US state is not required
			if( $user->country_id == 1 && isset($user->state) ){
				$tmp["step_4_complete"] = true;
			}elseif( $user->country_id != 1 ){
				$tmp["step_4_complete"] = true;
			}
		}

        // step 5 
       	if(isset($user->planned_start_term) && isset($user->planned_start_yr) && isset($user->financial_firstyr_affordibility)) {
                $tmp["step_5_complete"] = true;
       	   }

        // step 6
       if( $user->completed_signup == 1 ){
			$tmp["step_6_complete"] = true;
		}
         
		//check if user has clicked skip or continue to be complete
		if( $user->profile_page_lock_modal == 0 ){
			$tmp["step_7_complete"] = true;
		}

		if( isset($is_premium) ){
			$tmp["step_8_complete"] = true;
		}

		$user->save();

		//if step isn't set, find the last step that hasn't been completed yet
		$tmp = $this->getNextPossibleStep($tmp);


		//if step is null, then calculate the next step user should be on 
		//based on what information they have already filled out
		if( !isset($step) ){
			$data['currentStep'] = $tmp['current_step'];
		}else{
			//if step is set, if user inputs step number larger than the next possible step,
			//then prevent from skipping forward and make current step the next possible step
			//else allow them to go back a step
			if( (int)$step > (int)$tmp['current_step'] ){
				$data['currentStep'] = $tmp['current_step'];
			}else{
				$data['currentStep'] = $step;
			}
		}

		$data['steps_completed'] = $tmp;
     
		//if completed signup and user has a valid email, redirect to portal
		//else go to next step

        // Don't show scheduler
        if ($data['currentStep'] == 8) {
            $data['currentStep'] = 7;
        }
        
        if($data['currentStep'] == null){
          if(!empty($step))
          {
             $data['currentStep'] = $step;
          }else{
            $data['currentStep'] = 6;
          }
        }
         
        // Force user to go to step 9 if they are going to step 9
        if (isset($step) && $step == 9) {
        	$data['currentStep'] = 9;
		}
		return response()->json($data);
	}

	public function getusername(){
       $userdetails = array();
       
      if(Auth::user()){
         $userdetails['name'] = Auth::user()->fname ;  
         $userdetails['success'] = 1;
        
       }else{
       $userdetails['success'] = 0;
       }
       return response()->json($userdetails);
	}

    public function nextStepsIndex() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);
        $data['title'] = 'Get Started';
        $data['currentPage'] = 'plex-get-started';
        $data['currentStep'] = 7;
        $data['steps_completed'] = true;

        $input = Request::all();
        if ($data['signed_in'] === 0) {
            if (isset($input['ro_id']) && isset($input['college_id'])) {
                return redirect('/signin?redirect='.urlencode('next-steps?ro_id='.$input['ro_id'].'&college_id='.$input['college_id']));
            }else{
                return redirect('/signin?redirect=next-steps');
            }
        }
        if (isset($input['ro_id']) && isset($input['college_id'])) {
            $dc  = new DistributionController();
            $res = $dc->isEligible($data['user_id'], null, $input['ro_id'], $input['college_id']);
            $res = json_decode($res);
            if ($res->status == 'success') {
                $arr = array();
                $arr['ro_id'] = $input['ro_id'];
                $arr['college_id'] = $input['college_id'];
                $this->saveGetStartedThreeCollegesPins($arr, 'get_started_nextStepsIndex');
            }else{
                return redirect('get_started');
            }
        }
        return View('get_started.step_7', $data);
    }

	//save user info
	public function save($is_api = false, $api_input = null){

		//Build to $data array to pass to view.
		if( $is_api ){
			$data = array();
			$data['user_id'] = $api_input['user_id'];
			$input = $api_input;
		}else{
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData(true);
			$input = Request::all();
		}
        


		$user = User::find($data['user_id']);
		$userAI = UsersAddtlInfo::on('rds1')->where('user_id',$data['user_id'])->first();

		$hasError = false;
		$errorMsg = '';
   
		switch ($input['step']) {
			case '1':
				$user['is_'.$input['user_type']] = 1;

                if (isset($input['gender'])) {
                    $user->gender = $input['gender'];
                }

				if( $input['edu_level'] == 'college' ){
					$user->in_college = 1;
					$user->hs_grad_year = null;
					$user->college_grad_year = $input['grad_yr'];
				}else{
					$user->in_college = 0;
					$user->hs_grad_year = $input['grad_yr'];
					$user->college_grad_year = null;
				}

				if( isset($input['home_schooled']) ){
					$school_id = $input['home_schooled'];
				}else{
					if( isset($input['school_id']) && !empty($input['school_id']) ){
						$school_id = $input['school_id'];
					}else{
						$newSchool = $input['edu_level'] == 'college' ? new College : new Highschool;
						$newSchool->school_name = $input['school'];
						$newSchool->verified = 0;
						$newSchool->user_id_submitted = $user->id;
						$newSchool->save();
						$school_id = $newSchool->id;
					}
				}

				// $user->country_id = isset($input['country']) ? $input['country'] : null;
				$user->current_school_id = $school_id;
				$user->save();
				Session::put('userinfo.session_reset', 1);

				if( $is_api ){
					return $user;
				}
				break;
			case '2':
                $user_scores = Score::firstOrNew(['user_id' => $data['user_id']]);

                if (isset($input['weighted_gpa'])) {
                    $user_scores->weighted_gpa = $input['weighted_gpa'];
                }

                if (isset($input['unweighted_gpa'])) {
                    if (isset($user->in_college) && $user->in_college === 1) {
                        $user_scores->overall_gpa = $input['unweighted_gpa'];
                    } else {
                        $user_scores->hs_gpa = $input['unweighted_gpa'];
                    }
                }

				$user_scores->save();

				break;
			case '3':
				$errorMsg = $this->buildErrorMsg('profession');
				$obj = DB::table('objectives')->where('user_id', $data['user_id'])->get();

				//if there are currently saved objectives, remove them before adding new ones
				if( count($obj) > 0 ){
					foreach ($obj as $key) {
						$removeObjective = Objective::find($key->id);
						$removeObjective->delete();
					}
				}	

				//if user selected majors using the result dropdown, chosen_majors is an array containing major ids
				if (isset($input['chosen_majors'])) { //array of major ids

					for( $i = 0; $i < count($input['chosen_majors']); $i++ ){
						$obj = null;
						$obj = new Objective;
						$obj->user_id = $data['user_id'];
						$obj->degree_type = isset($input['degree']) ? $input['degree'] : null;
						$obj->major_id = isset($input['chosen_majors'][$i]) ? $input['chosen_majors'][$i] : null;
						$obj->university_location = isset($input['chosen_countries']) ? implode(',', $input['chosen_countries']).',' : null;

						//now check to see if profession is good
						$chosen_career = isset($input['chosen_career']) ? $input['chosen_career'] : null;
						$pro_id = $this->validateProfession($chosen_career, $input['career']); //will return false if invalid

						if( $pro_id ){
							$obj->profession_id = $pro_id;
						}else{
                            $obj->profession_id = null;
							// $hasError = true;
							// $errorMsg = $this->buildErrorMsg('profession');
							// break;
						}

						//if for any reason profession or major id == 0, then just throw error
						if( $obj->major_id == 0 ){
							$hasError = true;
							$errorMsg = $this->buildErrorMsg('profession or major');
						}

						if( !$hasError ){
							$vals = array();
							$vals['degree_type'] = $obj->degree_type;
							$vals['profession_id'] = $obj->profession_id;
							$vals['university_location'] = $obj->university_location;
							$saveObjective = Objective::updateOrCreate(['user_id' => $data['user_id'], 'major_id' => $obj->major_id], $vals);
						}
					}
				}else{ //else there are no valid major ids passed - if done correctly, array of major ids should have been posted

					//you get here if user only inputted one value for major and did not select from the results dropdown
					if( isset($input['major']) ){ //if they at least filled something out, check if that input is a major we actually have
						$maj = null;
						$maj = new Major;
						$maj_found = $maj->findMajorByName($input['major']);

						if( $maj_found ){ //if typed search is an actual major we have, save that majors id
							$obj = null;
							$obj = new Objective; //create new objective
							$obj->user_id = $data['user_id'];
							$obj->degree_type = isset($input['degree']) ? $input['degree'] : null; //add degree type
							$obj->major_id = $maj_found->id; //add major id
							$obj->university_location = isset($input['chosen_countries']) ? implode(',', $input['chosen_countries']).',' : null;

							//now check to see if profession is good
							$chosen_career = isset($input['chosen_career']) ? $input['chosen_career'] : null;
							$pro_id = $this->validateProfession($chosen_career, $input['career']); //will return false if invalid
							if( $pro_id ){
								$obj->profession_id = $pro_id;
							}else{
                                $obj->profession_id = null;
								// $hasError = true;
								// $errorMsg = $this->buildErrorMsg('profession');
							}	

							if( !$hasError ){
								//if for any reason profession or major id == 0, then just throw error
								if( $obj->major_id == 0 || $obj->profession_id == 0 ){
									$hasError = true;
									$errorMsg = $this->buildErrorMsg('profession or major');
								}else{
									$vals = array();
									$vals['degree_type'] = $obj->degree_type;
									$vals['profession_id'] = $obj->profession_id;
									$vals['university_location'] = $obj->university_location;
									$saveObjective = Objective::updateOrCreate(['user_id' => $data['user_id'], 'major_id' => $obj->major_id], $vals);
								}
								
							}
							
						}else{ //else return error msg
							$errorMsg = $this->buildErrorMsg('major');
							$hasError = true;
						}
					}else{ //else return error msg
						$errorMsg = $this->buildErrorMsg('major');
						$hasError = true;
					}	
				}

                if (isset($input['school_type'])) {
                    $user->interested_school_type = $input['school_type'];
                }

				if( !$hasError ){
					$user->save();
				}
				// Run the pick a college process
				PickACollegeProcess::dispatch($user->id);
				break;

			/*case '4':
				$user->financial_firstyr_affordibility = $input['financial_contribution'];
				$user->interested_in_aid = isset($input['interestedInFunding']) ? 1 : 0;
				
				$user->save();

				// ADD TO FINANCIAL LOGS FOR THE USER.
				$uffal = new UsersFinancialFirstyrAffordibilityLog;
				$uffal->add($data['user_id'], $input['financial_contribution'], $data['user_id'], 'get_started_save');
				break;
			case '5000000': // Not used at the moment
				// $score = DB::connection('rds1')->table('scores')->where('user_id', $data['user_id'])->select('hs_gpa')->get();
			
				$attr = array();
				$optional = [
					'weighted_gpa',
					'sat_writing',
					'sat_math',
					'sat_reading',
					'sat_total',
					'sat_reading_writing',
					'psat_math',
					'psat_reading',
					'psat_total',
					'psat_reading_writing',
					'act_english',
					'act_math',
					'act_composite',
					'toefl_reading',
					'toefl_listening',
					'toefl_speaking',
					'toefl_writing',
					'toefl_total',
					'toefl_toefl_ibt_reading',
					'toefl_ibt_listening',
					'toefl_ibt_speaking',
					'toefl_ibt_writing',
					'toefl_ibt_total',
					'toefl_pbt_reading',
					'toefl_pbt_listening',
					'toefl_pbt_written',
					'toefl_pbt_total',
					'ged_score',
					'ap_overall',
					'pte_total',
					'ielts_reading',
					'ielts_listening',
					'ielts_speaking',
					'ielts_writing',
					'ielts_total',
					'other_values',
					'other_exam',
					'lsat_total',
					'gmat_total',
					'gre_verbal',
					'gre_quantitative',
					'gre_analytical'
				];

				$is_pre_exam = [
					'is_pre_2016_sat',
					'is_pre_2016_psat'
				];

				//unweighted is mandatory, so add to attr
				if ($user->in_college == 1) {
					$attr['overall_gpa'] = $input['unweighted_gpa'];//overall_gpa is just input name, not changing on front end based on if in hs or not
				}else{
					$attr['hs_gpa'] = $input['unweighted_gpa'];
				}

				//then for the optional one's, add if not emtpy
				for($i = 0; $i < count($optional); $i++){
					if( isset($input[$optional[$i]]) && !empty($input[$optional[$i]]) ){
						$attr[$optional[$i]] = $input[$optional[$i]];
					}
				}

				//update or set pre_exam to 0.
				for($i = 0; $i < count($is_pre_exam); $i++){
					if( isset($input[$is_pre_exam[$i]]) && !empty($input[$is_pre_exam[$i]]) ){
						$attr[$is_pre_exam[$i]] = $input[$is_pre_exam[$i]];
					} else {
						$attr[$is_pre_exam[$i]] = 0;
					}
				}

				//then update or create and save 
				$score = Score::updateOrCreate(['user_id' => $data['user_id']], $attr);	
				$user->save();
				break; */

            case 'birthday': 
                if (isset($input['month']) && isset($input['day']) && isset($input['year'])) {
                    $user->birth_date = $input['year'] . '-' . $input['month'] . '-' . $input['day'];
                    $user->save();
                } else {
                    return 'fail';
                }

                break;

			case '4':
				if( isset($input['country']) ) $user->country_id = $input['country'];
				$user->address = $input['address'];
				$user->city = $input['city'];
				$user->state = isset($input['state']) ? $input['state'] : NULL;
				$user->zip = isset($input['zip']) && !empty($input['zip']) ? $input['zip'] : null; 
				

				if( isset($input['country']) ){
					$country = Country::find($input['country']);
					$country_dc = $country->country_phone_code;
				}
                 $user->save();
				

				break;
			 
			case '5':

	            $user->planned_start_term = $input['term'];
				$user->planned_start_yr = $input['year'];
				$user->financial_firstyr_affordibility = $input['payment'];
				$user->interested_in_aid = isset($input['intrest']) && !empty($input['intrest']) ? $input['intrest'] : null; 
				  
				$user->save();


				Session::put('userinfo.session_reset', 1);

				break;

			case 9:
				if (isset($input['phone']) && isset($input['countryCode'])) {
					$phone = '+'.$input['countryCode']. ' '.$input['phone'];
					$user->phone = $phone;
					$user->save();
				}
				
				if (isset($input['whatsapp'])) {
					$attr = array('user_id' => $data['user_id']);
					$val  = array('user_id' => $data['user_id'], 'whatsapp' => $input['whatsapp']);

					UsersAddtlInfo::updateOrCreate($attr, $val);
				}
			
				break;

			 default:
				return 'fail';
				break;
			
			case 'email':
				if (isset($input['email'])) {
					$user->email =  $input['email'];
					$user->save();
				}
				break;

			case 'leadid':
				if (isset($input['leadid'])) {
					$attr = array('user_id' => $data['user_id']);
					$val  = array('user_id' => $data['user_id'], 'leadid' => $input['leadid']);

					UsersAddtlInfo::updateOrCreate($attr, $val);
				}

				break;
			
			case '7':
				$tmp_input = $input;
				if (isset($input['leadid'])) {
					$attr = array('user_id' => $data['user_id']);
					$val  = array('user_id' => $data['user_id'], 'leadid' => $input['leadid']);

					UsersAddtlInfo::updateOrCreate($attr, $val);
					
					unset($tmp_input['leadid']);
				}
				
				unset($tmp_input['step']);
				
				foreach ($tmp_input as $key => $value) {
					$tmp = explode("_", $key);
					
					if (isset($tmp[0]) && !empty($tmp[0])) {
						$field_name = $tmp[0];
						$college_id = $tmp[1];
						$ro_id 		= $tmp[2];
						
						$attr = array('field_name' => $field_name, 'college_id' => $college_id, 
									  'ro_id' => $ro_id, 'user_id' => $data['user_id']);

						$val  = array('field_name' => $field_name, 'college_id' => $college_id, 
									  'ro_id' => $ro_id, 'user_id' => $data['user_id'], 'value' => $value);

						DistributionCustomQuestionUserAnswer::updateOrCreate($attr, $val);
					}
				}
				
				break;
		}

		if( $hasError ){
			$errObj = array();
			$errObj['error'] = true;
			$errObj['msg'] = $errorMsg;
			return $errObj;
		}else{
			$this->CalcIndicatorPercent($data['user_id']);//calling from BaseController - calculates percentage based on user information
			$this->CalcProfilePercent($data['user_id']);
			$this->CalcOneAppPercent($data['user_id']);
			return isset($userAI) ? $userAI->get_started_percent : $user->profile_percent;
		}
	}

	private function validateMajor( $id, $name ){
		$value = false;

		if( isset($id) ){ //if array contains a majors id, save
			$value = $id; //contains id of major

		}elseif( isset($name) ){ //elseif check if what they typed is a valid major that we have

			$maj = new Major;
			$maj_found = $maj->findMajorByName($name);

			if( $maj_found ){ //if typed search is an actual major we have, save that majors id
				$value = $maj_found->id;
			}

		}

		return $value;
	}

	private function validateProfession( $id, $name ){
		$value = false;

		if( isset($id) ){ //if proper profession is chosen, id is passed so save that.
			$value = $id;

		}elseif( isset($name) ){ //elseif user decided to type own profession, check if what they type matches any professions that we have

			$prof = new Profession;
			$prof_found = $prof->findProfessionByName($name);

			//if found, get and save that profession
			if( $prof_found ){ 
				$value = $prof_found->id;
			}
		}

		return $value;
	}

	private function buildErrorMsg($type = 'profession'){
		
		if($type == 'dialing'){
			return 'We have noticed your country phone numbers differs from your country of origin. Make sure your country of origin is reflected properly on Plexuss.';
		}

		return 'We either don\'t have the '.$type.' you are seeking or you may have misspelled it. Try choosing from the dropdown of results when typing. If you aren\'t sure right now, type "undecided".';
	}

	public function getDataFor($name = null){
		if( !isset($name) ){
			return 'fail';
		}

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$delim = explode('_', $name);
		$carousel = null;

		if( $delim[0] == 'step7' && count($delim) > 1 ){
			$name = $delim[0];
			$carousel = $delim[1];
			$skip = (int)$delim[2];
			$take = (int)$delim[3];
		}

		switch ($name) {
			case 'degree':
				$in_college = User::on('rds1')
							->where('id', $data['user_id'])
							->pluck('in_college');
				$in_college = $in_college[0];
				if(isset($in_college) && $in_college == 0){
					$returnData = Degree::on('rds1')->whereNotIn('id',array(4,5,9))->get();
				}else{
					$returnData = Degree::on('rds1')->get();
				}
				break;
			case 'country':
				$returnData = Country::on('rds1')->get();

				foreach ($returnData as $key => $value) {
					$value->id = (int)$value->id;
					$value->country_phone_code = (int)$value->country_phone_code;
				}

				break;
			case 'states':
				$returnData = DB::table('states')->get();

				foreach ($returnData as $key => $value) {
					$value->id = (int)$value->id;
				}

				break;
			case 'suggest_country':
				$university_location = Objective::on('rds1')
					->where('user_id', $data['user_id'])
					->pluck('university_location');

				$returnData = College::on('rds1')
					->join('countries', 'colleges.country_id', '=', 'countries.id')
					->where('colleges.in_our_network', 1)
					->where('colleges.country_id', '!=', 1);

				if(isset($university_location)){
					$university_location = explode(',', $university_location);
					$returnData = $returnData->whereNotIn('countries.id', $university_location);
				}
				
				$returnData = $returnData->select('countries.*')
										 ->distinct()
										 ->get();

				foreach ($returnData as $key => $value) {
					$value->id = (int)$value->id;
					$value->country_phone_code = (int)$value->country_phone_code;
				}

				break;
			case 'state':
				$returnData = DB::connection('rds1')->table('states')->get();
				break;
			case 'step1':
				$user = User::on('rds1')
							->where('id', $data['user_id'])
							->select('email', 'is_student', 'is_alumni', 'is_parent', 
									'is_counselor', 'is_university_rep', 'zip', 'hs_grad_year', 
									'college_grad_year', 'in_college', 'fname', 'lname', 
									'current_school_id', 'country_id', 'gender')
							->first();

				if( isset($user->in_college) ){

					//if in hs, get hs name
					//else if in college, get college name
					if( $user->in_college == 0 ){
						$hs = new Highschool;
						$tt = $hs->getHsName( $user->current_school_id );
					}else{
						$college = new College;
						$tt = $college->getCollegeName( $user->current_school_id );
					}

					$user->school_name = isset($tt) ? $tt->school_name : ''; //if set, save school_name
				}

				//if country id is set, get the country name
				if( isset($user->country_id) ){
					$ctry = new Country;
					$country = $ctry->getUsersCountryName( $data['user_id'] );
					$user->country_name = isset($country) ? $country->country_name : '';
				}

				//if current school id is the id for home_schooled, then make a prop for home_schooled
				if( isset($user->current_school_id) && $user->current_school_id == 35829 ){
					$user->home_schooled = true;					
				}

				if( isset($user->email) && ($user->email == 'none' || empty($user->email)) ){
					$user->email = null;
				}

				$returnData = isset($user) ? $user : false;
				break;
			case 'step2':
				// $returnData = User::on('rds1')->where('id', $data['user_id'])->select('planned_start_term', 'planned_start_yr', 'fname')->first();

                // $returnData = Score::on('rds1')->where('user_id', $data['user_id'])->leftJoin('users as u', 'u.id', '=', 'user_id')->first();

                $returnData = User::on('rds1')->where('users.id', $data['user_id'])->leftJoin('scores as s', 's.user_id', '=', 'users.id')->first();

				break;
			case 'step3':
				$tmp = array();
				$user = Objective::on('rds1')->where('user_id', $data['user_id'])->select('degree_type', 'major_id', 'profession_id', 'university_location')->get();

				foreach ($user as $key => $value) {
					$value->degree_type = (int)$value->degree_type;
					$value->major_id = (int)$value->major_id;
					$value->profession_id = (int)$value->profession_id;
				}

				$school_type = User::on('rds1')->where('id', $data['user_id'])->select('interested_school_type')->first();
				$profession = null;
				for ($i=0; $i < count($user); $i++) { 
					if( $i == 0 && ( isset($user[$i]['profession_id']) && !empty($user[$i]['profession_id']) ) ){
						$profession = Profession::find($user[$i]['profession_id']);
					}
					$major = isset($user[$i]['major_id']) && !empty($user[$i]['major_id']) ? Major::find($user[$i]['major_id']) : null;
					$user[$i]['major_name'] = isset($major->name) ? $major->name : null;
					$user[$i]['profession_name'] = isset($profession->profession_name) ? $profession->profession_name : null;
					$user[$i]['school_type'] = isset($school_type->interested_school_type) ? (int)$school_type->interested_school_type : null;
					$tmp[] = $user[$i];
				}


				
				$returnData = $tmp;
				break;
			case 'step4':
				$returnData = User::on('rds1')->where('id', $data['user_id'])->select('financial_firstyr_affordibility', 'interested_in_aid')->first();
				break;
			case 'DEPRECATED(USED_TO_BE_STEP_5)':
				$scores = array();
				$score = Score::on('rds1')->where('user_id', $data['user_id'])->get();
				if( isset($score[0]) && !empty($score[0]) ){
					$scores = $score[0];
				}

				$ctry = new Country;
				$country = $ctry->getUsersCountryName($data['user_id']); //return name of country of the user
				if( isset($country->country_name) && !empty($country->country_name) ){
					$scores['country_name'] = $country->country_name;
				}

				$returnData = $scores;
				break;
            case 'step5':
			case 'step6':
				$tmp = array();
				(!isset($data['country_id'])) ? $data['country_id'] = 1 : NULL;
				$country = Country::find($data['country_id']);
				$tmp['country'] = (int)$country->id;
				$user = User::find($data['user_id']);
				
				// WE DONT WANT TO FREAK OUT THE USERS THAT WE HAVE THEIR CITY, STATE AND ZIP
				// SO WHEN THEY SIGN UP , WE DONT PREPOPULATE THOSE DATA. WE DO IT AFTER A DAY
				$today = Carbon::today()->toDateString();
				$user_signed_up_date = Carbon::parse($user->created_at);
				$user_signed_up_date = $user_signed_up_date->toDateString();
				if ($today == $user_signed_up_date) {
					$tmp['address'] = NULL;
					$tmp['city'] = NULL;
					$tmp['state'] = NULL;
					$tmp['zip'] = NULL;
				}else{
					$tmp['address'] = $user->address;
					$tmp['city'] = $user->city;
					$tmp['state'] = $user->state;
					$tmp['zip'] = $user->zip;	
				}
				

				$returnData = $tmp;
				break; // this step has now become 4 step(25sep2018)

            case 'step5new':
             	$tmp = array();
				$user = User::find($data['user_id']);
				$tmp['term'] = $user->planned_start_term;
				$tmp['year'] =  $user->planned_start_yr;
				$tmp['payment'] = $user->financial_firstyr_affordibility;
				$tmp['intrest'] = (!empty($user->interested_school_type))?(($user->interested_school_type == true)?1:0):0;
				$returnData = $tmp;
			
                break ; // this step has now become 5 step(25sep2018)
               
 
			case 'step7':
				
				$user = User::find($data['user_id']);
				$tmp['is_coveted'] = false;

				// if( $user->financial_firstyr_affordibility == "0.00" ){
				// 	$tmp['is_coveted'] = false;
				// }

				// /* coveted user = start year is 2017 AND financial affordability is $20k or more */

				$amt = $user['financial_firstyr_affordibility'];
				$yr = $user['planned_start_yr'];

				//if users financial amt is greater than 20k, then they are coveted
				if( $amt == '10,000 - 20,000' || $amt == '20,000 - 30,000' || $amt == '30,000 - 50,000' || $amt == '50,000' ){
					// also users start year must be 2017
					/* CURRENTLY HARD CODED RIGHT NOW B/C NO ONE HAS TOLD US ABOUT NEXT YEARS PLAN */
					if( $yr == '2017' || $yr == '2018' ){
						$tmp['is_coveted'] = true;
					}
				}

				// FOR NOW, DON'T WANT TO SHOW YOUCANBOOKME FOR ANYONE
				$tmp['is_coveted'] = false;

				$returnData['coveted'] = $tmp['is_coveted'];

				$returnData['carousel'] = $this->getCarouselItems($carousel, $skip, $take, $data['user_id']);
				break;

			case 'step8':
				// $cu = CovetedUser::on('rds1')
				// 				 ->where('user_id', $data['user_id'])
				// 				 ->first();

				// $tmp = array();
				// $tmp['is_coveted'] = false;

				// if (isset($cu) && !empty($cu)) {
				// 	$tmp['is_coveted'] = true;
				// }

				
				break;

			case 'tcpa':
				$qry = DB::connection('rds1')->table('distribution_custom_questions as dcq')
											 ->join('distribution_clients as dc', 'dc.id', '=', 'dcq.dc_id')
											 ->join('colleges as c', 'c.id', 'dc.college_id')
											 ->join('recruitment as r', 'r.college_id', '=', 'c.id')

											 ->where('r.user_id', $data['user_id'])
											 ->where('dcq.is_tcpa', 1)
											 ->select('c.id as college_id', DB::raw('IF(LENGTH(c.logo_url), CONCAT("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/", c.logo_url) ,"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png") as college_logo'),
											 		  'c.school_name', 'dcq.question', 'dcq.field_name', 'dc.ro_id')
											 ->groupBy('c.id', 'dcq.field_name')
											 ->get();

				$ret = array();

				foreach ($qry as $key) {
					if (isset($ret[$key->college_id])) {
						$ret[$key->college_id][] = $key;
					}else{
						$ret[$key->college_id] = array();
						$ret[$key->college_id][] = $key;
					}
				}


				$returnData['data'] = $ret;

				break;

			default:
				# code...
				break;
		}

		return $returnData;
	}

	public function searchFor($name = null){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if( !isset($name) ){
			return 'fail';
		}


		$input = Request::all();

		$delim = explode('_', $name);
		$type = null;

		if( $delim[0] == 'college' && count($delim) > 1 ){
			$name = $delim[0];
			$type = $delim[1];
		}

		switch ($name) {
			case 'career':
				$pro = new Profession;
				$returnData = $pro->findProfession($input['input']);
				break;
			case 'major':
				$major = new Major;
				$returnData = $major->findMajor($input['input'], $data['user_id']);
				break;
			case 'similarMajors':
				$major = new Major;
				$major_row = $major->findMajorByName($input['input']);
				$similar = new SimilarMajor;
				$returnData = $similar->findSimilarMajors($major_row->id);
				break;
			case 'college':
				if( $type == 'hs' ){
					$hs = new Highschool;
					$returnData = $hs->findHighschools($input['input']);
				}else{
					$college = new College;
					$returnData = $college->findColleges($input['input']);
				}
				break;
			default:
				# code...
				break;
		}

		return $returnData;
	}

	//trigger by step6 component to know when user has been to and clicked next on 
	//the get recruited step (current is step 7 - I say that b/c it'll probably change) 
	public function getRecruitedStepDone(){
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
        $input = Request::all();

		$user = User::find($data['user_id']);
		$user->completed_signup = 1;
		$user->profile_page_lock_modal = 0;
		$user->save();

        // Submit NRCCUA schools.
        // if (!empty($input['selected_nrccua_colleges'])) {
        //     foreach ($input['selected_nrccua_colleges'] as $college) {
        //         Queue::push( new PostNrccuaLead($college['id'], $data['user_id']) );
        //     }
        // }

		return 'done';
	}

	//trigger by step7 component to know when user has been to and clicked start on
	//the upgrade member type step (current is step 8 - I say that b/c it'll probably change) 
	public function upgradeMembershipStepDone(){

		$user_id = Session::get('userinfo.id');

		$user = User::find($user_id);
		$user->completed_signup = 1;
		$user->save();

		//send financial email
		if($user->country_id != 1){
			$mac = new MandrillAutomationController();
			$mac->financialEmailForUsers($user);
		}

		return 'done';
	}

	public function getGradeCountries(){
		$gc = new GradeConversions;
		$countries = $gc->getUniqueConversionCountries();
		return $countries;
	}

	public function getGradeConversions(){
		$input = Request::all();	
		$gc = new GradeConversions;
		$grades = $gc->getConversionsFor($input['name']);
		return $grades;
	}

	private function getNextPossibleStep($tmp){
		$step = null;
		$done = false;
    	$user_id = Session::get('userinfo.id');

		for ($i=1; $i <= 8; $i++) { 
			$s = $i;
			$which_step = 'step_'.$s.'_complete';
			if( isset($tmp[$which_step]) && !$tmp[$which_step] ){
				$step = $s;
				$done = false;
				break;
			}else{
				$done = true;
			}
		}

		$tmp['current_step'] = $step;
		$tmp['done'] = $done;

		return $tmp;
	}

	private function getOutOfNetworkMatches($user_id = null){

		if(!isset($user_id)){
			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
			$user_id = $data['user_id'];
		}

		$matches = CollegeProgram::on('rds1')
			->join('objectives as o', function($join){
				$join->on('o.major_id','=','college_programs.major_id')
					 ->on('o.degree_type','=','college_programs.degree_type');
			})
			->where('o.user_id','=',$user_id)
			->groupBy('college_programs.college_id')
			->pluck('college_programs.college_id');

		$depts = DB::table('objectives as o')
			->join('majors','majors.id','=','o.major_id')
			->join('college_programs',function($join){
				$join->on('majors.department_id','=','college_programs.department_id')
					 ->on('o.degree_type','=','college_programs.degree_type');
			})
			->where('o.user_id','=',$user_id)
			->groupBy('college_programs.college_id')
			->pluck('college_programs.college_id');

		$arr = array($matches,$depts);

		return $arr;
	}

	private function getSchoolMatches($user_id){
		
		if (Cache::has(env('ENVIRONMENT').'_'.'school_matches_'.$user_id)){
			$arr = Cache::get(env('ENVIRONMENT').'_'.'school_matches_'.$user_id);
		}
		else{
			$branches = DB::table('organization_branches')
			 	->select('school_id','id as org_branch_id',DB::raw('null as org_portal_id'),DB::raw('null as aor_id'))
			 	->whereIn('aor_only',array(0,2));
				
			$portals = DB::table('organization_branches')
			 	->join('organization_portals as op','organization_branches.id','=','op.org_branch_id')
			 	->select('school_id','org_branch_id','op.id as org_portal_id',DB::raw('null as aor_id'));

		 	$aor = DB::table('organization_branches as ob')
		 		->join('aor_colleges as ac','ob.school_id','=','ac.college_id')
			 	->select('school_id','ob.id as org_branch_id',DB::raw('null as org_portal_id'),'ac.aor_id');

			$members = $branches->union($portals)->union($aor)->get();

			$matches = array();
			$depts = array();

			foreach($members as $key){

				if(in_array($key->school_id,$matches)){
					continue;
				}

				$needs_update = False;

				$qry = null;
				$dpt = null;
				unset($fltr_data);

				$x = (is_null($key->org_portal_id)) ? 0 : $key->org_portal_id;
				$y = (is_null($key->aor_id)) ? 0 : $key->aor_id;

				if (Cache::has(env('ENVIRONMENT').'_'.$key->org_branch_id.'_'.$x.'_'.$y)){

					$itm = Cache::get(env('ENVIRONMENT').'_'.$key->org_branch_id.'_'.$x.'_'.$y);
					$qry = $itm[1];
					$dpt = $itm[2];
					
					$updated = CollegeRecommendationFilters::select(DB::raw('max(updated_at) as time'))
						->where('college_id','=',$key->school_id);

					if(is_null($key->aor_id)){
						$updated = $updated->whereNull('aor_id');
					}
					else{
						$updated = $updated->where('aor_id','=',$key->aor_id);
					}
					if(is_null($key->org_portal_id)){
						$updated = $updated->whereNull('org_portal_id')
							->first();
					}
					else{
						$updated = $updated->where('org_portal_id','=',$key->org_portal_id)
							->first();
					}

					if(isset($updated->time) && (is_null($itm[0]) || $updated->time > $itm[0])){
						$needs_update = True;
					}
				}

				if (!Cache::has(env('ENVIRONMENT').'_'.$key->org_branch_id.'_'.$x.'_'.$y) || $needs_update){

					$fltr_data['org_branch_id'] = $key->org_branch_id;
					$fltr_data['org_school_id'] = $key->school_id;
					$fltr_data['aor_id'] = $key->aor_id;
					$fltr_data['default_organization_portal'] = (object) array();
					$fltr_data['default_organization_portal']->id = $key->org_portal_id;

					$crf = new CollegeRecommendationFilters;

					$qry = $crf->generateFilterQry($fltr_data);

					if(!empty($qry)){

						$qry = $this->getRawSqlWithBindings($qry);
						$fltr_data['department_query'] = True;
						$dpt = $crf->generateFilterQry($fltr_data);
						$dpt = $this->getRawSqlWithBindings($dpt);

						Cache::forever(env('ENVIRONMENT').'_'.$key->org_branch_id.'_'.$x.'_'.$y, array(Carbon::now()->toDateTimeString(),$qry,$dpt));
					}

					else{
						Cache::forever(env('ENVIRONMENT').'_'.$key->org_branch_id.'_'.$x.'_'.$y, array(null,null,null));
					}
				}

				if (!empty($qry)) {
					$qry = $qry.' and userFilter.id = '.$user_id;

					$qry = DB::select($qry);

					if (!empty($qry)){
						$matches[] = $key->school_id;
					}
					else{

						if(in_array($key->school_id,$depts)){
							continue;
						}

						$dpt = $dpt.' and userFilter.id = '.$user_id;

						$dpt = DB::select($dpt);

						if (!empty($dpt)){
							$depts[] = $key->school_id;
						}
					}
				}
			}

			$matches = array_values(array_unique($matches));
			$depts = array_values(array_unique($depts));
			$arr = array($matches,$depts);

			Cache::put(env('ENVIRONMENT').'_'.'school_matches_'.$user_id,$arr,60);
		}

		return $arr;
	}

	public function getSplit($user_id, $name){

		$st = SplitTest::firstOrNew([
			'user_id'  => $user_id,
			'name' => $name]);
		if(!isset($st->split)){
			$split = rand(0,1);
			$st->split = $split;
			$st->save();
		}
		return $st->split;
	}

	public function getCarouselItems($carousel, $skip, $take, $user_id){
		
		$result = null;

		$arr = null;

		$in_matches = null;

		$in_depts = null;

		$out_matches = null;

		$out_depts = null;
		
		// $in_network = $this->getSchoolMatches($user_id);
		// $in_matches = $in_network[0];
		// $in_depts = $in_network[1];

		// $out_of_network = $this->getOutOfNetworkMatches($user_id);
		// $out_matches = $out_of_network[0];
		// $out_depts = $out_of_network[1];

		$crc = new CollegeRecommendationController;
        
        if ($carousel !== 'nrccuaSchools' && $carousel !== 'educationDynamics') {
		  $in_matches = $crc->findCollegesForThisUserOnGetStarted($user_id);
        }

		$data = array();
		$data['ip'] = Session::get('userinfo.ip');
		$data['eddy_found'] = 'false';
		$users_ip_info = $this->iplookup($data['ip']);
		if($users_ip_info['countryAbbr'] == 'US' ){
			$data['eddy_found'] = 'true';
		}
		switch ($carousel) {
			case 'firstBox':
				

				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id', 'c.school_name', 'c.logo_url', 'cr.plexuss', 'c.city', 'c.state',
							 'coi.url as img_url')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('recruitment as r', function($join) use($user_id){
							$join->on('r.college_id', '=', 'c.id')
								 ->where('r.user_id', '=', $user_id);
					})
					->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						    $join->on('coi.is_video', '=', DB::raw(0));
						    $join->on('coi.is_tour', '=', DB::raw(0));
						})
					->where(function($qry){
							$qry->whereNotBetWeen('cr.plexuss', array(101, 7000));
					})
					->where('c.verified','=',1)
					->whereNotNull('c.logo_url')
					->whereNull('r.id')
					->orderby('priority.promote', 'DESC')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}

				$result = $result->orderBy('c.in_our_network', 'DESC')
					->orderBy('ob.bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;
			case 'secondBox':
				
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id', 'c.school_name', 'c.logo_url', 'cr.plexuss', 'c.city', 'c.state',
							 'coi.url as img_url',
							 DB::raw("'".$data['eddy_found']."' as eddy_found"))
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('recruitment as r', function($join) use($user_id){
							$join->on('r.college_id', '=', 'c.id')
								 ->where('r.user_id', '=', $user_id);
					})
					->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						    $join->on('coi.is_video', '=', DB::raw(0));
						    $join->on('coi.is_tour', '=', DB::raw(0));
						})
					->where(function($qry){
							$qry->whereNotBetWeen('cr.plexuss', array(1, 100))
								->whereNotBetWeen('cr.plexuss', array(501, 7000));
					})
					->where('c.verified','=',1)
					->whereNotNull('c.logo_url')
					->whereNull('r.id')
					->orderby('priority.promote', 'DESC')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}

				$result = $result->orderBy('c.in_our_network', 'DESC')
					->orderBy('ob.bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;
			case 'thirdBox':

				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id', 'c.school_name', 'c.logo_url', 'cr.plexuss', 'c.city', 'c.state',
							 'coi.url as img_url')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('recruitment as r', function($join) use($user_id){
							$join->on('r.college_id', '=', 'c.id')
								 ->where('r.user_id', '=', $user_id);
					})
					->leftjoin('college_overview_images as coi', function($join)
						{
						    $join->on('c.id', '=', 'coi.college_id');
						    $join->on('coi.url', '!=', DB::raw('""'));
						    $join->on('coi.is_video', '=', DB::raw(0));
						    $join->on('coi.is_tour', '=', DB::raw(0));
						})
					->where(function($qry){
							$qry->whereNotBetWeen('cr.plexuss', array(1, 500))
								->orWhereNull('cr.plexuss');
					})
					->where('c.verified','=',1)
					->whereNotNull('c.logo_url')
					->whereNull('r.id')
					->orderby('priority.promote', 'DESC')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}

				$result = $result->orderBy('c.in_our_network', 'DESC')
					->orderBy('ob.bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;

            case 'nrccuaSchools':
                $result = DB::connection('rds1')->table('nrccua_colleges as nc')
                                                ->leftJoin('colleges as c', 'c.id', '=', 'nc.college_id')
                                                ->leftJoin('users as u', 'u.id', '=', DB::raw($user_id))
                                                ->leftjoin('college_overview_images as coi', function($join)
                                                {
                                                    $join->on('c.id', '=', 'coi.college_id');
                                                    $join->on('coi.url', '!=', DB::raw('""'));
                                                    $join->on('coi.is_video', '=', DB::raw(0));
                                                    $join->on('coi.is_tour', '=', DB::raw(0));
                                                })
                                                ->select('c.city', 'c.id', 'c.logo_url', 'coi.url as img_url', 'nc.rank as plexuss', 'c.school_name', 'nc.state')
                                                ->orderByRaw('u.state = nc.state DESC, if (nc.rank is null, 99999, nc.rank) ASC')
                                                ->groupBy('c.id')
                                                ->get();
                break;

            case 'educationDynamics':
                // Currently only two schools are available for this.
                // Grand Canyon Univeristy (id: 105) && California InterContinental University (id: 6698)

                // These schools currently require: grad year at or before 2017, ok w/ online education, and US only.
                $meets_requirements = DB::connection('rds1')->table('users as u')
                                        ->where('u.id', '=', $user_id)
                                        ->where('u.country_id', '=', 1)
                                        ->where('u.hs_grad_year', '<=', 2017)
                                        ->whereIn('u.interested_school_type', [1, 2])
                                        ->first();

                if (!isset($meets_requirements)) {
                    break;
                }            

                $result = DB::connection('rds1')->table('colleges as c')
                                ->leftjoin('college_overview_images as coi', function($join)
                                {
                                    $join->on('c.id', '=', 'coi.college_id');
                                    $join->on('coi.url', '!=', DB::raw('""'));
                                    $join->on('coi.is_video', '=', DB::raw(0));
                                    $join->on('coi.is_tour', '=', DB::raw(0));
                                })
                                ->select('c.city', 'c.state', 'c.id', 'c.logo_url', 'coi.url as img_url', 'c.school_name')
                                ->groupBy('c.id')
                                ->whereIn('c.id', [105])
                                // ->whereIn('c.id', [105, 6698])
                                ->get();

                foreach ($result as $college) {
                    switch ($college->id) {
                        case 105:
                            $college->ad_redirect_url = 'https://plexuss.com/adRedirect?company=eddy&utm_source=plexuss_getstarted_pos3_lrnmre&cid=7';

                            $college->logo_url = 'eddy_ad_gcu.png';

                            break;

                        case 6698:
                            $college->ad_redirect_url = 'https://plexuss.com/adRedirect?company=eddy&utm_source=plexuss_getstarted_pos3_lrnmre&cid=8';
                            
                            $college->logo_url = 'eddy_ad_ciu.png';

                            break;

                        default:
                    }
                }

                break;

			case 'national':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id', 'c.school_name', 'c.logo_url', 'cr.plexuss')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('lists',function($join){
						$join->on('lists.custom_college', '=', 'c.id')
							->where('title', 'LIKE', '%liberal%');
					})
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->where('c.verified','=',1)
					->whereNull('custom_college')
					->where(function($query){
						$query = $query->orWhere('c.school_sector', '=', 'Public, 4-year or above')
									   ->orWhere('c.school_sector', '=', 'Private not-for-profit, 4-year or above')
									   ->orWhere('c.school_sector', '=', 'Private for-profit, 4-year or above');
					})
					->whereNotNull('c.logo_url')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;
			case 'liberal':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->join('lists as l','l.custom_college','=','c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->where('title','LIKE','%liberal%')
					->where('verified','=',1)
					->whereNotNull('c.logo_url')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;
			case 'community':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->where('school_sector', '=', 'Public, 2-year')
					->where('verified','=',1)
					->whereNotNull('c.logo_url')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;
			case 'specialtyARTS':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('lists as l', function($join){
						$join->on('l.custom_college','=','c.id')
					    	->where('title','=','PayScale Best Schools For Art Majors by Salary Potential');
					})
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->where('c.school_name','NOT LIKE', '%medical%')
					->where('c.school_name','NOT LIKE','%culinary%')
					->where('verified','=',1)
					->where(function($query){
		                return $query->where('c.school_name', 'LIKE', '% art%')
		                    ->orWhere('c.school_name', 'LIKE', '%design%');
				    })
					->whereNotNull('c.logo_url')
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
				    ->orderBy(DB::raw('ISNULL(rank_num), rank_num'), 'ASC')
					->skip($skip)
					->take($take)
					->get();
					
				break;
			case 'specialtyMUSIC':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->where(function($query) {
		                return $query->where('c.school_name','LIKE','%music%')
		                    ->orWhere('c.school_name', 'LIKE', '%conservatory%')
		                    ->orWhere('c.school_name','=','The Juilliard School');
		            })
					->whereNotNull('c.logo_url')
					->where('verified','=',1)
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();
					
				break;

			case 'international':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->where('c.country_id', '!=', 1)
					->whereNotNull('c.logo_url')
					->where('verified','=',1)
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();
					
				break;
			case 'specialtyENGINEERING':
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->where(function($query){
		                return $query->where('c.school_name', 'LIKE', '%engineering%')
	                    	->orWhere('c.school_name', 'LIKE', '%technolog%');
				    })
					->where(function($query){
			            return $query->where('c.school_name','NOT LIKE','%design%');
				    })
					->whereNotNull('c.logo_url')
					->where('verified','=',1)
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
					->orderBy(DB::raw('ISNULL(cr.plexuss), cr.plexuss'), 'ASC')
					->skip($skip)
					->take($take)
					->get();

				break;
			default:
				$result = DB::connection('rds1')->table('colleges as c')
					->select('c.id','c.school_name','c.logo_url')
					->leftjoin('lists as l', function($join){
					    $join->on('l.custom_college','=','c.id')
					    	->where('title','=','PayScale Best Schools For Art Majors by Salary Potential');
					})
					->leftjoin('priority','priority.college_id','=','c.id')
					->leftjoin('organization_branches as ob','ob.school_id','=','c.id')
					->where('c.school_name','NOT LIKE', '%medical%')
					->where('c.school_name','NOT LIKE','%culinary%')
					->where('verified','=',1)
					->whereNotNull('c.logo_url')
					->where(function($query){
		                return $query->where('c.school_name', 'LIKE', '% art%')
		                    ->orWhere('c.school_name', 'LIKE', '%design%');
				    })
					->groupBy('c.id');

				if(!empty($in_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_matches).') DESC'));
				}
				if(!empty($in_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $in_depts).') DESC'));
				}
				if(!empty($out_matches)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_matches).') DESC'));
				}
				if(!empty($out_depts)){
					$result = $result->orderByRaw(DB::raw('c.id IN ('.implode(",", $out_depts).') DESC'));
				}

				$result = $result->orderBy('in_our_network', 'DESC')
					->orderBy('bachelor_plan','DESC')
					->orderBy(DB::raw('ISNULL(priority.id)'))
					->orderby('priority.promote', 'DESC')
					->orderBy('priority.contract','DESC')
					->orderBy('priority.type','ASC')
				    ->orderBy(DB::raw('ISNULL(rank_num), rank_num'), 'ASC')
					->skip($skip)
					->take($take)
					->get();
		}

		return $result;
	}

	public function getGetStartedThreeCollegesPins($user_id = NULL, $queue_job = null){

        if (!$user_id) {
    		$viewDataController = new ViewDataController();
    		$data = $viewDataController->buildData(true);
        } else {
            $data = [];
            $data['user_id'] = $user_id;
        }

        if (!isset($queue_job)) {

        	if (Cache::has(env('ENVIRONMENT') .'_getGetStartedThreeCollegesPins_'. $data['user_id'])) {
        		return Cache::get(env('ENVIRONMENT') .'_getGetStartedThreeCollegesPins_'. $data['user_id']);
        	}
        }

		Session::forget('get_started_colleges_already_seen');

		$user = User::find($data['user_id']);
		// $user->country_id = 2;

		$for_higher_ed  = false;

		if (isset($data['user_id'])) {
			$tmp_user = User::on('rds1')->where('id', $data['user_id'])
									->select(DB::raw("TIMESTAMPDIFF(YEAR, date(birth_date), CURDATE()) as age"), 'country_id')
									->first();
			if (isset($tmp_user->country_id) && isset($tmp_user->age)) {
				if ($tmp_user->country_id == 1 && $tmp_user->age >= 24) {
					$for_higher_ed = true;
				}
			}
		}

		// If country_id is not set then set it.
		if (!isset($user->country_id)) {
			$ip = $this->iplookup();
			if (isset($ip['countryName'])) {
	            $countries = Country::on('rds1')->where('country_name', $ip['countryName'])->first();
	            if (isset($countries)) {
	                $user->country_id = $countries->id;
	                $user->save();
	            }
	        }	
		}

		if ($for_higher_ed) {
			$ro = RevenueOrganization::on('rds1')
								 ->where('id', 40)
								 ->get();
		}elseif ($user->country_id == 1) {
			$ro = RevenueOrganization::on('rds1')
								 ->where('active',  1)
		                         ->orderby('priority')
		                         ->where('id', '!=', 31)
		                         ->where('id', '!=', 40)
		                         ->get();
		}else{
			$ro = RevenueOrganization::on('rds1')
		                         ->orderby('priority')
		                         ->where('intl_has_filter', 1)
		                         ->get();
		}

		

		$ro_model = new RevenueOrganization;

		$ret = $this->innerMethodForgetGetStartedThreeCollegesPins($ro, $ro_model, $user, $data);
		$ret_arr = $ret['ret_arr'];
		$arr     = $ret['arr'];

		// THIS PORTION OF THE CODE WE ARE TRYING TO PRIORTIZING CAPPEX AND NRCCUA SCHOOLS FIRST
		// SO WE SHOW THEM ONE IN BETWEEN FOR EACH TAB AND THEN WE GO TO THE REST OF SCHOOLS.
		if ($user->country_id == 1) {
			$ro_arr = array();
			foreach ($ret_arr as $key) {
				if (isset($ro_arr[$key->ro_id])) {
					$ro_arr[$key->ro_id][] =  $key;
				}else{
					$ro_arr[$key->ro_id] = array();
					$ro_arr[$key->ro_id][] =  $key;
				}
			}

			$temp_arr = array();
			$cnt = 0;
			// FIRST ONE IN BETWEEN FOR NRCCUA AND CAPPEX
			$cappex_nrccua_arr = array();
			if (isset($ro_arr[1])) {
				$cappex_nrccua_arr[1] = $ro_arr[1];
				unset($ro_arr[1]);
			}
			if (isset($ro_arr[2])) {
				$cappex_nrccua_arr[2] = $ro_arr[2];
				unset($ro_arr[2]);
			}
			while (count($cappex_nrccua_arr) > 0) {
				foreach ($cappex_nrccua_arr as $key  => $value) {
					if (isset($value[0]) && !empty($value[0])) {
						
						$temp_arr[] =$value[0];
						unset($value[0]);
						
						$value = array_values($value);
						$cappex_nrccua_arr[$key] = $value;
						
					}else{
						unset($cappex_nrccua_arr[$key]);
					}					
				}
			}
			// END PARSING CAPPEX AND NRCCUA

			// DO THE REST OF REVENUE ORG
			while (count($ro_arr) > 0) {
				foreach ($ro_arr as $key  => $value) {
					if (isset($value[0]) && !empty($value[0])) {
						
						$temp_arr[] =$value[0];
						unset($value[0]);
						
						$value = array_values($value);
						$ro_arr[$key] = $value;
						
					}else{
						unset($ro_arr[$key]);
					}					
				}
			}
			// END OF REST OF REVENUE ORG
			$ret_arr = $temp_arr;
			// dd($temp_arr);			
		}

		$cnt = 0; 
		$tmp = array();
		foreach ($ret_arr as $key) {
			if ($cnt > 2) {
				$cnt = 0;
			}
			$tmp[$cnt][] = $key;
			$cnt++;
		}

		if (isset($tmp[0])) {
			$arr['tab1'] = $tmp[0];
		}
		if (isset($tmp[1])) {
			$arr['tab2'] = $tmp[1];
		}
		if (isset($tmp[2])) {
			$arr['tab3'] = $tmp[2];
		}

		// $this->customdd($ret_arr);
		// exit();
		// $tmp = array();
		// $tmp = array_chunk($ret_arr,(int)ceil(count($ret_arr)/3));

		// if (isset($tmp[0])) {
		// 	$arr['tab1'] = $tmp[0];
		// }
		// if (isset($tmp[1])) {
		// 	$arr['tab2'] = $tmp[1];
		// }
		// if (isset($tmp[2])) {
		// 	$arr['tab3'] = array_reverse($tmp[2]);
		// }

		if (!isset($queue_job)) {
	        Cache::put(env('ENVIRONMENT') .'_getGetStartedThreeCollegesPins_'. $data['user_id'], $arr, 240);
		}

		return $arr;
	}

	private function innerMethodForgetGetStartedThreeCollegesPins($ro, $ro_model, $user, $data){
		
		$arr = array();
		$arr['tab1'] = array();
		$arr['tab2'] = array();
		$arr['tab3'] = array();
		$arr['caps'] = array();

		if ($user->country_id == 1) {
			foreach ($ro as $key) {

				
				$cap = $ro_model->getRevenueOrganizationCap($key, $data['user_id']);

				$filter = NULL;
				if (isset($key->has_filter) && $key->has_filter == 1) {
					$crc = new CollegeRecommendationController();
					$filter = $crc->findCollegesForThisUserOnGetStarted($data['user_id'], $key->aor_id, $cap);
				}


				// $cap = $ro_model->getRevenueOrganizationCap($key, $data['user_id']);
				
				if ($cap <= 0 && $key->type != "post") {
					continue;
				}
				$tmp = array();
	            $tmp['ro_id'] = $key->id;
				$tmp['cap']   = $cap;
				$arr['caps'][] = $tmp;

				switch ($key->type) {
					case 'post':

						//NRCCUA only
						if ($key->id == 1) {
							$tmp_qry =  NrccuaNearbyState::on('rds1')
															   ->select('college_state')
															   ->where('user_state', $user->state)
															   ->get();

							$nearby_states_str = NULL;
							$nearby_states = array();
							$cnt = 0;
							foreach ($tmp_qry as $k) {
								$nearby_states[] = $k->college_state;

								$nearby_states_str .= "'".$k->college_state;
								if ((count($tmp_qry) - 1) != $cnt) {
									$nearby_states_str .= "', ";
								}else{
									$nearby_states_str .= "'";
								}
								$cnt++;
							}
						}

						$result = DB::connection('rds1')->table('colleges as c')
									->join('distribution_clients as dc', function($q) use ($key){
											$q->on('dc.college_id', '=', 'c.id')
											  ->on('dc.ro_id', '=', DB::raw($key->id))
											  ->on('dc.active', '=', DB::raw(1));
											  // ->on('dc.active', '=', DB::raw(1));
											  // CHANGE THIS FOR LIVE RELEASEEEEEE //////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
											  ///////////////////////////////////////////////////////
									})
									->leftjoin('distribution_custom_questions as dcqs', 'dcqs.dc_id', '=', 'dc.id')
									->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									->leftjoin('college_overview_images as coi', function($join)
										{
										    $join->on('c.id', '=', 'coi.college_id');
										    $join->on('coi.url', '!=', DB::raw('""'));
										    $join->on('coi.is_video', '=', DB::raw(0));
										    $join->on('coi.is_tour', '=', DB::raw(0));
										})
									// ->where('c.verified','=',1)
									  // MIGHT WANNA LOOK INTO THE ABOVE MAKE SURE YOU ARE NOT SHOWING THE
									  // COLLEGES YOU SHOULD NOT BE SHOWING.
									  ///////////////////////////////////////////////////////
									  ///////////////////////////////////////////////////////
									  ///////////////////////////////////////////////////////
									  ///////////////////////////////////////////////////////
									  ///////////////////////////////////////////////////////
									  ///////////////////////////////////////////////////////
									->whereNotNull('c.logo_url');
						
						if (Session::has('get_started_colleges_already_seen')) {
							$tmp_colleges = Session::get('get_started_colleges_already_seen');

							$tmp_arr = explode(",", $tmp_colleges);
							$result = $result->whereNotIn('c.id', $tmp_arr);
						}

						$tmp = clone $result;
						if (isset($nearby_states)) {
							$tmp = $tmp->whereIn('c.state', $nearby_states)->count();
							if ($tmp > 0 && !empty($nearby_states_str)) {
								$result = $result->orderByRaw("Case
																when `c`.`state` in(".$nearby_states_str.") then
																	0
																when `cr`.`plexuss` <= 150 then
																	1
																else
																	2
																end asc ,
																 coalesce(`cr`.`plexuss` , 999) asc");
							}else{
								$result = $result->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'));
							}
						}else{
							$tmp = $tmp->where('c.state', $user->state)->count();
							if ($tmp > 0 && !empty($nearby_states_str)) {
								$result = $result->orderByRaw("Case
																when `c`.`state` in(".$nearby_states_str.") then
																	0
																when `cr`.`plexuss` <= 150 then
																	1
																else
																	2
																end asc ,
																 coalesce(`cr`.`plexuss` , 999) asc");
							}else{
								$result = $result->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'));
							}

						}
						
						$college_ids = clone $result;
						$college_ids = $college_ids->select(DB::raw('GROUP_CONCAT(DISTINCT c.id SEPARATOR ",") as college_id'))
												   ->first();
						
						if (isset($college_ids->college_id) && !empty($college_ids->college_id)) {
							if (isset($tmp_colleges)) {
								Session::put('get_started_colleges_already_seen', $tmp_colleges.",".$college_ids->college_id);
							}else{
								Session::put('get_started_colleges_already_seen', $college_ids->college_id);
							}
						}

						$result = $result->groupBy('c.id')
										 ->select('c.id as college_id', 'c.school_name', 'c.logo_url', 
										 		  'cr.plexuss', 'c.city', 'c.state',  'coi.url as img_url', 
										 		  DB::raw("'".$key->id."' as `ro_id`"), 
										 		  DB::raw("'".$key->type."' as `type`"), 
										 		  DB::raw("'".$cap."' as `cap`"),
										 		  DB::raw("CASE WHEN dcqs.id IS NOT NULL 
																       THEN 1
																       ELSE 0
																END AS has_custom_qs") );
						
						$result = $result->get();
						
						if (!empty($result)) {
							if (isset($ret_arr)) {
								$ret_arr = $ret_arr->merge($result);
							}else{
								$ret_arr = $result;
							}
						}

						if (isset($filter)) {
							
							// $this->customdd("=============== RET_ARR BEFORE ===============<br>");
							// $this->customdd($ret_arr);
							// $this->customdd("=============== RET_ARR END ===============<br>");

							$tmp_colleges = array();
							foreach ($ret_arr as $key) {
								$tmp_colleges[] = $key->college_id;
							}

							// $this->customdd("=============== tmp_colleges BEFORE ===============<br>");
							// $this->customdd($tmp_colleges);
							// $this->customdd("=============== tmp_colleges END ===============<br>");

							// $this->customdd("=============== filter BEFORE ===============<br>");
							// $this->customdd($filter);
							// $this->customdd("=============== filter END ===============<br>");

							foreach ($tmp_colleges as $key => $value) {
								if (!in_array($value, $filter)) {
									unset($ret_arr[$key]);
								}
							}

							// $ret_arr = array_values($ret_arr);

							// $this->customdd("=============== After ret_arr BEFORE ===============<br>");
							// $this->customdd($ret_arr);
							// $this->customdd("=============== After ret_arr END ===============<br>");
							// exit();
						}
						break;
					case 'linkout':
						
						$result = DB::connection('rds1')->table('colleges as c')
									->join('distribution_clients as dc', function($q) use ($key){
											$q->on('dc.college_id', '=', 'c.id')
											  ->on('dc.ro_id', '=', DB::raw($key->id))
											  ->on('dc.active', '=', DB::raw(1));
									})
									->leftjoin('distribution_custom_questions as dcqs', 'dcqs.dc_id', '=', 'dc.id')
									->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									->leftjoin('college_overview_images as coi', function($join)
										{
										    $join->on('c.id', '=', 'coi.college_id');
										    $join->on('coi.url', '!=', DB::raw('""'));
										    $join->on('coi.is_video', '=', DB::raw(0));
										    $join->on('coi.is_tour', '=', DB::raw(0));
										})
									->where('c.verified','=',1)
									->whereNotNull('c.logo_url')
									->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'));

						// if ($user->country_id == 1 && isset($user->state) ) {
							if (Session::has('get_started_colleges_already_seen')) {
								$tmp_colleges = Session::get('get_started_colleges_already_seen');

								$tmp_arr = explode(",", $tmp_colleges);
								$result = $result->whereNotIn('c.id', $tmp_arr);
							}

							$tmp = clone $result;
							$tmp = $tmp->where('c.state', $user->state)->count();
							if ($tmp > 0) {
								$result = $result->where('c.state', $user->state);
							}

							$college_ids = clone $result;
							$college_ids = $college_ids->select(DB::raw('GROUP_CONCAT(DISTINCT c.id SEPARATOR ",") as college_id'))->first();
							
							if (isset($college_ids->college_id) && !empty($college_ids->college_id)) {
								if (isset($tmp_colleges)) {
									Session::put('get_started_colleges_already_seen', $tmp_colleges.",".$college_ids->college_id);
								}else{
									Session::put('get_started_colleges_already_seen', $college_ids->college_id);
								}
								
							}
	 					// }

						$result = $result->groupBy('c.id')
										 ->select('c.id as college_id', 'c.school_name', 'c.logo_url', 
										 		 'cr.plexuss', 'c.city', 'c.state', 'coi.url as img_url', 
										 		 DB::raw("'".$key->id."' as `ro_id`"), 
										 		 DB::raw("'".$key->type."' as `type`"), 
										 		 DB::raw("'".$cap."' as `cap`"),
										 		 DB::raw("CASE WHEN dcqs.id IS NOT NULL 
																       THEN 1
																       ELSE 0
																END AS has_custom_qs") )
										 ->get();
						
						if (!empty($result)) {
							if (isset($ret_arr)) {
								$ret_arr = $ret_arr->merge($result);
							}else{
								$ret_arr = $result;
							}
						}	

						break;
					case 'click':

						$result = DB::connection('rds1')->table('colleges as c')
									->join('ad_redirect_campaigns as arc', function($q) use ($key){
											$q->on('arc.college_id', '=', 'c.id')
											  ->on('arc.ro_id', '=', DB::raw($key->id));
									})
									->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
									->leftjoin('college_overview_images as coi', function($join)
										{
										    $join->on('c.id', '=', 'coi.college_id');
										    $join->on('coi.url', '!=', DB::raw('""'));
										    $join->on('coi.is_video', '=', DB::raw(0));
										    $join->on('coi.is_tour', '=', DB::raw(0));
										})
									->where('c.verified','=',1)
									->whereNotNull('c.logo_url')
									->orderby(DB::raw('`cr`.plexuss IS NULL,`cr`.`plexuss`'));

						// if ($user->country_id == 1 && isset($user->state) ) {
							if (Session::has('get_started_colleges_already_seen')) {
								$tmp_colleges = Session::get('get_started_colleges_already_seen');

								$tmp_arr = explode(",", $tmp_colleges);
								$result = $result->whereNotIn('c.id', $tmp_arr);
							}

							$tmp = clone $result;
							$tmp = $tmp->where('c.state', $user->state)->count();
							if ($tmp > 0) {
								$result = $result->where('c.state', $user->state);
							}

							$college_ids = clone $result;
							$college_ids = $college_ids->select(DB::raw('GROUP_CONCAT(DISTINCT c.id SEPARATOR ",") as college_id'))->first();
							
							if (isset($college_ids->college_id) && !empty($college_ids->college_id)) {
								if (isset($tmp_colleges)) {
									Session::put('get_started_colleges_already_seen', $tmp_colleges.",".$college_ids->college_id);
								}else{
									Session::put('get_started_colleges_already_seen', $college_ids->college_id);
								}
								
							}
	 					// }

						$result = $result->groupBy('c.id')
										 ->select('c.id as college_id', 'c.school_name', 'c.logo_url', 'cr.plexuss', 'c.city', 'c.state',
											 	  'coi.url as img_url', DB::raw("'".$key->id."' as `ro_id`"), DB::raw("'".$key->type."' as `type`"), DB::raw("'".$cap."' as `cap`") )->get();

						
						if (!empty($result)) {
							if (isset($ret_arr)) {
								$ret_arr = $ret_arr->merge($result);
							}else{
								$ret_arr = $result;
							}
						}

						break;
				}
			}
		}else{
			
			$uc = new UtilityController;
			$ro_id_arr = array();

			foreach ($ro as $key) {

				$qry = DB::connection('rds1')->table('users as u')
											 ->where('u.id', $user->id)
											 ->select('u.id');

				$tmp_qry = $uc->addCustomFiltersForRevenueOrgs($qry, $key->name);
				
				if($tmp_qry['status'] == "success"){
					$qry = $tmp_qry['qry'];
					$qry = $qry->first();
				}else{
					continue;
				}

				if(isset($qry)){
					$ro_id_arr[] = $key->id;

					$cap = $ro_model->getRevenueOrganizationCap($key, $user->id);

					$tmp = array();
		            $tmp['ro_id'] = $key->id;
					$tmp['cap']   = $cap;
					$arr['caps'][] = $tmp;
				}
			}

			if (isset($ro_id_arr) && !empty($ro_id_arr)) {
				$cap = 18;
				$result = DB::connection('rds1')->table('revenue_organizations as ro')
										->join('aor_colleges as ac', function($q){
											$q->on('ro.aor_id', '=', 'ac.aor_id')
											  ->on('ac.active', '=', DB::raw(1));
										})
										->join('colleges as c', 'c.id', '=', 'ac.college_id')
										->leftjoin('colleges_ranking as cr', 'cr.college_id', '=', 'c.id')
										->leftjoin('college_overview_images as coi', function($join)
											{
											    $join->on('c.id', '=', 'coi.college_id');
											    $join->on('coi.url', '!=', DB::raw('""'));
											    $join->on('coi.is_video', '=', DB::raw(0));
											    $join->on('coi.is_tour', '=', DB::raw(0));
										})
										->where('c.verified','=', 1)
										->whereNotNull('c.logo_url')
										->whereIn('ro.id', $ro_id_arr)
										->orderby('ro.priority');

				$result = $result->groupBy('c.id')
								 ->select('c.id as college_id', 'c.school_name', 'c.logo_url', 'cr.plexuss', 'c.city', 'c.state',
									 	  'coi.url as img_url', 'ro.id as ro_id', DB::raw("'".$key->type."' as `type`"), DB::raw("'".$cap."' as `cap`") )->get();
				
				
				if (!empty($result)) {
					if (isset($ret_arr)) {
						$ret_arr = $ret_arr->merge($result);
					}else{
						$ret_arr = $result;
					}
				}else{
					$ret_arr = array();
				}	
			}
		}

		
		$ret = array();
		$ret['arr'] 	= $arr;
		$ret['ret_arr'] = $ret_arr;
		
		return $ret;
	}
	public function saveGetStartedThreeCollegesPins($input = NULL, $utm_source = NULL){

        if (isset($input) && isset($input['user_id'])) { // For use with Plexuss Mobile
            $data = [];
            $data['user_id'] = $input['user_id'];
        } else { // Standard web case
    		$viewDataController = new ViewDataController();
    		$data = $viewDataController->buildData(true);
        }

		(!isset($input)) ? $input = Request::all() : NULL;		
		
		$ro = RevenueOrganization::find($input['ro_id']);
		$ret = array();

		(!isset($utm_source)) ? $utm_source = 'get_started' : NULL;

        if (isset($input['utm_source']) && !isset($utm_source)) {
            $utm_source = $input['utm_source'];
        }

        // Add college to user's list and college's inquiries bucket
        $recruitment_attributes = [
            'user_id' => $data['user_id'],
            'college_id' => $input['college_id'],
        ];

        $recruitment_values = [
            'user_id' => $data['user_id'],
            'college_id' => $input['college_id'],
            'type' => 'inquiry_pick_a_college',
            'status' => 1,
            'user_recruit' => 1,
        ];

        Recruitment::updateOrCreate($recruitment_attributes, $recruitment_values);
		
		switch ($ro->type) {
			case 'post':
			
				// $dc = new DistributionController();
				// $dc->postInquiriesWithQueue($input['ro_id'], $input['college_id'], $data['user_id']);
				
				// temporary don't post cappex
				if (isset($input['ro_id']) && $input['ro_id'] = 1) {
					// Queue::push( new PostInquiriesThroughDistributionClient($input['ro_id'], $input['college_id'], $data['user_id']));

					$attr = array('ro_id' => $input['ro_id'], 'college_id' => $input['college_id'], 'user_id' => $data['user_id']);
					$val  = array('ro_id' => $input['ro_id'], 'college_id' => $input['college_id'], 'user_id' => $data['user_id']);

					NrccuaQueue::updateOrCreate($attr, $val);
				}
				$ret['status'] = 'success';
				break;

			case 'linkout':
				$this_dc = DistributionClient::on('rds1')->where('ro_id', $input['ro_id'])
														 ->where('college_id', $input['college_id'])
														 ->first();
				$dc = new DistributionController();
				$str = $dc->generateLinkoutUrl($this_dc->id, $data['user_id']);

				$ret['type'] = $ro->type;
				$ret['url']  = env('CURRENT_URL').'adRedirect?company='.$ro->name.'&utm_source='.$utm_source.'&cid=-1&url='.urlencode($str);

				$ret['status'] = 'success';
				break;

			case 'click':
				$arc =  AdRedirectCampaign::on('rds1')->where('ro_id', $input['ro_id'])
													  ->where('college_id', $input['college_id'])
													  ->first();

                if (!isset($arc)) {
                    $arc =  AdRedirectCampaign::on('rds1')->where('ro_id', $input['ro_id'])->first();
                }

				$ret['type'] = $ro->type;
				$ret['url']  = env('CURRENT_URL').'adRedirect?company='.$arc->company.'&utm_source='.$utm_source.'&cid='.$arc->id;

				$ret['status'] = 'success';
				break;
		}

		Cache::forget(env('ENVIRONMENT') .'_getGetStartedThreeCollegesPins_'. $data['user_id']);

		return $ret;
	}

	public function saveNewPhone($api_input = null){
		if( isset($api_input) ){
			$input = $api_input;
		}else{
			$input = Request::all();
		}

		if( isset($input['phone']) && isset($input['dialing_code']) ){
			if( isset($api_input) ){
	    		$user_id = $input['user_id'];
			}else{
	    		$user_id = Session::get('userinfo.id');
			}

    		$user = User::find($user_id);
    		$user->phone = '+'.$input['dialing_code'].' '.$input['phone'];
    		$user->save();

    		$ret = array();
		    $ret['response'] = 'success';
		    $ret['msg'] = 'Successfully saved new phone number.';

		}else{
			$ret = array();
		    $ret['response'] = 'failed';
		    $ret['msg'] = 'No phone and/or dialing code input found.';
		}

		return $ret;
	}

	public function saveEmail(){
		$input = Request::all();	

		$data = array();
    	$user_id = Session::get('userinfo.id');

    	if( $user_id && Request::has('email') ){

    		//checking if $input['email'] is already taken or not
    		$duplicateEmail = User::where('email', $input['email'])->first();//returns user with this email, if any

			//if email doesn't already exist, save
			//else dont save
			if( !isset($duplicateEmail) ){
				$user = User::find($user_id);
	    		$user->email = $input['email'];
	    		$user->save();
	    		Session::put('userinfo.session_reset', 1);
	    		return 'saved';
			}else{
				return 'taken';
			}
    	}

    	return 'failed';
	}

    private function saveAdPassthroughInfo($input) {
        try {
            $user_id = Crypt::decrypt($input['uid']);
        } catch (\Exception $e) {
            // Do nothing
        }

        $ip = $this->iplookup();

        $device = Agent::device();

        $browser = Agent::browser();

        $passthrough = new AdPassthroughs;

        try {
            $url = Request::fullUrl();
            $passthrough->url = $url;
            
        } catch (\Exception $e) {
            // Do nothing
        }

        $passthrough->section        = isset($input['section']) ? $input['section'] : 'main';
        $passthrough->user_id        = isset($user_id) ? $user_id : NULL;
        $passthrough->user_invite_id = isset($input['uiid']) ? $input['uiid'] : NULL;
        $passthrough->cid            = isset($input['cid']) ? $input['cid'] : NULL;
        $passthrough->company        = isset($input['company']) ? $input['company'] : NULL;
        $passthrough->ip             = isset($ip['ip']) ? $ip['ip'] : NULL;
        $passthrough->device         = isset($device) ? $device : NULL;
        $passthrough->browser        = isset($browser) ? $browser : NULL;
        $passthrough->countryName    = isset($ip['countryName']) ? $ip['countryName'] : NULL;
        $passthrough->stateName      = isset($ip['stateName']) ? $ip['stateName'] : NULL;
        $passthrough->cityName       = isset($ip['cityName']) ? $ip['cityName'] : NULL;
        $passthrough->zip            = isset($ip['cityAbbr']) ? $ip['cityAbbr'] : NULL;
        $passthrough->utm_campaign   = isset($input['utm_campaign']) ? $input['utm_campaign'] : NULL;
        $passthrough->utm_source     = isset($input['utm_source']) ? $input['utm_source'] : NULL;

        $passthrough->save();

        return $passthrough;
    }

	public function userMissingFields($company = NULL, $cid = NULL, $uid = NULL, $uiid = NULL, $utm_source = NULL, $section = 'main') {
        $data = [];

        $data['title'] = 'Missing Fields';

        $data['currentPage'] = 'user-missing-fields';

        if (!isset($company) && !isset($cid) && !isset($uid) && !isset($uiid) && !isset($utm_source)) {
            $input = Request::all();
        } else {
            $input = [
                'company' => (isset($company) && $company !== 'NULL') ? $company : NULL,
                'utm_source' => (isset($utm_source) && $utm_source !== 'NULL') ? $utm_source : NULL,
                'cid' => (isset($cid) && $cid !== 'NULL') ? $cid : NULL,
                'uid' => (isset($uid) && $uid !== 'NULL') ? $uid : NULL,
                'uiid' => (isset($uiid) && $uiid !== 'NULL') ? $uiid : NULL,
                'section' => (isset($section) && $section !== 'NULL') ? $section : 'main',
            ];
        }

        $adPassthrough = $this->saveAdPassthroughInfo($input);

        $data['ad_passthrough_id'] = $adPassthrough->id;

        if (!isset($input['cid'])) {
            return "inputs are required";
        }

        $ip = $this->iplookup();

        $is_international = $ip['countryName'] !== 'United States';

        $data['stay_on_plexuss_link'] = $is_international
            ? '/college-application'
            : '/get_started';

        if (isset($input['uid'])) {
            try {
                $plainTextId = Crypt::decrypt($input['uid']);
            } catch (\Exception $e) {
                return 'inputs are invalid';
            }

            $data['user'] = User::on('rds1')
                                ->select('in_college', 'fname', 'lname', 'email', 'address', 'city', 
                                         'country_id', 'state', 'gender', 'birth_date', 'zip', 'phone',
                                        'financial_firstyr_affordibility', 'interested_in_aid',
                                        'is_university_rep', 'is_student', 'is_alumni', 'is_parent',
                                        'current_school_id', 'is_counselor', 'hs_grad_year', 'college_grad_year',
                                        'interested_school_type')
                                ->find($plainTextId);

            $data['score'] = Score::on('rds1')
                                  ->select('overall_gpa', 'hs_gpa', 'sat_math', 'sat_reading_writing', 'sat_total',
                                            'sat_reading', 'sat_writing', 'act_english', 'act_math', 'act_composite', 
                                            'gre_verbal', 'gre_quantitative', 'gre_analytical', 'gmat_total', 'toefl_total', 'toefl_ibt_total', 'toefl_pbt_total', 'ielts_total', 'is_pre_2016_sat')
                                  ->where('user_id', $plainTextId)
                                  ->first();

            $ucq = UsersCustomQuestion::select('is_transfer')->where('user_id', '=', $plainTextId)->whereNotNull('is_transfer')->first();

            if ($ucq) {
                $data['is_transfer'] = $ucq->is_transfer;
            }
            
            $data['plain_uid'] = $plainTextId;

            if (isset($data['user']->in_college) && $data['user']->in_college == 0) {
                $data['grad_year'] = $data['user']->hs_grad_year;
            } else {
                $data['grad_year'] = $data['user']->college_grad_year;
            }

            // Check if user_type is set
            if (isset($data['user']->is_student) && $data['user']->is_student == 1) {
                $data['user_type'] = 'student';
            } else if (isset($data['user']->is_alumni) && $data['user']->is_alumni == 1) {
                $data['user_type'] = 'alumni';
            } else if (isset($data['user']->is_parent) && $data['user']->is_parent == 1) {
                $data['user_type'] = 'parent';
            } else if (isset($data['user']->is_counselor) && $data['user']->is_counselor == 1) {
                $data['user_type'] = 'counselor';
            } else if (isset($data['user']->is_university_rep) && $data['user']->is_university_rep == 1) {
                $data['user_type'] = 'university_rep';
            }

        } else { // Create empty inputs to show in userMissingFields page.
            $data['user'] = [
                'fname' => '',
                'lname' => '',
                'email' => '',
                'address' => '',
                'city' => '',
                'country_id' => '',
                'state' => '',
                'gender' => '',
                'birth_date' => '',
                'zip' => '',
                'in_college' => '',
                'phone' => '',
            ];

            $data['score'] = [
                'hs_gpa' => '',
            ];

            $data['plain_uid'] = null;
        }

        $data['countries'] = Country::on('rds1')->get();

        $data['states'] = State::on('rds1')->get();

        $data['ad_redirect_campaigns'] = AdRedirectCampaign::on('rds1')->find($input['cid']);

        $data['utm_campaign'] = isset($input['utm_campaign']) ? $input['utm_campaign'] : '';

        $data['utm_source'] = isset($input['utm_source']) ? $input['utm_source'] : '';

        $data['uid'] = isset($input['uid']) ? $input['uid'] : '';

        $data['uiid'] = isset($input['uiid']) ? $input['uiid'] : '';

        if (isset($data['plain_uid'])) {
            $alreadySeenPassthru = AdPassthroughs::on('rds1')
                                                 ->select('id')
                                                 ->where('user_id', '=', $data['plain_uid'])
                                                 ->first();
        }


        if ($section == 'verify' || $section == 'main_override' || !isset($alreadySeenPassthru) || $section == 'main' || !isset($data['plain_uid'])) {

            if ($section == 'main_override') {
                $data['ad_redirect_campaigns']['toggle_address'] = 1;
                $data['ad_redirect_campaigns']['toggle_city'] = 1;
                $data['ad_redirect_campaigns']['toggle_state'] = 1;
                $data['ad_redirect_campaigns']['toggle_zip'] = 1;
                $data['ad_redirect_campaigns']['toggle_gender'] = 1;
                $data['ad_redirect_campaigns']['toggle_birth_date'] = 1;
                $data['ad_redirect_campaigns']['toggle_gpa'] = 1;
            }

            if ($section == 'verify') {
                return View('passthru.'.$section, $data);
            }

            return View('get_started.userMissingFields', $data);

        } else {
            $all_majors = Major::on('rds1')->where('promote', '=', 1)->get();

            if (isset($data['plain_uid'])) {
                $user_majors = Objective::on('rds1')
                                            ->leftJoin('majors as m', 'm.id', '=', 'objectives.major_id')
                                            ->leftJoin('professions as p', 'p.id', '=', 'objectives.profession_id')
                                            ->where('user_id', '=', $data['plain_uid'])
                                            ->select('m.id as id', 'profession_id', 'm.name', 'objectives.degree_type', 'p.profession_name', 'objectives.university_location')
                                            ->get();

                if (isset($data['user']['country_id'])) {
                    $data['from_united_states'] = $data['user']['country_id'] == 1;
                } else {
                    $ip = $this->iplookup();

                    $data['from_united_states'] = $ip['countryAbbr'];
                }



                if (isset($user_majors)) {
                    $data['selected_majors'] = $user_majors;
                }

                foreach ($user_majors as $user_major) {
                    $data['degree_type'] = $user_major->degree_type;
                    $data['profession_name'] = $user_major->profession_name;
                    $data['selected_countries'] = $user_major->university_location;
                }

            }
            
            $data['all_majors'] = $all_majors;

            // Special case for custom_questions, potentially break this up into a switch case if we're adding  a lot more later.
            if (strpos($section, 'not_qualified') !== FALSE) {
                $viewDataController = new ViewDataController();
                $session_data = $viewDataController->buildData();

                if (isset($session_data['signed_in']) && $session_data['signed_in'] == 1) {
                    $data['apply_scholarships_link'] = '/college-application';

                } else {
                    $data['apply_scholarships_link'] = '/signin?redirect=college-application';
                };

                $ip = $this->iplookup();

                $data['passthru_redirect_status'] = ('redirect_' . $data['ad_redirect_campaigns']['company']);

                $data['passthru_directclick_status'] = ('directclick_' . $data['ad_redirect_campaigns']['company']);

                $adPassthrough = AdPassthroughs::on('rds1')
                                               ->select('id')
                                               ->where('cid', '=', $data['ad_redirect_campaigns']['id'])
                                               ->where('ip', '=', $ip['ip'])
                                               ->whereIn('section', ['main', 'main_override', 'personal', 'contact', 'scores', 'goals', 'preferences'])
                                               ->orderBy('id', 'desc')
                                               ->first();

                if ($adPassthrough) {
                    $data['passthru_redirect_status'] .= ('_' . $adPassthrough->id);
                    $data['passthru_directclick_status'] .= ('_' . $adPassthrough->id);
                }

                // Start Financials part
                if (isset($data['user']['country_id'])) {
                    $data['from_united_states'] = $data['user']['country_id'] == 1;
                } else {
                    $ip = $this->iplookup();

                    $data['from_united_states'] = $ip['countryAbbr'];
                }

                if (isset($data['user']['country_id'])) {
                    $country_id = $data['user']['country_id'];

                } else if (isset($ip) && isset($ip['countryAbbr'])) {
                    $country = Country::select('id')
                                      ->where('country_code', '=', $ip['countryAbbr'])
                                      ->first();


                    if (isset($country)) {
                        $country_id = $country->id;
                    }
                }

                if (isset($country_id)) {
                    $data['country_id'] = $country_id;
                }

                if (isset($data['from_united_states']) && $data['from_united_states'] == true) {
                    if (isset($session_data['signed_in']) && $session_data['signed_in'] == 1) {
                        $data['plexuss_application_link'] = '/get_started';
                    } else {
                        $data['plexuss_application_link'] = '/signin?redirect=get_started';
                    }
                } else {
                    if (isset($session_data['signed_in']) && $session_data['signed_in'] == 1) {
                        $data['plexuss_application_link'] = '/college-application';
                    } else {
                        $data['plexuss_application_link'] = '/signin?redirect=college-application';
                    }
                }
                // End Financials part
            }

            // TODO determine section based on priority and user country.
            return View('passthru.'.$section, $data);
        }

    }

    public function saveMissingFields(){
	    $input = Request::all();
        $hashed_user_id = $input['uid'];

        // Need to set uiid as NULL string in the case it does not exist to work with passthruIntermission:
        $uiid = isset($input['uiid']) ? $input['uiid'] : 'NULL';

        $ad_passthrough_id = isset($input['ad_passthrough_id']) ? $input['ad_passthrough_id'] : '';

        if (isset($input['section']) && $input['section'] == 'verify') {
            try {
                $user_id = Crypt::decrypt($hashed_user_id);

            } catch (\Exception $e) {
                $user_id = 'NULL';
            }

            return redirect('/passthruIntermission/'.$input['company_name'].'/'.$input['redirect_id'].'/'.$ad_passthrough_id.'/'.$user_id.'/'.$uiid.'/'.$input['utm_source'].'/continue-blank');
        }

        // Secondary passthru pages check and save
        if (isset($input['section'])) {
            try {
                $user_id = Crypt::decrypt($hashed_user_id);

            } catch (\Exception $e) {
                return 'invalid inputs';
            }

            $ret = $this->saveSecondaryMissingFields($input);

            // Start see if custom question exists
            $input['company'] = $input['company_name'];
            $input['cid'] = $input['redirect_id'];
            $input['hid'] = $hashed_user_id;

            $custom_question_redirect_url = $this->getPassthroughCustomQuestionRedirect($input);

            if (isset($custom_question_redirect_url)) {
                return redirect($custom_question_redirect_url);
            }
            // End see if custom question exists

            return redirect('/passthruIntermission/'.$input['company_name'].'/'.$input['redirect_id'].'/'.$ad_passthrough_id.'/'.$user_id.'/'.$uiid.'/'.$input['utm_source'].'/submit');
        }


        if (isset($hashed_user_id)) { 
            try {
                $user_id = Crypt::decrypt($hashed_user_id);

            } catch (\Exception $e) {
                return 'invalid inputs';
            }

            if (isset($input['email'])) {
                $emailAlreadyExists = User::on('rds1')
                                          ->select('id')
                                          ->where('email', '=', $input['email'])
                                          ->where('id', '!=', $user_id)
                                          ->first();

                if (!empty($emailAlreadyExists)) {
                    return Redirect::back()->withErrors(['email' => 'That email has already been taken.'])->withInput($input);;
                }
            }

            if (isset($input['birth_date'])) {
                $age = floor((time() - strtotime($input['birth_date'])) / 31556926);

                if ($age < 13) {
                    return Redirect::back()->withErrors(['birth_date' => 'Must be age 13 or older to apply.'])->withInput($input);;
                }
            }

    	    $values = array(
                'fname'=>$input['fname'],
                'lname'=> $input['lname'],
                'email' => $input['email'],
                'txt_opt_in' => (isset($input['txt_opt_in']) && $input['txt_opt_in'] == 'on') ? 1 : 0,
            );

            isset($input['address']) ? $values['address'] = $input['address'] : NULL;
            isset($input['city']) ? $values['city'] = $input['city'] : NULL;
            isset($input['country']) ? $values['country_id'] = $input['country'] : NULL;
            isset($input['state']) ? $values['state'] = $input['state'] : NULL;
            isset($input['gender']) ? $values['gender'] = $input['gender'] : NULL;
            isset($input['birth_date']) ? $values['birth_date'] = $input['birth_date'] : NULL;
            isset($input['zip']) ? $values['zip'] = $input['zip'] : NULL;
            isset($input['phone']) ? $values['phone'] = $input['phone'] : NULL;

            $updated = User::where('id', $user_id)->update($values);

            if($updated){
                if(isset($input['overall-gpa'])|| isset($input['hs-gpa'])) {
                    if ($input['in_college'] == 1) {
                        $attributes = ['user_id' => $user_id];
                        $values = [
                            'user_id' => $user_id, 
                            'overall_gpa' => isset($input['overall-gpa']) ? $input['overall-gpa'] : NULL,
                        ];
                        $updateScore = Score::updateOrCreate($attributes, $values);
                    } else {
                        $attributes = ['user_id' => $user_id];
                        $values = [
                            'user_id' => $user_id,
                            'hs_gpa' => isset($input['hs-gpa']) ? $input['hs-gpa'] : NULL,
                        ];
                        $updateScore = Score::updateOrCreate($attributes, $values);
                    }
                }
                
                $this->CalcIndicatorPercent($user_id);
                $this->CalcProfilePercent($user_id);
                $this->CalcOneAppPercent($user_id);

                // Start see if custom question exists
                $input['company'] = $input['company_name'];
                $input['cid'] = $input['redirect_id'];
                $input['hid'] = $hashed_user_id;

                $custom_question_redirect_url = $this->getPassthroughCustomQuestionRedirect($input);

                if (isset($custom_question_redirect_url)) {
                    return redirect($custom_question_redirect_url);
                }
                // End see if custom question exists

                return redirect('/passthruIntermission/'.$input['company_name'].'/'.$input['redirect_id'].'/'. $ad_passthrough_id .'/'.$user_id.'/'.$uiid.'/'.$input['utm_source'].'/submit');
            }
        } else {
            $ac = new AuthController;
            $saveResponse = $ac->saveNewUserMissingFields($input);

            if (isset($saveResponse['status']) && $saveResponse['status'] === 'failed') {
                if ($saveResponse['error_message'] == 'Email already exists') {
                    return Redirect::back()->withErrors(['email' => 'That email has already been taken.'])->withInput($input);
                } else if ($saveResponse['error_message'] == 'Age less than 13') {
                    return Redirect::back()->withErrors(['birth_date' => 'Must be age 13 or older to apply.'])->withInput($input);;
                } else {
                    // Error saving. Just redirect to ad campaign.

                    return redirect('/passthruIntermission/'.$input['company_name'].'/'.$input['redirect_id'].'/'.$ad_passthrough_id.'/NULL/'.$uiid.'/'.$input['utm_source'].'/skip');
                }
            } else if ($saveResponse['status'] === 'success') {
                // Saved new user successful. Redirect to ad directly.
                $input['company'] = $input['company_name'];
                $input['cid'] = $input['redirect_id'];
                $input['hid'] = Crypt::encrypt($saveResponse['user_id']);

                $custom_question_redirect_url = $this->getPassthroughCustomQuestionRedirect($input);

                if (isset($custom_question_redirect_url)) {
                    return redirect($custom_question_redirect_url);
                }

                return redirect('/passthruIntermission/'.$input['company_name'].'/'.$input['redirect_id'].'/'.$ad_passthrough_id.'/'.$saveResponse['user_id'].'/'.$uiid.'/'.$input['utm_source'].'/submit');
            }

        }
    }

    public function passthruIntermission($company = NULL, $cid = NULL, $ad_passthrough_id = NULL, $uid = NULL, $uiid = NULL, $utm_source = NULL, $status = NULL) {
        $data = [];

        if (isset($uid) && $uid !== -1) {
            $hid = Crypt::encrypt($uid);
        } else {
            $hid = '';
        }

        $cid               = (isset($cid) && $cid !== 'NULL') ? $cid : '';
        $uid               = (isset($uid) && $uid !== 'NULL') ? $uid : '';
        $uiid              = (isset($uiid) && $uiid !== 'NULL') ? $uiid : '';
        $status            = (isset($status) && $status !== 'NULL') ? $status : '';
        $company           = (isset($company) && $company !== 'NULL') ? $company : '';
        $utm_source        = (isset($utm_source) && $utm_source !== 'NULL') ? $utm_source : '';
        $ad_passthrough_id = (isset($ad_passthrough_id) && $ad_passthrough_id !== 'NULL') ? $ad_passthrough_id : '';

        $data['title'] = 'Passthrough Intermission';
        $data['redirect_url'] = '/adRedirect?company='.$company.'&cid='.$cid.'&utm_source=' . $utm_source. '&pass_through=false&uid='.$uid.'&ad_passthrough_id='.$ad_passthrough_id . '&uiid=' . $uiid . '&hid=' . $hid . '&passthru_intermission=true&passthru_status='.$status;

        return View('passthru.passthruIntermission', $data);
    }

    private function saveSecondaryMissingFields($input) {
        try {
            $user_id = Crypt::decrypt($input['uid']);
        } catch (\Exception $e) {
            return 'failed';
        }

        $section = $input['section'];

        $user = User::find($user_id);

        if (!isset($user)) return 'failed';

        switch($section) {
            case 'personal':
                $values = [];
                if (isset($input['user_type'])) {
                    // Set all user_types to 0
                    $values['is_student'] = 0;
                    $values['is_alumni'] = 0;
                    $values['is_parent'] = 0;
                    $values['is_counselor'] = 0;
                    $values['is_university_rep'] = 0;

                    // Set the user selected one back to 1
                    $values['is_'.$input['user_type']] = 1;
                }

                isset($input['in_college']) ? $values['in_college'] = $input['in_college'] : NULL;

                isset($input['gender']) ? $values['gender'] = $input['gender'] : NULL;

                if (isset($input['grad_year'])) {
                    if (isset($input['in_college']) && $input['in_college'] == 0) {
                        $values['hs_grad_year'] = $input['grad_year'];

                    } else {
                        $values['college_grad_year'] = $input['grad_year'];

                    }
                }

                if (isset($input['school_name'])) {
                    if (isset($input['in_college']) && $input['in_college'] == 0) {
                        $highSchool = HighSchool::on('rds1')->select('id')->where('school_name', '=', $input['school_name'])->orderBy('verified', 'DESC')->first();

                        if (!isset($highSchool)) { 
                            $hsAttributes = [
                                'school_name' => $input['school_name'],
                                'user_id_submitted' => $user_id,
                            ];

                            $hsValues =  [
                                'school_name' => $input['school_name'],
                                'user_id_submitted' => $user_id,
                                'verified' => 0,
                            ];

                            $highSchool = HighSchool::updateOrCreate($hsAttributes, $hsValues);
                        }

                        isset($highSchool->id) ? $values['current_school_id'] = $highSchool->id : NULL;

                    } else {
                        $college = College::on('rds1')->select('id')->where('school_name', '=', $input['school_name'])->orderBy('verified', 'DESC')->first();

                        if (!isset($college)) {
                            $collegeAttributes = [
                                'school_name' => $input['school_name'],
                                'user_id_submitted' => $user_id,
                            ];

                            $collegeValues = [
                                'school_name' => $input['school_name'],
                                'user_id_submitted' => $user_id,
                                'verified' => 0,
                            ];

                            $college = College::updateOrCreate($collegeAttributes, $collegeValues);
                        }

                        isset($college->id) ? $values['current_school_id'] = $college->id : NULL;

                    }             
                }

                $user->update($values);

                break;

            case 'contact':
                $values = [];

                (isset($input['txt_opt_in']) && $input['txt_opt_in'] == 'on') ? $values['txt_opt_in'] = 1 : NULL;
                isset($input['country']) ? $values['country_id'] = $input['country'] : NULL;
                isset($input['address']) ? $values['address'] = $input['address'] : NULL;
                isset($input['city']) ? $values['city'] = $input['city'] : NULL;
                isset($input['state']) ? $values['state'] = $input['state'] : NULL;
                isset($input['phone']) ? $values['phone'] = $input['phone'] : NULL;
                isset($input['zip']) ? $values['zip'] = $input['zip'] : NULL;

                $user->update($values);

                break;

            case 'scores':
                $scoreAttributes = [
                    'user_id' => $user_id,
                ];

                $scoreValues = [];

                $scoreFields = [
                    'sat_math',
                    'sat_reading_writing',
                    'sat_total',
                    'sat_reading',
                    'sat_writing',
                    'act_english',
                    'act_math',
                    'act_composite',
                    'gre_verbal',
                    'gre_quantitative',
                    'gre_analytical',
                    'gmat_total',
                    'toefl_total',
                    'toefl_ibt_total',
                    'toefl_pbt_total',
                    'ielts_total',
                ];

                if (isset($input['pre-2016-check']) && $input['pre-2016-check'] == 'on') {
                    $scoreValues['is_pre_2016_sat'] = 1;

                } else {
                    $scoreValues['is_pre_2016_sat'] = 0;
                }

                if (isset($input['converted_gpa'])) {
                    if (isset($input['in_college']) && $input['in_college'] == 0) {
                        // Highschool
                        $scoreValues['hs_gpa'] = $input['converted_gpa'];
                    } else {
                        // College
                        $scoreValues['overall_gpa'] = $input['converted_gpa'];
                    }
                }

                foreach ($scoreFields as $field) {
                    isset($input[$field]) ? $scoreValues[$field] = $input[$field] : NULL;
                }

                Score::updateOrCreate($scoreAttributes, $scoreValues);

                break;

            case 'goals':
                $userValues = [];
                $degree_type = $input['degree_type'];
                $profession_id = null;

                if (isset($input['profession_name'])) {
                    $profession_query = Profession::on('rds1')->where('profession_name', '=', $input['profession_name'])->first();

                    if (isset($profession_query)) {
                        $profession_id = $profession_query->id;
                    } else {
                        $attributes = [
                            'profession_name' => $input['profession_name'],
                        ];

                        $insert_profession = Profession::updateOrCreate($attributes, $attributes);

                        $profession_id = $insert_profession->id;
                    }
                }

                if (isset($input['selected_majors'])) {
                    // $objectives 
                    $obj = DB::connection('rds1')->table('objectives')->where('user_id', $user_id)->get();

                    //if there are currently saved objectives, remove them before adding new ones
                    if (count($obj) > 0) {
                        foreach ($obj as $key) {
                            $removeObjective = Objective::find($key->id);
                            $removeObjective->delete();
                        }
                    }

                    $selected_majors = json_decode($input['selected_majors']);
                    
                    foreach ($selected_majors as $major) {
                        $objAttributes = [
                            'user_id' => $user_id,
                            'major_id' => $major->id,
                        ];

                        $objValues = [
                            'user_id' => $user_id,
                            'major_id' => $major->id,
                            'degree_type' => $degree_type,
                            'profession_id' => $profession_id,
                        ];

                        Objective::updateOrCreate($objAttributes, $objValues);
                    }
                }

                isset($input['financial_firstyr_affordibility']) ? $userValues['financial_firstyr_affordibility'] = $input['financial_firstyr_affordibility'] : NULL;

                $userValues['interested_in_aid'] = (isset($input['interested_in_aid']) && $input['interested_in_aid'] == 'on') ?  1 : 0;

                $user->update($userValues);

                break;

            case 'preferences':
                $country_ids = [];

                $attributes = [
                    'user_id' => $user_id,
                ];

                $ucqValues = [
                    'user_id' => $user_id,
                ];

                $userValues = [
                    'user_id' => $user_id,
                ];

                isset($input['interested_school_type']) ? $userValues['interested_school_type'] = $input['interested_school_type'] : NULL;

                if (isset($input['is_transfer'])) {
                    $ucqValues['is_transfer'] = $input['is_transfer'];

                    UsersCustomQuestion::updateOrCreate($attributes, $ucqValues);
                }

                if (isset($input['selected_countries'])) {
                    $selected_countries = json_decode($input['selected_countries']);

                    foreach ($selected_countries as $country) {
                        $country_ids[] = $country->id;
                    }
                }

                if (!empty($country_ids)) {
                    $objAttributes = ['user_id' => $user_id];
                    $objValues = ['user_id' => $user_id, 'university_location' => implode(",", $country_ids)];
                    
                    Objective::updateOrCreate($objAttributes, $objValues);
                }

                $user->update($userValues);

                break;

            case 'custom_question_ielts':
                if (isset($input['plan_to_take_ielts'])) {
                    $attributes = [
                        'user_id' => $user_id
                    ];

                    $values = [
                        'user_id' => $user_id,
                        'plan_to_take_ielts' => $input['plan_to_take_ielts'],
                    ];

                    UsersCustomQuestion::updateOrCreate($attributes, $values);
                }

                break;

            case 'custom_question_financials':
                $values = [];

                if (isset($input['financial_firstyr_affordibility'])) {
                    $values['financial_firstyr_affordibility'] = $input['financial_firstyr_affordibility'];

                    $user->update($values);
                }

            default:
                return 'failed';
        }

        return 'success';

    }

}//end of GetStartedController 
