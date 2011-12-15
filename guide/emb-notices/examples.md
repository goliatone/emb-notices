##Examples

In your controller method, when handling validation errors, you can set a notice:

	...
	catch( Validate_Exception $e )
	{
		$errors = $e->array->errors('blog/post');
		Notice::error("Post Error", $errors,'post.create');
	}

Then, in yuor view:

	if(Notice::queued('post.create')) echo Notice::render('post.create');