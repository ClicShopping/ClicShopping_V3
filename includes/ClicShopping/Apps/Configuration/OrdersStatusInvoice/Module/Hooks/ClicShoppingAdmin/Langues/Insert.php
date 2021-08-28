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

  namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\OrdersStatusInvoice\OrdersStatusInvoice as OrdersStatusInvoiceApp;
  use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected mixed $lang;

    public function __construct()
    {
      if (!Registry::exists('OrdersStatusInvoice')) {
        Registry::set('OrdersStatusInvoice', new OrdersStatusInvoiceApp());
      }

      $this->app = Registry::get('OrdersStatusInvoice');
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      $insert_language_id = LanguageAdmin::getLatestLanguageID();

      $QordersStatusInvoice = $this->app->db->get('orders_status_invoice', '*', ['language_id' => $this->lang->getId()]);

      while ($QordersStatusInvoice->fetch()) {
        $cols = $QordersStatusInvoice->toArray();

        $cols['language_id'] = (int)$insert_language_id;

        $this->app->db->save('orders_status_invoice', $cols);
      }
    }

    public function execute()
    {
      if (isset($_GET['Langues']) && isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }