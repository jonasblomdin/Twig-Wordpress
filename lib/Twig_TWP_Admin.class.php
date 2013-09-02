<?php

/**
 *
 * Twig-Wordpress Admin
 */
class Twig_TWP_Admin
{
  
  private $nonce = 'twp_nonce';
  
  /**
   *
   * Render admin page
   *
   * @access public
   * @param string $location
   * @return void
   */
  public function renderCache($location = null)
  {
    global $twig, $params;
    $params += array(
      'action' => 'admin.php?page=twig',
      'nonce' => wp_nonce_field(-1, $this->nonce));
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($this->validate()) {
        $twig->clearCacheFiles();
        $data['message'] = 'The Twig cache was successfully cleared';
      } else $data['error'] = 'Nonce is invalid';
    }
    $twig->setCache(false);
    $twig->setLoader(new Twig_Loader_Filesystem(sprintf('%s/twig/templates/', TWP___ROOT)));
    $twig->display('admin-cache.html.twig', $params);
  }
  
  /**
   *
   * Render metabox
   *
   * @access public
   * @param object $post
   * @return void 
   */
  public function renderMetabox($post)
  {
    global $twig, $params;
    $params += array(
      'nonce' => wp_nonce_field(-1, $this->nonce),
      'name' => TWP___CUSTOM_TEMPLATE,
      'value' => get_post_meta($post->ID, TWP___CUSTOM_TEMPLATE, true),
      'templates' => $this->getTemplates());
    $twig->setCache(false);
    $twig->setLoader(new Twig_Loader_Filesystem(sprintf('%s/twig/templates/', TWP___ROOT)));
    $twig->display('admin-metabox.html.twig', $params);
  }
  
  /**
   *
   * Save metabox input
   *
   * @access public
   * @param integer $post_id
   * @return mixed
   */
  public function saveMetabox($post_id)
  {
    if (!$this->validate()) {
      return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    $template = sanitize_text_field($_POST[TWP___CUSTOM_TEMPLATE]);
    update_post_meta($post_id, TWP___CUSTOM_TEMPLATE, $template);
  }
  
  /**
   *
   * Retrive templates
   *
   * @todo Get comment tags by Twig lexer instead of using defaults
   * @access private
   * @return array
   */
  private function getTemplates()
  {
    $files = (array) $this->scandir(TWP___TEMPLATE_PATH, 'twig', -1);
    foreach ($files as $file => $path)
    {
      if (!preg_match('|Template Name:(.*)$|mi', file_get_contents($path), $header))
        continue;
      $header = trim(preg_replace("/\s*(?:\*\/|#}).*/", '', $header[1]));
      $page_templates[$file] = $header;
    }
    return $page_templates;
  }
  
  /**
   *
   * Validate nonce
   *
   * @access private
   * @return boolean
   */
  private function validate()
  {
    if (!isset($_POST[$this->nonce])) {
      return false;
    }
    
    $nonce = $_POST[$this->nonce];
    if (!wp_verify_nonce($nonce)) {
      return false;
    }
    return true;
  }
  
	/**
	 * Scans a directory for files of a certain extension.
	 *
   * @see WP_Theme::scandir
	 */
	private function scandir($path, $extensions = null, $depth = 0, $relative_path = '')
  {
		if (!is_dir($path))
			return false;

		if ($extensions) {
			$extensions = (array) $extensions;
			$_extensions = implode('|', $extensions);
		}

		$relative_path = trailingslashit($relative_path);
		if ('/' == $relative_path)
			$relative_path = '';

		$results = scandir($path);
		$files = array();

		foreach ($results as $result)
    {
			if ('.' == $result[0])
				continue;
			if (is_dir($path . '/' . $result)) {
				if (!$depth || 'CVS' == $result)
					continue;
				$found = $this->scandir($path . '/' . $result, $extensions, $depth - 1 , $relative_path . $result);
				$files = array_merge_recursive($files, $found);
			} elseif (!$extensions || preg_match('~\.(' . $_extensions . ')$~', $result)) {
				$files[$relative_path . $result] = $path . '/' . $result;
			}
		}
		return $files;
	}
}