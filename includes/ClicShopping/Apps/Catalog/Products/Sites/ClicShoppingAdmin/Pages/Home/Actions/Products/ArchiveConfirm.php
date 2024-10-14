<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ArchiveConfirm extends \ClicShopping\OM\PagesActionsAbstract
{
  private mixed $app;
  protected $ID;
  protected $cPath;

  public function __construct()
  {
    $this->app = Registry::get('Products');

    $this->ID = HTML::sanitize($_POST['products_id']);
    $this->cPath = HTML::sanitize($_GET['cPath']);
  }

  public function execute()
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $Qupdate = $this->app->db->prepare('update :table_products
                                          set products_archive = 1,
                                              products_ordered = 0
                                          where products_id = :products_id
                                        ');
    $Qupdate->bindInt(':products_id', $this->ID);
    $Qupdate->execute();

// Mise a zero des stats

    $Qupdate = $this->app->db->prepare('update :table_products_description
                                          set products_viewed = 0
                                          where products_id = :products_id
                                        ');
    $Qupdate->bindInt(':products_id', $this->ID);
    $Qupdate->execute();


    Cache::clear('categories');
    Cache::clear('products-also_purchased');
    Cache::clear('upcoming');

    $CLICSHOPPING_Hooks->call('Products', 'Archive');

    $this->app->redirect('Products&cPath=' . $this->cPath);
  }
}