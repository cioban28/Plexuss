<?php

namespace App\Http\Controllers;

use Request, DB, Queue;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

use App\Console\Commands\PasswordResetQueue;

class ResetPasswordController extends Controller
{
    /**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind(){
		return View('public.registration.forgot');
	}


    public function postPasswordResetQueue($email = NULL) {
        if (!isset($email)) { return 'failed, no email'; }

        Queue::push(new PasswordResetQueue($email));

        return 'success';
    }

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind($email = NULL){
        // validates and sends email to provided email with a link to a page to input new password
        if (isset($email)) {
            $response = Password::sendResetLink(['email' => $email]);
        } else {
		    $response = Password::sendResetLink(Request::only('email'));
        }

		switch ( $response ){
			case Password::INVALID_USER:
				$e = array('Cant find that email in the system.');
				return redirect()->back()->withErrors($e);

			case Password::RESET_LINK_SENT:
				return redirect()->back()->with('message', 'Please check your email for a reset instructions.<br/>It may be in your spam folder.');
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null){
		// if token is null, return 404 page
		if( is_null($token) ) App::abort(404);

		// if token set, return view with token
		return View('public.registration.reset')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Request::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		Password::validator(function($credentials){
		    return preg_match( "/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/", $credentials['password']);
		});

		$response = Password::reset($credentials, function($user, $password){
			$user->password = Hash::make($password);
			$user->save();
		});

		switch ($response){

			case Password::INVALID_PASSWORD:
				return "INVALID_PASSWORD";

			case Password::INVALID_TOKEN:
				return 'The token is not valid anymore. Please request a new one using forgot password form.';

			case Password::INVALID_USER:
				return redirect()->back()->with('error', Lang::get($response));

			case Password::PASSWORD_RESET:
				$status = array('status' => 'Your password has been reset. Please Log into Plexuss.');
				return redirect()->to('/signin')->withErrors($status);

		}
	}
}
