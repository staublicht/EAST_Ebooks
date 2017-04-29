<?php define( 'master', true );

require('init.php');

require('config/register.php');


/*
 * validate JSON input
 */
if( isset( $_POST['request'] ) )
    $request = $api->sanitize( json_decode( $_POST['request'] ) );


/*
 * handle session tasks
 */
if( isset( $request->session ) )
{

    if( isset( $request->session->login ) )
    {
        $username = $request->session->login->username;
        $password = $request->session->login->password;
        $users = $mysql->selectUsers();
        $mysql->login( $session->login( $users, $username, $password ) );
    }

    if( isset( $request->session->logout ) )
    {
        $mysql->logout( $session->logout() );
    }

}


/*
 * handle account tasks
 */
if( isset( $request->account ) )
{

    if( isset( $request->account->id ) )
    {

    }

}


$api->addOutput( array( 'session' => $session->status ) );
