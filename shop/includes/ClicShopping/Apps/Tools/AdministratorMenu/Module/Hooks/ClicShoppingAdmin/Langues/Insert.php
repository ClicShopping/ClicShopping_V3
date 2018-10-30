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

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Tools\AdministratorMenu\AdministratorMenu as AdministratorMenuApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $insert_language_id;

    public function __construct() {
      if (!Registry::exists('AdministratorMenu')) {
        Registry::set('AdministratorMenu', new AdministratorMenuApp());
      }

      $this->app = Registry::get('AdministratorMenu');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
      $this->lang =  Registry::get('Language');
    }

    private function insert() {
      if (isset($this->insert_language_id)) {
// administrator_description records
        $QadministratorMenu = $this->app->db->prepare('select a.id as orig_id,
                                                              amd.*
                                                       from :table_administrator_menu a left join :table_administrator_menu_description amd on a.id = amd.id
                                                       where amd.language_id = :language_id
                                                      ');

        $QadministratorMenu->bindInt(':language_id', (int)$this->lang->getId());
        $QadministratorMenu->execute();

        while ($QadministratorMenu->fetch()) {
          $cols = $QadministratorMenu->toArray();

          $cols['id'] = $cols['orig_id'];
          $cols['language_id'] = $this->insert_language_id;

          unset($cols['orig_id']);

          $this->app->db->save('administrator_menu_description', $cols);
        }
      }
    }

    public function execute() {
      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }