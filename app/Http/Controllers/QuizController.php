<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class QuizController extends Controller
{
    //This should return an array with a quizes in random order
	public function LoadQuiz($count=null){
		
		$Quizs2 = DB::raw("
			SELECT 
		 		Q.id,
		    	Q.active,
		    	Q.question,
		 		Q.answers,
		 		Q.image,
		 		Q.image_title,
		 		Q.page_link,
		 		Q.blurb,
		 		(select count(*) from quizzes_1_results where `quizzes_1_results`.`is_correct` and  quizzes_1_results.quizzes_1_id = Q.id) AS PassTotal,
		 		( select count(*) from quizzes_1_results where quizzes_1_results.quizzes_1_id = Q.id) AS TotalCount,
				(CASE 
				 	WHEN ( select count(*) from quizzes_1_results where quizzes_1_results.quizzes_1_id = Q.id) != 0 THEN
				  	CONCAT( round(( (select count(*) from quizzes_1_results where `quizzes_1_results`.`is_correct` and  quizzes_1_results.quizzes_1_id = Q.id) / ( select count(*) from quizzes_1_results where quizzes_1_results.quizzes_1_id = Q.id) )* 100), '%')
				ELSE
					'0%'
				 END) as percent
			FROM
	    		plexuss.quizzes_1 as Q
			Where 
			Q.active = 1;
		");

		$results = DB::select( $Quizs2 );

		shuffle($results);

		foreach ($results as $key => $q) {
			$results[$key]->answers = json_decode($q->answers);
			$results[$key]->percentfail = number_format(((float)(100) - (float)($q->percent))) . "%";
		}

		return $results;
	}
}
