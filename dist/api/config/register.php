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
$actions->account->delete->id = null;
$actions->account->get->id = null;
$actions->account->get->username = null;
$actions->account->post->id = null;
$actions->account->put->id = null;

/*
 * set ebooks actions
 */
$actions->ebooks->delete->id = null;
$actions->ebooks->get->id = null;
$actions->ebooks->get->limit = null;
$actions->ebooks->get->offset = null;
$actions->ebooks->get->return_fields = null;
$actions->ebooks->post->data = null;
$actions->ebooks->put->id = null;
$actions->ebooks->put->data = null;

/*
 * register actions
 */
$api->addFunction( $actions );
