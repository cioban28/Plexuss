<?php

namespace App\Http\Controllers;

use App\EmailSuppressionList;
use Request;
use App\User, App\PlexussAdmin, App\Recruitment, App\College;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    // New battle functionality for page load AND ajax calls.
	public function comparison(){

		$type = Request::get('type');
		$UrlSlugs=Request::get('UrlSlugs');
		$remove_ele = Request::get('remove_ele');
		$typefor = Request::get('typefor');
		$college_id= Request::get('college_id');

		//echo $UrlSlugs;die;
		// $UrlSlugs = 'auburn-university,concordia-college-alabama,faulkner-university,birmingham-southern-college';
		$schoolSlugArray = array_map('trim', explode(',', $UrlSlugs));
		//Template base arrays
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();



		$data['title'] = 'Plexuss School Comparison';
		$data['currentPage'] = 'comparison';
		$src="/images/profile/default.png";
		$data['isInUserList'] = 0;

		if (Auth::check()){
		   //Get user logged in info.
			$user = User::find( Auth::user()->id );

			//Set admin array for topnav Link
			$admin = PlexussAdmin::where('user_id', '=', $user->id)->get()->toArray();
			$admin = array_shift($admin);
			$data['admin'] = $admin;


			if($user->profile_img_loc!=""){
				$src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$user->profile_img_loc;
			}

            /* check user in email suppression list */
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




			// check the user if school is in user list to initiate Get Recruited or Already recruited

			$recruitment = Recruitment::where('user_id', '=', $user->id)
				->where('status', '=', '1')
				->get();


		} else {

		}

		$data['profile_img_loc'] = $src;
		$logoPath = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/";
		$defaultLogo = '/images/no_photo.jpg';

		$data['collegeData']=array();

		$college=new College();
		$collegeData = $college->collegeInfo($schoolSlugArray);  //DB CALL 1
		if(!empty($collegeData))
		{
			$data['collegeData']=$collegeData;
			$data['addschool']=true;
		}


		if(isset($recruitment)){

			$recritment_college_id = array();

			foreach ($recruitment as $k) {
				array_push($recritment_college_id, $k->college_id);
			}

			foreach ($data['collegeData'] as $key => $value) {


				if(in_array($data['collegeData'][$key]->id, $recritment_college_id)){

					//dd($data['collegeData'][$key]->id);

					$data['collegeData'][$key]->isInUserList=1;
				}else{
					$data['collegeData'][$key]->isInUserList =0;
				}

			}
			/////////////////////////////////////////////////////////

		}else{

			foreach ($data['collegeData'] as $key => $value) {

				$data['collegeData'][$key]->isInUserList =0;
			}

		}





		$startloop='0';

		// check data count when $collegeData is not null


		if(isset($type) && $type=='Ajaxcall')
		{
			if(isset($remove_ele) && $remove_ele=='true')
			{
				for($i=$startloop;$i<1;$i++)
				{
					$data['collegeData'][$i]='';
					$data['collegeData'][$i] = (object)$data['collegeData'][$i];
				}
			}

			if($typefor=='json')
			{
				$college = College::where('id', '=',$college_id)->first()->toArray();
				$return_array=array();

				if($college['logo_url']!='')
				{
					$logo_img=$logoPath.$college['logo_url'].'';
				}
				else
				{
					$logo_img=$defaultLogo;
				}
				$return_array['logo_url']=$logo_img;
				$return_array['slug']=$college['slug'];
				$return_array['school_name']=$college['school_name'];
				return response()->json([$return_array]);
			}

			else
			{
				return View('private.battle.comparisonColumn', $data);
			}
		}
		else
	  {
			if(count($data['collegeData'])<3)
			{

				if(count($collegeData)==1)
					{$startloop=1;}
				elseif(count($collegeData)==2)
					{$startloop=2;}

				for($i=$startloop;$i<3;$i++)
				{
					$data['collegeData'][$i]='';
					$data['collegeData'][$i] = (object)$data['collegeData'][$i];
				}
			}

			$quizs = new QuizController();
			$data['quizInfo'] = $quizs->LoadQuiz();

			$data['right_handside_carousel'] = $this->getRightHandSide($data);
			return View('private.battle.comparison', $data);
		}

	}
}
