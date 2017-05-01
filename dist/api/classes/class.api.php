<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/**
 * Class api
 */
class api
{

    /*
     * expectable JSON objects
     */
    public $actions = NULL;

    /*
     * array collection for JSON output on destruct
     */
    private $output = array();

    function __destruct()
    {
        echo json_encode( $this->output );
    }

    /**
     * Add expected $_POST structure to whitelist
     * @param $actions
     */
    public function addFunction( $actions )
    {
        foreach( $actions as $key => $value )
        {
            $this->actions->$key = $value;
        }
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

        $return = $this->input_get_default( $request, $this->actions, $data );

        foreach( $data as $key => $value )
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

                    if( $requestValue == $object )
                        foreach( $structureValue as $key => $value )
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
     * Check if incomming $_POST request
     * matches whitelisted actions structure
     * @param bool $input
     * @return bool
     */
    public function sanitize( $input = false )
    {

        $result = $this->sanitize_get_whitelist( json_decode( $input ), $this->actions );

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
                            $result->$inputKey = (array) $inputValue;
                        else
                            $result->$inputKey = $this->sanitize_get_whitelist( $inputValue, $structureValue );

                    }

                    else if( is_string( $inputValue ) || is_int( $inputValue ) || is_bool( $inputValue ) || is_array( $inputValue ) )
                        $result->$inputKey = $inputValue;

                    else
                        $result = false;

                }
            }
        }

        return $result;

    }

}