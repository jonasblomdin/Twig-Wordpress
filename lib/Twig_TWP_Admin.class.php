<?php

/**
 *
 * Twig-Wordpress Admin
 */
class Twig_TWP_Admin
{
  
  /**
   *
   * Render admin page
   *
   * @param string $location
   * @return void
   */
  public function render($location = null)
  {
    global $twig;
    $data = array(
      'action' => 'admin.php?page=twig');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->delete(TWP___CACHE_PATH);
      $data['message'] = 'The Twig cache was successfully cleared';
    }
    $twig->setLoader(new Twig_Loader_Filesystem(sprintf('%s/twig/templates/', TWP_ROOT)));
    $twig->display('admin.html.twig', $data);
  }
  
  /**
   *
   * Recursive delete files and folders
   *
   * @param string $dir
   * @return void
   */
  private function delete($dir)
  {
    $handle = opendir($dir);
    while (false !== ($entry = readdir($handle)))
    {
      $path = sprintf('%s/%s', $dir, $entry);
      if (substr($entry, 0, 1) == '.') continue;
      if (is_dir($path)) {
        $this->delete($path);
        rmdir($path);
      } else {
        unlink($path);
      }
    }
    unset($handle);
  }
}