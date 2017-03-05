<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/*
 * if possible get input from file
 */
if( is_file( './input.php' ) )
    require_once( './input.php' );

/*
 * load configuration
 */
$config = require_once( 'config.php' );
if( !$config )
    die( 'Configuration not found' );


/*
 * load debug functions
 */
require_once( 'inc/debug.php' );


/*
 * load and initialize api class
 */
require_once( 'inc/class.api.php' );
$api = new api;


/*
 * load and initialize mysql connection
 */
require_once( 'inc/class.mysql.php' );
$mysql = new mysql;


/*
 * load and initialize session
 */
require_once( 'inc/class.session.php' );
$session = new session;
