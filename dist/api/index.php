<?php define( 'master', true );

require('init.php');

require('config/register.php');


/*
 * validate JSON input
 */
if( isset( $_POST ) )
    $request = $api->sanitize( $_POST['request'] );


/*
 * handle ebook data tasks
 */

if( $input = $api->input( $request, $request->ebooks->get ) )
    $api->addOutput(array( 'data' => $mysql->select('ebooks', $input['return_fields'], $input['id'], $input['limit'], $input['offset'] ) ) );

if( $input = $api->input( $request, $request->ebooks->put ) )
    $api->addOutput( array( 'data' => $mysql->update('ebooks', $input['id'], $input['data'] ) ) );


/*
 * handle session tasks
 */

if( $input = $api->input( $request, $request->session->login ) )
    if( $users = $mysql->select('users', '*', false, false, false ) )
        $mysql->login( $session->login( $users, $input['username'], $input['password'] ) );

if( $input = $api->input( $request, $request->session->logout ) )
    $mysql->logout( $session->logout() );

$api->addOutput( array( 'session' => $session->status ) );