<?php

namespace App\Http\Controllers;

use Request, DB;
use App\User;

class ResetUserController extends Controller
{
    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{	
		$user = User::all();
		return  View('public.reset.showresetlist', array('users'=> $user)   );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = User::find(Request::get('userid'));

		$user->confirmtoken()->delete();
		// $user->ajaxtoken()->delete();
		$user->educations()->delete();
		$user->collegelists()->delete();
		$user->educations()->delete();
		$user->objective()->delete();
		DB::table('password_reminders')->where('email', '=', $user->email )->delete();

		$user->delete();
		return redirect('/');
	}
}
