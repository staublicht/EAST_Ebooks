<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

// load configuration
require_once ( 'config.php' );

// load debug functions
require_once ( 'inc/debug.php' );

// require classes
require_once ( 'inc/class.api.php' );
require_once ( 'inc/class.mysql.php' );
require_once ( 'inc/class.session.php' );

// initialize api class
$api = new api;

// initialize mysql connection
$mysql = new mysql;

// initialize session
$session = new session;
