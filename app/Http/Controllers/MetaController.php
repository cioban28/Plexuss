<?php

namespace App\Http\Controllers;

use Request, DB;
use App\College, App\CollegeAdmission, App\CollegeTuition, App\CollegeRanking, App\CollegeEnrollment, App\CollegeFinancialAid;

class MetaController extends Controller
{
    protected $variation_num = 1;
	public function run(){

		//$this->generateOverview();
		//$this->generateStats();
		//$this->generateRanking();
		//$this->generateFinancialAid();
		//$this->generateAdmission();
		//$this->generateTuition();
		$this->generateEnrollment();
	}

	/*
	* Generates the page title, meta keywords, and meta description for each of colleges
	*
	*/
	private function generateOverview(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			$school_name = $key->school_name;
			$alias = $key->alias;

			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($alias) >1) {
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = 'Overview '.$school_name.' | Plexuss.com';

						$keywords = $school_name;

						if(isset($tmp)){
							$c = 0;
							foreach ($tmp as $k => $v) {
								if ($c >=2) {
									break;

								}
								if ($v != '') {
									$keywords = $keywords. ', '. $v;
									$c++;
								}
							}
						}

						$description = "Get all the information you need about ".$school_name.". Find out more about ".$alias."'s school history, campus setting, academic calendar, and more. Useful links to financial aid details and application form.";
						break;
					
					case 2:
						
						$page_title = 'Overview '.$school_name.' | Plexuss.com';

						$keywords = $school_name;

						if(isset($tmp)){
							$c = 0;
							foreach ($tmp as $k => $v) {
								if ($c >=2) {
									break;

								}
								if ($v != '') {
									$keywords = $keywords. ', '. $v;
									$c++;
								}
							}
						}

						$description = "Get trusted and up-to-date information about ".$school_name.". Discover ".$alias." admission date, application deadline, tuition fees, financial aid, admission stats, SAT scores and more.";

						break;
					
					case 3:
						$page_title = 'Overview '.$school_name.' | Plexuss.com';

						$keywords = $school_name;

						if(isset($tmp)){
							$c = 0;
							foreach ($tmp as $k => $v) {
								if ($c >=2) {
									break;

								}
								if ($v != '') {
									$keywords = $keywords. ', '. $v;
									$c++;
								}
							}
						}

						$description = 'Is '.$school_name.' the best college for you? The complete guide to '.$alias.'. Admission, rankings, history, tuition, financial aid and more.';

						break;
					
					case 4:
						$page_title = 'Overview '.$school_name.' | Plexuss.com';

						$keywords = $school_name;

						if(isset($tmp)){
							$c = 0;
							foreach ($tmp as $k => $v) {
								if ($c >=2) {
									break;

								}
								if ($v != '') {
									$keywords = $keywords. ', '. $v;
									$c++;
								}
							}
						}

						$description = 'Want to attend '.$school_name.'? Find out how '.$alias.' acceptance rate impacts you and get recruited by the college of your dream today!';

						break;
					
					case 5:
						$page_title = 'Overview '.$school_name.' | Plexuss.com';

						$keywords = $school_name;

						if(isset($tmp)){
							$c = 0;
							foreach ($tmp as $k => $v) {
								if ($c >=2) {
									break;

								}
								if ($v != '') {
									$keywords = $keywords. ', '. $v;
									$c++;
								}
							}
						}

						$description = 'Learn all about '.$school_name.' '.'('.$alias.'), including financial aid, acceptance rate, deadline, test score,  admission, tuition and campus life.';

						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = 'Overview '.$school_name.' | Plexuss.com';

				$keywords = $school_name;

				$description = 'Learn all about '.$school_name.', including financial aid, acceptance rate, deadline, test score,  admission, tuition and campus life.';
				
			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}

