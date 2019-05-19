<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\RSS\Actions;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Communication\PageManager\Classes\Shop\RSS as RSSApp;

  class RSS extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_RSS = new RSSApp();
      Registry::set('RSS', $CLICSHOPPING_RSS);

      $CLICSHOPPING_RSS = Registry::get('RSS');

      if (!function_exists('getallheaders')) {
        function getallheaders()
        {
          settype($headers, 'array');
          foreach ($_SERVER as $h => $v) {
            if (preg_match('#HTTP_(.+)#', $h, $hp)) {
              $headers[$hp[1]] = $v;
            }
          }
          return $headers;
        }
      }

      header('Content-Type: application/rss+xml; charset=UTF-8');
      header('Last-Modified: ' . gmdate("D, d M Y G:i:s", strtotime($CLICSHOPPING_RSS->productDateAdded())) . ' GMT');

      $CLICSHOPPING_RSS->getMaxListing(20);
      echo $CLICSHOPPING_RSS->displayFeed();
      exit;
    }
  }
