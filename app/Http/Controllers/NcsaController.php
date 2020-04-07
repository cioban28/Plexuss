<?php

namespace App\Http\Controllers;

use Request, Session, DB, Validator, AWS, DateTime;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

use App\PlexussAdmin; 
use App\User;
use App\College;

class NcsaController extends Controller
{
	public function index(){
		//Get user logged in info and ajaxtoken.
		if (Auth::check()){
			$user = User::find( Auth::user()->id );
		}

		// $token = $user->ajaxtoken->toArray();
		//Build to $data array to pass to view.
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss NCSA Page';

		// Needed for TopNav
		$data['currentPage'] = 'ncsa';

		// needed for autofill
		// $data['fname']
		// $data['lname']
		// $data['phone']
		// $data['email']
		// $data['zip'] 


		return View('ncsa.info', $data);
	}
}