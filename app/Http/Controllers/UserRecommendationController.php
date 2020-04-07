<?php

namespace App\Http\Controllers;

use Request, Session, DB;
use App\User, App\Ranking, App\Score, App\Recruitment, App\PortalNotification, App\Objective;

class UserRecommendationController extends Controller
{
    /*
	This will set college recommendations for the user
	*/
	
	public function generateCollegeRecommendation($api_input = null){

		$data = array();

		if( isset($api_input) ){
			$data['user_id'] = $api_input['user_id'];
		}else{
			$data['user_id'] = Session::get('userinfo.id');
		}

		$user_total_colleges = array();
		$usrScores = array();		

		$scoreModel = Score::on('rds1')->where('user_id', '=', $data['user_id'])->first();

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

		/*
		//Conditions to be able to run Recommendations	
		if($usrScores['sat_total'] == '' || $usrScores['sat_total'] == 0 || $usrScores['sat_total'] == NULL ||
			$usrScores['act_composite'] == '' || $usrScores['act_composite'] == 0 || $usrScores['act_composite'] == NULL ||
			//$usrScores['overall_gpa'] == '' || $usrScores['overall_gpa'] == '0.00' || $usrScores['overall_gpa'] == NULL ||
			$usrScores['hs_gpa'] == '' || $usrScores['hs_gpa'] == '0.00' || $usrScores['hs_gpa'] == NULL 
			){

			return $data;
		}
		*/

		// User list of colleges
		// $recruitment_model = Recruitment::where('user_id', '=', $data['user_id'])
		// 	->get();

		// $latest_school_id = -1;
		
		// $check = true;
		// foreach ($recruitment_model as $school) {
		// 	$check = false;
		// 	$latest_school_id = $school->college_id;
		// 	array_push($user_total_colleges, $school->college_id);
		// }

		$latest_school_id = -1;
		
		$check = true;
		$recruitment_model = Recruitment::on('rds1')->where('user_id', '=', $data['user_id'])
										->orderBy('created_at')
										->first();

		if (isset($recruitment_model) && !empty($recruitment_model)) {
			$check = false;
			$latest_school_id = $recruitment_model->college_id;
		}								
			
		
		$eligible_colleges = DB::connection('rds1')->table('colleges as cl')
												   ->join('colleges_ranking as cr', 'cr.college_id', '=', 'cl.id')
												   ->join('colleges_admissions as ca' , 'ca.college_id', '=', 'cl.id')
												   ->join('colleges_tuition as ct', 'ct.college_id', '=', 'cl.id')
												   ->where('cl.control_of_school','!=','Private for-profit')
												   ->where('cr.plexuss', '!=', '')
												   ->orderBy('cr.plexuss', 'asc')
												   ->select('ca.sat_read_75', 'ca.sat_write_75', 'ca.sat_math_75', 
												   			'cl.id as college_id', 'cl.school_name as school_name', 
												   			'cr.plexuss as rank', 'ca.act_composite_75 as act',
															'ct.tuition_avg_in_state_ftug as inStateTuition',
															'ct.tuition_avg_out_state_ftug as outStateTuition', 'cl.state as state');

		if(!$check){

			// Already recommended schools for the user

			// $portal_notification_model = PortalNotification::where('user_id', '=', $data['user_id'])
			// 	->get();

			// foreach ($portal_notification_model as $school) {
			// 	array_push($user_total_colleges, $school->school_id);		
			// }

			// //Engine begins here

			// $eligible_colleges = $eligible_colleges
			// 	->whereNotIn('cl.id', $user_total_colleges);

			$eligible_colleges = $eligible_colleges->whereRaw("not exists (Select 1 from recruitment as r where r.user_id = 
															  ".$data['user_id']." and r.college_id = cl.id)")
												   ->whereRaw("not exists (Select 1 from portal_notifications as pn where pn.user_id = ".$data['user_id']." and pn.school_id = cl.id)");
		}

		$objective = Objective::on('rds1')->where('user_id', '=', $data['user_id'])->first();

		if(!empty($objective)){

			//get elgigible colleges with the user's degree & major

			$major = clone $eligible_colleges;

			$major = $major
				->join('college_programs as cp', 'cp.college_id', '=', 'cl.id')
				->join('objectives as o', function($join) use($data){
					$join->on('cp.major_id', '=', 'o.major_id')
						 ->on('cp.degree_type', '=', 'o.degree_type')
						 ->where('o.user_id', '=', $data['user_id']);
			 	})
			 	->leftjoin('organization_branches as ob', 'cl.id', '=', 'ob.school_id')
			 	->leftjoin('priority as p', 'cl.id', '=', 'p.college_id')
				->get();
		}

		if(isset($major) && !empty($major)){
			$recommendedSchools = $this->recommendationFunnel($major, $usrScores, $latest_school_id, 'major', $api_input);
		
		}if(!isset($recommendedSchools) || empty($recommendedSchools)){
			
			//get elgigible colleges with the user's degree & department

			$department = clone $eligible_colleges;

			$department = $department
				->join('college_programs as cp', 'cp.college_id', '=', 'cl.id')
				->join('objectives as o', function($join) use($data){
					$join->on('o.degree_type', '=', 'cp.degree_type')
						 ->where('o.user_id', '=', $data['user_id']);
			 	})
			 	->join('majors as m', function($join){
			 		$join->on('m.id', '=', 'o.major_id')
			 			 ->on('m.department_id', '=', 'cp.department_id');
	 			})
			 	->leftjoin('organization_branches as ob', 'ob.school_id', '=', 'cl.id')
			 	->leftjoin('priority as p', 'cl.id', '=', 'p.college_id')
			 	->get();

			if(!empty($department)){
				$recommendedSchools = $this->recommendationFunnel($department, $usrScores, $latest_school_id, 'department', $api_input);
			}

		}if(!isset($recommendedSchools) || empty($recommendedSchools)){
			
			//if the major and department matches did not generate recs -> old method

			$eligible_colleges = $eligible_colleges->get();
			$recommendedSchools = $this->recommendationFunnel($eligible_colleges, $usrScores, $latest_school_id, null, $api_input);
		}

		if(count($recommendedSchools) !=0){
			foreach ($recommendedSchools as $k) {
				
				$portal_user_id = array('user_id' => $data['user_id'], 'school_id' => $k->college_id);
				$portal_val  = array('school_id' => $k->college_id, 'is_recommend' => 1, 
									 'is_higher_rank_recommend' => $k->is_higher_rank_recommend,
									 'is_top_75_percentile_recommend' => $k->is_top_75_percentile_recommend, 
									 'is_lower_tuition_recommend' => $k->is_lower_tuition_recommend,
									 'recommend_based_on_college_id' => $k->recommend_based_on_college_id,
									 'is_major_recommend' => $k->is_major_recommend, 
									 'is_department_recommend' => $k->is_department_recommend);

				$updateUsrRecommendation = PortalNotification::updateOrCreate($portal_user_id, $portal_val);
			}
		}
	}

