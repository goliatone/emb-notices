#Notice

The **Notice** module enables to manage application's notifications.

It has a simple api and a light weight implementation, that allows for notice queueing and grouping. 
The rendering of the notices is decoupled from the module and fully customizable. 

To set a notice, usually from one of your controllers, you simple call `Notice::add`

	$level   = Notice::ERROR;
	$header  = 'Validation Error'; 
	$message = 'You need to provide a valid email.';
	$notice_group = 'post.create';
	Notice::add(Notice::ERROR, $message,$header, $notice_group);

To render a notice, usually inside one of your views or partials, you can check for an specific group:

	if(Notice::queued('post.create')) echo Notice::render('post.create');

Or just go ahead, and reder all notices:

	if(Notice::queued()) echo Notice::render();

Which, in turn will call the default basic view:

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


###Notice Levels

There are four default notice levels, and four shortcut methods to call them. 
This way we don't have to specify the level like we do with `Notice::add`

	Notice::notice($message,$header, $notice_group);
	Notice::success($message,$header, $notice_group);
	Notice::warn($message,$header, $notice_group);
	Notice::error($message,$header, $notice_group);
