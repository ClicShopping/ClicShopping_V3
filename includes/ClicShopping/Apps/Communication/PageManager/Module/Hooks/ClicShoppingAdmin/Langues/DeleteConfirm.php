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

  namespace ClicShopping\Apps\Communication\PageManager\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('PageManager')) {
        Registry::set('PageManager', new PageManagerApp());
      }

      $this->app = Registry::get('PageManager');
    }

    private function delete($id)
    {
      if (!\is_null($id)) {
        $this->app->db->delete('pages_manager_description', ['language_id' => $id]);
      }
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS') || CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }