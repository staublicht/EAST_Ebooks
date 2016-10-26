<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class mysql
{

    private $connection;

    function __construct()
    {

        $connection = new mysqli ( mysqli_host, mysqli_username, mysqli_passwd, mysqli_dbname, mysqli_port, mysqli_socket );

        // if connection failed
        if ( $connection->connect_errno )
        {

            die( 'DB Connection failed' );

        } else {

            // success
        }

    }

}