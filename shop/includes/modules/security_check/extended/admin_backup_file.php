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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class securityCheckExtended_admin_backup_file
  {
    public $type = 'error';
    public $has_doc = true;

    public function __construct()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/admin_backup_file', null, null, 'Shop');

      $this->title = CLICSHOPPING::getDef('module_security_check_extended_admin_backup_file_title');
    }

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
        $request = $this->getHttpRequest(CLICSHOPPING::BASE_DIR . 'Work/Backups/' . $backup_file);

        $result = ($request['http_code'] != 200);
      }

      return $result;
    }

    public function getMessage()
    {
      return CLICSHOPPING::getDef('module_security_check_extended_admin_backup_file_http_200', [
        'backups_path' => CLICSHOPPING::getConfig('http_path', 'Shop') . 'includes/ClicShopping/Work/Backups/'
      ]);
    }

    public function getHttpRequest($url)
    {

      $server = parse_url($url);

      $server['scheme'] = '';

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

        $info = curl_getinfo($curl);

        curl_close($curl);

        return $info;
      }
    }
  }