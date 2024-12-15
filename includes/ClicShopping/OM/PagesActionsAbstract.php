<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

/**
 * This abstract class implements the PagesActionsInterface and serves as a base
 * class for handling page-specific actions in the application.
 */
abstract class PagesActionsAbstract implements \ClicShopping\OM\PagesActionsInterface
{
  protected $page;
  protected $file;
  protected bool $is_rpc = false;

  /**
   * Constructor method for initializing the page object.
   *
   * @param \ClicShopping\OM\PagesInterface $page An instance of PagesInterface representing the page to be initialized.
   * @return void
   */
  public function __construct(\ClicShopping\OM\PagesInterface $page)
  {
    $this->page = $page;

    if (isset($this->file)) {
      $this->page->setFile($this->file);
    }
  }

  /**
   * Checks if the current request is an RPC (Remote Procedure Call) request.
   *
   * @return bool Returns true if the request is an RPC request, otherwise false.
   */
  public function isRPC()
  {
    return ($this->is_rpc === true);
  }
}
