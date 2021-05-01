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

  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new CategoriesApp());
      }

      $this->app = Registry::get('Categories');
    }

    private function delete($id)
    {
      if (!\is_null($id)) {
        $this->app->db->delete('categories_description', ['language_id' => $id]);
      }
    }

    public function execute()
    {

      if (!\defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }