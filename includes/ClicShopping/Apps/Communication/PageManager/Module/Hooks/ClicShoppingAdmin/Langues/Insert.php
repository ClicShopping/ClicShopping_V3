<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;
use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected $insert_language_id;

  public function __construct()
  {
    if (!Registry::exists('PageManager')) {
      Registry::set('PageManager', new PageManagerApp());
    }

    $this->app = Registry::get('PageManager');
    $this->lang = Registry::get('Language');
  }

  private function insert()
  {
    $insert_language_id = LanguageAdmin::getLatestLanguageID();

    $QpageManagerDescription = $this->app->db->prepare('select pm.pages_id as orig_pages_id,
                                                                 pmd.*
                                                          from :table_pages_manager pm left join :table_pages_manager_description pmd on pm.pages_id = pmd.pages_id
                                                          where pmd.language_id = :language_id
                                                        ');

    $QpageManagerDescription->bindInt(':language_id', (int)$this->lang->getId());
    $QpageManagerDescription->execute();

    while ($QpageManagerDescription->fetch()) {
      $cols = $QpageManagerDescription->toArray();

      $cols['pages_id'] = $cols['orig_pages_id'];
      $cols['language_id'] = (int)$insert_language_id;

      unset($cols['orig_pages_id']);

      $this->app->db->save('pages_manager_description', $cols);
    }
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS') || CLICSHOPPING_APP_PAGE_MANAGER_PM_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Langues'], $_GET['Insert'])) {
      $this->insert();
    }
  }
}