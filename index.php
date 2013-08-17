<?php
/**
 *
 * Load and display template
 */
$template = $twig->loadTemplate($tpl);
$template->display($data);