Twig-Wordpress
==============

An implementation which aims to bring the [Twig Template Engine](http://twig.sensiolabs.org) to [Wordpress](http://wordpress.org).

##Install

For git users, clone the repo.

    git clone --recursive https://github.com/jonasblomdin/Twig-Wordpress twig-wordpress

For subversion users, add it as an external.

    svn propset svn:externals twig-wordpress https://github.com/jonasblomdin/Twig-Wordpress/trunk

Twig-Wordpress should than be bundled with your theme. Put this inside your *functions.php*.

    require 'twig-wordpress/bootstrap.php';
 
Your theme should contain a twig folder with the structure below. The structure could of course be overridden using the constants *before* you load the bootstrap.

    your-theme
      twig
        cache
        layouts
        templates

## Using the_loop
[The Loop](http://codex.wordpress.org/The_Loop) is very central for Wordpress. As a result, I've tried to make an implementation for use in Twig.  
The *loop* variable is an instance of the Twig_TWP_Loop class, which is an iterator with Wordpress flavor.  
It's *current* method will give you the current global *$post*, after [the_post](http://codex.wordpress.org/Function_Reference/the_post) has been called.

    {% for post in loop %}
      <h1>{{ post.post_title }}</h1>
    {% else %}
      <p>Nothing to read.</p>
    {% endfor %}

You can always use [the_post action](http://codex.wordpress.org/Plugin_API/Action_Reference/the_post) to alter the post properties runtime.

    function my_post($post)
    {
      $post->content = get_the_content();
    }
    add_action('the_post', 'my_post');

With that in place, you can now use the altered property in Twig.

    {% for post in loop %}
      <h1>{{ post.post_title }}</h1>
      <div class="content">
        {{ post.content|raw }}
      </div>
    {% else %}
      <p>Nothing to read.</p>
    {% endfor %}

## Constants

#####TWP___DEBUG
Twig debug flag. Defaults to WP_DEBUG.

#####TWP___THEME_ROOT
Default set to a folder named "twig", including a trailing slash , within your theme root.

#####TWP___TEMPLATE_PATH
Default set to a folder named "templates", including a trailing slash, within TWP___THEME_ROOT.

#####TWP___CACHE_PATH
A writeable folder for your cache.

## Actions

#####TWP__init
This action is triggered when the Twig_TWP_Environment is instantiated. It provides the environment instance and data as it's parameters.  
This example adds the [Debug Extension](http://twig.sensiolabs.org/doc/extensions/debug.html) to Twig.

    function my_init($twig, $data)
    {
      $twig->addExtension(new Twig_Extension_Debug());
    }
    add_action('TWP__init', 'my_init', 1, 2);

#####TWP__environemnt
This action is triggered in the constructor of Twig_TWP_Environment. It provides the environment instance as it's only parameter.  
This example adds the [query_posts](http://codex.wordpress.org/Function_Reference/query_posts) function to Twig.

    function my_environemnt($environment)
    {
      $environment->addFunction('query_posts', new Twig_Function_Function('query_posts'));
    }
    add_action('TWP__environemnt', 'my_environemnt');

## Filters

#####TWP__template
This filter runs when the Twig template has been found.  
This example makes every template 'merry-christmas.html.twig' when the day and month is 12/24.

    function my_template($name, $index)
    {
      if (date('m/d', time()) == '12/24') {
        return 'merry-christmas.html.twig'
      }
      return $name;
    }
    add_action('TWP__template', 'my_template', 1, 2);

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