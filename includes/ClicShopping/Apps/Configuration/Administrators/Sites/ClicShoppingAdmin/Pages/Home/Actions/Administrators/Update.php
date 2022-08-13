<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Administrators\Sites\ClicShoppingAdmin\Pages\Home\Actions\Administrators;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Hash;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Administrators');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Mail = Registry::get('Mail');

      $name = HTML::sanitize($_POST['name']);
      $first_name = HTML::sanitize($_POST['first_name']);
      $username = HTML::sanitize($_POST['username']);
      $password = HTML::sanitize($_POST['password']);
      $access = HTML::sanitize($_POST['access_administrator']);

      if (empty($access)) {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_administrator_select'), 'error');
        if (\is_null($_GET['aID'])) {
          $this->app->redirect('Edit&aID=' . $_GET['aID']);
        }
      }

      $Qcheck = $this->app->db->get('administrators', ['id',
        'user_name',
        'first_name',
        'name',
        'access'
      ], [
          'id' => (int)$_GET['aID']
        ]
      );

// update username in current session if changed
      if (($Qcheck->valueInt('id') === $_SESSION['admin']['id']) && ($username !== $_SESSION['admin']['username'])) {
        $_SESSION['admin']['username'] = $username;
        $_SESSION['admin']['name'] = $name;
        $_SESSION['admin']['first_name'] = $first_name;
        $_SESSION['admin']['access'] = $access;
      }

      $this->app->db->save('administrators', ['user_name' => $username,
        'name' => $name,
        'first_name' => $first_name,
        'access' => $access
      ],
        ['id' => (int)$_GET['aID']]
      );

      if (!empty($password)) {
        $this->app->db->save('administrators', [
          'user_password' => Hash::encrypt($password),
        ], [
            'id' => (int)$_GET['aID']
          ]
        );
      }

// mail report
      $CLICSHOPPING_Mail->clicMail(STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER, $this->app->getDef('report_password_change_subject', ['username' => $username]), $this->app->getDef('report_password_change_text', ['username' => $username]), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);

      $this->app->redirect('Administrators&aID=' . (int)$_GET['aID']);
    }
  }