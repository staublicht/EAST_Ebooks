<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/**
 * Class api
 */
class api
{

    /*
     * registered API methods
     */
    public $methods = null;

    /*
     * array collection for JSON output
     */
    private $output = array();

    /*
     * mysql object
     */
    private $mysql = null;

    /**
     * api constructor.
     * @param $mysql
     */
    function __construct( $mysql )
    {

        $this->mysql = $mysql;
        $this->methods = new stdClass();

    }

    /**
     * api destructor
     */
    function __destruct()
    {

        echo json_encode( $this->output );

    }

    /**
     * Add array $output to JSON output
     * @param array $output
     */
    public function addOutput( $output = array() )
    {
        array_push( $this->output, $output );
    }

    /**
     * @param bool $request
     * @param array|bool $data
     * @return array|bool
     */
    public function input( $request = false, $data = false )
    {

        if( !$request || !$data )
            return false;

        $return = $this->input_get_default( $request, $this->methods, $data );

        foreach( (array) $data as $key => $value )
            $return[$key] = $value;

        return $return;

    }

    /**
     * @param $request
     * @param $structure
     * @param $object
     * @return array
     */
    private function input_get_default( $request, $structure, $object )
    {

        $return = [];

        foreach( $structure as $structureKey => $structureValue )
        {
            foreach( $request as $requestKey => $requestValue )
            {
                if( $structureKey == $requestKey)
                {

                    if( $requestValue === $object )
                        foreach( (array) $structureValue as $key => $value )
                            $return[$key] = false;

                    if( is_object( $requestValue ) )
                        if( !empty( $input = $this->input_get_default( $requestValue, $structureValue, $object ) ) )
                            $return = array_merge( $return, $input);

                }
            }
        }

        return $return;

    }

    /**
     * Add expected $_POST structure to whitelist
     * @param $methods array|bool
     * @param $recursive bool indicates, if function is called recursively
     * @return object|bool
     */
    public function register( $methods = false, $recursive = false )
    {

        if( !$methods )
            return false;

        $object = new stdClass();

        foreach( $methods as $key => $value )
        {
            if( strlen( $key ) )
            {

                if( is_array( $value ) )
                    $object->$key = $this->register( $value, true );
                else
                    $object->$value = null;

            }
        }

        if( !$recursive )
            foreach( $object as $key => $obj )
                $this->methods->$key = $obj;

        return $object;

    }

    /**
     * Check if incomming $_POST requests
     * matches whitelisted methods
     * @param bool|object $input
     * @return bool|object
     */
    public function sanitize( $input = false )
    {

        $result = $this->sanitize_get_whitelist( json_decode( $input ), $this->methods );

        return $result;

    }

    /**
     * @param $input
     * @param $structure
     * @return bool
     */
    private function sanitize_get_whitelist( $input, $structure )
    {

        $result = false;

        foreach( $structure as $structureKey => $structureValue )
        {
            foreach ($input as $inputKey => $inputValue)
            {
                if( $inputKey == $structureKey )
                {

                    if( is_object( $inputValue ) )
                    {

                        if( $inputKey == 'data' ) // keep input 'data' object alive
                        {
                            if(!is_object($result)) $result = new stdClass();
                            $result->$inputKey = (array) $inputValue;
                        }
                        else
                        {
                            if(!is_object($result)) $result = new stdClass();
                            $result->$inputKey = $this->sanitize_get_whitelist( $inputValue, $structureValue );
                        }

                    }

                    else if( is_string( $inputValue ) || is_int( $inputValue ) || is_bool( $inputValue ) || is_array( $inputValue ) )
                    {
                        if(!is_object($result)) $result = new stdClass();
                        $result->$inputKey = $inputValue;
                    }

                    else
                        $result = false;

                }
            }
        }

        return $result;

    }

}