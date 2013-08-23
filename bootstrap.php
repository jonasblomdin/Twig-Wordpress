<?php
/**
 *
 * Load environment
 *
 * @author Jonas Blomdin <jonas@hoku.am>
 */

/**
 *
 * Environmental constants
 */
define('TWP_ROOT', dirname(__FILE__));

/**
 *
 * Customizable constants
 */
if (!defined('TWP___THEME_ROOT') || !is_dir(TWP___THEME_ROOT)) {
  if (!is_dir(get_template_directory().'/twig/')) {
    trigger_error('Create the twig folder. Check TWP___THEME_ROOT for more information', E_USER_ERROR);
  }
  define('TWP___THEME_ROOT', get_template_directory().'/twig/');
}
if (!defined('TWP___TEMPLATE_PATH') || !is_dir(TWP___TEMPLATE_PATH)) {
  if (!is_dir(TWP___THEME_ROOT.'templates/')) {
    trigger_error('Create the template folder. Check the TWP___TEMPLATE_PATH for more information.', E_USER_ERROR);
  }
  define('TWP___TEMPLATE_PATH', TWP___THEME_ROOT.'templates/');
}
if (defined('TWP___CACHE_PATH') && !is_writable(TWP___CACHE_PATH)) {
  trigger_error('Twig cache path is not writeable', E_USER_ERROR);
}

/**
 *
 * Template constants
 */
define(
  'TWP___TEMPLATE_404', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_404', '404.html.twig'));
define(
  'TWP___TEMPLATE_SEARCH', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_search', 'search.html.twig'));
define(
  'TWP___TEMPLATE_TAX', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_tax', 'tax.html.twig'));
define(
  'TWP___TEMPLATE_TAX_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_tax-override', 'tax-%s.html.twig'));
define(
  'TWP___TEMPLATE_FRONT_PAGE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_front-page', 'front.html.twig'));
define(
  'TWP___TEMPLATE_HOME', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_home', 'home.html.twig'));
define(
  'TWP___TEMPLATE_ATTACHMENT', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_attachment', 'attachment.html.twig'));
define(
  'TWP___TEMPLATE_SINGLE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_single', 'single.html.twig'));
define(
  'TWP___TEMPLATE_SINGLE_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_single-override', '%s.html.twig'));
define(
  'TWP___TEMPLATE_PAGE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_page', 'page.html.twig'));
define(
  'TWP___TEMPLATE_PAGE_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_page-override', 'page-%s.html.twig'));
define(
  'TWP___TEMPLATE_CATEGORY', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_category', 'category.html.twig'));
define(
  'TWP___TEMPLATE_CATEGORY_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_category-override', 'category-%s.html.twig'));
define(
  'TWP___TEMPLATE_TAG', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_tag', 'tag.html.twig'));
define(
  'TWP___TEMPLATE_TAG_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_tag_override', 'tag-%s.html.twig'));
define(
  'TWP___TEMPLATE_AUTHOR', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_author', 'author.html.twig'));
define(
  'TWP___TEMPLATE_AUTHOR_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_author-override', 'author-%s.html.twig'));
define(
  'TWP___TEMPLATE_DATE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_date', 'date.html.twig'));
define(
  'TWP___TEMPLATE_ARCHIVE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_archive', 'archive.html.twig'));
define(
  'TWP___TEMPLATE_ARCHIVE_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_archive-override', 'archive-%s.html.twig'));