	/*
	* Generates the page title, meta keywords, and meta description for each of colleges-stats
	*
	*/
	private function generateStats(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			$school_name = $key->school_name;
			$alias = $key->alias;

			$college_admission = CollegeAdmission::where('college_id', '=', $key->id)->first();

			$admission_deadline = $college_admission->deadline;


			$acceptance_rate = -1;
			if($college_admission->applicants_total>0)	{		
					$acceptance_rate=($college_admission->admissions_total/($college_admission->applicants_total));
					$acceptance_rate=number_format($acceptance_rate,2).'%';
			}

			$student_body = number_format($key->undergrad_enroll_1112);

			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($admission_deadline) >0 && strlen($alias) >1 && strlen($student_body) >0 && $acceptance_rate != -1) {
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				$keywords = $school_name. " stats";

				if(isset($tmp)){
					$c = 0;
					foreach ($tmp as $k => $v) {
						if ($c >=2) {
							break;

						}
						if ($v != '') {
							$keywords = $keywords. ', '. $v." stats";
							$c++;
						}
					}
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = $school_name.' | '.$alias.' Stats | Plexuss.com';

						$description = "Find Information about ".$school_name." statistics! ".$school_name." Admission deadline is ".$admission_deadline.", has an Acceptance rate of ".$acceptance_rate.", and student body of ".$student_body.". Learn more about Stanford's graduation rate, test scores, student to faculty ratio, endowment, and more.";

						break;
					
					case 2:
						
						$page_title = $alias.' Stats | '.$school_name.' | Plexuss.com';


						$description = "Get ".$school_name." stats! ".$alias." admission deadline is ".$admission_deadline.", has an acceptance rate of ".$acceptance_rate.", and a student body of ".$student_body.". Learn about ".$alias."'s graduation rate, student to faculty ratio, endowment, and test scores.";


						break;
					
					case 3:
						$page_title = $school_name.' Stats | '.$alias.' | Plexuss.com';

						$description = "Find ".$school_name." stats! ".$alias." admission deadline is ".$admission_deadline.", has an acceptance rate of ".$acceptance_rate.", student body of ".$student_body.". Learn more about ".$alias."'s graduation rate, test scores, student to faculty ratio, endowment, and more.";

						break;
					
					case 4:
						$page_title = $school_name.' Stats | '.$alias.' | Plexuss.com';


						$description = "Find ".$school_name." stats! ".$alias." admission deadline is ".$admission_deadline.", has an acceptance rate of ".$acceptance_rate.", and a student body of ".$student_body.". Learn more about ".$alias."'s graduation rate, test scores, Student to faculty ratio, endowment.";

						break;
					
					case 5:
						$page_title = $alias.' Stats | '.$school_name.' | Plexuss.com';


						$description = "Find ".$alias." stats! ".$alias." admission deadline is ".$admission_deadline.", has an acceptance rate of ".$acceptance_rate.", and a student body of ".$student_body.". Learn more about ".$school_name."'s graduation rate, test scores, student to faculty ratio, and endowment.";

						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = $school_name.' | '.$school_name. ' Stats | Plexuss.com';


				$keywords = $school_name. " stats";

				$description= "What are my chances of getting in to ".$school_name."? Comprehensive and up-to-date stats about ".$school_name.". Find stats for ".$school_name." including admission deadline, acceptance rate, student body, tuition and much more.";

				
			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges_stats', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}

	/*
	* Generates the page title, meta keywords, and meta description for each of colleges-ranking
	*
	*/
	private function generateRanking(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			$school_name = $key->school_name;
			$alias = $key->alias;

			$college_ranking = CollegeRanking::where('college_id', '=', $key->id)->first();

			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($alias) >1 && isset($college_ranking->plexuss)) {

				$college_ranking = $college_ranking->plexuss;
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				$keywords = $school_name . " ranking";

				if(isset($tmp)){
					$c = 0;
					foreach ($tmp as $k => $v) {
						if ($c >=2) {
							break;

						}
						if ($v != '') {
							$keywords = $keywords. ', '. $v. " ranking";
							$c++;
						}
					}
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = $school_name.' Ranking | '.$alias.' | Plexuss.com';

						$description = "Want to learn more about ".$school_name." ranking? Compare and discover ".$school_name."'s Plexuss Ranking. ".$school_name." Ranked #".$college_ranking."!";

						break;
					
					case 2:

						//if we have multiple alias use different ones
						if (isset($tmp[1])) {
							$page_title = $alias.' Ranking | '.$tmp[1].' | Plexuss.com';
						}else{
							$page_title = $alias.' Ranking | '.$school_name.' | Plexuss.com';
						}

						$description = $school_name." is one of the the top universities in the US, Discover ".$alias." ranking on Plexuss and find out more about the top universities in the US.";

						break;
					
					case 3:
						$page_title = $school_name.' Ranking | '.$school_name.' | Plexuss.com';

						$description = "Find rankings of universities and institutes. Harvard University ranking is #".$college_ranking." at Plexuss. Learn More about US News, Reuters, Forbes, QS, and Shanghai rankings for ".$alias.".";

						break;
					
					case 4:
						$page_title = $school_name.' Ranking | '.$school_name.' | Plexuss.com';	

						$description = "Is ".$school_name." (".$alias.") undergraduate program right for you? ".$alias." ranking is #".$college_ranking.", see ".$school_name." ranking. Compare school rankings!";

						break;
					
					case 5:
						$page_title = $alias.' Ranking | '.$school_name.' | Plexuss.com';

						$description = $school_name." ranking is #".$college_ranking." on plexuss. Find more ranking information about UC Berkeley on our website!";

						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = $school_name.' Ranking | Plexuss.com';	

				$keywords = $school_name . " ranking";

				$description = "Use our rankings to see if ".$school_name." is a good fit for you. Discover rankings on Plexuss and Learn more about the universities and colleges in the US."; 
				
			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges_ranking', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}
	/*
	* Generates the page title, meta keywords, and meta description for each of colleges-financial aid
	*
	*/
	private function generateFinancialAid(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();
		//$colleges = College::where('id', '>=', 6457)->get();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			//dd($key);

			$school_name = $key->school_name;

			
			$alias = $key->alias;

			$college_financial_aid_table = CollegeFinancialAid::where('college_id', '=', $key->id)->first();
			$college_tuition_table = CollegeTuition::where('college_id', '=', $key->id)->first();

			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($alias) >1 && isset($college_financial_aid_table->undergrad_grant_avg_amt) && isset($college_tuition_table->tuition_avg_in_state_ftug) && isset($college_tuition_table->tuition_avg_out_state_ftug) 

				//&& $college_tuition_table->tuition_avg_in_state_ftug != 0 && $college_tuition_table->tuition_avg_out_state_ftug !=0

				) {

				$grants_awarded = $college_financial_aid_table->undergrad_grant_avg_amt;

				$in_state_tuition = "$".number_format($college_tuition_table->tuition_avg_in_state_ftug);

				$out_state_tuition = "$".number_format($college_tuition_table->tuition_avg_out_state_ftug);

				$total_expense = "$".number_format(($college_tuition_table->tuition_avg_in_state_ftug)+($college_tuition_table->books_supplies_1213)+
							   ($college_tuition_table->room_board_on_campus_1213)+($college_tuition_table->other_expenses_on_campus_1213));
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				$keywords = $school_name . " financial aid";

				if(isset($tmp)){
					$c = 0;
					foreach ($tmp as $k => $v) {
						if ($c >=2) {
							break;

						}
						if ($v != '') {
							$keywords = $keywords. ', '. $v. " financial aid";
							$c++;
						}
					}
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = $school_name.' Financial Aid | Plexuss.com';

						$description = "What's the ".$school_name." financial aid situation? For 2013-2014, ".$alias." tuition for in state students is ".$in_state_tuition.", and ".$total_expense." with tuition, room, board and other fees combined. Average grant and scholarship aid is ".$total_expense.".";


						break;
					
					case 2:

						//if we have multiple alias use different ones
						if (isset($tmp[1])) {
							$page_title = $alias.' Financial Aid | '.$tmp[1].' | Plexuss.com';
						}else{
							$page_title = $alias.' Financial Aid | '.$school_name.' | Plexuss.com';
						}

						$description = "Get ".$school_name." (".$alias.") financial aid and tuition information - fees, costs, expenses, federal, state and local grants, and student loans.";


						break;
					
					case 3:
						$page_title = $school_name.' Financial Aid | '.$school_name.' | Plexuss.com';

						$description = "What are the ".$school_name." financial aid options? Comprehensive ".$school_name." information, including in and out-of-state cost of attendance, financial aid, grant and scholarship.";


						break;
					
					case 4:
						$page_title = $school_name.' Financial Aid | '.$school_name.' | Plexuss.com';

						$description = "Check out what it actually costs to go to ".$alias.". Get information about ".$school_name." financial aid, tuition & fees, grants, scholarship, in state and out of state cost and more.";


						break;
					
					case 5:
						$page_title = $alias.' Financial Aid | '.$school_name.' | Plexuss.com';

						$description = "Find out how to pay for ".$school_name.". Get statistics on ".$alias." financial aid, scholarships, loans, and grants for students. Learn about financial aid programs on Plexuss.";


						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = $school_name.' Financial Aid | Plexuss.com';	

				$keywords = $school_name . " financial aid";

				$description = "Plexuss has the most comprehensive information about financial aid,including scholarships, grants, and loans. Get information about ".$school_name.". ";

				
			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges_financial_aid', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}

	/*
	* Generates the page title, meta keywords, and meta description for each of colleges-Admissions
	*
	*/
	private function generateAdmission(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			$school_name = $key->school_name;
			$alias = $key->alias;

			$college_admission = CollegeAdmission::where('college_id', '=', $key->id)->first();
			$applicants_total = number_format($key->applicants_total);
			$admissions_total = number_format($key->admissions_total);
			

			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($alias) >1 && isset($college_admission->deadline) && $applicants_total != 0 && $admissions_total != 0) {

				$admission_deadline = $college_admission->deadline;
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				$keywords = $school_name . " admission";

				if(isset($tmp)){
					$c = 0;
					foreach ($tmp as $k => $v) {
						if ($c >=2) {
							break;

						}
						if ($v != '') {
							$keywords = $keywords. ', '. $v. " admission";
							$c++;
						}
					}
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = $school_name.' Admission | '.$alias.' Aplication Info | Plexuss.com';

						$description = "What is your chance of getting into ".$school_name."? Discover Stanford admission, average SAT and ACT scores, acceptance rate, financial aid, and other admission data. Visit our website to learn more!";


						break;
					
					case 2:

						//if we have multiple alias use different ones
						if (isset($tmp[1])) {
							$page_title = $alias.' Admission | '.$tmp[1].' | Plexuss.com';
						}else{
							$page_title = $alias.' Admission | '.$school_name.' | Plexuss.com';
						}

						
						$description = "Find information about ".$school_name." (".$alias.") admission, application requirements, admitted & Enrolled students, test scores, male student to female student ratio and more at our website.";

						break;
					
					case 3:
						$page_title = $school_name.' Admission | '.$school_name.' | Plexuss.com';


						$description = "Comprehensive information on ".$school_name." admission rates, including admission requirements, aplication deadlines, admitted and enrolled students, male-to-female ratio and more. Learn more about ".$alias." at our website!";

						break;
					
					case 4:
						$page_title = $school_name.' Admission | '.$school_name.' | Plexuss.com';	


						$description = "Get valuable information about ".$school_name." admission rates. ".$alias." application deadline is ".$admission_deadline.", number of applicants ".$applicants_total.", number of admitted student ".$admissions_total.". Learn more about admission, test score, and student gender.";

						break;
					
					case 5:
						$page_title = $alias.' Admission | '.$school_name.' | Plexuss.com';

						$description = "Find information about ".$school_name." admission rates. ".$alias." application deadline is ".$admission_deadline.", has ".$applicants_total." number of applicants, and ".$admissions_total." number of admitted student. Learn more about ".$alias."'s admissions, and average SAT and ACT scores on our website.";

						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = $school_name.' Admission | Plexuss.com';	

				$keywords = $school_name . " admission";

				$description = "Get into the university of your dreams! Learn all about ".$school_name." admissions, application requirements, admitted & Enrolled students, test scores, and more at our website."; 

			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges_admissions', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}

	/*
	* Generates the page title, meta keywords, and meta description for each of colleges-tuition
	*
	*/
	private function generateTuition(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();
		//$colleges = College::all();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			$school_name = $key->school_name;
			$alias = $key->alias;

			$college_financial_aid_table = CollegeFinancialAid::where('college_id', '=', $key->id)->first();
			$college_tuition_table = CollegeTuition::where('college_id', '=', $key->id)->first();

			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($alias) >1 && isset($college_financial_aid_table->undergrad_grant_avg_amt) && isset($college_tuition_table->tuition_avg_in_state_ftug) && isset($college_tuition_table->tuition_avg_out_state_ftug)
			 && isset($college_tuition_table->books_supplies_1213) && isset($college_tuition_table->room_board_on_campus_1213)
				) {

				//$grants_awarded = $college_financial_aid_table->undergrad_grant_avg_amt;

				
				if ( $college_tuition_table->tuition_avg_out_state_ftug == 0 || $college_tuition_table->tuition_avg_in_state_ftug == 0 || $college_tuition_table->books_supplies_1213 == 0 || $college_tuition_table->room_board_on_campus_1213 ==0) {
					
					$page_title = $school_name.' Tuition | Plexuss.com';	

					$keywords = $school_name . " tuition";

					$description = "Find the annual ".$school_name." costs of any colleges and university in the United States. Learn more about the tuition and other expenses at Plexuss.";

					$this->updateMetaData($key->id, 'colleges_tuition', $page_title, $keywords, $description);

					continue;

				}
				
				$in_state_tuition = "$".number_format($college_tuition_table->tuition_avg_in_state_ftug);

				$out_state_tuition = "$".number_format($college_tuition_table->tuition_avg_out_state_ftug);

				$total_expense = "$".number_format(($college_tuition_table->tuition_avg_in_state_ftug)+($college_tuition_table->books_supplies_1213)+
							   ($college_tuition_table->room_board_on_campus_1213)+($college_tuition_table->other_expenses_on_campus_1213));

				$book_supplies = "$".number_format($college_tuition_table->books_supplies_1213);

				$room_board = "$".number_format($college_tuition_table->room_board_on_campus_1213);
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				$keywords = $school_name . " tuition";

				if(isset($tmp)){
					$c = 0;
					foreach ($tmp as $k => $v) {
						if ($c >=2) {
							break;

						}
						if ($v != '') {
							$keywords = $keywords. ', '. $v. " tuition";
							$c++;
						}
					}
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = $school_name.' Tuition | Plexuss.com';

						$description = "Find out ".$school_name." tuition. In-state, full-time undergraduate students at Stanford paid ".$in_state_tuition.", while out-of-state students paid ".$out_state_tuition.". Book & Supplies cost is ".$book_supplies." and Room & Board ".$room_board.".";


						break;
					
					case 2:

						//if we have multiple alias use different ones
						if (isset($tmp[1])) {
							$page_title = $alias.' Tuition | '.$tmp[1].' | Plexuss.com';
						}else{
							$page_title = $alias.' Tuition | '.$school_name.' | Plexuss.com';
						}


						$description = "Interested in applying to ".$school_name." (".$alias.")? Want to learn valuable information about ".$alias." tuition and costs? ".$alias." in state tuition is ".$in_state_tuition.", in-state full expenses ".$total_expense." and out-of-state tuition is ".$out_state_tuition.".";


						break;
					
					case 3:
						$page_title = $school_name.' Tuition | '.$school_name.' | Plexuss.com';

						$description = "What are the ".$school_name." Tuition options? Comprehensive ".$school_name." information, including in and out-of-state cost of attendance, Tuition, grant and scholarship.";


						break;
					
					case 4:
						$page_title = $school_name.' Tuition | '.$school_name.' | Plexuss.com';

						$description = "Want to learn valuable information about ".$school_name." tuition and costs? ".$alias." Has a total in state cost of ".$in_state_tuition." and in state full expenses ".$total_expense.". Book and supplies cost is ".$book_supplies." and room and board is ".$room_board.".";

						break;
					
					case 5:
						$page_title = $alias.' Tuition | '.$school_name.' | Plexuss.com';

						$description = "View ".$school_name." tuition and cost of attendance information. ".$alias." tuition for in-state students is ".$in_state_tuition." and with book & supplies and room & board it's ".$total_expense.". Learn more about the on campus, off campus and out of state expenses!";

						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = $school_name.' Tuition | Plexuss.com';	

				$keywords = $school_name . " tuition";

				$description = "Find the annual ".$school_name." costs of any colleges and university in the United States. Learn more about the tuition and other expenses at Plexuss.";

				
			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges_tuition', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}

	/*
	* Generates the page title, meta keywords, and meta description for each of colleges-enrollment
	*
	*/
	private function generateEnrollment(){

		$colleges = College::where('id', '>=', 153653)->where('verified', 1)->get();

		$keywords = '';
		$page_title ='';
		$description ='';

		$tmp = null;
		$cnt = 0;

		foreach ($colleges as $key ) {
			//dd($key->school_name);

			$school_name = $key->school_name;
			$alias = $key->alias;

			$college_enrollment_model = CollegeEnrollment::where('college_id', '=', $key->id)->first();

			$total_enrollment = number_format($college_enrollment_model->undergrad_total);
			$total_transfer = number_format($college_enrollment_model->undergrad_transfers_total);
			$full_time_attendance_status = number_format($college_enrollment_model->undergrad_full_time_total);
			$part_time_attendance_status = number_format($college_enrollment_model->undergrad_part_time_total);



			//if alias exists then rotate with 5 different variation, if not, there's a general message to go with
			if (strlen($alias) >1 && isset($full_time_attendance_status) && isset($part_time_attendance_status) &&  $full_time_attendance_status!=0 && 
				$part_time_attendance_status != 0 && $total_enrollment != 0 && $total_transfer != 0) {

				//$foreignPct = round(($college_enrollment_model->undergrad_foreign_total / $college_enrollment_model->undergrad_total) * 100);
				
				print_r('alias is =>'. $alias. "<br>");

				if (strpos($alias,'|') !== false) {
    				
    				$tmp = explode('|', $alias);

    				$alias = $tmp[0];
				}

				$keywords = $school_name . " enrollment";

				if(isset($tmp)){
					$c = 0;
					foreach ($tmp as $k => $v) {
						if ($c >=2) {
							break;

						}
						if ($v != '') {
							$keywords = $keywords. ', '. $v. " enrollment";
							$c++;
						}
					}
				}

				// Choose between the variations
				switch ($this->variation_num) {
					case 1:
						$page_title = $school_name.' Enrollment | Plexuss.com';

						$description = $school_name." enrollment total is ".$total_enrollment.", transfer enrollment of ".$total_transfer.", attendance status of ".$full_time_attendance_status." for full time and ".$part_time_attendance_status." for part time students.";


						break;
					
					case 2:

						//if we have multiple alias use different ones
						if (isset($tmp[1])) {
							$page_title = $alias.' Enrollment | '.$tmp[1].' | Plexuss.com';
						}else{
							$page_title = $alias.' Enrollment | '.$school_name.' | Plexuss.com';
						}

						$description = "The current ".$alias." enrollment is ".$total_enrollment.". ".$school_name." transfer enrollment is ".$total_transfer.", attendance status: ".$full_time_attendance_status." full time and ".$part_time_attendance_status." part time.";

						break;
					
					case 3:
						$page_title = $school_name.' Enrollment | '.$school_name.' | Plexuss.com';

						$description = "Considering going to ".$alias."? ".$school_name." enrollment total is ".$total_enrollment.", has a transfer enrollment of ".$total_transfer.", attendance status of ".$full_time_attendance_status." full time and ".$part_time_attendance_status." part time students. Find more enrollment information about ".$alias."!";


						break;
					
					case 4:
						$page_title = $school_name.' Enrollment | '.$school_name.' | Plexuss.com';	

						$description = "Is ".$alias." the right school for you? Find ".$school_name." enrollment information. ".$alias."'s total enrollment is ".$total_enrollment." with transfer enrollment of ".$total_transfer.". attendance status at ".$school_name." is composed of ".$full_time_attendance_status." full time and ".$part_time_attendance_status." part time students";


						break;
					
					case 5:
						$page_title = $alias.' Enrollment | '.$school_name.' | Plexuss.com';

						$description = "Is ".$school_name." the right place for you? Get valuable ".$alias." enrollment information. ".$alias." total enrollment is ".$total_enrollment.", transfer enrollment ".$total_transfer.", attendance status ".$full_time_attendance_status." full time and ".$part_time_attendance_status." part time.";


						break;
					
					default:
						# code...
						break;
				}
				unset($tmp);
			}else {

				$page_title = $school_name.' Enrollment | Plexuss.com';	

				$keywords = $school_name . " enrollment";

				$description = "Get into the university of your dreams! Plexuss has the most comprehensive information about ".$school_name." enrollment, total enrollment, in state and out of state student."; 

				
			}

			print_r('page title: '.$page_title. "<br>");
			print_r('keywords: '.$keywords."<br>");
			print_r('description: '. $description."<br>");
			print_r('<br><br>');

			$cnt = $cnt +1;

			if($this->variation_num == 5){

				$this->variation_num = 1;
			}else{

				$this->variation_num +=1; 
			}

			$this->updateMetaData($key->id, 'colleges_enrollment', $page_title, $keywords, $description);

			/*
			if ($cnt == 20) {
				exit();
			}
			*/


		}

	}
	

	//update database with meta data
	private function updateMetaData($college_id, $table , $page_title, $keywords, $description){

		if ($table == "colleges") {
			$cid_name = 'id';
		}else{

			$cid_name = 'college_id';
		}

		DB::table($table)
            ->where($cid_name, $college_id)
            ->update(array('page_title' => $page_title, 'meta_keywords' => $keywords, 'meta_description' => $description));


	}
}
