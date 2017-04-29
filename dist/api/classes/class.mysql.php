<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/**
 * Class mysql
 */
class mysql
{

    /*
     * mysql connection stream
     */
    private $connection;

    /**
     * mysql constructor.
     */
    function __construct()
    {

        $this->connection = new mysqli( mysqli_host, mysqli_username, mysqli_passwd, mysqli_dbname, mysqli_port, mysqli_socket );

        /*
         * if connection failed
         */
        if( $this->connection->connect_errno )
        {

            die( 'DB Connection failed' );

        } else {
            /*
             * success
             */
        }

    }

    /**
     * @param bool $user
     */
    public function login( $user = false )
    {

        if( $user )
        {

            $statement = $this->connection->prepare( "UPDATE users SET session_id = ? WHERE id = ?" );

            $statement->bind_param("si", session_id(), $user);

            $result = $statement->execute();

        }

    }

    /**
     * @param bool $user
     */
    public function logout( $user = false )
    {

        if( $user )
        {

            $statement = $this->connection->prepare( "UPDATE users SET session_id = '' WHERE id = ?" );

            $statement->bind_param("i", $user);

            $result = $statement->execute();

        }

    }

    /**
     * @param $table
     * @param $id
     * @param string $selector
     * @return bool|mysqli_result
     */
    public function select( $table, $id, $selector = '*' )
    {

        $query = "SELECT $selector FROM $table WHERE id = $id";

        $result = $this->connection->query( $this->connection->real_escape_string( $query ) );

        return $result;

    }

    /**
     * @return bool|mysqli_result
     */
    public function selectUsers()
    {

        $query = "SELECT * FROM users";

        $result = $this->connection->query( $this->connection->real_escape_string( $query ) );

        return $result;

    }

}