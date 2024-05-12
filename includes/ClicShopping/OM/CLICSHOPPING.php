<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use ClicShopping\Service\Shop\SEFU;
use DirectoryIterator;
use PDO;
use function array_slice;
use function call_user_func_array;
use function count;
use function defined;
use function func_get_args;
use function function_exists;
use function is_array;
use function is_null;
use function strlen;

class CLICSHOPPING
{
  public const BASE_DIR = CLICSHOPPING_BASE_DIR;
  public const VALID_CLASS_NAME_REGEXP = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/'; // https://php.net/manual/en/language.oop5.basic.php

  protected static string $version;
  protected static string $site = 'Shop';
  protected static array $cfg = [];
  protected static ?string $application;

  public static function initialize()
  {
    self::loadConfig();

    DateTime::setTimeZone();

    ErrorHandler::initialize();

    HTTP::setRequestType();
    HTTP::getHSTS();

    self::setSiteApplication();
  }

  /**
   * Get the installed version number
   * @return string|null
   */
  public static function getVersion(): string|null
  {
    if (!isset(self::$version)) {
      $file = self::BASE_DIR . 'version.json';

      $current = trim(file_get_contents($file));

      $v = json_decode($current);

      if (is_numeric($v->version)) {
        self::$version = $v->version;
      } else {
        trigger_error('Version number is not numeric. Please verify: ' . $file);
      }
    }
    return self::$version;
  }

  /**
   * Get the installed directory version number
   * @return string|null
   */
  public static function getVersionDirectory(): string|null
  {
    if (!isset(self::$version)) {
      $file = self::BASE_DIR . 'version.json';

      $current = trim(file_get_contents($file));

      $v = json_decode($current);

      if (is_numeric($v->directory_version)) {
        self::$directoryVersion = $v->directory_version;
      } else {
        trigger_error('Directory Version number is not numeric. Please verify: ' . $file);
      }
    }
    return self::$directoryVersion;
  }

  /**
   * @param string $site
   * @param bool $strict
   * @return bool
   */
  public static function siteExists(string $site): bool
  {
    return self::isValidClassName($site) && class_exists('ClicShopping\Sites\\' . $site . '\\' . $site);
  }

  /**
   * @param string|null $site
   */
  public static function loadSite(string|null $site = null)
  {
    if (!isset($site)) {
      $site = self::$site;
    }

    self::setSite($site);
  }

  /**
   * @param string $site
   */
  public static function setSite(string $site)
  {
    if (!self::siteExists($site)) {
      $site = self::$site;
    }

    self::$site = $site;

    $class = 'ClicShopping\Sites\\' . $site . '\\' . $site;

    $CLICSHOPPING_Site = new $class();
    Registry::set('Site', $CLICSHOPPING_Site);

    $CLICSHOPPING_Site->setPage();
  }

  /**
   * @return string
   */
  public static function getSite(): string
  {
    return self::$site;
  }

  /**
   * @return bool
   */
  public static function hasSite(): bool
  {
    return isset(self::$site);
  }

  /**
   * @return mixed
   */
  public static function hasSitePage(): mixed
  {
    return Registry::get('Site')->hasPage();
  }

  /**
   * @return mixed
   */
  public static function getSitePageFile(): mixed
  {
    return Registry::get('Site')->getPage()->getFile();
  }

  /**
   * @return mixed
   */
  public static function useSiteTemplateWithPageFile(): mixed
  {
    return Registry::get('Site')->getPage()->useSiteTemplate();
  }

  /**
   * @return bool
   */
  public static function isRPC(): bool
  {
    $CLICSHOPPING_Site = Registry::get('Site');

    return $CLICSHOPPING_Site->hasPage() && $CLICSHOPPING_Site->getPage()->isRPC();
  }

