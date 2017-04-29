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
     * @param string $return_fields
     * @return bool|mysqli_result
     */
    public function select( $table, $return_fields = '*', $id = false, $limit = 10, $offset = 0, $sort = 'desc' )
    {

        if( $return_fields == '*' )
            $selector_string = '*';

        else if( is_array( $return_fields ) )
        {

            $selector_array = [];

            $query = "SHOW COLUMNS FROM $table";

            $result = $this->connection->query( $this->connection->real_escape_string( $query ) );

            while( $data = $result->fetch_object() )
                if( in_array($data->Field, $return_fields) )
                    array_push( $selector_array, $data->Field );

            $selector_string = implode( ', ', $selector_array );

            if( $selector_string == '' )
            {
                $selector_string = 'null';
                $limit = 0;
            }

        }

        else
        {
            $selector_string = 'null';
            $limit = 0;
        }


        $query = "SELECT $selector_string FROM $table";

        if( is_int( $id ) )
            $query .= " WHERE id = $id";

        if( $limit !== -1 )
        {

            if( is_int( $limit ) )
                if( $limit >= 0 )
                    $query .= " LIMIT $limit";

            if( is_int( $offset ) )
                if( $offset >= 0 )
                    $query .= " OFFSET $offset";

        }

        $result = $this->connection->query( $this->connection->real_escape_string( $query ) );

        return $result;

    }

}