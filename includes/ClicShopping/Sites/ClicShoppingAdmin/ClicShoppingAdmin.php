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

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Cookies;
  use ClicShopping\OM\Db;
  use ClicShopping\OM\Hooks;
  use ClicShopping\OM\Language;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Session;
  use ClicShopping\OM\Service;
  use ClicShopping\OM\HTTP;

  class ClicShoppingAdmin extends \ClicShopping\OM\SitesAbstract
  {

    protected static string $default_application = 'Dashboard';

    protected function init()
    {
      global $login_request;

      $CLICSHOPPING_Cookies = new Cookies();
      Registry::set('Cookies', $CLICSHOPPING_Cookies);

      try {
        $CLICSHOPPING_Db = Db::initialize();
        Registry::set('Db', $CLICSHOPPING_Db);
      } catch (\Exception $e) {
        include_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/error_documents/maintenance.php');
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

// Used in the "Backup Manager" to compress backups
      define('LOCAL_EXE_GZIP', 'gzip');
      define('LOCAL_EXE_GUNZIP', 'gunzip');
      define('LOCAL_EXE_ZIP', 'zip');
      define('LOCAL_EXE_UNZIP', 'unzip');

      $CLICSHOPPING_Session = Session::load();
      Registry::set('Session', $CLICSHOPPING_Session);

      $CLICSHOPPING_Session->start();

// language
      $CLICSHOPPING_Language = new Language();

      Registry::set('Language', $CLICSHOPPING_Language);

// Template
      Registry::set('TemplateAdmin', new TemplateAdmin());

// Take language session
      $CLICSHOPPING_Language->getLanguageCode();

// redirect to login page if administrator is not yet logged in
      if (!isset($_SESSION['admin'])) {
        $redirect = false;

        $current_page = CLICSHOPPING::getBaseNameIndex();

// if the first page request is to the login page, set the current page to the index page
// so the redirection on a successful login is not made to the login page again
        if (($current_page == 'login.php') && !isset($_SESSION['redirect_origin'])) {
          $current_page = 'index.php';
        }

        if ($current_page != 'login.php') {
          if (!isset($_SESSION['redirect_origin'])) {
            $_SESSION['redirect_origin'] = ['page' => $current_page,
              'get' => []
            ];
          }

// try to automatically login with the HTTP Authentication values if it exists
          if (!isset($_SESSION['auth_ignore'])) {
            if (isset($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['PHP_AUTH_PW'])) {
              $_SESSION['redirect_origin']['auth_user'] = $_SERVER['PHP_AUTH_USER'];
              $_SESSION['redirect_origin']['auth_pw'] = $_SERVER['PHP_AUTH_PW'];
            }
          }

          $redirect = true;
        }

        if (!isset($login_request) || isset($_GET['login_request']) || isset($_POST['login_request']) || isset($_COOKIE['login_request']) || isset($_SESSION['login_request']) || isset($_FILES['login_request']) || isset($_SERVER['login_request'])) {
          $redirect = true;
        }

        if ($redirect === true) {
          CLICSHOPPING::redirect('login.php', (isset($_SESSION['redirect_origin']['auth_user']) ? 'action=process' : ''));
        }
      }

// include the language translations
      $CLICSHOPPING_Language->loadDefinitions('main');

      $current_page = CLICSHOPPING::getBaseNameIndex();

      if ($CLICSHOPPING_Language->definitionsExist(pathinfo($current_page, PATHINFO_FILENAME))) {
        $CLICSHOPPING_Language->loadDefinitions(pathinfo($current_page, PATHINFO_FILENAME));
      }

      if ((HTTP::getRequestType() === 'NONSSL') && ($_SERVER['REQUEST_METHOD'] === 'GET') && (parse_url(CLICSHOPPING::getConfig('http_server'), PHP_URL_SCHEME) == 'https')) {
        $url_req = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        HTTP::redirect($url_req, 301);
      }

// configuration generale du systeme
      require_once(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/config_clicshopping.php');

      Registry::set('Service', new Service());
      Registry::get('Service')->start();
    }

    public function setPage()
    {

//important dans la redirection sinon page blanche
      $page_code = static::getDefaultApplication();
      $class = 'ClicShopping\Sites\\' . $this->code . '\Pages\\' . $page_code . '\\' . $page_code;

      if (!empty($_GET)) {
        $req = basename(array_keys($_GET)[0]);

        if (($req == 'A') && (\count($_GET) > 1)) {
          $app = array_keys($_GET)[1];

          if (str_contains($app, '\\')) {
            list($vendor, $app) = explode('\\', $app);

            if (Apps::exists($vendor . '\\' . $app) && ($page = Apps::getRouteDestination(null, $vendor . '\\' . $app)) !== null) {
// get controller class name from namespace
              $page_namespace = explode('\\', $page);
              $page_code = $page_namespace[\count($page_namespace) - 1];

              if (class_exists('ClicShopping\Apps\\' . $vendor . '\\' . $app . '\\' . $page . '\\' . $page_code)) {
                $this->app = $vendor . '\\' . $app;
                $this->route = $this->app . '\\' . $page;
                $this->actions_index = 2;

                $class = 'ClicShopping\Apps\\' . $this->app . '\\' . $page . '\\' . $page_code;
              }
            }
          }
        } else {
          if (class_exists('ClicShopping\Sites\\' . $this->code . '\Pages\\' . $req . '\\' . $req)) {
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
          trigger_error('ClicShopping\Sites\ClicShoppingAdmin\ClicShoppingAdmin::setPage() - ' . $page_code . ': Page does not implement ClicShopping\OM\PagesInterface and cannot be loaded.');
        }
      }
    }

    /**
     * @param array $route
     * @param array $routes
     */
    public static function resolveRoute(array $route, array $routes)
    {
      return array_values($routes)[0];
    }

    /**
     * @return string
     */
    public static function getDefaultApplication(): string
    {
      return static::$default_application;
    }
  }
