<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

error_reporting( E_ERROR );

mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );

function console_log( $data )
{

    if( debug === true )
    {
        echo '<script>';
        echo 'console.log(' . json_encode( $data ) . ')';
        echo '</script>';
    }

}