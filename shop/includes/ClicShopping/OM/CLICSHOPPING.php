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

  namespace ClicShopping\OM;

  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\ErrorHandler;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;

  use ClicShopping\Service\Shop\SEFU;

  class CLICSHOPPING {
    const BASE_DIR = CLICSHOPPING_BASE_DIR;

    protected static $version;
    protected static $site = 'Shop';
    protected static $cfg = [];
    protected static $_application;

    public static function initialize()  {

      static::loadConfig();

      DateTime::setTimeZone();

      ErrorHandler::initialize();

      HTTP::setRequestType();
      HTTP::getHSTS();

      static::setSiteApplication();
    }

/**
 * Get the installed version number
 *
 * @param string $v get the clicshopping_verion
 * @retunr : version of the site
 * @access public
 */
    public static function getVersion()  {
      if (!isset(static::$version)) {
        $file = static::BASE_DIR . 'version.json';

        $current = trim(file_get_contents($file));

        $v = json_decode($current);

        if (is_numeric($v->version)) {
          static::$version = $v->version;
        } else {
          trigger_error('Version number is not numeric. Please verify: ' . $file);
        }
      }
      return static::$version;
    }

    public static function siteExists($site, $strict = true) {
      $class = 'ClicShopping\Sites\\' . $site . '\\' . $site;

      if (class_exists($class)) {
        if (is_subclass_of($class, 'ClicShopping\OM\SitesInterface')) {
          return true;
        } else {
          trigger_error('ClicShopping\OM\CLICSHOPPING::siteExists() - ' . $site . ': Site does not implement ClicShopping\OM\SitesInterface and cannot be loaded.');
        }
      } elseif ($strict === true) {
        trigger_error('ClicShopping\OM\CLICSHOPPING::siteExists() - ' . $site . ': Site does not exist.');
      }

      return false;
    }

    public static function loadSite($site = null)  {

      if (!isset($site)) {
        $site = static::$site;
      }

      static::setSite($site);
    }

    public static function setSite($site) {

      if (!static::siteExists($site)) {
        $site = static::$site;
      }

       static::$site = $site;

      $class = 'ClicShopping\Sites\\' . $site . '\\' . $site;

      $CLICSHOPPING_Site = new $class();
      Registry::set('Site', $CLICSHOPPING_Site);

      $CLICSHOPPING_Site->setPage();
    }

    public static function getSite()  {
      return static::$site;
    }

    public static function hasSitePage()  {
      return Registry::get('Site')->hasPage();
    }

    public static function getSitePageFile()  {
      return Registry::get('Site')->getPage()->getFile();
    }

    public static function useSiteTemplateWithPageFile() {
      return Registry::get('Site')->getPage()->useSiteTemplate();
    }

    public static function isRPC()  {
      $CLICSHOPPING_Site = Registry::get('Site');

      return $CLICSHOPPING_Site->hasPage() && $CLICSHOPPING_Site->getPage()->isRPC();
    }

/**
 * Return an internal URL address.
 *
 * @param string $page The Site to link to. Default: The currently used Site.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @param bool $add_session_id Add the session ID to the link. Default: True.
 * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
 * @return string The URL address.
 */
    public static function link($page = null, $parameters = null, $add_session_id = true, $search_engine_safe = true)  {

      if (is_null($page)) {
        $page = static::getConfig('bootstrap_file');
      }

      $page = HTML::sanitize($page);

      $site = $req_site = static::$site;

      if ((strpos($page, '/') !== false) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $page, $matches) === 1) && static::siteExists($matches[1], false)) {
          $req_site = $matches[1];
          $page = $matches[2];
      }

      if (!is_bool($add_session_id)) {
        $add_session_id = true;
      }

      if (!is_bool($search_engine_safe)) {
        $search_engine_safe = true;
      }

      if (($add_session_id === true) && ($site !== $req_site)) {
        $add_session_id = false;
      }

      $link = static::getConfig('http_server', $req_site) . static::getConfig('http_path', $req_site) . $page;

      if (!empty($parameters)) {
        $p = HTML::sanitize($parameters);

        if (static::$site == 'ClicShoppingAdmin') {
          $replace_backslash = "%5C";
        } else {
          $replace_backslash = "\\";
        }

        $p = str_replace([
                          "\\", // apps
                          '{', // product attributes
                          '}' // product attributes
                          ], [
                            $replace_backslash,
                            '%7B',
                            '%7D'
                          ], $p);

        $link .= '?' . $p;
        $separator = '&';
      } else {
        $separator = '?';
      }

      while((substr($link, -1) == '&') || (substr($link, -1) == '?')) {
        $link = substr($link, 0, -1);
      }

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
      if (($add_session_id === true) && Registry::exists('Session')) {
        $CLICSHOPPING_Session = Registry::get('Session');

        if ($CLICSHOPPING_Session->hasStarted() && ($CLICSHOPPING_Session->isForceCookies() === false)) {
          if ((strlen(SID) > 0) || (((HTTP::getRequestType() == 'NONSSL') && (parse_url(static::getConfig('http_server', $req_site), PHP_URL_SCHEME) == 'https')) || ((HTTP::getRequestType() == 'SSL') && (parse_url(static::getConfig('http_server', $req_site), PHP_URL_SCHEME) == 'http')))) {
            $link .= $separator . HTML::sanitize(session_name() . '=' . session_id());
          }
        }
      }

      while(strpos($link, '&&') !== false) {
        $link = str_replace('&&', '&', $link);
      }

      if ($search_engine_safe === true && defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true' && SEFU::start() && static::getSite() != 'ClicShoppingAdmin') {
        $link = str_replace(['?', '&', '='], ['/', '/', '-'], $link);
      }

      return $link;
    }

    public static function linkImage() {
        $args = func_get_args();

        if (!isset($args[0])) {
            $args[0] = null;
        }

        if (!isset($args[1])) {
            $args[1] = null;
        }

        $args[2] = false;

        $page = $args[0];
        $req_site = static::$site;

        if ((strpos($page, '/') !== false) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $page, $matches) === 1) && static::siteExists($matches[1], false)) {
          $req_site = $matches[1];
          $page = $matches[2];
        }

        $args[0] = $req_site . '/' . static::getConfig('http_images_path', $req_site) . $page;

        $url = forward_static_call_array('static::link', $args);

        return $url;
    }

