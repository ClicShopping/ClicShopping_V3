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

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\AdministratorMenu\AdministratorMenu as AdministratorMenuApp;
  use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;
    protected mixed $lang;

    public function __construct()
    {
      if (!Registry::exists('AdministratorMenu')) {
        Registry::set('AdministratorMenu', new AdministratorMenuApp());
      }

      $this->app = Registry::get('AdministratorMenu');
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      $insert_language_id = LanguageAdmin::getLatestLanguageID();

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
        $cols['language_id'] = (int)$insert_language_id;

        unset($cols['orig_id']);

        $this->app->db->save('administrator_menu_description', $cols);
      }
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_ADMINISTRATOR_MENU_AM_STATUS') || CLICSHOPPING_APP_ADMINISTRATOR_MENU_AM_STATUS == 'False') {
       return false;
      }

      if (isset($_GET['Langues'], $_GET['Insert'])) {
        $this->insert();
      }
    }
  }