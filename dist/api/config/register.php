<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/*
 * set session actions
 */
$actions->session->login->username = null;
$actions->session->login->password = null;
$actions->session->logout = null;

/*
 * set account actions
 */
$actions->account->get->id = null;
$actions->account->get->username = null;
$actions->account->post->id = null;
$actions->account->update->id = null;
$actions->account->delete->id = null;

/*
 * set ebooks actions
 */
$actions->ebooks->get->limit = null;
$actions->ebooks->get->offset = null;
$actions->ebooks->get->return_fields = null;

/*
 * register actions
 */
$api->addFunction( $actions );