define(
  'TWP___TEMPLATE_COMMENTS_POPUP', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_comments-popup', 'comments-popup.html.twig'));
define(
  'TWP___TEMPLATE_PAGED', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_paged', 'paged.html.twig'));
define(
  'TWP___TEMPLATE_INDEX', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_index', 'index.html.twig'));

/**
 *
 * Load Twig
 */
require_once dirname(__FILE__).'/vendor/Twig/lib/Twig/Autoloader.php';

/**
 *
 * Autoload dependencies
 */
spl_autoload_register(function($name) {
  if (file_exists(TWP_ROOT.'/lib/'.$name.'.class.php')) {
    require_once TWP_ROOT.'/lib/'.$name.'.class.php';
  }
});

/**
 *
 * Instantiate environment
 *
 * @return void
 */
function TWP__init()
{
	global $twig, $data;
  
	Twig_Autoloader::register();
	Twig_TWP_Proxy::register();
  
	$twig = new Twig_TWP_Environment(
    new Twig_Loader_Filesystem(TWP___TEMPLATE_PATH), 
    array(
		  'debug' => defined('TWP___DEBUG') ? TWP___DEBUG : WP_DEBUG,
		  'cache' => defined('TWP___CACHE_PATH') ? TWP___CACHE_PATH : false)
  );
	$data = array(
		'wp' => new Twig_TWP_Proxy,
		'loop' => new Twig_TWP_Loop
  );
  
  do_action('TWP__init', $twig, $data);
}
add_action('init', 'TWP__init', 10, 2);

/**
 *
 * Setup templates
 * The original tests makes file-exist conditions based on the non Twig template, so we need to re-run the tests
 *
 * @see wp-includes/template-loader.php
 * @return void
 */
function TWP__template()
{
	global $tpl, $post;

	$templates = array();
  switch(true)
  {
    case is_404():
      $templates[] = TWP___TEMPLATE_404;
      break;
    case is_tax():
		  $term = get_queried_object();
		  if ($term) {
			  $taxonomy = $term->taxonomy;
			  $templates[] = sprintf(TWP___TEMPLATE_TAX_OVERRIDE, "$taxonomy-{$term->slug}");
			  $templates[] = sprintf(TWP___TEMPLATE_TAX_OVERRIDE, $taxonomy);
		  }
		  $templates[] = TWP___TEMPLATE_TAX;
      break;
    case is_front_page():
      $templates[] = TWP___TEMPLATE_FRONT_PAGE;
      break;
    case is_home():
      $templates[] = TWP___TEMPLATE_HOME;
      break;
    case is_attachment():
      $templates[] = TWP___TEMPLATE_ATTACHMENT;
      break;
    case is_single():
  		$object = get_queried_object();
  		$custom = get_post_meta($post->ID, TWP___CUSTOM_FIELD_TEMPLATE, true);
  		if ($custom && 0 === validate_file(TWP___TEMPLATE_PATH.$custom)) {
  			$templates[] = TWP___TEMPLATE_PATH.$custom;
  		}
  		if ($object) {
  			$templates[] = sprintf(TWP___TEMPLATE_SINGLE_OVERRIDE, $object->post_type);
  		}
  		$templates[] = TWP___TEMPLATE_SINGLE;
      break;
    case is_page():
  		$id = get_queried_object_id();
  		$custom = get_post_meta($post->ID, TWP___CUSTOM_FIELD_TEMPLATE, true);
  		$pagename = get_query_var('pagename');
  		if (!$pagename && $id) {
  			$post = get_queried_object();
  			$pagename = $post->post_name;
  		}
  		if ($custom && 0 === validate_file(TWP___TEMPLATE_PATH.$custom)) {
  			$templates[] = TWP___TEMPLATE_PATH.$custom;
  		}
  		if ($pagename) {
  			$templates[] = sprintf(TWP___TEMPLATE_PAGE_OVERRIDE, $pagename);
  		}
  		if ($id) {
  			$templates[] = sprintf(TWP___TEMPLATE_PAGE_OVERRIDE, $id);
  		}
  		$templates[] = TWP___TEMPLATE_PAGE;
      break;
    case is_category():
		  $category = get_queried_object();
		  if ($category) {
			  $templates[] = sprintf(TWP___TEMPLATE_CATEGORY_OVERRIDE, $category->slug);
			  $templates[] = sprintf(TWP___TEMPLATE_CATEGORY_OVERRIDE, $category->term_id);
		  }
		  $templates[] = TWP___TEMPLATE_CATEGORY;
      break;
    case is_tag():
		  $tag = get_queried_object();
		  if ($tag) {
			  $templates[] = sprintf(TWP___TEMPLATE_TAG_OVERRIDE, $tag->slug);
			  $templates[] = sprintf(TWP___TEMPLATE_TAG_OVERRIDE, $tag->term_id);
		  }
		  $templates[] = TWP___TEMPLATE_TAG;
      break;
    case is_author():
		  $author = get_queried_object();
		  if ($author) {
			  $templates[] = sprintf(TWP___TEMPLATE_AUTHOR_OVERRIDE, $author->user_nicename);
			  $templates[] = sprintf(TWP___TEMPLATE_AUTHOR_OVERRIDE, $author->ID);
		  }
		  $templates[] = TWP___TEMPLATE_AUTHOR;
      break;
    case is_date():
      $templates[] = TWP___TEMPLATE_DATE;
      break;
    case is_archive():
		  $post_types = array_filter((array) get_query_var('post_type'));
		  if (count($post_types) == 1) {
			  $post_type = reset($post_types);
			  $templates[] = sprintf(TWP___TEMPLATE_ARCHIVE_OVERRIDE, $post_type);
		  }
		  $templates[] = TWP___TEMPLATE_ARCHIVE;
      break;
    case is_comments_popup():
  		$templates[] = TWP___TEMPLATE_COMMENTS_POPUP;
      break;
    case is_paged():
  		$templates[] = TWP___TEMPLATE_PAGED;
      break;
  }
  $templates[] = TWP___TEMPLATE_INDEX;

	foreach ($templates as $template)
	{
		if (file_exists($template)) {
			$tpl = basename($template);
			break;
		}
	}
}
add_action('template_redirect', 'TWP__template');

/**
 *
 * Load Twig thru index template
 *
 * @return string
 */
function TWP_index() 
{
  return dirname(__FILE__).'/index.php';
}
add_filter('template_include', 'TWP_index', 9999);