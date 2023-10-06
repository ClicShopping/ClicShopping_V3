<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\Apps\Catalog\Categories\Categories as CategoriesApp;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

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