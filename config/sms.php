<?php return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('SMS_DRIVER', 'sns'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'providers' => [

        'sns' => [
            'driver' => 'sns',
            'region' => env('AWS_REGION', 'us-east-1'),
            'version'=> 'latest',
            'sender_id' => 'testSend'
        ],
    ]
];
