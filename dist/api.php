<?php define ( 'master', true );

require ( 'init.php' );

//print_r( $_POST );
//print_r( $_SESSION );

// test output
if( isset ( $_POST['action'] ) )
{

    echo json_encode( array( 'session' => $session->status ) );

}
else
{
    //
}