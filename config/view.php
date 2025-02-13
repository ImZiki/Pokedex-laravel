<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most Laravel applications store their view files in the `resources/views`
    | directory. You may change the location of this path if you like. For
    | example, you may store all of your views in a different disk or cloud.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, you may change this if you like.
    |
    */

    'compiled' => storage_path('framework/views'),

];
