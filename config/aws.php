<?php

use Aws\Laravel\AwsServiceProvider;

return [

    
    // |--------------------------------------------------------------------------
    // | AWS SDK Configuration
    // |--------------------------------------------------------------------------
    // |
    // | The configuration options set in this file will be passed directly to the
    // | `Aws\Sdk` object, from which all client objects are created. The minimum
    // | required options are declared here, but the full set of possible options
    // | are documented at:
    // | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    // |
    

    'region' => 'us-west-2',
    'version' => 'latest',
    // 'ua_append' => [
    //     'L5MOD/' . AwsServiceProvider::VERSION,
    // ],
    'credentials' => [
        'key'    => 'AKIAJGLTNZSIBBGTBFKQ',
        'secret' => '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',
    ],
];
// return [
//     'credentials' => [
//         'key'    => 'AKIAJGLTNZSIBBGTBFKQ',
//         'secret' => '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',
//     ],
//     'region' => 'us-west-2',
//     'version' => 'latest',
    
//     // You can override settings for specific services
//     'Ses' => [
//         'region' => 'us-east-1',
//     ],
// ];
