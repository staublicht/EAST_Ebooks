<?php define( 'master', true );

require('init.php');

require('config/register.php');


/*
 * validate JSON input
 */
if( isset( $_POST ) )
    $request = $api->sanitize( $_POST );


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

    if( isset( $request->ebooks->get ) )
    {

        $limit = ( isset( $request->ebooks->get->limit ) ) ? $request->ebooks->get->limit : false;
        $offset = ( isset( $request->ebooks->get->offset ) ) ? $request->ebooks->get->offset : false;
        $return_fields = ( isset( $request->ebooks->get->return_fields ) ) ? $request->ebooks->get->return_fields : '*';

        $return = [];
        $ebooks = $mysql->select( 'ebooks', $return_fields, false, $limit, $offset );

        while( $ebook = $ebooks->fetch_array(MYSQLI_ASSOC) )
            array_push($return, $ebook);

        $api->addOutput( array( 'booklist' => $return ) );

    }

}

$api->addOutput( array( 'session' => $session->status ) );