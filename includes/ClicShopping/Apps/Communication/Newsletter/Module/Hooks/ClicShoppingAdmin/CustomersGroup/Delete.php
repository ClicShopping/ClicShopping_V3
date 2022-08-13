<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Communication\Newsletter\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\Newsletter\Newsletter as NewsletterApp;

  class Delete implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Newsletter')) {
        Registry::set('Newsletter', new NewsletterApp());
      }

      $this->app = Registry::get('Newsletter');
    }

    /**
     * @param int $group_id
     */
    private function delete(int $group_id) :void
    {
      $QnewsletteCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                         from :table_newsletters
                                                         where customers_group_id = :customers_group_id
                                                       ');
      $QnewsletteCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QnewsletteCustomersId->execute();

      if ($QnewsletteCustomersId->valueInt('count') > 0) {
        $Qdelete = $this->app->db->prepare('delete
                                            from :table_newsletters
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