<?php if ( !defined ( 'master' ) ) die ( header ( 'HTTP/1.0 404 Not Found' ) );

class session
{

    public $status;

    function __construct()
    {

        if( session_status() == PHP_SESSION_NONE )
        {
            session_start();
        }

        if( isset( $_SESSION['user'] ) )
        {

            $this->status = true;

        } else {

            $this->status = false;
            
        }

    }

    public function login( $users )
    {

        while( $user = $users->fetch_object() )
        {

            if( strtolower ( $user->username ) == strtolower ( $_POST['username'] ) )
            {

                if( password_verify( $_POST['password'], $user->password ) ) {

                    foreach ( $user as $key=>$value ) {
                        $_SESSION['user'][$key] = $value;
                    }

                    $_SESSION['user']['session_id'] = session_id();

                    $this->status = true;

                    return $user->id;

                }

            }

        }

        return false;

    }
    
    public function logout() {

        if( isset( $_SESSION['user']['id'] ) )
        {
            $id = $_SESSION['user']['id'];
        }

        unset( $_SESSION['user'] );

        $this->status = false;

        return $id;

    }

}