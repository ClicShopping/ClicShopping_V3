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


  namespace ClicShopping\Apps\Configuration\TaxClass\Sites\ClicShoppingAdmin\Pages\Home\Actions\TaxClass;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('TaxClass');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
      $tax_class_id = HTML::sanitize($_GET['tID']);

      $this->app->db->delete('tax_class', ['tax_class_id' => (int)$tax_class_id]);

      $this->app->redirect('TaxClass&page=' . $page);
    }
  }