<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;
    protected $insert_language_id;

    public function __construct()
    {
      if (!Registry::exists('Suppliers')) {
        Registry::set('Suppliers', new SuppliersApp());
      }

      $this->app = Registry::get('Suppliers');
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      if (isset($this->insert_language_id, $_GET['Langues'], $_GET['Insert'])) {
        $this->insert_language_id = HTML::sanitize($_POST['insert_id']);

        $Qsuppliers = $this->app->db->prepare('select m.suppliers_id as orig_suppliers_id,
                                                      mi.*
                                              from :table_suppliers m left join :table_suppliers_info mi on m.suppliers_id = mi.suppliers_id
                                              where mi.languages_id = :languages_id
                                             ');

        $Qsuppliers->bindInt(':languages_id', $this->lang->getId());
        $Qsuppliers->execute();

        while ($Qsuppliers->fetch()) {
          $cols = $Qsuppliers->toArray();

          $cols['suppliers_id'] = $cols['orig_suppliers_id'];
          $cols['languages_id'] = $this->insert_language_id;

          unset($cols['orig_suppliers_id']);

          $this->app->db->save('suppliers_info', $cols);
        }
      }
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }