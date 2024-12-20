<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Catalog\Suppliers\Suppliers as SuppliersApp;

class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;

  /**
   * Constructor method for initializing the Suppliers application.
   *
   * Checks if the Suppliers instance exists in the Registry.
   * If not, creates a new SuppliersApp instance and registers it.
   * Then retrieves and assigns the Suppliers instance to the $app property.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Suppliers')) {
      Registry::set('Suppliers', new SuppliersApp());
    }

    $this->app = Registry::get('Suppliers');
  }

  /**
   * Deletes a record from the suppliers_info table based on the provided language ID.
   *
   * @param int|null $id The ID of the language record to be deleted. If null, no action is taken.
   * @return void
   */
  private function delete(int $id)
  {
    if (!\is_null($id)) {
      $this->app->db->delete('suppliers_info', ['languages_id' => $id]);
    }
  }

  /**
   * Executes the deletion process for a supplier if the necessary conditions are met.
   * Checks if the application is enabled and processes deletion based on the presence of a DeleteConfirm parameter.
   *
   * @return bool Returns false if the application is not enabled; otherwise, performs the delete operation if applicable.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_SUPPLIERS_CS_STATUS') || CLICSHOPPING_APP_SUPPLIERS_CS_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['DeleteConfirm'])) {
      $id = HTML::sanitize($_GET['lID']);
      $this->delete($id);
    }
  }
}