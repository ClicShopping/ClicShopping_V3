<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\Hooks\ClicShoppingAdmin\Products;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

/**
 * Class DeleteConfirm
 * Handles the deletion confirmation of products and their associated embeddings
 */
class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  /** @var mixed Reference to the Products application */
  public mixed $app;

  /** @var int Product ID to be deleted */
  protected $Id;

  /** @var int Product categories ID */
  protected $productCategoriesId;

  /**
   * Constructor
   * Initializes the Products app and gets the product ID from POST data
   */
  public function __construct()
  {
    // Get the Products application instance from Registry
    $this->app = Registry::get('Products');

    // Sanitize and store the product ID from POST data
    $this->Id = HTML::sanitize($_POST['products_id']);
  }

  /**
   * Execute the deletion of product embeddings
   * Triggered when DeleteConfirm action is requested
   */
  public function execute()
  {
    // Check if DeleteConfirm is set in GET and we have a valid product ID
    if (isset($_GET['DeleteConfirm']) && isset($this->Id)) {
      // Delete the product embedding from the database
      $this->app->db->delete('products_embedding', ['products_id' => (int)$this->Id]);
    } // end if
  }
}