  /**
   * Return an internal URL address.
   *
   * @param string|null $page The Site to link to. Default: The currently used Site.
   * @param string|null $parameters Parameters to add to the link. Example: key1=value1&key2=value2
   * @param bool $add_session_id Add the session ID to the link. Default: True.
   * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
   * @return string The URL address.
   */
  public static function link(string $page = null, string $parameters = null, bool $add_session_id = true, bool $search_engine_safe = true): string
  {
    /*
     * remove index.php for the seo
     */
    if (is_null($page)) {
      if (self::getSite() === 'ClicShoppingAdmin') {
        $page = self::getConfig('bootstrap_file');
      } else {
        if ((defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true') && (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true')) {
//SEO with htaccess
// force to remove seo htaccess if the customer is connected
          if (isset($_SESSION['login_customer_id'])) {
            $page = self::getConfig('bootstrap_file');
          } else {
            $page = '';
          }
        } elseif ((defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true')) {
          $page = self::getConfig('bootstrap_file');
        } else {
          $page = self::getConfig('bootstrap_file');
        }
      }
    }

    $page = HTML::sanitize($page);

    $site = $req_site = self::$site;

    if ((str_contains($page, '/')) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $page, $matches) === 1) && self::siteExists($matches[1])) {
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

    $link = self::getConfig('http_server', $req_site) . self::getConfig('http_path', $req_site) . $page;

    if (!empty($parameters)) {
      $p = HTML::sanitize($parameters);

      if (self::$site == 'ClicShoppingAdmin') {
        $replace_backslash = "%5C";
      } else {
        $replace_backslash = "\\";
      }

      $search = [
        "\\", // apps
        '{', // product attributes
        '}' // product attributes
      ];

      $replace = [
        $replace_backslash,
        '%7B',
        '%7D'
      ];

      $p = str_replace($search, $replace, $p);
//xss patch
      if ((defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'false')) {
        $p = htmlspecialchars($p);
      }

      $link .= '?' . $p;
      $separator = '&';
    } else {
      $separator = '?';
    }

    while ((substr($link, -1) == '&') || (substr($link, -1) == '?')) {
      $link = substr($link, 0, -1);
    }

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if (($add_session_id === true) && Registry::exists('Session')) {
      $CLICSHOPPING_Session = Registry::get('Session');

      if ($CLICSHOPPING_Session->hasStarted() && ($CLICSHOPPING_Session->isForceCookies() === false)) {
        if ((strlen(SID) > 0) || (((HTTP::getRequestType() == 'NONSSL') && (parse_url(self::getConfig('http_server', $req_site), PHP_URL_SCHEME) == 'https')) || ((HTTP::getRequestType() == 'SSL') && (parse_url(self::getConfig('http_server', $req_site), PHP_URL_SCHEME) == 'http')))) {
          $link .= $separator . HTML::sanitize(session_name() . '=' . session_id());
        }
      }
    }

    while (str_contains($link, '&&')) {
      $link = str_replace('&&', '&', $link);
    }

    /*
     * Change url syntax if Seo is enable or not
     */

    if (self::getSite() === 'Shop') {
//SEO with htaccess
      if ($search_engine_safe === true && SEFU::start() && defined('SEARCH_ENGINE_FRIENDLY_URLS_PRO') && SEARCH_ENGINE_FRIENDLY_URLS_PRO == 'true' && (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true')) {
//SEO with htaccess
// remove seo htaccess if the customer is connected
        if (isset($_SESSION['login_customer_id'])) {
          $link = str_replace(['?', '&', '='], ['/', '/', '-'], $link);
        } else {
          $link = str_replace(['?', '&', '='], ['', '/', '-'], $link);
        }
      } elseif ($search_engine_safe === true && SEFU::start() && (defined('SEARCH_ENGINE_FRIENDLY_URLS') && SEARCH_ENGINE_FRIENDLY_URLS == 'true')) {
        $link = str_replace(['?', '&', '='], ['/', '/', '-'], $link);
      }
    }

    return $link;
  }

  /**
   * @return string
   */
  public static function linkImage(): string
  {
    $args = func_get_args();

    if (!isset($args[0])) {
      $args[0] = null;
    }

    if (!isset($args[1])) {
      $args[1] = null;
    }

    $args[2] = false;

    $page = $args[0];
    $req_site = self::$site;

    if ((str_contains($page, '/')) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $page, $matches) === 1) && self::siteExists($matches[1])) {
      $req_site = $matches[1];
      $page = $matches[2];
    }

    $args[0] = $req_site . DIRECTORY_SEPARATOR . self::getConfig('http_images_path', $req_site) . $page;

