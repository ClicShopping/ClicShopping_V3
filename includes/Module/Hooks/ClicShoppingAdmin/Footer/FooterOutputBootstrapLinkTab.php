<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\ClicShoppingAdmin\Footer;

class FooterOutputBootstrapLinkTab
{
  /**
   * @return string|bool
   */
  public function display(): string|bool
  {
    $params = $_SERVER['QUERY_STRING'];

    if (empty($params)) {
      return false;
    }

    $output = '';

    if (isset($_SESSION['admin'])) {
      $output .= '<!-- Bootstrap Link tab Script start-->' . "\n";
      $output .= '
  <!-- if the page request contains a link to a tab, open that tab on page load -->
  <script>
      $(function () {
          var url = document.location.toString();
  
          if (url.match(\'#\')) {
              if ($(\'.nav-tabs a[data-bs-target="#\' + url.split(\'#\')[1] + \'"]\').length === 1) {
                  $(\'.nav-tabs a[data-bs-target="#\' + url.split(\'#\')[1] + \'"]\').tab(\'show\');
              }
          }
      });
  </script>
          ' . "\n";
      $output .= '<!--Bootstrap Link tab end -->' . "\n";
    } else {
      return false;
    }

    return $output;
  }
}