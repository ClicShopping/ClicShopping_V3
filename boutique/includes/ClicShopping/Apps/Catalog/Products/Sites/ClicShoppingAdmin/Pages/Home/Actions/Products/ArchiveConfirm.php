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


  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  class ArchiveConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $ID;
    protected $cPath;

    public function __construct(){
      $this->app = Registry::get('Products');

      $this->ID = HTML::sanitize($_POST['products_id']);
      $this->cPath = HTML::sanitize($_GET['cPath']);
    }

    public function execute()  {
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


// update the products cross sell and related master
      $Qupdate = $this->app->db->prepare('update :table_products_related
                                      set products_cross_sell = 0,
                                          products_related = 0
                                      where products_related_id_master = :products_related_id_master
                                    ');
      $Qupdate->bindInt(':products_related_id_master',$this->ID);
      $Qupdate->execute();

// update the products cross sell and related salve

      $Qupdate = $this->app->db->prepare('update :table_products_related
                                      set products_cross_sell = 0,
                                          products_related = 0
                                      where products_related_id_slave = :products_related_id_slave
                                    ');
      $Qupdate->bindInt(':products_related_id_slave', $this->ID);
      $Qupdate->execute();

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

      $CLICSHOPPING_Hooks->call('Products','Archive');

      $this->app->redirect('Products&cPath=' . $this->cPath);

    }
  }