    $url = forward_static_call_array('self::link', $args);

    return $url;
  }

  /**
   * @return string
   */
  public static function linkPublic(): string
  {
    $args = func_get_args();

    if (!isset($args[0])) {
      $args[0] = null;
    }

    if (!isset($args[1])) {
      $args[1] = null;
    }

    $args[2] = false;

    $page = $args[0];
    $req_site = self::$site;

    if ((str_contains($page, '/')) && (preg_match('/^([A-Z][A-Za-z0-9-_]*)\/(.*)$/', $page, $matches) === 1) && self::siteExists($matches[1])) {
      $req_site = $matches[1];
      $page = $matches[2];
    }

    $args[0] = 'Shop/public/Sites/' . $req_site . DIRECTORY_SEPARATOR . $page;

    $url = forward_static_call_array('self::link()', $args);

    return $url;
  }

  /**
   * Redirect to a page
   * @return string $url, url to redirect
   */
  public static function redirect(): string
  {
    $args = func_get_args();

    $url = forward_static_call_array('self::link', $args);

    if ((strstr($url, "\n") !== false) || (strstr($url, "\r") !== false)) {
      $url = self::link(null, '', false);
    }

    HTTP::redirect($url);
  }

  /**
   * Return a language definition
   *
   * @return string The language definition
   */
  public static function getDef(): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    return call_user_func_array([$CLICSHOPPING_Language, 'getDef'], func_get_args());
  }

  /**
   * @param array $path
   * @return bool
   */
  public static function hasRoute(array $path): bool
  {
    return array_slice(array_keys($_GET), 0, count($path)) == $path;
  }

  /**
   * load config element to connect db and path
   */
  public static function loadConfig(): void
  {
    self::loadConfigFile(self::BASE_DIR . 'Conf/global.php', 'global');

    if (is_file(self::BASE_DIR . 'Custom/Conf/global.php')) {
      self::loadConfigFile(self::BASE_DIR . 'Custom/Conf/global.php', 'global');
    }

    foreach (glob(self::BASE_DIR . 'Sites/*', GLOB_ONLYDIR) as $s) {
      $s = basename($s);
      if (self::siteExists($s, false) && is_file(self::BASE_DIR . 'Sites/' . $s . '/site_conf.php')) {
        self::loadConfigFile(self::BASE_DIR . 'Sites/' . $s . '/site_conf.php', $s);

        if (is_file(self::BASE_DIR . 'Custom/Sites/' . $s . '/site_conf.php')) {
          self::loadConfigFile(self::BASE_DIR . 'Custom/Sites/' . $s . '/site_conf.php', $s);
        }
      }
    }
  }

  /**
   * @param string $file
   * @param string $group
   */

  public static function loadConfigFile(string $file, string $group): void
  {
    $cfg = [];

    if (is_file($file)) {
      include($file);

      if (isset($ini)) {
        $cfg = parse_ini_string($ini);
      }
    }

    if (!empty($cfg)) {
      self::$cfg[$group] = (isset(self::$cfg[$group])) ? array_merge(self::$cfg[$group], $cfg) : $cfg;
    }
  }

  /**
   * @param string $key
   * @param string|null $group
   * @return mixed
   */
  public static function getConfig(string $key, string|null $group = null)
  {
    if (!isset($group)) {
      $group = self::getSite();
    }

    if (isset(self::$cfg[$group][$key])) {
      return self::$cfg[$group][$key];
    }

    if (isset(self::$cfg['global'][$key])) {
      return self::$cfg['global'][$key];
    }
  }

  /**
   * @param string $key
   * @param string|null $group
   * @return bool
   */
  public static function configExists(string $key, string|null $group = null): bool
  {
    if (!isset($group)) {
      $group = self::getSite();
    }

    if (isset(self::$cfg[$group][$key])) {
      return true;
    }

    return isset(self::$cfg['global'][$key]);
  }

  /**
   * @param string $key
   * @param $value
   * @param string|null $group
   */
  public static function setConfig(string $key, $value, string|null $group = null)
  {
    if (!isset($group)) {
      $group = 'global';
    }

    self::$cfg[$group][$key] = $value;
  }

  /**
   * @param string $class
   *
   */
  public static function autoload(string $class)
  {
    $prefix = 'ClicShopping\\';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
      $class_path = str_replace('\\', '/', $class);

      $file = self::BASE_DIR . 'External' . DIRECTORY_SEPARATOR . $class_path . '.php';

      if (is_file($file)) {
        require_once($file);

        return true;
      }

      $site_dirs = [
        'Sites',
        'Custom'
      ];

      foreach ($site_dirs as $site_dir) {
        $dir = new DirectoryIterator(self::BASE_DIR . $site_dir);

        foreach ($dir as $f) {
          if (!$f->isDot() && $f->isDir()) {
            $file = $f->getPath() . DIRECTORY_SEPARATOR . $f->getFilename() . DIRECTORY_SEPARATOR . 'External' . DIRECTORY_SEPARATOR . $class_path . '.php';

            if (is_file($file)) {
              require($file);

              return true;
            }
          }
        }
      }

      return false;
    }

    if (strncmp($prefix . 'OM\Module\\', $class, strlen($prefix . 'OM\Module\\')) === 0) { // TODO remove and fix namespace
      $file = dirname(self::BASE_DIR) . DIRECTORY_SEPARATOR . str_replace(['ClicShopping\OM\\', '\\'], ['', '/'], $class) . '.php';
      $custom = dirname(self::BASE_DIR) . DIRECTORY_SEPARATOR . str_replace(['ClicShopping\OM\\', '\\'], ['ClicShopping\Custom\OM\\', '/'], $class) . '.php';
    } else {
      $file = dirname(self::BASE_DIR) . DIRECTORY_SEPARATOR . str_replace('\\', '/', $class) . '.php';
      $custom = str_replace('ClicShopping/OM/', 'ClicShopping/Custom/OM/', $file);
    }

    if (is_file($custom)) {
      require_once($custom);
    } elseif (is_file($file)) {
      require_once($file);
    }

    if (is_file(self::BASE_DIR . 'External/vendor/autoload.php')) {
      require_once(self::BASE_DIR . 'External/vendor/autoload.php');
    }
  }

  /**
   * @param string $application
   * @return bool
   */
  protected static function siteApplicationExists(string $application): bool
  {
    $class = self::isValidClassName($application) && class_exists('ClicShopping\\Sites\\' . self::getSite() . '\\Pages\\' . $application . '\\' . $application);

    return $class;
  }

  /**
   * @param string|null $application
   */
  protected static function setSiteApplication(?string $application = null)
  {
    if (isset($application)) {
      if (!self::siteApplicationExists($application)) {
        trigger_error('Apps \'' . $application . '\' does not exist for Site \'' . self::getSite());

        $application = null;
      }
    } else {
      if (!empty($_GET)) {
        $key = key(array_slice($_GET, 0, 1, true));

        if (isset($key)) {
          $requested_application = HTML::sanitize(basename($key));

          if ($requested_application == self::getSite()) {
            $key = key(array_slice($_GET, 1, 1, true));

            if (isset($key)) {

              $requested_application = HTML::sanitize(basename($key));
            }
          }

          if ((preg_match('/^[A-Za-z0-9-_]+$/', $requested_application) === 1) && self::siteApplicationExists($requested_application)) {
            $application = $requested_application;
          }
        }
      }
    }

    self::$application = $application;
  }

  /**
   * @return string|null
   */
  public static function getSiteApplication(): ?string
  {
    return self::$application;
  }

  /**
   * Get all parameters in the GET scope
   *
   * @param array $exclude A list of parameters to exclude
   * @return string
   */
  public static function getAllGET($exclude = null)
  {
    if (!is_array($exclude)) {
      if (!empty($exclude)) {
        $exclude = [$exclude];
      } else {
        $exclude = [];
      }
    }

    $params = '';

    $array = [
      self::getSite(),
      Registry::get('Session')->getName(),
      'error',
      'x',
      'y'
    ];

    $exclude = array_merge($exclude, $array);

    if (is_array($_GET)) {
      foreach ($_GET as $key => $value) {
        if (!\in_array($key, $exclude, true)) {
          $params .= $key . (!empty($value) ? '=' . $value : '') . '&';
        }
      }
    }

    if (!empty($params)) {
      $params = substr($params, 0, -1);
    }

    return $params;
  }

  /**
   * global scope
   * @return string
   */
  public static function getIndex(): string
  {
    $req = parse_url($_SERVER['SCRIPT_NAME']);
    $result = substr($req['path'], strlen(self::getConfig('http_path', 'Shop')));

    return $result;
  }

  /**
   * @return string
   */
  public static function getBaseNameIndex(): string
  {
    return basename(self::getIndex());
  }


  /**
   * @param $array
   * @param string $exclude
   * @param string $equals
   * @param string $separator
   * @return bool|string
   *
   */
  public static function arrayToString(array $array, string|array $exclude = '', string|array $equals = '=', string $separator = '&'): ?string
  {
    if (!is_array($exclude)) {
      $exclude = [];
    }

    $get_string = '';

    if (is_array($array)) {
      foreach ($array as $key => $value) {
        if ((!\in_array($key, $exclude, true)) && ($key != 'x') && ($key != 'y')) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }

      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
  }

  /**
   * @param string $classname
   * @return bool
   */
  public static function isValidClassName(string $classname): bool
  {
    return preg_match(self::VALID_CLASS_NAME_REGEXP, $classname) === 1;
  }

  /**
   * Retrieve web server and database server information
   * return $data, array og php.ini information
   */
  public static function getSystemInformation(): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qdate = $CLICSHOPPING_Db->query('select now() as datetime');

    [$system, $host, $kernel] = preg_split('/[\s,]+/', @exec('uname -a'), 5);

    $data = [];

    $data['clicshopping'] = ['version' => self::getVersion()];

    $data['system'] = [
      'date' => date('Y-m-d H:i:s O T'),
      'system' => $system,
      'host' => $host,
      'os' => PHP_OS,
      'kernel' => $kernel,
      'uptime' => @exec('uptime'),
      'http_server' => $_SERVER['SERVER_SOFTWARE']
    ];

    $data['mysql'] = [
      'version' => $CLICSHOPPING_Db->getAttribute(PDO::ATTR_SERVER_VERSION),
      'date' => $Qdate->value('datetime')
    ];

    $data['php'] = [
      'version' => PHP_VERSION,
      'zend' => zend_version(),
      'sapi' => PHP_SAPI,
      'int_size' => defined('PHP_INT_SIZE') ? PHP_INT_SIZE : '',
      'open_basedir' => (int)@ini_get('open_basedir'),
      'memory_limit' => @ini_get('memory_limit'),
      'error_reporting' => error_reporting(),
      'display_errors' => (int)@ini_get('display_errors'),
      'allow_url_fopen' => (int)@ini_get('allow_url_fopen'),
      'file_uploads' => (int)@ini_get('file_uploads'),
      'upload_max_filesize' => @ini_get('upload_max_filesize'),
      'post_max_size' => @ini_get('post_max_size'),
      'disable_functions' => @ini_get('disable_functions'),
      'disable_classes' => @ini_get('disable_classes'),
      'filter.default' => @ini_get('filter.default'),
      'unicode.semantics' => (int)@ini_get('unicode.semantics'),
      'zend_thread_safty' => (int)function_exists('zend_thread_id'),
      'extensions' => get_loaded_extensions()
    ];

    return $data;
  }

  /**
   * @param string $string
   * @return string
   * replace utf8_decode
   * utf8_to_iso8859_1
   */
  public static function utf8Decode(string $string): string
  {
    $s = $string;
    $len = strlen($s);

    for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
      switch ($s[$i] & "\xF0") {
        case "\xC0":
        case "\xD0":
          $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
          $s[$j] = $c < 256 ? \chr($c) : '?';
          break;

        case "\xF0":
          ++$i;
        // no break

        case "\xE0":
          $s[$j] = '?';
          $i += 2;
          break;

        default:
          $s[$j] = $s[$i];
      }
    }

    return substr($s, 0, $j);
  }

  /**
   * @param string $string
   * @return string
   * replace utf8_encode
   * iso8859_1_to_utf8
   */
  public static function utf8Encode(string $string, string $iso = 'ISO-8859-1'): string
  {
    $result = mb_convert_encoding($string, 'UTF-8', $iso);

    return $result;
  }
}
