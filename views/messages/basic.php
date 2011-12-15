<?php
/*
 * We want to make sure we are accessing the righ
 * variable. We have assumed that the we are getting
 * a variable named messages, but just in case. 
 */ 
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
?>