<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions\Marketplace;

use ClicShopping\OM\Registry;

class UpdateMarketplace extends \ClicShopping\OM\PagesActionsAbstract
{
  protected mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('Upgrade');
  }

  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    if (isset($_GET['UpdateMarketplace'], $_GET['Marketplace'])) {
      $this->app->db->delete('marketplace_categories');
      $this->app->db->delete('marketplace_files');
      $this->app->db->delete('marketplace_file_informations');

      $CLICSHOPPING_MessageStack->add($this->app->getDef('Marketplace'), 'success');
    }

    $this->app->redirect('Marketplace');
  }
}