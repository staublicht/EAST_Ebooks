<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class api
{

    // legit key structure for incomming $_POST
    public $functions = array();
    // collection for JSON output on destruct
    private $output = array();

    function __destruct()
    {
        echo json_encode( $this->output );
    }

    /*
     * Add expected $_POST structure to whitelist
     */
    public function addFunction( $index )
    {
        array_push( $this->functions, $index );
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
     * matches whitelisted functions structure
     */
    public function isValidRequest()
    {

        $status = false;

        if( isset( $_POST['function'] ) )
        {
            $request = $_POST;
            unset( $request['function'] );
        } else {
            return false;
        }

        /*
         * Parse whitelist against request
         */
        function parse_whitelist( $request, $structure )
        {
            $status = false;

            foreach( $structure as $structureKey => $structureValue ) {

                foreach ($request as $requestKey => $requestValue) {

                    if( $requestKey == $structureKey )
                    {

                        if( in_array( false, $structure ) ) // found end = success
                        {
                            $status = true;
                        }
                        else
                        {
                            unset( $request[$requestKey] );
                            $status = parse_whitelist( $request, $structure[$structureKey] );
                        }

                    }

                }

            }

            return $status;
        }

        foreach ($this->functions as $function) {

            $status = parse_whitelist( $request, $function );
            if( $status == true )
                return true;

        }

        exit;

    }

}