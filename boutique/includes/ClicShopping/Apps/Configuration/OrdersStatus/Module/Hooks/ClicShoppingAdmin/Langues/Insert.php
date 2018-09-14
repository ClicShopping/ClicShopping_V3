<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Configuration\OrdersStatus\OrdersStatus as OrdersStatusApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $insert_language_id;

    public function __construct() {
      if (!Registry::exists('OrdersStatus')) {
        Registry::set('OrdersStatus', new OrdersStatusApp());
      }

      $this->app = Registry::get('OrdersStatus');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
      $this->lang =  Registry::get('Language');
    }

    private function insert() {
      if (isset($this->insert_language_id)) {
        $QordersStatus = $this->app->db->get('orders_status', '*', ['language_id' => $this->lang->getId()]);

        while ($QordersStatus->fetch()) {
          $cols = $QordersStatus->toArray();

          $cols['language_id'] = $this->insert_language_id;

          $this->app->db->save('orders_status', $cols);
        }
      }
    }

    public function execute() {
      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }