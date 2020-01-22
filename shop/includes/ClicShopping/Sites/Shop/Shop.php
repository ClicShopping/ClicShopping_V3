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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Cookies;
  use ClicShopping\OM\Db;
  use ClicShopping\OM\Hooks;
  use ClicShopping\OM\Language;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Preload;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Session;
  use ClicShopping\OM\Service;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Tools\WhosOnline\Classes\Shop\WhosOnlineShop;

  class Shop extends \ClicShopping\OM\SitesAbstract
  {
    protected static $_application;

    protected function init()
    {
      error_reporting(E_ALL & ~E_NOTICE);
      
      $CLICSHOPPING_Cookies = new Cookies();
      Registry::set('Cookies', $CLICSHOPPING_Cookies);

//check configuration
      if (!CLICSHOPPING::configExists('db_server') || (strlen(CLICSHOPPING::getConfig('db_server')) < 1)) {
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/install')) {
          header('Location: /install/index.php');
          exit;
        } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . '/shop/install/index.php')) {
          header('Location: /shop/install/index.php');
          exit;
        } else {
          echo 'Please look your install directory to begin your new installation like https://wwww.mydomain.com/MyDirectory/install';
          exit;
        }
      }

      try {
        $CLICSHOPPING_Db = Db::initialize();
        Registry::set('Db', $CLICSHOPPING_Db);
      } catch (\Exception $e) {
        include_once(CLICSHOPPING::getConfig('dir_root') . 'includes/error_documents/maintenance.php');
        exit;
      }

      Registry::set('Hooks', new Hooks());

// set the application parameters
      $Qcfg = $CLICSHOPPING_Db->prepare('select configuration_key as k,
                                             configuration_value as v
                                       from :table_configuration
                                       ');
      $Qcfg->setCache('configuration');

      $Qcfg->execute();

      while ($Qcfg->fetch()) {
        define($Qcfg->value('k'), $Qcfg->value('v'));
      }

// set the session name and save path
      $CLICSHOPPING_Session = Session::load();
      Registry::set('Session', $CLICSHOPPING_Session);

// start the session
      $CLICSHOPPING_Session->start();

      $this->ignored_actions[] = session_name();

//request
      if ((HTTP::getRequestType() === 'NONSSL') && ($_SERVER['REQUEST_METHOD'] === 'GET') && (parse_url(CLICSHOPPING::getConfig('http_server'), PHP_URL_SCHEME) == 'https')) {
        $url_req = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        HTTP::redirect($url_req, 301);
      }


// Security
      require_once(CLICSHOPPING::getConfig('dir_root') . 'includes/modules/security_pro/Security.php');
      $security_pro = new \Security();

// If you need to exclude a file from cleansing then you can add it like below
//$security_pro->addExclusion( 'some_file.php' );
      $security_pro->cleanse(CLICSHOPPING::getBaseNameIndex());

//template
      Registry::set('Template', new Template());

// language
      $CLICSHOPPING_Language = new Language();
      $CLICSHOPPING_Language->setUseCache(true);
      Registry::set('Language', $CLICSHOPPING_Language);

// language
// voir ligne 84
      $CLICSHOPPING_Language->getLanguageCode();

// include the language translations
      $CLICSHOPPING_Language->loadDefinitions('main');

// Shopping cart actions
      if (isset($_GET['action'])) {
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
        if (Registry::get('Session')->hasStarted() === false) {
          CLICSHOPPING::redirect(null, 'Info&Cookies');
        }
      }

      WhosOnlineShop::getUpdateWhosOnline();

      Registry::get('Hooks')->watch('Session', 'Recreated', 'execute', function ($parameters) {
        WhosOnlineShop::getWhosOnlineUpdateSession_id($parameters['old_id'], session_id());
      });

      if (is_file(CLICSHOPPING::getConfig('dir_root') . 'includes/config_clicshopping.php')) {
        require_once(CLICSHOPPING::getConfig('dir_root') . 'includes/config_clicshopping.php');
      }

      Registry::set('Service', new Service());
      Registry::get('Service')->start();

      Preload::execute();

//must start after manufacturer service
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Breadcrumb->getCategoriesManufacturer();
    }

    public function setPage()
    {

// en relation avec SitesAbstract
      $page_code = $this->default_page;

      if (class_exists('ClicShopping\Custom\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code)) {
        $class = 'ClicShopping\Custom\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code;
      } elseif (class_exists('ClicShopping\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code)) {
        $class = 'ClicShopping\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code;
      }

      if (!empty($_GET)) {
        if (($route = Apps::getRouteDestination()) !== null) {
          $this->route = $route;

          list($vendor_app, $page) = explode('/', $route['destination'], 2);

// get controller class name from namespace
          $page_namespace = explode('\\', $page);
          $page_code = $page_namespace[count($page_namespace) - 1];

          if (class_exists('ClicShopping\Apps\\' . $vendor_app . '\\' . $page . '\\' . $page_code)) {
            $class = 'ClicShopping\Apps\\' . $vendor_app . '\\' . $page . '\\' . $page_code;
          }
        } else {
          $req = basename(array_keys($_GET)[0]);

          if (class_exists('ClicShopping\Custom\Sites\\' . $this->code . '\Pages\\' . $req . '\\' . $req)) {
            $page_code = $req;

            $class = 'ClicShopping\Custom\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code;
          } elseif (class_exists('ClicShopping\Sites\\' . $this->code . '\Pages\\' . $req . '\\' . $req)) {
            $page_code = $req;

            $class = 'ClicShopping\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code;
          }
        }
      }

      if (isset($class)) {
        if (is_subclass_of($class, 'ClicShopping\OM\PagesInterface')) {
          $this->page = new $class($this);

          $this->page->runActions();
        } else {
          trigger_error('ClicShopping\Sites\Shop\Shop::setPage() - ' . $page_code . ': Page does not implement ClicShopping\OM\PagesInterface and cannot be loaded.');
        }
      }
    }

    public static function resolveRoute(array $route, array $routes)
    {
      $result = [];

      foreach ($routes as $vendor_app => $paths) {
        foreach ($paths as $path => $page) {
          $path_array = explode('&', $path);

          if (count($path_array) <= count($route)) {
            if ($path_array == array_slice($route, 0, count($path_array))) {
              $result[] = [
                'path' => $path,
                'destination' => $vendor_app . '/' . $page,
                'score' => count($path_array)
              ];
            }
          }
        }
      }

      if (!empty($result)) {
        usort($result, function ($a, $b) {
          if ($a['score'] == $b['score']) {
            return 0;
          }

          return ($a['score'] < $b['score']) ? 1 : -1; // sort highest to lowest
        }
        );

        return $result[0];
      }
    }
  }
