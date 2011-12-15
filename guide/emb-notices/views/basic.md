#Views

##Basic View

The basic and default view for rendering `Notices` assumes that a `$messages`
variable is set to the view.

The view has the following code:	
	
	$messages = isset($messages) ? $messages : ${Notice::$view_var_name};
	
	if (! empty($messages)) {
	   $output = '';
	    foreach ($messages as $type => $message) {
	        foreach ($message as $notice) {
	            $output .= '<div class="message closeable '.$notice->type.'">';
				$output .= '<h4>'.$notice->header.'</h4>';
				$output .= '<p>'.$notice->message.'</p>';
				$output .= '</div>';
	        }
	    }
		echo $output;
	}

If `$messages` is null, then we try to dynamically 
access a variable named `Notice::$view_var_name`

##Rendering

When rendering, usually inside a view, we simple check for queued items, and then call render:

	if(Notice::queued('post.create')) echo Notice::render('post.create');

##Rendering Options
We can specify which view we want to use when calling `render`- _second parameter_- and also the name of the variable to set 
the messages to- _third parameter_.

We can specify this variable's default name globally at runtime with `Notice::$view_var_name`
and specify the deafult value on the `config.php` file. 

Specifically, this code takes care of that: 

	$messages = isset($messages) ? $messages : ${Notice::$view_var_name};

Note that this will not catch the case when we have specified a different value for the variable name on the 
`Notice::render`.

	Notice::render( $notice_group, $view_path, $var_name_in_view, $autorender)