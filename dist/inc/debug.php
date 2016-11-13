<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

error_reporting( E_ALL );

function console_log( $data )
{

    if( debug === true )
    {
        echo '<script>';
        echo 'console.log(' . json_encode( $data ) . ')';
        echo '</script>';
    }

}