/**
 * Return an internal URL address for public objects.
 *
 * @param string $url The object location from the public/sites/SITE/ directory.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @param string $site Get a public link from a specific Site
 * @return string The URL address.
 */
    public static function linkPublic() {
      $args = func_get_args();

      if (!isset($args[0])) {
        $args[0] = null;
      }

      if (!isset($args[1])) {
        $args[1] = null;
      }

      $args[2] = false;

      $page = $args[0];
      $req_site = static::$site;

      if ((strpos($page, '/') !== false) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $page, $matches) === 1) && static::siteExists($matches[1], false)) {
        $req_site = $matches[1];
        $page = $matches[2];
      }

      $args[0] = 'Shop/public/Sites/' . $req_site . '/' . $page;

      $url = forward_static_call_array('static::link', $args);

      return $url;
    }

/**
 * Redirect to a page
 *
 * @param string $v get the clicshopping_verion
 * @return string $url, url to redirect
 * @access public
 */
    public static function redirect() {
      $args = func_get_args();

      $url = forward_static_call_array('static::link', $args);

      if ((strstr($url, "\n") !== false) || (strstr($url, "\r") !== false)) {
        $url = static::link(null, '', false);
      }

      HTTP::redirect($url);
    }

/**
 * Return a language definition
 *
 * @param string $key The language definition to return
 * @param array $values Replace keywords with values
 * @return string The language definition
 */
    public static function getDef() {
      $CLICSHOPPING_Language = Registry::get('Language');

      return call_user_func_array([$CLICSHOPPING_Language, 'getDef'], func_get_args());
    }

    public static function hasRoute(array $path)  {
      return array_slice(array_keys($_GET), 0, count($path)) == $path;
    }

    public static function loadConfig() {
      static::loadConfigFile(static::BASE_DIR . 'Conf/global.php', 'global');

      if (is_file(static::BASE_DIR . 'Custom/Conf/global.php')) {
         static::loadConfigFile(static::BASE_DIR . 'Custom/Conf/global.php', 'global');
      }

      foreach (glob(static::BASE_DIR . 'Sites/*', GLOB_ONLYDIR) as $s) {
        $s = basename($s);

        if (static::siteExists($s, false) && is_file(static::BASE_DIR . 'Sites/' . $s . '/site_conf.php')) {
          static::loadConfigFile(static::BASE_DIR . 'Sites/' . $s . '/site_conf.php', $s);

          if (is_file(static::BASE_DIR . 'Custom/Sites/' . $s . '/site_conf.php')) {
            static::loadConfigFile(static::BASE_DIR . 'Custom/Sites/' . $s . '/site_conf.php', $s);
          }
        }
      }
    }

    public static function loadConfigFile($file, $group) {

      $cfg = [];

      if (is_file($file)) {
        include($file );

        if (isset($ini)) {
            $cfg = parse_ini_string($ini);
        }
      }

      if (!empty($cfg)) {
        static::$cfg[$group] = (isset(static::$cfg[$group])) ? array_merge(static::$cfg[$group], $cfg) : $cfg;
      }
    }

    public static function getConfig($key, $group = null) {
      if (!isset($group)) {
          $group = static::getSite();
      }

      if (isset(static::$cfg[$group][$key])) {
          return static::$cfg[$group][$key];
      }

      return static::$cfg['global'][$key];
    }

    public static function configExists($key, $group = null) {
      if (!isset($group)) {
          $group = static::getSite();
      }

      if (isset(static::$cfg[$group][$key])) {
          return true;
      }

      return isset(static::$cfg['global'][$key]);
    }

    public static function setConfig($key, $value, $group = null) {
      if (!isset($group)) {
          $group = 'global';
      }

      static::$cfg[$group][$key] = $value;
    }

    public static function autoload($class)  {
      $prefix = 'ClicShopping\\';

      if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return false;
      }

      if (strncmp($prefix . 'OM\Module\\', $class, strlen($prefix . 'OM\Module\\')) === 0) { // TODO remove and fix namespace
        $file = dirname(CLICSHOPPING_BASE_DIR) . '/' . str_replace(['ClicShopping\OM\\', '\\'], ['', '/'], $class) . '.php';
        $custom = dirname(CLICSHOPPING_BASE_DIR) . '/' . str_replace(['ClicShopping\OM\\', '\\'], ['ClicShopping\Custom\OM\\', '/'], $class) . '.php';
      } else {
        $file = dirname(CLICSHOPPING_BASE_DIR) . '/' . str_replace('\\', '/', $class) . '.php';
        $custom = str_replace('ClicShopping/OM/', 'ClicShopping/Custom/OM/', $file);
      }

      if (is_file($custom)) {
        require_once($custom);
      } elseif (is_file($file)) {
        require_once($file);
      }
    }


