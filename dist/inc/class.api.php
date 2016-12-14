<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

class api
{

    // expectable JSON objects
    public $actions = NULL;
    // collection for JSON output on destruct
    private $output = array();

    function __destruct()
    {
        echo json_encode( $this->output );
    }

    /*
     * Add expected $_POST structure to whitelist
     */
    public function addFunction( $actions )
    {
        foreach( $actions as $key => $value )
        {
            $this->actions->$key = $value;
        }
    }

    /*
     * Add array $output to JSON output
     */
    public function addOutput( $output = array() )
    {
        array_push( $this->output, $output );
    }

    /*
     * Check if incomming $_POST request
     * matches whitelisted actions structure
     */
    public function sanitize( $input = false )
    {

        function whitelist( $input, $structure )
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

                            $result->$inputKey = whitelist( $inputValue, $structureValue );

                        }
                        else if( is_string( $inputValue ) || is_bool( $inputValue ) )
                        {

                            $result->$inputKey = $inputValue;

                        }
                        else
                        {

                            $result = false;

                        }

                    }
                }
            }

            return $result;

        }

        $result = whitelist( $input, $this->actions );
            
        return $result;

    }

}