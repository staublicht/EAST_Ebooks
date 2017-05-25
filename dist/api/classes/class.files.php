<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/**
 * Class files
 */
class files
{

    /*
     * home directory
     */
    private $directory = null;

    /*
     * mysql object
     */
    private $mysql = null;

    /**
     * files constructor.
     * @param $mysql
     */
    function __construct( $mysql )
    {

        $this->mysql = $mysql;

        $this->directory = ( defined('files_directory' ) ) ? rtrim( files_directory, '/' ) : 'files';
        if( !is_dir( $this->directory ) )
            if( !$created = @mkdir( $this->directory ) )
                die( 'Files Directory does not exist' );

    }

    /**
     * @param $input
     * @return bool
     */
    public function download( $input )
    {

        $input = array_pop( explode( '/', $input ) );

        $input_id = array_shift( explode( '-', array_shift( explode('.', $input ) ) ) );
        $input_extension = array_pop( explode('.', $input ) );

        foreach( $this->mysql->select( 'ebooks_content_types' ) as $entry )
        {
            if( $entry['extension'] == $input_extension )
            {
                $type_id = $entry['id'];
                $type_mime = $entry['mime'];
                $type_extension = $entry['extension'];
            }
        }

        if( isset( $type_id, $type_mime, $type_extension ) )
        {
            foreach( $this->mysql->select( 'ebooks_content' ) as $entry )
            {
                if ($entry['ebooks_id'] == $input_id && $entry['ebooks_content_types_id'] == $type_id ) {

                    $path = $this->directory . '/' . $input_id . '.' . $type_extension;

                    if( !is_file( $path ) )
                        return false;

                    $result = $this->mysql->select( 'ebooks', array( 'title' ), $input_id, 1 );
                    $filename = ( ( isset( $result[0]['title'] ) ) ? $result[0]['title'] : $input_id ) . '.' . $type_extension;

                    header('Content-Type: ' . $type['mime']);
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . $filename . "\"");
                    readfile($path);

                    exit();
                }
            }
        }

        return false;
    }

    /**
     * read and calculate maximum upload file size
     * @return int|string
     */
    public function fileUploadMaxSize()
    {

        function bytes( $val )
        {
            $val = trim( $val );
            $last = strtolower( $val[strlen( $val ) - 1] );
            switch($last) {
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }

            return $val;
        }

        $max_size = bytes( ini_get( 'post_max_size' ) );
        $upload_max = bytes( ini_get( 'upload_max_filesize' ) );

        if( $upload_max > 0 && $upload_max < $max_size )
            $max_size = $upload_max;

        return $max_size;
    }

    /**
     * @return array|bool
     */
    public function upload()
    {

        $return = [];

        if( !empty( $_FILES ) )
        {

            $filetypes = $this->mysql->select( 'ebooks_content_types' );

            foreach( $_FILES as $id => $file )
            {

                $id = (int) $id;

                if( $file['error'] != 0 )
                    continue;

                if( $file['size'] == 0 )
                    continue;

                foreach( $filetypes as $filetype )
                {
                    if( $filetype['mime'] === $file['type'] ) {

                        if( move_uploaded_file($file['tmp_name'], $this->directory . '/' . $id . '.' . $filetype['extension']) )
                        {

                            foreach ($this->mysql->select('ebooks_content') as $entry)
                                if ($entry['ebooks_id'] == $id && $entry['ebooks_content_types_id'] == $filetype['id'])
                                    $this->mysql->delete('ebooks_content', $entry['id']);

                            $data = array(
                                'ebooks_id' => $id,
                                'ebooks_content_types_id' => $filetype['id']
                            );
                            $this->mysql->insert('ebooks_content', $data);

                            array_push($return, $id);

                        }

                    }
                }

            }

        }

        return ( empty( $return ) ) ? false : $return;
    }

}