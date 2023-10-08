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
use ClicShopping\OM\Registry;

// start the timer for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());
define('CLICSHOPPING_BASE_DIR', __DIR__ . '/includes/ClicShopping/');

require_once(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

CLICSHOPPING::initialize();

//check configuration
if (!CLICSHOPPING::configExists('db_server') || (\strlen(CLICSHOPPING::getConfig('db_server')) < 1)) {
  if (realpath(__DIR__ . '/install/')) {
    $realDocRoot = realpath($_SERVER['DOCUMENT_ROOT']);
    $realDirPath = realpath(__DIR__);
    $suffix = str_replace($realDocRoot, '', $realDirPath);
    $prefix = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $folderUrl = $prefix . $_SERVER['HTTP_HOST'] . $suffix . '/install';

    header('Location:' . $folderUrl);
    exit;
  } else {
    echo 'Please look your install directory to begin your new installation like https://wwww.mydomain.com/MyDirectory/install';
    exit;
  }
}

CLICSHOPPING::loadSite('Shop');

if (CLICSHOPPING::hasSitePage()) {
  if (CLICSHOPPING::isRPC() === false) {
    $page_file = CLICSHOPPING::getSitePageFile();

    if (empty($page_file) || !is_file($page_file)) {
      $page_file = CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop') . 'error_documents/404.php';
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

require_once(CLICSHOPPING::BASE_DIR . '/Sites/Shop/Templates/Default/footer.php');
