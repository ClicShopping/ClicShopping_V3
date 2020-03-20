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

  namespace ClicShopping\Apps\Configuration\Settings\Sites\ClicShoppingAdmin\Pages\Home\Actions\SettingsPopUp;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Hash;
  use ClicShopping\OM\Cache;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Settings');
    }

    public function execute()
    {

      if (isset($_POST['configuration'])) {
        foreach ($_POST['configuration'] as $value) {
          $configuration_value = $value;
        }
      } else {
        $configuration_value = $_POST['configuration_value'];
      }

      $cID = HTML::sanitize($_GET['cID']);
      $gID = HTML::sanitize($_GET['gID']);

      if (isset($_POST['configuration_value'])) {
        $configuration_value = $_POST['configuration_value'];
      }

      $this->app->db->save('configuration', [
        'configuration_value' => $configuration_value,
        'last_modified' => 'now()'
      ], [
          'configuration_id' => (int)$cID
        ]
      );

      Cache::clear('configuration');

      $this->app->redirect('Settings&gID=' . $gID . '&cID=' . $cID);
    }
  }