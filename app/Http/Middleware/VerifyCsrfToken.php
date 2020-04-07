<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    	'setRedisOnlineUser', 'removeRedisOnlineUser', 'phone/twiml', 'phone/sms/receive', 'phone/sms/callback', 'phone/makeCall', 
        'phone/recordCallBack', 'phone/callStatus', 'phone/incomingCall', 'signin', 'signup', 'forgotpassword', 'resetpassword/{token}', 'ajax/profile/uploadcenter/{token}',

        'applyNowClicked', 'adClicked', 'phone/validatePhoneNumber', 'saveMissingFields','/autoUploadLogoCollege', 'phone/conference/wait'
        
    ];
}
