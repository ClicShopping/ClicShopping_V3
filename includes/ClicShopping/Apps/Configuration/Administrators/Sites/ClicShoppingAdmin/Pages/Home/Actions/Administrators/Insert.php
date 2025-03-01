<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions\Administrators;

use ClicShopping\OM\Hash;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Insert extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Administrators');
  }

  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $name = HTML::sanitize($_POST['name']);
    $first_name = HTML::sanitize($_POST['first_name']);
    $username = HTML::sanitize($_POST['username']);
    $password = HTML::sanitize($_POST['password']);
    $access = HTML::sanitize($_POST['access_administrator']);

    if (empty($access)) {
      $CLICSHOPPING_MessageStack->add($this->app->getDef('error_administrator_select'), 'error');
      $this->app->redirect('Administrators.php&Insert');
    }

    $Qcheck = $this->app->db->get('administrators', 'id', ['user_name' => $username], null, 1);

    if (!empty($username)) {
      if (!$Qcheck->check()) {

        $this->app->db->save('administrators', ['user_name' => $username,
            'user_password' => Hash::encrypt($password),
            'name' => $name,
            'first_name' => $first_name,
            'access' => $access,
            'date_added' => 'now()'
          ]
        );
      }
    } else {
      $CLICSHOPPING_MessageStack->add($this->app->getDef('error_administrator_exists'), 'error');
    }

    $this->app->redirect('Administrators');
  }
}