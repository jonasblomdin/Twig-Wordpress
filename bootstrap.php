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
define('TWP___ROOT', dirname(__FILE__));
define('TWP___CUSTOM_TEMPLATE', '_wp_twig_template');

/**
 *
 * Customizable constants
 */
if (!defined('TWP___TWIG_ROOT') || !is_dir(TWP___TWIG_ROOT)) {
  if (defined('TWP___TWIG_ROOT') && !is_dir(TWP___TWIG_ROOT)) {
    trigger_error('Make sure your Twig root folder has been created. Check TWP___TWIG_ROOT for more information.', E_USER_ERROR);
  }
  define('TWP___TWIG_ROOT', TWP___ROOT.'/twig/');
}
if (!defined('TWP___TEMPLATE_PATH') || !is_dir(TWP___TEMPLATE_PATH)) {
  if (!is_dir(TWP___TWIG_ROOT.'templates/')) {
    trigger_error('Make sure your Twig template folder has been created. Check the TWP___TEMPLATE_PATH for more information.', E_USER_ERROR);
  }
  define('TWP___TEMPLATE_PATH', TWP___TWIG_ROOT.'templates/');
}
if (defined('TWP___CACHE_PATH') && !is_writable(TWP___CACHE_PATH)) {
  trigger_error('Twig cache path is not writeable', E_USER_ERROR);
}
if (!defined('TWP___ADMIN')) {
  define('TWP___ADMIN', true);
}
if (!defined('TWP___CUSTOM_TEMPLATE_TYPES')) {
  define('TWP___CUSTOM_TEMPLATE_TYPES', serialize(
    array('page', 'post')));
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
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_taxonomy', 'taxonomy.html.twig'));
define(
  'TWP___TEMPLATE_TAX_OVERRIDE', 
  TWP___TEMPLATE_PATH.apply_filters('TWP__template_taxonomy-override', 'taxonomy-%s.html.twig'));
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
  if (file_exists(TWP___ROOT.'/lib/'.$name.'.class.php')) {
    require_once TWP___ROOT.'/lib/'.$name.'.class.php';
  }
});

/**
 *
 * Instantiate Twig environment
 *
 * @return void
 */
function TWP__init()
{
	global $twig, $params;
  
	Twig_Autoloader::register();
	Twig_TWP_Proxy::register();
  
  $options = array(
    'debug' => defined('TWP___DEBUG') ? TWP___DEBUG : WP_DEBUG,
    'cache' => defined('TWP___CACHE_PATH') ? TWP___CACHE_PATH : false
  );
	$twig = new Twig_TWP_Environment(
    new Twig_Loader_Filesystem(TWP___TEMPLATE_PATH), 
    apply_filters('TWP__options', $options)
  );
	$params = array(
		'wp' => new Twig_TWP_Proxy,
		'loop' => new Twig_TWP_Loop
  );
  
  do_action('TWP__init', $twig, $params);
}
add_action('init', 'TWP__init', 10, 2);

/**
 *
 * Add admin menu to clear Twig cache
 *
 * @return void
 */
function TWP__admin_menu()
{
  add_menu_page(
    __('Twig'),
    __('Twig'),
    'manage_options',
    'twig',
    array(new Twig_TWP_Admin, 'renderCache'),
    '',
    3);
}
if (defined('TWP___CACHE_PATH') && TWP___ADMIN) {
  add_action('admin_menu', 'TWP__admin_menu');
}

/**
 *
 * Add custom template metabox
 *
 * @return void
 */
function TWP__metabox()
{
  if ($custom_types = unserialize(TWP___CUSTOM_TEMPLATE_TYPES)) {
    $post_types = get_post_types();
    $types = array_intersect($post_types, $custom_types);
    foreach ($types as $type)
    {
      if (count(Twig_TWP_Admin::getTemplates($type)) > 0) {
        add_meta_box(
          'TWP__template',
          __('Custom Template'),
         array(new Twig_TWP_Admin, 'renderMetabox'),
          $type,
          'side'
        );
      }
    }
  }
}
if (defined('TWP___CUSTOM_TEMPLATE_TYPES') && TWP___CUSTOM_TEMPLATE_TYPES) {
  add_action('add_meta_boxes', 'TWP__metabox');
  add_action('save_post', array(new Twig_TWP_Admin, 'saveMetabox'));
}

/**
 *
 * Setup Twig templates
 * The original tests makes file-exist conditions based on the Wordpress template, so we need to re-run the tests
 *
 * @see wp-includes/template-loader.php
 * @return void
 */
function TWP__template()
{
	global $tpl, $post, $posts;

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
      $templates[] = TWP___TEMPLATE_ARCHIVE;
      break;
    case is_front_page():
      $templates[] = TWP___TEMPLATE_FRONT_PAGE;
      break;
    case is_home():
      $templates[] = TWP___TEMPLATE_HOME;
      break;
    case is_attachment():
  	  if (!empty($posts) && isset($posts[0]->post_mime_type)) {
  		  $type = explode('/', $posts[0]->post_mime_type);
  		  if (!empty($type)) {
  			  if ($template = get_query_template($type[0]))
  				  return $template;
  			  elseif (!empty($type[1])) {
  				  if ($template = get_query_template($type[1]))
  					  return $template;
  				  elseif ($template = get_query_template("$type[0]_$type[1]"))
  					  return $template;
  			  }
  		  }
  	  }
      $templates[] = TWP___TEMPLATE_ATTACHMENT;
      $templates[] = sprintf(TWP___TEMPLATE_SINGLE_OVERRIDE, 'attachment');
      $templates[] = TWP___TEMPLATE_SINGLE;
      break;
    case is_single():
  		$object = get_queried_object();
  		$custom = get_post_meta($post->ID, TWP___CUSTOM_TEMPLATE, true);
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
  		$custom = get_post_meta($post->ID, TWP___CUSTOM_TEMPLATE, true);
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
      $templates[] = TWP___TEMPLATE_ARCHIVE;
      break;
    case is_tag():
		  $tag = get_queried_object();
		  if ($tag) {
			  $templates[] = sprintf(TWP___TEMPLATE_TAG_OVERRIDE, $tag->slug);
			  $templates[] = sprintf(TWP___TEMPLATE_TAG_OVERRIDE, $tag->term_id);
		  }
		  $templates[] = TWP___TEMPLATE_TAG;
      $templates[] = TWP___TEMPLATE_ARCHIVE;
      break;
    case is_author():
		  $author = get_queried_object();
		  if ($author) {
			  $templates[] = sprintf(TWP___TEMPLATE_AUTHOR_OVERRIDE, $author->user_nicename);
			  $templates[] = sprintf(TWP___TEMPLATE_AUTHOR_OVERRIDE, $author->ID);
		  }
		  $templates[] = TWP___TEMPLATE_AUTHOR;
      $templates[] = TWP___TEMPLATE_ARCHIVE;
      break;
    case is_date():
      $templates[] = TWP___TEMPLATE_DATE;
      $templates[] = TWP___TEMPLATE_ARCHIVE;
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
 * @param string $template
 * @return string
 */
function TWP_index($template) 
{
  return dirname(__FILE__).'/index.php';
}
add_filter('template_include', 'TWP_index', 9999);