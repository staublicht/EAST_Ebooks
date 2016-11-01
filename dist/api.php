<?php define ( 'master', true );

require ( 'init.php' );

//print_r( $_POST );
//print_r( $_SESSION );

$output = [];

// test output
if( isset( $_POST['action'] ) )
{

		array_push( $output, array( 'session' => $session->status ) );

		// temp. output for JSON test

		// test db load
		// send POST
		// action => load, type => ebook, id => 123
		// returns name, author and isbn for now
		if( $_POST['action'] == 'load' )
		{
			if( isset( $_POST['type'] ) )
			{
				if( $_POST['type'] == 'ebook' )
				{
					if( isset( $_POST['id'] ) )
					{
						// not necessary for now,
						// just output test data
						array_push( $output, array( 'name' => 'eBook Title', 'author' => 'Author Name', 'isbn' => '01234567890' ) );
					}
				}
			}
		}

		// test db save
		// send POST
		// action => save, type => ebook, id => 123,
		// save_name => *name of column to save to, e.g. author*,
		// save_value => *value to save, e.g. Author Name*
		// outputs save => true for now
		if( $_POST['action'] == 'save' )
		{
			if( isset( $_POST['type'] ) )
			{
				if( $_POST['type'] == 'ebook' )
				{
					if( isset( $_POST['id'] ) )
					{
						if( isset( $_POST['save_name'] ) )
						{
							if( isset( $_POST['save_value'] ) )
							{
								// assuming everything went fine
								// return true, else false [bool]
								array_push( $output, array( 'save' => true ) );
							}
						}
					}
				}
			}
		}

}
else
{
		//
}

echo json_encode( $output );
