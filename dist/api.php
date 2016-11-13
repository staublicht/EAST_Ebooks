<?php define ( 'master', true );

require ( 'init.php' );

// register account function
$function = array(
    'account' => array(
        'get' => array(
            'id' => array(),
            'username' => array()
            ),
        'post' => array(
            'id'
            ),
        'update' => array(
            'id'
            ),
        'delete' => array(
            'id'
            )
        )
);
$api->addFunction( $function );

// register ebooks function
$function = array(
    'ebooks' => array(
        'get' => array(
            'id' => array(),
            'username' => array()
            )
        )
);
$api->addFunction( $function );

$api->addOutput( array( 'session' => $session->status ) );


/* Static Test Data ***
$_POST['function'] = 'ebooks';
$_POST['ebooks'] = 'get';
$_POST['get'] = 'id';
$_POST['id'] = 1;
***/

$api->isValidRequest();

$result = $mysql->select( $_POST['function'], $_POST['id'] );

$api->addOutput( $result );
