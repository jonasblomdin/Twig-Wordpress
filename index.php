<?php
/**
 *
 * Load and display template
 */
$template = $twig->loadTemplate($template);
$template->display($data);