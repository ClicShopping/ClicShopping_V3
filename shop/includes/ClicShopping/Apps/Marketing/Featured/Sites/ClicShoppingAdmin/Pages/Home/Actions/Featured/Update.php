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


  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home\Actions\Featured;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {

      $CLICSHOPPING_Featured = Registry::get('Featured');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
        $_GET['page'] = 1;
      }

      $products_featured_id = HTML::sanitize($_POST['products_featured_id']);
      $expdate = HTML::sanitize($_POST['expdate']);
      $schdate = HTML::sanitize($_POST['schdate']);

      $expires_date = '';
      $scheduled_date = '';

      if (!empty($expdate)) {
        $expires_date = substr($expdate, 0, 4) . substr($expdate, 5, 2) . substr($expdate, 8, 2);
      }

      if (!empty($schdate)) {
        $scheduled_date = substr($schdate, 0, 4) . substr($schdate, 5, 2) . substr($schdate, 8, 2);
      }

      $Qupdate = $CLICSHOPPING_Featured->db->prepare('update :table_products_featured
                                                      set products_featured_last_modified = now(),
                                                          expires_date = :expires_date,
                                                          scheduled_date = :scheduled_date
                                                      where products_featured_id = :products_featured_id
                                                    ');
      $Qupdate->bindValue(':expires_date', !empty($expires_date) ? $expires_date  : null);
      $Qupdate->bindValue(':scheduled_date', !empty($scheduled_date) ? $scheduled_date : null);
      $Qupdate->bindInt(':products_featured_id', $products_featured_id);

      $Qupdate->execute();

      $CLICSHOPPING_Hooks->call('Featured','Update');

      $CLICSHOPPING_Favorites->redirect('Featured', (isset($page) ? 'page=' . $page . '&' : '') . 'sID=' . $products_featured_id);
    }
  }