<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class api
{

    private $secureIndex = array();

    public function secureIndexAdd( $index )
    {

        array_push( $secureIndex, $index );

    }

}