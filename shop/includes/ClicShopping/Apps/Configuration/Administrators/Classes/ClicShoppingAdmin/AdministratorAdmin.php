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

  namespace ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class AdministratorAdmin {

/**
 * get the user administrator
 * @param string $user_administrator
 */

    public static function getUserAdmin() {
      $CLICSHOPPING_Db = Registry::get('Db');

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

      $Qlogins->bindValue(':module',  'ar_admin_login');
      $Qlogins->bindValue(':user_name',  $username);

      $Qlogins->execute();

      $administrator =  HTML::output($Qlogins->value('first_name') . ' ' . $Qlogins->value('name'));

      return $administrator;
    }

/**
 * get the administrator right
 * @param string $default , default right
 * @return string $administrator_right_array ,  right selected
 */

    public static function getAdministratorRight($default = '') {

      $administrator_right_array = [];

      if ($default) {
        $administrator_right_array[] = ['id' => '',
                                        'text' => $default
                                       ];
      }

      $administrator_right_array[] =  array('id' => '1', 'text' => CLICSHOPPING::getDef('text_all_rights_admin'));
      $administrator_right_array[] =  array('id' => '2', 'text' =>  CLICSHOPPING::getDef('text_rights_employee'));
      $administrator_right_array[] = array('id' => '3', 'text'  =>   CLICSHOPPING::getDef('text_rights_visitor'));

      return $administrator_right_array;
    }

/**
* get the administrator menu right right
* @param string $default , default menu right
* @return string $administrator_right_array , menu right selected
*/

    public static function getAdministratorMenuRight($default = '') {

      $administrator_right_array = [];

      if ($default) {
        $administrator_right_array[] = ['id' => '',
                                        'text' => $default
                                      ];
      }

      $administrator_right_array[] =  array('id' => '0', 'text' => CLICSHOPPING::getDef('text_all_right'));
      $administrator_right_array[] =  array('id' => '1', 'text' => CLICSHOPPING::getDef('text_all_rights_admin'));
      $administrator_right_array[] =  array('id' => '2', 'text' =>  CLICSHOPPING::getDef('text_rights_employee'));
      $administrator_right_array[] = array('id' => '3', 'text'  =>   CLICSHOPPING::getDef('text_rights_visitor'));

      return $administrator_right_array;
    }
  }