	public function recommendationFunnel($eligible_colleges, $usrScores, $latest_school_id, $match=null, $api_input = null){

		$recommendedSchools = array();
		$recommendedTuition = array();

		$data = array();

		if( isset($api_input) ){
			$data['user_id'] = $api_input['user_id'];
		}else{
			$data['user_id'] = Session::get('userinfo.id');
		}
			
		$user = User::on('rds1')->find($data['user_id']);

		//SHOULD WE CHECK IF THE NUMBER OF ELIGIBLE COLLEGES ARE LESS THAN OR EQUAL TO TWO

		$tempArr = array();

		if(count($eligible_colleges) < 2){
			foreach ($eligible_colleges as $school) {

				$tempArr = $school;
				$tempArr->is_higher_rank_recommend =0;
				$tempArr->is_lower_tuition_recommend =0;
				$tempArr->is_top_75_percentile_recommend =0;
				$tempArr->recommend_based_on_college_id =  -1;
				$tempArr->is_major_recommend = 0; 
				$tempArr->is_department_recommend = 0;

				if(isset($match)){
					if($match == 'major'){
						$tempArr->is_major_recommend = 1; 
					}
					else{
						$tempArr->is_department_recommend = 1;
					}
				}

				$recommendedSchools[] = $tempArr;
			}

			return $recommendedSchools;
		}
		// It means the user don't have any colleges in their list
		if($latest_school_id == -1){
			//check to see if the user has added SAT and ACT score
			if(isset($usrScores['sat_total']) && isset($usrScores['act_composite'])){
				foreach ($eligible_colleges as $school) {

					$tmpTuition = "";
					//check to see if the user is in-state, or out-state
					if($school->state == $user->state){
						$tmpTuition  = $school->inStateTuition;
						
					}else{
						$tmpTuition  = $school->outStateTuition;
						
					}
					
					if((($school->sat_read_75 + $school->sat_write_75 + $school->sat_math_75 )<= $usrScores['sat_total'] )&&
						 ($school->act <= $usrScores['act_composite'])){

						//if we haven't added any school to the recommended list, add the first two, and for the the rest of
						//results compare to the ones in the array.
						//echo count($recommendedSchools) . " swdadsa <br>";
						if(count($recommendedSchools) <2){

							//check to see if the user is in-state, or out-state

							$recommendedTuition[$school->college_id] = $tmpTuition;

							$tempArr = array();
							$tempArr = $school;
							$tempArr->is_major_recommend = 0; 
							$tempArr->is_department_recommend = 0;

							if(isset($match)){
								if($match == 'major'){
									$tempArr->is_major_recommend = 1; 
								}
								else{
									$tempArr->is_department_recommend = 1;
								}
							}

							$tempArr->is_higher_rank_recommend =0;
							$tempArr->is_lower_tuition_recommend =0;
							$tempArr->is_top_75_percentile_recommend =1;
							$tempArr->recommend_based_on_college_id =  -1;

							$recommendedSchools[] = $tempArr;
							
						}else{
							foreach ($recommendedSchools as $k ) {

								$cid = $k->college_id;

								if(isset($recommendedTuition[$cid])){
									if($tmpTuition < $recommendedTuition[$cid] && $tmpTuition!=0){

										//Replace the school tuition array tracker that has a higher tuition to the school that has a lower tuition
										unset($recommendedTuition[$k->college_id]);
										$recommendedTuition[$school->college_id] = $tmpTuition;
										

										//Remove the school that has a higher tuition
										$tmpArr = array();
										foreach ($recommendedSchools as $innerKey) {
											if($innerKey->college_id != $cid){
												$tmpArr[] = $innerKey;
											}
										}

										$recommendedSchools = $tmpArr;

										$tempArr = array();
										$tempArr = $school;
										$tempArr->is_major_recommend = 0; 
										$tempArr->is_department_recommend = 0;
										
										if(isset($match)){
											if($match == 'major'){
												$tempArr->is_major_recommend = 1; 
											}
											else{
												$tempArr->is_department_recommend = 1;
											}
										}

										$tempArr->is_higher_rank_recommend =0;
										$tempArr->is_lower_tuition_recommend =0;
										$tempArr->is_top_75_percentile_recommend =1;
										$tempArr->recommend_based_on_college_id =  -1;


										$recommendedSchools[] = 	$tempArr;

										break;

									}
								}
							}
						}	
					}	
				}
			}
		}else{

			// USER HAS ADDED A COLLEGE TO THEIR LIST
			$tmpRank = Ranking::on('rds1')->where('college_id', '=', $latest_school_id)->first();

			if(!isset($tmpRank)){
				return $recommendedSchools;
			}else{

				$latest_school_modal = DB::connection('rds1')->table('colleges_ranking as cr')
										 ->join('colleges_tuition as ct', 'ct.college_id' , '=' , 'cr.college_id')
										 ->join('colleges as cl', 'cl.id' ,'=','cr.college_id')
										 ->select('cr.plexuss as plexuss', 'ct.tuition_avg_in_state_ftug as inStateTuition', 
										 		  'ct.tuition_avg_out_state_ftug as outStateTuition', 'cl.state as state')
										 ->where('cl.id', '=', $latest_school_id)
										 ->first();

				$latest_school_rank = $latest_school_modal->plexuss;
				$latest_school_tuition = 0;
				
				if($user->state == $latest_school_modal->state){

					$latest_school_tuition = $latest_school_modal->inStateTuition;
				}else{
					$latest_school_tuition = $latest_school_modal->outStateTuition;
				}
			}
			//$latest_school_rank = $latest_school_rank_modal->plexuss;

			
	
			//check to see if the school has a rank
			if($latest_school_rank == "N/A"){
				return $recommendedSchools;
			}else{


				foreach ($eligible_colleges as $school) {

					// if this is an array instead of an object
					if (is_array($school) && isset($school[0])) {
						$school = $school[0];
					}
					//THE SCHOOL THAT YOU HAVE ADDED HAS LESS HIGHER RANK THAN ANY SCHOOL WE CAN RECOMMEND YOU

					if($school->rank > $latest_school_rank){
						//return $recommendedSchools;
						break;
					}else{
						
						$tmpTuition = "";
						//check to see if the user is in-state, or out-state
						if($school->state == $user->state){
							$tmpTuition  = $school->inStateTuition;
							
						}else{
							$tmpTuition  = $school->outStateTuition;
							
						}
						
						//check to see if the user has added SAT and ACT score
						if(isset($usrScores['sat_total']) && isset($usrScores['act_composite'])){




							if((($school->sat_read_75 + $school->sat_write_75 + $school->sat_math_75 )<= $usrScores['sat_total'] )&&
						 		($school->act <= $usrScores['act_composite'])){

								//if we haven't added any school to the recommended list, add the first two, and for the the rest of
								//results compare to the ones in the array.

								if(count($recommendedSchools) <2){

									//check to see if the user is in-state, or out-state

									$recommendedTuition[$school->college_id] = $tmpTuition;
									
									$tempArr = array();
									$tempArr = $school;
									
									$tempArr->is_major_recommend = 0; 
									$tempArr->is_department_recommend = 0;
									
									if(isset($match)){
										if($match == 'major'){
											$tempArr->is_major_recommend = 1; 
										}
										else{
											$tempArr->is_department_recommend = 1;
										}
									}
									$tempArr->is_higher_rank_recommend =1;
									$tempArr->is_lower_tuition_recommend =0;
									$tempArr->is_top_75_percentile_recommend =1;
									$tempArr->recommend_based_on_college_id =  $latest_school_id;

									$recommendedSchools[] = 	$tempArr;
									
								}else{
									foreach ($recommendedSchools as $k ) {

										$cid = $k->college_id;

										if(isset($recommendedTuition[$cid])){
											if($tmpTuition < $recommendedTuition[$cid] && $tmpTuition!=0){

												//Replace the school tuition array tracker that has a higher tuition to the school that has a lower tuition
												unset($recommendedTuition[$k->college_id]);
												$recommendedTuition[$school->college_id] = $tmpTuition;
												

												//Remove the school that has a higher tuition
												$tmpArr = array();
												foreach ($recommendedSchools as $innerKey) {
													if($innerKey->college_id != $cid){
														$tmpArr[] = $innerKey;
													}
												}

												$recommendedSchools = $tmpArr;
												$tempArr = array();
												$tempArr = $school;
												
												$tempArr->is_major_recommend = 0; 
												$tempArr->is_department_recommend = 0;

												if(isset($match)){
													if($match == 'major'){
														$tempArr->is_major_recommend = 1; 
													}
													else{
														$tempArr->is_department_recommend = 1;
													}
												}

												$tempArr->is_higher_rank_recommend =1;


												if($tmpTuition < $latest_school_tuition){
													$tempArr->is_lower_tuition_recommend =1;
												}else{
													$tempArr->is_lower_tuition_recommend =0;
												}
												//var_dump($school);
												//var_dump($tmpTuition);
												//var_dump($latest_school_tuition);

												$tempArr->is_top_75_percentile_recommend =1;
												$tempArr->recommend_based_on_college_id =  $latest_school_id;


												$recommendedSchools[] = 	$tempArr;

												break;

											}
										}
									}
								}
							}else{
								$tempArr = array();
								$tempArr = $school;
								
								$tempArr->is_major_recommend = 0; 
								$tempArr->is_department_recommend = 0;
								
								if(isset($match)){
									if($match == 'major'){
										$tempArr->is_major_recommend = 1; 
									}
									else{
										$tempArr->is_department_recommend = 1;
									}
								}
								$tempArr->is_higher_rank_recommend =1;
								$tempArr->is_lower_tuition_recommend =0;
								$tempArr->is_top_75_percentile_recommend =0;
								$tempArr->recommend_based_on_college_id =  $latest_school_id;

								$recommendedSchools[] = 	$tempArr;
							}

						}else{
							
							//if we haven't added any school to the recommended list, add the first two, and for the the rest of
								//results compare to the ones in the array.
								
							if(count($recommendedSchools) <2){
								
								//check to see if the user is in-state, or out-state

								$recommendedTuition[$school->college_id] = $tmpTuition;
								
								$tempArr = array();
								$tempArr = $school;
								
								$tempArr->is_major_recommend = 0; 
								$tempArr->is_department_recommend = 0;
								
								if(isset($match)){
									if($match == 'major'){
										$tempArr->is_major_recommend = 1; 
									}
									else{
										$tempArr->is_department_recommend = 1;
									}
								}

								$tempArr->is_higher_rank_recommend =1;
								$tempArr->is_lower_tuition_recommend =0;
								$tempArr->is_top_75_percentile_recommend =0;
								$tempArr->recommend_based_on_college_id =  $latest_school_id;

								$recommendedSchools[] = 	$tempArr;
								
							}else{
								
								foreach ($recommendedSchools as $k ) {

									$cid = $k->college_id;

									if(isset($recommendedTuition[$cid])){
										if($tmpTuition < $recommendedTuition[$cid] && $tmpTuition!=0){

											//Replace the school tuition array tracker that has a higher tuition to the school that has a lower tuition
											unset($recommendedTuition[$k->college_id]);
											$recommendedTuition[$school->college_id] = $tmpTuition;
											

											//Remove the school that has a higher tuition
											$tmpArr = array();
											foreach ($recommendedSchools as $innerKey) {
												if($innerKey->college_id != $cid){
													$tmpArr[] = $innerKey;
												}
											}

											$recommendedSchools = $tmpArr;
											
											$tempArr = array();
											$tempArr = $school;
											$tempArr->is_major_recommend = 0; 
											$tempArr->is_department_recommend = 0;
											$tempArr->is_higher_rank_recommend =1;

											if($tmpTuition < $latest_school_tuition){
												$tempArr->is_lower_tuition_recommend =1;
											}else{
												$tempArr->is_lower_tuition_recommend =0;
											}

											$tempArr->is_top_75_percentile_recommend =0;
											$tempArr->recommend_based_on_college_id =  $latest_school_id;

											if(isset($match)){
												if($match == 'major'){
													$tempArr->is_major_recommend = 1; 
												}
												else{
													$tempArr->is_department_recommend = 1;
												}
											}

											$recommendedSchools[] = 	$tempArr;

											break;

										}
									}
								}
							}	
						}
					}
				}

				if (isset($api_input) && empty($recommendedSchools)) {
					foreach ($eligible_colleges as $school) {
						$tempArr = array();
						$tempArr = $school;

						$tempArr->is_major_recommend = 0; 
						$tempArr->is_department_recommend = 0;
						if(isset($match)){
							if($match == 'major'){
								$tempArr->is_major_recommend = 1; 
							}
							else{
								$tempArr->is_department_recommend = 1;
							}
						}
						$tempArr->is_higher_rank_recommend =0;
						$tempArr->is_lower_tuition_recommend =0;
						$tempArr->is_top_75_percentile_recommend =0;
						$tempArr->recommend_based_on_college_id =  -1;

						$recommendedSchools[] = 	$tempArr;
						break;
					}
				}
			}
		}
		return $recommendedSchools;
	}
}
