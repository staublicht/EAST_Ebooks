<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class session
{

    public $status;

    function __construct()
    {

        $this->status = false;

        if( session_status() == PHP_SESSION_NONE )
        {
            session_start();
        }

    }

}