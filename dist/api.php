<?php define ( 'master', true );

require ( 'init.php' );

echo '<pre>';

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

/* Static Test Data ***
$_POST['function'] = 'session';
$_POST['session'] = 'login';
$_POST['login'] = 'username';
$_POST['login'] = 'password';
$_POST['username'] = 'admin';
$_POST['password'] = 'admin';
***/

// validate incoming $_POST request
$api->isValidRequest();

// handle session tasks
if( $_POST['function'] == 'session' )
{

    if( $_POST['session'] == 'login' )
    {

        $mysql->login( $session->login( $mysql->selectUsers() ) );

    }

    if( $_POST['session'] == 'logout' )
    {
        
        $mysql->logout( $session->logout() );

    }

}

$api->addOutput( array( 'session' => $session->status ) );
