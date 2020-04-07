<?php

return [

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => '\\OAuth\\Common\\Storage\\Session',

	/**
	 * Consumers
	 */
	'consumers' => [

		'Facebook' => [
            'client_id'     => '858655780878212', 
            'client_secret' => 'f89091494c74ea3a66ec406d206f98a5',
            'scope'         => ['user_friends', 'user_status','email'],
        ],	
        
        'Google' => [
		    'client_id'     => env('GOOGLE_ID'),
		    'client_secret' => env('GOOGLE_SECRET'),
		    'scope'         => ['contact', 'userinfo_email', 'userinfo_profile'],
		],  

		'Yahoo' => [
            'client_id'     => env('YAHOO_ID'),
            'client_secret' => env('YAHOO_SECRET'), 
		],  			

		'Microsoft' => [
            'client_id'     => env('MICROSOFT_ID'),
            'client_secret' => env('MICROSOFT_SECRET'),
            'scope'         => ['basic', 'contacts_emails', 'contacts_photos'], 
		],

		'Linkedin' => [
			'client_id'  	=> '86meni1wkohd2e',
            'client_secret' => 'pRYZVCPO2i5xn3KB',
		],

	]

];
