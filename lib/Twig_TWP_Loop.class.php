<?php

/**
 *
 * Twig-Wordpress "The Loop" based while iterator
 */
class Twig_TWP_Loop implements Iterator 
{
	
	/**
	 *
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
  public function __construct()
	{
		
  }
	
	/**
	 *
	 * Do we have posts?
	 *
	 * @access public
	 * @return mixed
	 */
  public function valid()
	{
		return call_user_func(function() {
			return have_posts();
		});
  }
	
	/**
	 *
	 * Current
	 *
	 * @see http://www.php.net/current
	 * @access public
	 * @return mixed
	 */
  public function current()
	{
		return call_user_func(function() {
			global $post;
			the_post();
			return $post;
		});
  }
	
	/**
	 *
	 * Next
	 *
	 * @see http://www.php.net/next
	 * @access public
	 * @return void
	 */
  public function next()
	{
  	
  }
	
	/**
	 *
	 * Rewind
	 *
	 * @see http://www.php.net/rewind
	 * @access public
	 * @return void
	 */
  public function rewind()
	{
		return call_user_func(function() {
			return rewind_posts();
		});
	}
	
	/**
	 *
	 * Key
	 *
	 * @see http://www.php.net/key
	 * @access public
	 * @return void
	 */
  public function key()
	{
		 
	}
}