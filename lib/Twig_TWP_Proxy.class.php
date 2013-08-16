<?php

/**
 *
 * Twig-Wordpress environemntal proxy
 */
class Twig_TWP_Proxy
{
	
	/**
	 *
	 * Twig registration
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function register()
	{
		ini_set('unserialize_callback_func', 'spl_autoload_call');
		spl_autoload_register(array(new self, 'autoload'));
	}
	
	/**
	 *
	 * Twig autoloader
	 *
	 * @static
	 * @access public
	 * @param string $class
	 * @return mixed
	 */
	public static function autoload($class)
	{
		if (0 !== strpos($class, 'Twig_TWP')) 
			return;

		if (file_exists($file = dirname(__FILE__) . '/../' . str_replace(array('_', "\0"), array('/', ''), $class) . '.php')) {
			echo($file);
			exit;
			require $file;
		}
	}
	
	/**
	 *
	 * Twig proxy
	 *
	 * @access public
	 * @param string $function
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($function, $arguments)
	{
		if (!function_exists($function)) {
			trigger_error('call to unexisting function ' . $function, E_USER_ERROR);
			return NULL;
		}
		return call_user_func_array($function, $arguments);
	}
}