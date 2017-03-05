<?php
/**
 * fixed input data
 * for testing purposes
 * clear / remove file on production site
 */

/**

$input = array(
    'session' => array(
        'login' => array(
            'username' => '',
            'password' => ''
        )
    )
);

$_POST['request'] = json_encode( $input );
