<?php
/**
 *
 * Load and display template
 */
$template = $twig->loadTemplate($tpl, null, true);
$template->display($params);