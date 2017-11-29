<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/**
 * register API methods
 */
$api->register([

    'session' => [
        'login' => [
            'username',
            'password',
        ],
        'logout',
    ],

    'account' => [
        'delete' => [
            'id',
        ],
        'get' => [
            'id',
            'username',
        ],
        'post' => [
            'id',
        ],
        'put' => [
            'id',
        ],
    ],

    'ebooks' => [
        'delete' => [
            'id',
        ],
        'get' => [
            'id',
            'limit',
            'offset',
            'return_fields',
        ],
        'post' => [
            'data',
        ],
        'put' => [
            'id',
            'data',
        ],
    ],

]);
