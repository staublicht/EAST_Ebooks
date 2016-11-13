<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class mysql
{

    private $connection;

    function __construct()
    {

        $this->connection = new mysqli ( mysqli_host, mysqli_username, mysqli_passwd, mysqli_dbname, mysqli_port, mysqli_socket );

        // if connection failed
        if ( $this->connection->connect_errno )
        {

            die( 'DB Connection failed' );

        } else {
            // success
        }

    }

    public function select( $table, $id, $selector = '*' )
    {

        $query = "SELECT $selector FROM $table WHERE id = $id";

        $result = $this->connection->query( $this->connection->real_escape_string( $query ) );

        return $result->fetch_array(MYSQLI_ASSOC);

    }

}