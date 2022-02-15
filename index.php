<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());
  define('CLICSHOPPING_BASE_DIR', __DIR__ . '/includes/ClicShopping/');

  require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  CLICSHOPPING::loadSite('Shop');

  if (CLICSHOPPING::hasSitePage()) {
    if (CLICSHOPPING::isRPC() === false) {
      $page_file = CLICSHOPPING::getSitePageFile();

      if (empty($page_file) || !is_file($page_file)) {
        $page_file = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'error_documents/404.php';
      }

      if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
        include_once(Registry::get('Template')->getFile('header.php', 'Default'));
      }

      include_once($page_file);

      if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
        require_once(Registry::get('Template')->getFile('footer.php', 'Default'));
      }
    }

    goto main_sub3;
  }

  main_sub3: // Sites and Apps skip to here

  require_once(CLICSHOPPING::BASE_DIR .'/Sites/Shop/Templates/Default/footer.php');
