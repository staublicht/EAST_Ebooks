<?php if( !defined( 'master' ) ) die( header( 'HTTP/1.0 404 Not Found' ) );

/**
 * Class files
 */
class files
{

    private $directory = null;
    private $mysql = null;

    /**
     * files constructor.
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
     *
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

}