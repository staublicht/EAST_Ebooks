<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/*
 * load configuration
 */
$config = require_once('config/config.php');
if( !$config )
    die( 'Configuration not found' );

/*
 * load debug functions
 */
require_once('config/debug.php');


/*
 * load and initialize api class
 */
require_once('classes/class.api.php');
$api = new api;


/*
 * load and initialize mysql connection
 */
require_once('classes/class.mysql.php');
$mysql = new mysql;


/*
 * load and initialize session
 */
require_once('classes/class.session.php');
$session = new session;


/*
 * load and initialize files class
 */
require_once('classes/class.files.php');
$files = new files( $mysql );
