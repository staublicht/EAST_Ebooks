<?php

function console_log( $data )
{

    if( debug === true )
    {
        echo '<script>';
        echo 'console.log(' . json_encode( $data ) . ')';
        echo '</script>';
    }

}