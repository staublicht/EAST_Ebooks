<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

// load configuration
require_once ( 'config.php' );

// load debug functions
require_once ( 'inc/debug.php' );

// require classes
require_once ( 'inc/class.api.php' );

// open database connection as global object
$GLOBALS['db'] = new mysqli ( mysqli_host, mysqli_username, mysqli_passwd, mysqli_dbname, mysqli_port, mysqli_socket );

// if connection failed
if ( $GLOBALS['db']->connect_errno )
{

    die( 'DB Connection failed' );

} else {

    // success
}

if( session_status() == PHP_SESSION_NONE )
{

    session_start();

}
