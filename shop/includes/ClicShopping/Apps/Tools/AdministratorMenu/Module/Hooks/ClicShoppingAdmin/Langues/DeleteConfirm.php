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

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Tools\AdministratorMenu\AdministratorMenu as AdministratorMenuApp;

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('AdministratorMenu')) {
        Registry::set('AdministratorMenu', new AdministratorMenuApp());
      }

      $this->app = Registry::get('AdministratorMenu');
    }

    private function delete($id) {
      if (!is_null($id)) {
        $this->app->db->delete('administrator_menu_description', ['language_id' => $id]);
      }
    }

    public function execute() {
      if (isset($_GET['DeleteConfirm'])) {
        $id = HTML::sanitize($_GET['lID']);
        $this->delete($id);
      }
    }
  }