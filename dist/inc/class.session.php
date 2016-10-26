<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class session
{

    function __construct()
    {

        if( session_status() == PHP_SESSION_NONE )
        {
            session_start();
        }

    }

}