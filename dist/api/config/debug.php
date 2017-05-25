<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

error_reporting( E_ALL );

mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );

/*
 * for debugging purposes
 * get input from file
 */
if( isset( $_GET['debug'] ) )
    if( is_file( 'config/input.php' ) )
    {

        echo '<pre>';
        require_once( 'config/input.php' );

    }

function console_log( $data )
{

    if( debug === true )
    {
        echo '<script>';
        echo 'console.log(' . json_encode( $data ) . ')';
        echo '</script>';
    }

}