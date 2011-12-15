<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Notice will let you easily send messages
 * in your application, _Flash Messages_.
 *
 * @package		Notice
 * @category	Core
 * @author 		Emiliano Burgos <hello@goliatone.com>
 * @copyright  	(c) 20011 Emiliano Burgos
 * @url			http://www.enjoy-mondays.com
 * @license    	http://kohanaphp.com/license
 */
abstract class Core_Notice
{
	/**
	 * Constant for error message type.
	 */
	const ERROR 	= 'error';
	
	/**
	 * Constant for error notice type.
	 */
	const NOTICE 	= 'notice';
	
	/**
	 * Constant for error success type.
	 */
	const SUCCESS 	= 'success';
	
	/**
	 * Constant for error warn type.
	 */
	const WARN 		= 'warn';
		
	/**
	 * Just to make sure it does not clash with others.
	 * 
	 * @var string Default key name to store notices in session.
	 */
	public static $skey = '__KO3__MESSAGES__';
	
	/**
	 * Used to display and replace messages: $messages.
	 * 
	 * @var string Name of variable holder on view. We can configure this in <code>notices.php</code>
	 */
	public static $view_var_name = 'messages';
	
	/**
	 * @var string Path to default message view. We can configure this in <code>notices.php</code>
	 */
	public static $view_path 	 = 'messages/basic';
	//public static $view_path 	 = Kohana::$config->load('notices.view_base_path');
	
	/**
	 * @var	mixed	The message to display.
	 */
	public $message;

	/**
	 * @var	string	The type of message.
	 */
	public $type;
	
	/**
	 * If empty, will show error type.
	 * @var string The header of the message. 
	 */
	public $header;

	/**
	 * Creates a new <code>Core_Notice</code> instance.
	 *
	 * @param	string	Type of message
	 * @param	mixed	Notice to display, either string or array
	 * @param	string	Header of message.
	 */
	private function __construct($type, $message, $header = NULL )
	{
		$this->type 	= $type;
		$this->message 	= $message;
		$this->header	= $header ? $header : ucfirst($type);
	}

	
	
	/**
	 * Clears messages from the session. If $notice_group is 
	 * not specified, then all global messages are erased.
	 * 
	 * @param	string	$notice_group	Notice group ID.
	 * @return	void
	 */
	public static function clear( $notice_group = NULL )
	{
		Session::instance()->delete( self::_group_key($notice_group) );
	}
	
	/**
	 * Clears messages from the session. If $notice_group is 
	 * not specified, then all global messages are erased. 
	 * @param	string	$notice_group	Notice group ID.
	 * @return	void
	 */
	public static function queued( $notice_group = NULL )
	{
		return ( Session::instance()->get( self::_group_key($notice_group), '__NULL_MESSAGES__') !== '__NULL_MESSAGES__');
	}
	
	/**
	 * Displays the message. It will render a view specified by
	 * <code>Notice::$view_path</code> and will set variable with name
	 * <code>$var_name_in_view</code> with the messages.
	 * 
	 * If <code>$var_name_in_view</code> is not specified, it will default to 
	 * <code>Notice::$view_var_name</code>
	 * 
	 * @param	string	$notice_group	Notice group ID.
	 * @param 	string	$view_path		Path to the view to render.		
	 * @param 	string	$var_name_in_view Name of the variable to store the messages in.		
	 * @param 	string	$autorender		Do we need to cal render on the view, or not.
	 * 
	 * @return	mixed	HTML string representation of message string or a View instance.
	 */	
	public static function render( $notice_group = null, $view_path = NULL, $var_name_in_view = NULL, $autorender = TRUE)
	{
		if( ! self::queued($notice_group) )
		{
			return '';
		}
		
		$view_path = $view_path ? $view_path : self::$view_path;
		
		$var_name_in_view = $var_name_in_view ? $var_name_in_view : self::$view_var_name;
		
		$messages = self::get($notice_group);
		
		self::clear($notice_group);
		
		$view = View::factory( $view_path );
		$view->set($var_name_in_view,$messages);
		
		if($autorender) return $view->render();
		else return $view;		
	}

