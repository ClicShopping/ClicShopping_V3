<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\ProductsLength\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsLength;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class ClassUpdate extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('ProductsLength');
    }

    public function execute() {

      $products_length_class_from_id_old = HTML::sanitize($_GET['wID']);
      $products_length_class_to_id_old = HTML::sanitize($_GET['tID']);

      $products_length_class_from_id = HTML::sanitize($_POST['products_length_class_id']);
      $products_length_class_to_id = HTML::sanitize($_POST['products_length_class_to_id']);
      $products_length_class_rule  = $_POST['products_length_class_rule'];

      $Qcheck = $this->app->db->prepare('select products_length_class_from_id,
                                                products_length_class_to_id
                                          from :table_products_length_classes_rules
                                          where products_length_class_from_id = :products_length_class_from_id_old
                                          and products_length_class_to_id = :products_length_class_to_id_old
                                        ');

      $Qcheck->bindInt(':products_length_class_from_id_old', $products_length_class_from_id_old);
      $Qcheck->bindInt(':products_length_class_to_id_old', $products_length_class_to_id_old);
      $Qcheck->execute();

      if ($Qcheck->fetch()) {
        $Qupdate = $this->app->db->prepare('update :table_products_length_classes_rules
                                            set products_length_class_from_id = :products_length_class_from_id,
                                            products_length_class_to_id = :products_length_class_to_id,
                                            products_length_class_rule = :products_length_class_rule
                                            where products_length_class_from_id = :products_length_class_from_id_old
                                            and products_length_class_to_id = :products_length_class_to_id_old
                                          ');

        $Qupdate->bindInt(':products_length_class_from_id', $products_length_class_from_id);
        $Qupdate->bindInt(':products_length_class_to_id', $products_length_class_to_id);
        $Qupdate->bindDecimal(':products_length_class_rule',$products_length_class_rule);
        $Qupdate->bindInt(':products_length_class_from_id_old', $products_length_class_from_id_old);
        $Qupdate->bindInt(':products_length_class_to_id_old', $products_length_class_to_id_old);
        $Qupdate->execute();
      }

      Cache::clear('products_length-classes');
      Cache::clear('products_length-rules');

      $this->app->redirect('ProductsLength&'. (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }