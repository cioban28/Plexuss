<?php

namespace App\Http\Controllers;

use Request, Validator;
use Illuminate\Support\Facades\Mail;


class BetaSignupController extends Controller
{
	public function submitBetaForm() {
		$input = Request::all();
		$v = Validator::make( $input, BetaUser::$rules );

		if ( $v->passes() ) {
			$name = Request::get( 'name' );
			$email = Request::get( 'email' );
			$confirmation = str_random(20);

			$user = new BetaUser;
			$user->name = $name;
			$user->email = $email;
			$user->highschool_id = Request::get( 'response' );
			$user->phone = Request::get( 'phone' );
			$user->schoolname = Request::get( 'school' );
			$user->usertype = Request::get( 'type' );
			$user->confirmation = $confirmation;
			$user->save();

			$this->sendconfirmationEmail( $name, $email, $confirmation );

			return redirect( 'thankyou' );
		}

		return redirect( '/#form' )->withErrors( $v )->withInput();
	}

	public function confirmEmail( $confirmation ) {
		$betauser = BetaUser::where('confirmation', '=', $confirmation)->first();

		if($betauser)
		{
			if ($betauser->confirmed == 0) 
			{
				$name = $betauser->name;
				$email = $betauser->email;
				$betauser->confirmed = 1;
				$betauser->save();
				$this->thankyouEmail( $name, $email);
				return View('betapublic.formresponse.thankYouForConfirmingEmail');
			} 
			else 
			{
				return 'It looks like you have all ready confirmed your email.';
			}
		}
		else
		{
			return "Sorry this user key was not found.";
		}
	}

	/*
	*
	*Private methods below here.
	*/
	
	private function sendconfirmationEmail( $name, $emailaddress, $confirmation ) {
		$data = array( 'confirmation' => $confirmation, 'name'=> $name );
		Mail::send( 'emails.betaThanks', $data, function( $message )use ( $emailaddress, $name ) {
				$message->to( $emailaddress, $name )->subject( 'Thank you for signing up for Beta!' );
			}
		);
	}

	private function thankyouEmail( $name, $emailaddress ) {
		$data = array('name'=> $name );
		Mail::send( 'emails.betaThanksConfirmation', $data, function( $message )use ( $emailaddress, $name ) {
				$message->to( $emailaddress, $name )->subject( 'Welcome to Plexuss!' );
			}
		);
	}
}