	/**
	 * Gets the current messages for a specified notice
	 * group ID. If not group ID is specified, global notices
	 * are returned.
	 * 
	 * @param	string	$notice_group	Notice group ID.	
	 * 
	 * @return	mixed	An array containing the messages or FALSE
	 */
	public static function get( $notice_group = NULL )
	{
		return Session::instance()->get(self::_group_key($notice_group), FALSE);
	}

	/**
	 * Sets a message, of certain type.
	 * 
	 * We can spceficy a header for the message, if we don't then the 
	 * <code>$type</code> will be used, with <code>ucfirst</code>.
	 * 
	 * Notice group specifies a filter for messages, grouping them under
	 * a common ID. Later, we can check for messages queued and render
	 * messages for that group ID. 
	 *
	 * @param	string	$type			Type of message
	 * @param	mixed	$message		Array/String for the message body.
	 * @param	string	$header			Header of the message.
	 * @param	string	$notice_group	Notice group ID.
	 * 
	 * @return	void
	 */
	public static function add($type, $message, $header = NULL, $notice_group = NULL )	
	{
		// get session messages for specified notice group.
		$messages = self::_messages($notice_group);
		
		 // append to messages
	  	$messages[$type][] = new Notice($type, $message, $header);
	  
	  	// set messages
	  	Session::instance()->set(self::_group_key($notice_group), $messages);		
	}
	
	/**
	 * Sets an <code>Notice::ERROR</code> message.
	 *
	 * @param	mixed	$message		String/Array for the message(s)
	 * @param	string	$header			Header of the message.
	 * @param	string	$notice_group	Notice group ID.
	 * @return	void
	 */
	public static function error($message, $header = NULL, $notice_group = NULL )
	{
		self::add(Notice::ERROR, $message, $header, $notice_group);
	}

	/**
	 * Sets a <code>Notice::NOTICE</code> message.
	 *
	 * @param	mixed	$message		String/Array for the message(s)
	 * @param	string	$header			Header of the message.
	 * @param	string	$notice_group	Notice group ID.
	 * @return	void
	 */
	public static function notice($message, $header = NULL, $notice_group = NULL )
	{
		self::add(Notice::NOTICE, $message, $header, $notice_group);
	}

	/**
	 * Sets a <code>Notice::SUCCESS</code> message.
	 *
	 * @param	mixed	$message		String/Array for the message(s)
	 * @param	string	$header			Header of the message.
	 * @param	string	$notice_group	Notice group ID.
	 * @return	void
	 */
	public static function success($message, $header = NULL, $notice_group = NULL )
	{
		self::add(Notice::SUCCESS, $message, $header, $notice_group);
	}

	/**
	 * Sets a <code>Notice::WARN</code> message.
	 *
	 * @param	mixed	$message		String/Array for the message(s)
	 * @param	string	$header			Header of the message.
	 * @param	string	$notice_group	Notice group ID.
	 * @return	void
	 */
	public static function warn($message, $header = NULL, $notice_group = NULL )
	{
		self::add(Notice::WARN, $message, $header, $notice_group);
	}
	
	/**
	 * Retrieve messages for key group
	 * @param	string	$notice_group	Notice group ID.
	 * @return	Array	Array of notices related to group ID.
	 * @private
	 */
	protected static function _messages( $notice_group = NULL )
	{
		return Session::instance()->get(self::_group_key($notice_group),array());
	}
	
	/**
	 * If not notice group is provided, return default value.
	 * @param	string	$key	Notice group ID.
	 * 
	 * @private
	 */
	protected static function _group_key( $key = NULL ){
		return $key ? $key : self::$skey;
	}
}

/*
 * Ugly way to initialize static vars from config file :(
 */ 
Core_Notice::$view_path 	= Kohana::$config->load('notices.view_base_path');
Core_Notice::$view_var_name = Kohana::$config->load('notices.view_var_name');