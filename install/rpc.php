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
use ClicShopping\OM\Db;
use ClicShopping\OM\HTTP;

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

require_once('includes/application.php');

$dir_fs_www_root = __DIR__;

$result = [
  'status' => '-100',
  'message' => 'noActionError'
];

if (isset($_GET['action']) && !empty($_GET['action'])) {
  switch ($_GET['action']) {
    case 'httpsCheck':
      if (isset($_GET['subaction']) && ($_GET['subaction'] == 'do')) {
        if ((isset($_SERVER['HTTPS']) && (mb_strtolower($_SERVER['HTTPS']) == 'on')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443))) {
          $result['status'] = '1';
          $result['message'] = 'success';
        }
      } else {
        $url = 'https://' . $_SERVER['HTTP_HOST'];

        if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
          $url .= $_SERVER['REQUEST_URI'];
        } else {
          $url .= $_SERVER['SCRIPT_FILENAME'];
        }

        $url .= '&subaction=do';

// errors are silenced to not log failed connection checks
        $response = @HTTP::getResponse([
          'url' => $url,
          'verify_ssl' => false
        ]);

        if (!empty($response)) {
          $response = json_decode($response, true);

          if (\is_array($response) && isset($response['status']) && ($response['status'] == '1')) {
            $result['status'] = '1';
            $result['message'] = 'success';
          }
        }
      }

      break;

    case 'dbCheck':
      try {
        $CLICSHOPPING_Db = Db::initialize(isset($_POST['server']) ? $_POST['server'] : '', isset($_POST['username']) ? $_POST['username'] : '', isset($_POST['password']) ? $_POST['password'] : '', isset($_POST['name']) ? $_POST['name'] : '', null, null, ['log_errors' => false]);

        $result['status'] = '1';
        $result['message'] = 'success';
      } catch (\Exception $e) {
        $result['status'] = $e->getCode();
        $result['message'] = $e->getMessage();

        if (($e->getCode() == '1049') && isset($_GET['createDb']) && ($_GET['createDb'] == 'true')) {
          try {
            $CLICSHOPPING_Db = Db::initialize($_POST['server'], $_POST['username'], $_POST['password'], '', null, null, ['log_errors' => false]);

            $CLICSHOPPING_Db->exec('create database ' . Db::prepareIdentifier($_POST['name']) . ' character set utf8mb4 collate utf8mb4_unicode_ci');

            $result['status'] = '1';
            $result['message'] = 'success';
          } catch (\Exception $e2) {
            $result['status'] = $e2->getCode();
            $result['message'] = $e2->getMessage();
          }
        }
      }

      break;

    case 'dbImport':
      try {
        $CLICSHOPPING_Db = Db::initialize(isset($_POST['server']) ? $_POST['server'] : '', isset($_POST['username']) ? $_POST['username'] : '', isset($_POST['password']) ? $_POST['password'] : '', isset($_POST['name']) ? $_POST['name'] : '');
        $CLICSHOPPING_Db->setTablePrefix('');

        $CLICSHOPPING_Db->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach (glob(CLICSHOPPING::BASE_DIR . 'Schema/MariaDb/*.txt') as $f) {
          $schema = $CLICSHOPPING_Db->getSchemaFromFile($f);

          $sql = $CLICSHOPPING_Db->getSqlFromSchema($schema, $_POST['prefix']);

          $CLICSHOPPING_Db->exec('DROP TABLE IF EXISTS ' . $_POST['prefix'] . basename($f, '.txt'));

          $CLICSHOPPING_Db->exec($sql);
        }

        if ($_POST['demo'] == 'demo') {
          $CLICSHOPPING_Db->importSQL($dir_fs_www_root . '/Db/demo_clicshopping_en.sql', $_POST['prefix']);
        } elseif ($language == 'french') {
          $CLICSHOPPING_Db->importSQL($dir_fs_www_root . '/Db/clicshopping.sql', $_POST['prefix']);
        } else {
          $CLICSHOPPING_Db->importSQL($dir_fs_www_root . '/Db/clicshopping_en.sql', $_POST['prefix']);
        }

        $CLICSHOPPING_Db->exec('SET FOREIGN_KEY_CHECKS = 1');

        $result['status'] = '1';
        $result['message'] = 'success';
      } catch (\Exception $e) {
        $result['status'] = $e->getCode();
        $result['message'] = $e->getMessage();
      }

      break;
  }
}

echo json_encode($result);
