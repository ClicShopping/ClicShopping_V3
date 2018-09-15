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

  use ClicShopping\Apps\Tools\WhosOnline\Classes\Shop\WhosOnlineShop;

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());
  define('CLICSHOPPING_BASE_DIR', __DIR__ . '/ClicShopping/');

// Set the level of error reporting
  defined( 'E_DEPRECATED' ) ? error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED ) : error_reporting( E_ALL & ~E_NOTICE );

  require(CLICSHOPPING_BASE_DIR . 'OM/CLICSHOPPING.php');

  spl_autoload_register('ClicShopping\OM\CLICSHOPPING::autoload');

  CLICSHOPPING::initialize();

  if (!CLICSHOPPING::configExists('db_server') || (strlen(CLICSHOPPING::getConfig('db_server')) < 1)) {
    if (is_dir('install')) {
      header('Location: boutique/install/index.php');
      exit;
    }
  }

// configuration generale du systeme
  require_once('includes/config_clicshopping.php');

  if (PHP_VERSION_ID < 70000) {
    include('includes/third_party/random_compat/random.php');
  }

  CLICSHOPPING::loadSite('Shop');

// Security Pro
  require_once('includes/modules/security_pro/Security.php');
  $security_pro = new Security();
// If you need to exclude a file from cleansing then you can add it like below
//$security_pro->addExclusion( 'some_file.php' );
  $security_pro->cleanse(CLICSHOPPING::getBaseNameIndex());


  if ((HTTP::getRequestType() === 'NONSSL') && ($_SERVER['REQUEST_METHOD'] === 'GET') && (parse_url(CLICSHOPPING::getConfig('http_server'), PHP_URL_SCHEME) == 'https')) {
    $url_req = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    HTTP::redirect($url_req, 301);
  }

  $CLICSHOPPING_Db = Registry::get('Db');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_CategoryCommon = Registry::get('CategoryCommon');
  $CLICSHOPPING_Category = Registry::get('Category');
  $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');

  Registry::get('Hooks')->watch('Session', 'Recreated', 'execute', function($parameters) {
    WhosOnlineShop::getWhosOnlineUpdateSession_id($parameters['old_id'], session_id());
  });


// Shopping cart actions
  if ( isset($_GET['action']) ) {
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
    if ( Registry::get('Session')->hasStarted() === false ) {
      CLICSHOPPING::redirect('index.php', 'Info&Cookies');
    }
  }

// calculate category path
  if (!is_null($CLICSHOPPING_Category->getPath())) {
    $cPath = $CLICSHOPPING_Category->getPath();
  } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
    $cPath = $CLICSHOPPING_Category->getProductPath($_GET['products_id']);
  } else {
    $cPath = '';
  }

  if (!empty($CLICSHOPPING_Category->getPath())) {
    $cPath_array = $CLICSHOPPING_CategoryCommon->getParseCategoryPath($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = $cPath_array[(count($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }

// add category names or the manufacturer name to the breadcrumb trail
  if (isset($cPath_array)) {
    for ($i=0, $n=count($cPath_array); $i<$n; $i++) {
      $Qcategories = $CLICSHOPPING_Db->get('categories_description', 'categories_name', ['categories_id' => (int)$cPath_array[$i],
                                                                                         'language_id' => $CLICSHOPPING_Language->getId()
                                                                                        ]
                                          );

      if ($Qcategories->fetch() !== false) {
        $CLICSHOPPING_Breadcrumb->add($Qcategories->value('categories_name'), CLICSHOPPING::link('index.php', 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
      } else {
        break;
      }
    }
  } elseif (isset($_GET['manufacturers_id'])) {
    $Qmanufacturer = $CLICSHOPPING_Db->get('manufacturers', 'manufacturers_name', ['manufacturers_id' => (int)$_GET['manufacturers_id']]);

    if ( $Qmanufacturer->fetch() !== false ) {
      $CLICSHOPPING_Breadcrumb->add($Qmanufacturer->value('manufacturers_name'), CLICSHOPPING::link('index.php', 'manufacturers_id=' . (int)$_GET['manufacturers_id']));
    }
  }
