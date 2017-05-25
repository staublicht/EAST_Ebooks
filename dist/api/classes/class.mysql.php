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

            $this->connection->set_charset("utf8");

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
     * @param bool $id
     * @return bool|int
     */
    public function delete( $table, $id = false )
    {

        $table = $this->connection->real_escape_string( $table );

        if( $id )
        {

            $id = $this->connection->real_escape_string( ( is_string( $id ) ) ? intval( $id ) : $id );

            $query = "DELETE FROM $table WHERE id = $id";

            $result = $this->connection->query( $query );

            $return = $this->connection->affected_rows;

            /**
             * remove associated data
             */
            switch( $table )
            {

                case 'ebooks':
                    $query = "DELETE FROM ebooks_categories_assignment WHERE ebooks_id = $id";
                    $result = $this->connection->query( $query );
                    break;

            }

            return $return;

        }

        return false;

    }

    /**
     * @param bool $table
     * @param bool $data
     * @return bool|mysqli_result
     */
    public function insert( $table, $data = false )
    {

        $table = $this->connection->real_escape_string( $table );

        if( !$data )
            return false;

        if( is_array( $data ) )
        {

            $set = [];

            $query = "SHOW COLUMNS FROM $table";

            $result = $this->connection->query( $query );

            while( $column = $result->fetch_object() )
            {

                if( array_key_exists($column->Field, $data) )
                    $set[$column->Field] = $data[$column->Field];

                if( $table == 'ebooks' )
                {

                    if( $column->Field == 'users_id_edited' )
                        $set[$column->Field] = intval( $_SESSION['user']['id'] );

                    if( $column->Field == 'date_edited' )
                        $set[$column->Field] = date("Y-m-d H:i:s");

                }

            }

            if( empty( $set ) )
                return false;

            $query = "INSERT INTO $table (";

            foreach( $set as $key => $value )
            {

                $key = $this->connection->real_escape_string( $key );
                $query .= " $key,";

            }

            $query = rtrim( $query,',' ) . ') VALUES (';

            foreach( $set as $key => $value )
            {

                $value = $this->connection->real_escape_string( $value );
                $query .= " '$value',";

            }

            $query = rtrim( $query,',' ) . ')';

            $result = $this->connection->query( $query );

            if( $result )
                return $this->connection->insert_id;

        }

        return false;

    }

    /**
     * @param $table
     * @param string $return_fields
     * @param bool $id
     * @param int $limit
     * @param int $offset
     * @return array|bool
     */
    public function select( $table, $return_fields = '*', $id = false, $limit = 10, $offset = 0 )
    {

        $table = $this->connection->real_escape_string( $table );

        if( ( $return_fields == '*' ) || ( is_array( $return_fields ) ) )
        {

            if( is_array( $return_fields ) )
            {

                $selector_array = [];

                $query = "SHOW COLUMNS FROM $table";

                $result = $this->connection->query( $query );

                while( $column = $result->fetch_object() )
                    if( in_array($column->Field, $return_fields) )
                        array_push( $selector_array, $column->Field );

                $selector_string = implode( ', ', $selector_array );

                if( $selector_string == '' )
                    return false;

            }
            else
            {
                $selector_string = $return_fields;
            }

            if( is_int( $id ) || is_string( $id ) || ( $id === false ) )
            {

                $query = "SELECT $selector_string FROM $table";

                if( !is_bool( $id ) )
                {

                    $id = $this->connection->real_escape_string( ( is_string( $id ) ) ? intval( $id ) : $id );
                    $query .= " WHERE id = $id";
                }

                if( $limit !== -1 )
                {

                    if( is_int( $limit ) )
                    {

                        if( $limit >= 0 )
                        {

                            $limit = $this->connection->real_escape_string( $limit );
                            $query .= " LIMIT $limit";

                            if( is_int( $offset ) )
                                if( $offset >= 0 )
                                {
                                    $offset = $this->connection->real_escape_string( $offset );
                                    $query .= " OFFSET $offset";
                                }

                        }

                    }

                }

                $result = $this->connection->query( $query );

                $return = [];

                while( $entry = $result->fetch_array(MYSQLI_ASSOC) )
                    array_push($return, $entry);

                return $return;

            }

            return false;

        }

        return false;

    }

    /**
     * @param bool $table
     * @param bool $id
     * @param bool $data
     * @return bool|mysqli_result
     */
    public function update( $table, $id = false, $data = false )
    {

        $table = $this->connection->real_escape_string( $table );

        if( !$id || !$data )
            return false;

        if( is_array( $data ) )
        {

            $set = [];

            $query = "SHOW COLUMNS FROM $table";

            $result = $this->connection->query( $query );

            while( $column = $result->fetch_object() )
            {

                if( array_key_exists($column->Field, $data) )
                    $set[$column->Field] = $data[$column->Field];

                if( $column->Field == 'users_id_edited' )
                    $set[$column->Field] = intval( $_SESSION['user']['id'] );

                if( $column->Field == 'date_edited' )
                    $set[$column->Field] = date("Y-m-d H:i:s");

            }

            if( empty( $set ) )
                return false;

            if( is_int( $id ) || is_string( $id ) )
            {

                $query = "UPDATE $table SET";

                foreach( $set as $key => $value )
                {

                    $key = $this->connection->real_escape_string( $key );
                    $value = $this->connection->real_escape_string( $value );
                    $query .= " $key = '$value',";

                }

                $query = rtrim( $query,',' );

                $id = $this->connection->real_escape_string( ( is_string( $id ) ) ? intval( $id ) : $id );
                $query .= " WHERE id = '$id'";

                $result = $this->connection->query( $query );

                return $result;

            }

        }

        return false;

    }

}