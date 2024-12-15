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

/**
 * This class performs a security check to ensure that no backup files are publicly accessible in the administrator backup directory.
 */
class securityCheckExtended_admin_backup_file
{
  public $type = 'danger';
  public $has_doc = true;

  /**
   * Constructor method initializes the class by loading language definitions
   * and setting the title property.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/admin_backup_file', null, null, 'Shop');

    /**
     *
     */
      $this->title = CLICSHOPPING::getDef('module_security_check_extended_admin_backup_file_title');
  }

  /**
   * Checks the presence and accessibility of backup files in a specific directory.
   *
   * The method inspects the backup directory for specific backup file types
   * (zip, sql, gz) and prioritizes binary formats (zip and gz) over plain
   * text files (sql). If a suitable file is found, it validates its accessibility
   * by sending an HTTP request and checking the response.
   *
   * @return bool Returns true if the backup directory is empty, or no suitable
   *              backup files are found, or the backup file is inaccessible;
   *              otherwise, returns false.
   */
  public function pass()
  {
    $backup_directory = CLICSHOPPING::BASE_DIR . 'Work/Backups/';

    $backup_file = null;

    if (is_dir($backup_directory)) {
      $dir = dir($backup_directory);
      $contents = [];

      while ($file = $dir->read()) {
        if (!is_dir($backup_directory . $file)) {
          $ext = substr($file, strrpos($file, '.') + 1);

          if (in_array($ext, array('zip', 'sql', 'gz')) && !isset($contents[$ext])) {
            $contents[$ext] = $file;

            if ($ext != 'sql') { // zip and gz (binaries) are prioritized over sql (plain text)
              break;
            }
          }
        }
      }

      if (isset($contents['zip'])) {
        $backup_file = $contents['zip'];
      } elseif (isset($contents['gz'])) {
        $backup_file = $contents['gz'];
      } elseif (isset($contents['sql'])) {
        $backup_file = $contents['sql'];
      }
    }

    $result = true;

    if (isset($backup_file)) {
      if (is_file(CLICSHOPPING::BASE_DIR . 'Work/Backups/' . $backup_file)) {
        $request = $this->getHttpRequest(HTTP::getShopUrlDomain() . 'includes/Work/Backups/' . $backup_file);

        $result = ($request['http_code'] !== 200);
      }
    }

    return $result;
  }

  /**
   * Retrieves a predefined message based on the security check for admin backup file with context-specific data.
   *
   * @return string The formatted message with the appropriate backups path.
   */
  public function getMessage(): string
  {
    return CLICSHOPPING::getDef('module_security_check_extended_admin_backup_file_http_200', [
      'backups_path' => CLICSHOPPING::getConfig('http_path', 'Shop') . 'includes/ClicShopping/Work/Backups/'
    ]);
  }

  /**
   * Sends an HTTP request to a given URL using the HEAD method and returns connection or response information.
   *
   * @param string|null $url The URL to send the HTTP request to. It can be null, in which case no request will be made.
   * @return array|string Returns an array containing connection or response information if successful, or 'error' if the request fails.
   */
  public function getHttpRequest(?string $url)
  {
    $server = parse_url($url);

    if (isset($server['port']) === false) {
      $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
    }

    if (isset($server['path']) === false) {
      $server['path'] = '/';
    }

    if (!empty($server['scheme'])) {
      $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
      curl_setopt($curl, CURLOPT_PORT, $server['port']);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
      curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
      curl_setopt($curl, CURLOPT_NOBODY, true);

      if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        curl_setopt($curl, CURLOPT_USERPWD, $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);

        $this->type = 'warning';
      }

      $result = curl_exec($curl);

      if (empty($result)) {
        $info = curl_getinfo($curl);
        curl_close($curl);
      } else {
        $info = 'error';
      }

      return $info;
    }
  }
}