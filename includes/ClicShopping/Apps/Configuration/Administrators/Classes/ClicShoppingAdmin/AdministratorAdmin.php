<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;

class AdministratorAdmin
{
  public static function checkUserAccess(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    if (isset($_SESSION['admin']['id'], $_SESSION['admin']['access'])) {
      $Qcheck = $CLICSHOPPING_Db->prepare('select id
                                          from :table_administrators
                                          where id = :id
                                          and access = :access
                                          ');
      $Qcheck->bindint(':id', $_SESSION['admin']['id']);
      $Qcheck->bindint(':access', 1);
      $Qcheck->execute();

      if ($Qcheck->fetch() === false) {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('no_access_warning'), 'warning');
        HTTP::redirect('index.php');
      }
    } else {
      $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('no_access_warning'), 'warning');
      HTTP::redirect('index.php');
    }
  }

  /**
   * @param int $id
   * @return string
   */
  public static function getconsultantNameById(int $id): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qname = $CLICSHOPPING_Db->prepare('select name,
                                               first_name
                                        from :table_administrators
                                        where id = :id
                                        ');
    $Qname->bindint(':id', $id);
    $Qname->execute();

    return $Qname->value('first_name') . ' ' . $Qname->value('name');
  }

  /**
   * get the user administrator
   * @return string
   */
  public static function getUserAdmin(): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (isset($_SESSION['admin'])) {
      $username = array($_SESSION['admin']);
      $username = $username[0]['username'];

      $Qlogins = $CLICSHOPPING_Db->prepare('select a.name,
                                                     a.first_name
                                              from :table_action_recorder ar,
                                                   :table_administrators a
                                              where  ar.user_id = a.id
                                              and ar.module = :module
                                              and ar.user_name = :user_name
                                              limit 1
                                             ');

      $Qlogins->bindValue(':module', 'ar_admin_login');
      $Qlogins->bindValue(':user_name', $username);

      $Qlogins->execute();

      $administrator = HTML::output($Qlogins->value('first_name') . ' ' . $Qlogins->value('name'));

    } else {
      $administrator = 'Shop action';
    }

    return $administrator;
  }

  /**
   * @return int
   */
  public static function getUserAdminId(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (isset($_SESSION['admin'])) {
      $username = array($_SESSION['admin']);
      $username = $username[0]['username'];

      $Qlogins = $CLICSHOPPING_Db->prepare('select a.id
                                              from :table_action_recorder ar,
                                                   :table_administrators a
                                              where  ar.user_id = a.id
                                              and ar.module = :module
                                              and ar.user_name = :user_name
                                              limit 1
                                             ');

      $Qlogins->bindValue(':module', 'ar_admin_login');
      $Qlogins->bindValue(':user_name', $username);

      $Qlogins->execute();

      $administrator = $Qlogins->valueInt('id');
    } else {
      $administrator = null;
    }

    return $administrator;
  }

  /**
   * get the administrator right
   * @param string $default , default right
   * @return array $administrator_right_array ,  right selected
   */

  public static function getAdministratorRight(string $default = ''): array
  {
    $administrator_right_array = [];

    if ($default) {
      $administrator_right_array[] = [
        'id' => '',
        'text' => $default
      ];
    }

    $administrator_right_array[] = ['id' => '1', 'text' => CLICSHOPPING::getDef('text_all_rights_admin')];
    $administrator_right_array[] = ['id' => '2', 'text' => CLICSHOPPING::getDef('text_rights_employee')];
    $administrator_right_array[] = ['id' => '3', 'text' => CLICSHOPPING::getDef('text_rights_visitor')];

    return $administrator_right_array;
  }

  /**
   * get the administrator menu right
   * @param string $default , default menu right
   * @return array $administrator_right_array , menu right selected
   */

  public static function getAdministratorMenuRight(string $default = ''): array
  {

    $administrator_right_array = [];

    if ($default) {
      $administrator_right_array[] = [
        'id' => '',
        'text' => $default
      ];
    }

    $administrator_right_array[] = array('id' => '0', 'text' => CLICSHOPPING::getDef('text_all_right'));
    $administrator_right_array[] = array('id' => '1', 'text' => CLICSHOPPING::getDef('text_all_rights_admin'));
    $administrator_right_array[] = array('id' => '2', 'text' => CLICSHOPPING::getDef('text_rights_employee'));
    $administrator_right_array[] = array('id' => '3', 'text' => CLICSHOPPING::getDef('text_rights_visitor'));

    return $administrator_right_array;
  }
}