/**
 * Retrieve web server and database server information
 * return $data, array og php.ini information
 * @access public
 */
    public static function getSystemInformation() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qdate = $CLICSHOPPING_Db->query('select now() as datetime');

      @list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

      $data = [];

      $data['clicshopping']  = ['version' => static::getVersion()];

      $data['system'] = ['date' => date('Y-m-d H:i:s O T'),
                          'os' => PHP_OS,
                          'kernel' => $kernel,
                          'uptime' => @exec('uptime'),
                          'http_server' => $_SERVER['SERVER_SOFTWARE']
                        ];

      $data['mysql']  = ['version' => $CLICSHOPPING_Db->getAttribute(\PDO::ATTR_SERVER_VERSION),
                         'date' => $Qdate->value('datetime')
                        ];

      $data['php']    = ['version' => PHP_VERSION,
                          'zend' => zend_version(),
                          'sapi' => PHP_SAPI,
                          'int_size' => defined('PHP_INT_SIZE') ? PHP_INT_SIZE : '',
                          'open_basedir' => (int) @ini_get('open_basedir'),
                          'memory_limit' => @ini_get('memory_limit'),
                          'error_reporting' => error_reporting(),
                          'display_errors' => (int)@ini_get('display_errors'),
                          'allow_url_fopen' => (int) @ini_get('allow_url_fopen'),
                          'allow_url_include' => (int) @ini_get('allow_url_include'),
                          'file_uploads' => (int) @ini_get('file_uploads'),
                          'upload_max_filesize' => @ini_get('upload_max_filesize'),
                          'post_max_size' => @ini_get('post_max_size'),
                          'disable_functions' => @ini_get('disable_functions'),
                          'disable_classes' => @ini_get('disable_classes'),
                          'enable_dl'	=> (int) @ini_get('enable_dl'),
                          'filter.default'   => @ini_get('filter.default'),
                          'unicode.semantics' => (int) @ini_get('unicode.semantics'),
                          'zend_thread_safty'	=> (int) function_exists('zend_thread_id'),
                          'extensions' => get_loaded_extensions()
                          ];

      return $data;
    }

    protected static function siteApplicationExists($application) {
      $class = 'ClicShopping\\Sites\\' . static::getSite() . '\\Pages\\' . $application . '\\' . $application;

      return $class;
    }

    protected static function setSiteApplication($application = null) {
      if ( isset($application) ) {
        if ( !static::siteApplicationExists($application) ) {
          trigger_error('Application \'' . $application . '\' does not exist for Site \'' . static::getSite());

          $application = null;
        }
      } else {
        if ( !empty($_GET) ) {
          $requested_application = HTML::sanitize(basename(key(array_slice($_GET, 0, 1, true))));

          if ( $requested_application == static::getSite() ) {
            $requested_application = HTML::sanitize(basename(key(array_slice($_GET, 1, 1, true))));
          }

          if ( !empty($requested_application) && static::siteApplicationExists($requested_application) ) {
            $application = $requested_application;
          }
        }
      }
      static::$_application = $application;
    }

    public static function getSiteApplication() {
      return static::$_application;
    }


