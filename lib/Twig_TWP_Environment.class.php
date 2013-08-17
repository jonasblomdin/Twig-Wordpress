<?php

/**
 *
 * Twig-Wordpress Environment
 */
class Twig_TWP_Environment extends Twig_Environment
{
	
  /**
	 *
   * Add functions or other customization tasks for the environment
   *
   * @see Twig_Environment::__construct
   * @param Twig_LoaderInterface $loader A Twig_LoaderInterface instance
   * @param array $options An array of options
   */
  public function __construct(Twig_LoaderInterface $loader = null, $options = array())
  {
		parent::__construct($loader, $options);
    do_action('TWP__environemnt', $this);
	}
	
  /**
	 *
   * Override template paths runtime
   *
	 * @see Twig_Environment::loadTemplate
   * @param string  $name The template name
   * @param integer $index The index if it is an embedded template
   * @return Twig_TemplateInterface A template instance representing the given template name
   */
  public function loadTemplate($name, $index = null)
	{
		$name = apply_filters('TWP__template', $name, $index);
    return parent::loadTemplate($name, $index);
	}
}