<?php define( 'master', true );

require('init.php');

require('config/register.php');


/*
 * validate JSON input
 */
if( isset( $_POST ) )
    $request = $api->sanitize( $_POST['request'] );


/*
 * handle session tasks
 */
if( isset( $request->session ) )
{

    if( isset( $request->session->login ) )
    {

        $username = $request->session->login->username;
        $password = $request->session->login->password;
        $users = $mysql->select( 'users', '*', false, false, false );

        $mysql->login( $session->login( $users, $username, $password ) );

    }

    if( isset( $request->session->logout ) )
    {
        $mysql->logout( $session->logout() );
    }

}


/*
 * handle ebook data tasks
 */
if( isset( $request->ebooks ) )
{

    /**
     * TODO: make validation function
     * validate object ($request->ebooks->*)
     * returning sanitized values above as data array/object members
     */

    if( isset( $request->ebooks->get ) )
    {

        $id = ( isset( $request->ebooks->get->id ) ) ? $request->ebooks->get->id : false;
        $limit = ( isset( $request->ebooks->get->limit ) ) ? $request->ebooks->get->limit : false;
        $offset = ( isset( $request->ebooks->get->offset ) ) ? $request->ebooks->get->offset : false;
        $return_fields = ( isset( $request->ebooks->get->return_fields ) ) ? $request->ebooks->get->return_fields : '*';

        $api->addOutput( array( 'data' => $mysql->select( 'ebooks', $return_fields, $id, $limit, $offset ) ) );

    }

    if( isset( $request->ebooks->put ) )
    {

        $id = ( isset( $request->ebooks->put->id ) ) ? $request->ebooks->put->id : false;
        $data = ( isset( $request->ebooks->put->data ) ) ? $request->ebooks->put->data : false;

        $api->addOutput( array( 'data' => $mysql->update( 'ebooks', $id, $data ) ) );

    }

}

$api->addOutput( array( 'session' => $session->status ) );