/**
 * Get all parameters in the GET scope
 *
 * @param array $exclude A list of parameters to exclude
 * @return string
 */
    public static function getAllGET($exclude = null) {
      if ( !is_array($exclude) ) {
        if ( !empty($exclude) ) {
          $exclude = [$exclude];
        } else {
          $exclude = [];
        }
      }

      $params = '';

      $array = [static::getSite(),
                Registry::get('Session')->getName(),
                'error',
                'x',
                'y'
                ];

      $exclude = array_merge($exclude, $array);

      foreach ( $_GET as $key => $value ) {
        if ( !in_array($key, $exclude) ) {
           $params .= $key . (!empty($value) ? '=' . $value : '') . '&';
         }
      }

      if ( !empty($params) ) {
        $params = substr($params, 0, -1);
      }

      return $params;
    }

/*  the global scope
*   @return String : element of url like ClicShoppingAdmin/index.php or Shop/index.php
*/
    public static function getIndex() {
      $req = parse_url($_SERVER['SCRIPT_NAME']);
      $result = substr($req['path'], strlen(static::getConfig('http_path', 'Shop')));

      return $result;
    }

/*  Take only index.php
*   @return String : element of url like index.php
*/
    public static function getBaseNameIndex() {
      return basename(static::getIndex());
    }


    public static function ArrayToString($array , $exclude = '', $equals = '=', $separator = '&') {
      if (!is_array($exclude)) $exclude = [];

      $get_string = '';

      if (!empty($array)) {
        foreach ($array as $key => $value) {
          if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
            $get_string .= $key . $equals . $value . $separator;
          }
        }
        $remove_chars = strlen($separator);
        $get_string = substr($get_string, 0, -$remove_chars);
      }

      return $get_string;
    }
 }
