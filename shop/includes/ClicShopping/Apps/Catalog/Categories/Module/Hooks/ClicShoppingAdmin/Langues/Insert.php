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

  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $lang;
    protected $insert_language_id;

    public function __construct()   {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new CategoriesApp());
      }

      $this->app = Registry::get('Categories');
      $this->lang = Registry::get('Language');
      $this->insert_language_id = HTML::sanitize($_POST['insert_id']);
    }

    private function insert() {

      if (isset($this->insert_language_id)) {
        $Qcategories = $this->app->db->prepare('select c.categories_id as orig_category_id,
                                                       cd.*
                                                from :table_categories c left join :table_categories_description cd on c.categories_id = cd.categories_id
                                                where cd.language_id = :language_id
                                                ');

        $Qcategories->bindInt(':language_id', (int)$this->lang->getId());
        $Qcategories->execute();

        while ($Qcategories->fetch()) {
          $cols = $Qcategories->toArray();

          $cols['categories_id'] = $cols['orig_category_id'];
          $cols['language_id'] = $this->insert_language_id;

          unset($cols['orig_category_id']);

          $this->app->db->save('categories_description', $cols);
        }
      }
    }

    public function execute() {

      if (!defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }