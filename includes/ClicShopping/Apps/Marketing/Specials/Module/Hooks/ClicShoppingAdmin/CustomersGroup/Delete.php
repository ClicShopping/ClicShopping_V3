<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Specials\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Specials\Specials as SpecialsApp;

  class Delete implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Specials')) {
        Registry::set('Specials', new SpecialsApp());
      }

      $this->app = Registry::get('Specials');
    }

    /**
     * @param int $group_id
     */
    private function delete(int $group_id) :void
    {
      $QspecialsProductsCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                               from :table_specials
                                                               where customers_group_id = :customers_group_id
                                                              ');
      $QspecialsProductsCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QspecialsProductsCustomersId->execute();

      if ($QspecialsProductsCustomersId->valueInt('count') > 0) {

        $Qdelete = $this->app->db->prepare('delete
                                            from :table_specials
                                            where customers_group_id = :customers_group_id
                                          ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
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