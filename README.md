Twig-Wordpress
==============

An implementation which aims to bring the [Twig Template Engine](http://twig.sensiolabs.org) to [Wordpress](http://wordpress.org) with flexibility and as little hassle as possible.

##Install

Twig-Wordpress is supposed to be required from your theme and comes with Twig as a submodule.  
So start by downloading the source into the theme you want to use Twig in.  
For git users, clone the repo.
```bash
git clone --recursive https://github.com/jonasblomdin/Twig-Wordpress twig-wordpress
```
For subversion users, add it as an external.
```bash
svn propset svn:externals twig-wordpress https://github.com/jonasblomdin/Twig-Wordpress/trunk
```
Load the bootstrap. Put this inside your *functions.php*.
```php
require 'twig-wordpress/bootstrap.php';
```
Your theme should contain a folder with the structure below. The structure could of course be overridden using the [constants](#constants) *before* you load the bootstrap.  
To get started, put an index template in your templates folder. 

    your-theme
      twig
        templates
          index.html.twig

##Templates

The [Template Hierarchy](http://codex.wordpress.org/Template_Hierarchy) used is the same as the original for Wordpress. The file extension is twig though, instead of php.  
The bootstrap loads the correct template based on it's hierarchy and if the template file exists. So make sure your template exists in your [TWP___TEMPLATE_PATH](#twp___template_path).

#####Using The Loop
[The Loop](http://codex.wordpress.org/The_Loop) is very central for Wordpress. As a result, I've tried to make an implementation for use in Twig.  
The *loop* property is an instance of the *Twig\_TWP\_Loop* class, which is an iterator with Wordpress flavor.  
It's *current* method will return the current global *$post*, after [the_post](http://codex.wordpress.org/Function_Reference/the_post) has been called.
```jinja
{% for post in loop %}
  <h1>{{ post.post_title }}</h1>
{% else %}
  <p>Nothing to read.</p>
{% endfor %}
```

You can also use [the_post](http://codex.wordpress.org/Plugin_API/Action_Reference/the_post) action to alter the post specific properties.
```php
function my_post($post)
{
  $post->content = get_the_content();
}
add_action('the_post', 'my_post');
```

With that in place, you can now use the altered post properties in Twig.
```jinja
{% for post in loop %}
  <h1>{{ post.post_title }}</h1>
  <article>
    {{ post.content|raw }}
  </article>
{% else %}
  <p>Nothing to read.</p>
{% endfor %}
```

#####Custom templates
Every post, regardless of type, can also use [Custom Templates](http://codex.wordpress.org/Page_Templates). 
You can specify both name and which post types your custom template should be available for. The *Template Name* is required and the *Post Type* is optional. If no *Post Type* is provided, the template will be available for all post types.
```jinja
{#
Template Name: My Custom Template
Post Type: page, post
#}
```

##Constants

#####TWP___DEBUG
Twig debug flag. Defaults to *WP_DEBUG* constant value. Note that enabling [TWP___DEBUG](#twp___debug) disables the Twig template cache, even though [TWP___CACHE_PATH](#twp___cache_path) is set.

#####TWP___TWIG_ROOT
Twig root path. Defaults to a folder named "twig", including a trailing slash , within your Twig-Wordpress directory.

#####TWP___TEMPLATE_PATH
Twig template path. Defaulta to a folder named "templates", including a trailing slash, within [TWP___TWIG_ROOT](#twp___twig_root).

#####TWP___CACHE_PATH
Twig cache path. A writeable folder for your Twig template cache.

#####TWP___ADMIN
Twig admin flag. Use it to activate, when [TWP___CACHE_PATH](#twp___cache_path) also has been set, an admin menu item which can be used to clear the Twig cache. Defaults to true. 

#####TWP\___CUSTOM\_TEMPLATE\_TYPES
Twig custom templates types. Use it to override which post types that should have the custom template options or use false to disable custom templates. The value must be either false or a serialized array with the post types as values. Defaults to a serialized array containing the values page and post.

##Actions

#####TWP__init
This action is triggered just after the *Twig\_TWP\_Environment* is instantiated. It provides the environment instance and params as it's parameters.  
This example adds the [Debug Extension](http://twig.sensiolabs.org/doc/extensions/debug.html) to Twig.
```php
function my_init($twig, $params)
{
  $twig->addExtension(new Twig_Extension_Debug());
}
add_action('TWP__init', 'my_init', 1, 2);
```

This example adds an additional param to Twig, which can be used in your templates.
```php
function my_init($twig, $params)
{
  $params['home'] = get_bloginfo('url');
}
add_action('TWP__init', 'my_init', 1, 2);
```

#####TWP__environemnt
This action is triggered in the constructor of *Twig\_TWP\_Environment*. It provides the environment instance as it's only parameter.  
This example adds the [query_posts](http://codex.wordpress.org/Function_Reference/query_posts) function to Twig.
```php
function my_environment($environment)
{
  $environment->addFunction('query_posts', new Twig_Function_Function('query_posts'));
}
add_action('TWP__environemnt', 'my_environment');
```

##Filters

#####TWP__options
This filter runs just before the *Twig\_TWP\_Environment* is instantiated. It provides the environment options as it's only parameter.  
Remember that this filter is executed after the [TWP___DEBUG](#twp___debug) and [TWP___CACHE_PATH](#twp___cache_path) constants has been assigned to the options, so changes here will override them.  
This example disables auto-escaping for Twig.
```php
function my_options($options)
{
  $options['autoescape'] = false;
  return $options;
}
add_filter('TWP__options', 'my_options');
```

#####TWP__template
This filter runs when the Twig template has been found.  
This example loads 'happy-new-year.html.twig' as the template on new years eve.
```php
function my_template($name, $index)
{
  if (date('m/d', time()) == '12/31') {
    return 'happy-new-year.html.twig';
  }
  return $name;
}
add_filter('TWP__template', 'my_template', 1, 2);
```

#####TWP\__template\_(type)
The template filters runs when the default Twig templates gets declared.  
This example changes the 404 template name to *not-found.html.twig*.
```php
function my_404_template($filename)
{
  return 'not-found.html.twig';
}
add_filter('TWP__template_404', 'my_404_template');
```

The following template filters are available.

- TWP\__template\_404
- TWP\__template\_search
- TWP\__template\_taxonomy
- TWP\__template\_front-page
- TWP\__template\_home
- TWP\__template\_attachment
- TWP\__template\_single
- TWP\__template\_page
- TWP\__template\_category
- TWP\__template\_tag
- TWP\__template\_author
- TWP\__template\_date
- TWP\__template\_archive
- TWP\__template\_comments-popup
- TWP\__template\_paged
- TWP\__template\_index

#####TWP\__template\_(type)-override
The override template filter runs when the Twig templates, wihich override the defaults, gets declared.  
Note that each overridable template filter need to include '%s', which replaces the corresponding object.  
This example changes the taxonomy override template, which now can be found in the folder *override*.
```php
function my_taxonomy_template_override($filename)
{
  return 'override/'.$filename;
}
add_filter('TWP__template_taxonomy-override', 'my_taxonomy_template_override');
```

The following overridable template filters are available.

- TWP\__template\_taxonomy-override
- TWP\__template\_single-override
- TWP\__template\_page-override
- TWP\__template\_category-override
- TWP\__template\_tag_override
- TWP\__template\_author-override
- TWP\__template\_archive-override