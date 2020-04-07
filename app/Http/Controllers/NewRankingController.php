<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request, DB, Session, Validator;
use Illuminate\Support\Facades\Auth;
use App\PlexussAdmin, App\User, App\Search;
use App\Http\Controllers\QuizController;

class NewRankingController extends Controller {
    
    public function index() {
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();
		
		$data['title']='Plexuss Ranking';
		$data['currentPage'] = 'ranking';

		$data['profile_perc'] = 0;
		
		$src="/images/profile/default.png";

		if (Auth::check()) {
			$user = User::find( Auth::user()->id );

			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;

			$data['profile_perc'] = $user->profile_percent;

			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
		}

        $emailSuppressionList = EmailSuppressionList::where('uid', $user->id)->first();

        if (isset($emailSuppressionList)) {
            if ($emailSuppressionList['uid'] != '') {
                array_push( $data['alerts'],
                    array(
                        'type' => 'hard',
                        'dur' => '10000',
                        'msg' => '<span class=\"pls-confirm-msg subcribe-msg\">Oops, seems like you are on our unsubscribe list. In order to get the best experience from Plexuss,</span> <span id=\"'.$emailSuppressionList['uid'].'\" class=\"subscribe-now\">Subscribe Now</span> <div class=\"loader loader-hidden\"></div>'
                    )
                );
            }
        }

		if ( !$user->email_confirmed ) {
			array_push( $data['alerts'], 
				array(
					'img' => '/images/topAlert/envelope.png',
					'type' => 'hard',
					'dur' => '10000',
					'msg' => '<span class=\"pls-confirm-msg\">Please confirm your email to gain full access to Plexuss tools.</span> <span class=\"go-to-email\" data-email-domain=\"'.$data['email_provider'].'\"><span class=\"hide-for-small-only\">Go to</span><span class=\"show-for-small-only\">Confirm</span> Email</span> <span> | <span class=\"resend-email\">Resend <span class=\"hide-for-small-only\">Email</span></span></span> <span> | <span class=\"change-email\" data-current-email=\"'.$data['email'].'\">Change email <span class=\"hide-for-small-only\">address</span></span></span>'
				)
			);
		}
			
		}
		
		$data['profile_img_loc'] = $src;
		$RankingData=DB::select(DB::raw("select colleges_ranking.plexuss,colleges_ranking.College,colleges.city,colleges.state,colleges.slug from colleges_ranking 
		join colleges on (colleges_ranking.college_id=colleges.id)
		order by -plexuss desc
		limit 3"));
		$data['RankingData']=$RankingData;
		
		$catData=DB::connection('rds1')->table('lists')->take(3)->get();
		$data['catData']=$catData;
		
		$quizs = new QuizController();
		$data['quizInfo'] = $quizs->LoadQuiz();

		$data['right_handside_carousel'] = $this->getRightHandSide($data);
		
		return response()->json($data);
	}
}