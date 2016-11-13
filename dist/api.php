<?php define ( 'master', true );

require ( 'init.php' );

// register session function
$function = array(
    'session' => array(
        'login' => array(
            'username' => array(),
            'password' => array()
            ),
        'logout' => array()
        )
);
$api->addFunction( $function );

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

//$api->addOutput( array( 'session' => $session->status ) );


/* Static Test Data ***
$_POST['function'] = 'session';
$_POST['session'] = 'login';
$_POST['login'] = 'username';
$_POST['login'] = 'password';
$_POST['username'] = 'asdf';
$_POST['password'] = '1234';
***/

$api->isValidRequest();

if( $_POST['function'] == 'session' )
{

    if( $_POST['session'] == 'login' )
    {
        
        $api->addOutput( array( 'session' => true ) );

    }

    if( $_POST['session'] == 'logout' )
    {
        
        $api->addOutput( array( 'session' => false ) );

    }

}

//$result = $mysql->select( $_POST['function'], $_POST['id'] );

//$api->addOutput( $result );


