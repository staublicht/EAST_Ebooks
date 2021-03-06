<?php define( 'master', true );

require('init.php');

require('config/register.php');


/*
 * secure file download
 */
if( isset( $_SERVER['REDIRECT_URL'] ) )
    if( stripos( trim( $_SERVER['REDIRECT_URL'], '/'), 'download') === 0)
        $files->download( $_SERVER['REDIRECT_URL'] );


/*
 * save uploaded files
 */
if( !empty( $_FILES ) )
    $api->addOutput( array( 'upload' => $files->upload() ) );


/*
 * validate JSON input
 */
if( isset( $_POST['request'] ) )
    $request = $api->sanitize( $_POST['request'] );
else $request = false;


/*
 * handle ebook data tasks
 */

if( $input = $api->input( $request, @$request->ebooks->delete ) )
    $api->addOutput( array( 'data' => $mysql->delete('ebooks', $input['id'] ) ) );

if( $input = $api->input( $request, @$request->ebooks->get ) )
    $api->addOutput( array( 'data' => $mysql->select('ebooks', $input['return_fields'], $input['id'], $input['limit'], $input['offset'] ) ) );

if( $input = $api->input( $request, @$request->ebooks->post ) )
    $api->addOutput( array( 'data' => $mysql->insert('ebooks', $input['data'] ) ) );

if( $input = $api->input( $request, @$request->ebooks->put ) )
    $api->addOutput( array( 'data' => $mysql->update('ebooks', $input['id'], $input['data'] ) ) );


/*
 * handle session tasks
 */

if( $input = $api->input( $request, @$request->session->login ) )
    if( $users = $mysql->select('users', '*', false, false, false ) )
        $mysql->login( $session->login( $users, $input['username'], $input['password'] ) );

if( $input = $api->input( $request, @$request->session->logout ) )
    $mysql->logout( $session->logout() );

$api->addOutput( array( 'session' => $session->status ) );