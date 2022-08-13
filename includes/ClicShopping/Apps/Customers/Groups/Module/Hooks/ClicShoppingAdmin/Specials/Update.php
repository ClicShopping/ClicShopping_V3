<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Specials;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

  class Update implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Groups')) {
        Registry::set('Groups', new GroupsApp());
      }

      $this->app = Registry::get('Groups');
    }

    public function execute()
    {
      if (isset($_GET['Update'])) {
        if (isset($_POST['customers_group'])) {
          $customers_group_id = HTML::sanitize($_POST['customers_group']);

          $specials_id = HTML::sanitize($_POST['specials_id']);

          $sql_data_array = ['customers_group_id' => (int)$customers_group_id];

          $this->app->db->save('specials', $sql_data_array, ['specials_id' => (int)$specials_id]);
        }
      }
    }
  }