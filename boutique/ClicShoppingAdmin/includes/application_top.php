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
  use ClicShopping\OM\HTTP;

// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

  define('CLICSHOPPING_BASE_DIR', realpath(__DIR__ . '/../../includes/ClicShopping/') . '/');

// Set the level of error reporting
  defined( 'E_DEPRECATED' ) ? error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED ) : error_reporting( E_ALL & ~E_NOTICE );

  require(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');
  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  if (PHP_VERSION_ID < 70000) {
    include(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/third_party/random_compat/random.php');
  }

  CLICSHOPPING::loadSite('ClicShoppingAdmin');

  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

// calculate category path
  if (isset($_POST['cPath']) || isset($_GET['cPath'])) {
    if (isset($_POST['cPath'])) {
      $cPath = $_POST['cPath'];
    } else {
      $cPath = $_GET['cPath'];
    }
  } else {
    $cPath = '';
  }

  if (!empty($cPath)) {
    $cPath_array = $CLICSHOPPING_CategoriesAdmin->getPathArray();
    $cPath = implode('_', $cPath_array);
    $current_category_id = $cPath_array[(count($cPath_array)-1)];
  } else {
    $cPath_array = [];
    $current_category_id = 0;
  }

  unset($group); // unset reference variable
