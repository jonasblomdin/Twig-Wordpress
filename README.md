Twig-Wordpress
==============

An implementation which aims to bring the Twig template engine to Wordpress.

##Install

For git users, clone the repo.

    git clone --recursive https://github.com/jonasblomdin/Twig-Wordpress twig-wordpress

For subversion users, add it as an external.

    svn propset svn:externals twig-wordpress https://github.com/jonasblomdin/Twig-Wordpress/trunk

Twig-Wordpress should than be bundled with your theme. Put this inside your *functions.php*.

    require 'twig-wordpress/bootstrap.php';
 
Your theme should contain a twig folder with the structure below. The structure could off course be overridden using the constants *before* you load the bootstrap.

    your-theme
      twig
        cache
        layouts
        templates
 
## Constants

#####TWP___DEBUG
Twig debug flag. Defaults to WP_DEBUG.

#####TWP___THEME_ROOT
Default set to a folder named "twig", including a trailing slash , within your theme root.

#####TWP___TEMPLATE_PATH
Default set to a folder named "templates", with no traling slash, within your TWP___THEME_ROOT.

#####TWP___CACHE_PATH
A writeable folder for your cache.

## Actions

#####TWP__init
    function my_init($twig, $data)
    {
      $twig->addExtension(new Twig_Extension_Debug());
    }
    add_action('TWP__init', 'my_init', 1, 2);

#####TWP__environemnt
    function my_environemnt($environment)
    {
      $environment->addFunction('query_posts', new Twig_Function_Function('query_posts'));
    }
    add_action('TWP__environemnt', 'my_environemnt');

#####TWP__template
    function my_template($name, $index)
    {
      return $name;
    }
    add_action('TWP__template', 'my_template', 1, 2);

## Filters

#####TWP__template_404
#####TWP__template_search
#####TWP__template_tax
#####TWP__template_tax-override
#####TWP__template_front-page
#####TWP__template_home
#####TWP__template_attachment
#####TWP__template_single
#####TWP__template_single-override
#####TWP__template_page
#####TWP__template_page-override
#####TWP__template_category
#####TWP__template_category-override
#####TWP__template_tag
#####TWP__template_tag_override
#####TWP__template_author
#####TWP__template_author-override
#####TWP__template_date
#####TWP__template_archive
#####TWP__template_archive-override
#####TWP__template_comments-popup
#####TWP__template_paged
#####TWP__template_index