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

  namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\PageManager;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

  class Update implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Groups')) {
        Registry::set('Groups', new GroupsApp());
      }

      $this->app = Registry::get('Groups');
    }

    public function execute() {
      $customers_group_id = HTML::sanitize($_POST['customers_group']);

      $pages_id = HTML::sanitize($_POST['pages_id']);

      $sql_data_array =  ['customers_group_id' => (int)$customers_group_id];

      $this->app->db->save('pages_manager', $sql_data_array,  ['pages_id' => (int)$pages_id]);
    }
  }