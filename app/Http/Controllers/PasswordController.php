<?php

namespace App\Http\Controllers;

use Hash, Request, Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use App\User;

class PasswordController extends Controller
{
    
    use ResetsPasswords;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getResetAuthenticatedView(Request $request)
    {
        return view('auth.passwords.reset-auth');
    }

    /**
     * Reset password for logged in users.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetAuthenticated(){	

        $ret = array();
    	$input = Request::all();
    	$credentials = array( 'email' => Request::get( 'email' ), 'password' => Request::get( 'oldpassword' ) );

    	$rules = array( 'email' => 'required', 'oldpassword' => array( 'required', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/' ),
    				    'password' => array( 'required', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/' ),
    				    'confirmed_password' => array( 'required', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/' ) );// "/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/"

    	if ($input['password'] != $input['confirmed_password']) {
    		$ret['status'] = "failed";
    		$ret['error_log'] = "Password and confirmed password do not match";

    		return json_encode($ret);
    	}

		$v = Validator::make( $input, $rules );

		if ( $v->passes() ) {
	    	if ( Auth::attempt( $credentials, true ) ) {
	    		$user = User::find(Auth::user()->id);

	    		$user->password = Hash::make($input['password']);
	    		$user->save();

	    		$ret['status'] = "success";

	    	}else{
	    		$ret['status'] = "failed";
    			$ret['error_log'] = "Inavlid email, or old pass";
	    	}
	    }else{
	    	$ret['status'] = "failed";
    		$ret['error_log'] = "Inavlid email, old pass, or password";
	    }

    	return json_encode($ret);
    }
}