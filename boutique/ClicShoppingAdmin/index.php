<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());
  define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../includes/ClicShopping/') . '/');

// Set the level of error reporting
  defined( 'E_DEPRECATED' ) ? error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED ) : error_reporting( E_ALL & ~E_NOTICE );

  require(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  if (PHP_VERSION_ID < 70000) {
    include(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/third_party/random_compat/random.php');
  }

  CLICSHOPPING::loadSite('ClicShoppingAdmin');

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  if (CLICSHOPPING::hasSitePage()) {
    if (CLICSHOPPING::isRPC() === false) {

      $page_file = CLICSHOPPING::getSitePageFile();

      if (empty($page_file) || !is_file($page_file)) {
        $page_file = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/error_documents/404.php';
      }

      if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
        require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('header.php'));
      }

      include($page_file);

      if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
        require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('footer.php'));
      }
    }
    goto main_sub3;
  }

  main_sub3: // Sites and Apps skip to here

  require($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('application_bottom.php'));
