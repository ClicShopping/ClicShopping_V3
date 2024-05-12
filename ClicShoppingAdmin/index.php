<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;

// Start the clock for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());

define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../includes/ClicShopping/') . DIRECTORY_SEPARATOR);

require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

CLICSHOPPING::initialize();

CLICSHOPPING::loadSite('ClicShoppingAdmin');
$CLICSHOPPING_Template = Registry::get('TemplateAdmin');

if (CLICSHOPPING::hasSitePage()) {
  if (CLICSHOPPING::isRPC() === false) {

    $page_file = CLICSHOPPING::getSitePageFile();

    if (empty($page_file) || !is_file($page_file)) {
      HTTP::redirect(CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'error_documents/404.php');
    }

    if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
      require_once($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('header.php'));
    }

    include($page_file);

    if (CLICSHOPPING::useSiteTemplateWithPageFile()) {
      require_once($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('footer.php'));
    }
  }

  goto main_sub3;
}

main_sub3: // Sites and Apps skip to here

require_once($CLICSHOPPING_Template->getTemplateHeaderFooterAdmin('application_bottom.php'));
