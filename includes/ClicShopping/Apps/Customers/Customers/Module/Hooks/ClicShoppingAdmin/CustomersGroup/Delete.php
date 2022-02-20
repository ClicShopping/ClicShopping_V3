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

  namespace ClicShopping\Apps\Customers\Customers\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

  class Delete implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Customers')) {
        Registry::set('Customers', new CustomersApp());
      }

      $this->app = Registry::get('Customers');
    }

    /**
     * @param int $group_id
     */
    private function delete(int $group_id) :void
    {
      // update all customers
      $QcustomersId = $this->app->db->prepare('select customers_id
                                               from :table_customers
                                               where customers_group_id = :customers_group_id
                                             ');
      $QcustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QcustomersId->execute();

      while ($QcustomersId->fetch()) {
        $Qupdate = $this->app->db->prepare('update :table_customers
                                            set customers_group_id = :customers_group_id
                                            where customers_id = :customers_id
                                            ');
        $Qupdate->bindValue(':customers_group_id', 1);
        $Qupdate->bindInt(':customers_id', $QcustomersId->valueInt('customers_id'));
        $Qupdate->execute();
      }
    }

    public function execute()
    {
      if (isset($_GET['Delete'])) {
        $id = HTML::sanitize($_GET['cID']);
        $this->delete($id);
      }
    }
  }