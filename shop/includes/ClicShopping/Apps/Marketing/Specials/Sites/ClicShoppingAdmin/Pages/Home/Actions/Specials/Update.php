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


  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home\Actions\Specials;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {

      $CLICSHOPPING_Specials = Registry::get('Specials');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      if (isset($_POST['specials_id'])) $specials_id = HTML::sanitize($_POST['specials_id']);
      if (isset($_POST['products_price'])) $products_price = HTML::sanitize($_POST['products_price']);
      if (isset($_POST['specials_price'])) $specials_price = HTML::sanitize($_POST['specials_price']);
      if (isset($_POST['expdate'])) $expdate = HTML::sanitize($_POST['expdate']);
      if (isset($_POST['schdate'])) $schdate = HTML::sanitize($_POST['schdate']);

      if (isset($_POST['flash_discount']) && HTML::sanitize($_POST['flash_discount']) == 1) {
        $flash_discount = 1;
      } else {
        $flash_discount = 0;
      }

      if (substr($specials_price, -1) == '%') $specials_price = ($products_price - (($specials_price / 100) * $products_price));

      $expires_date = '';
      $scheduled_date = '';

      if (!empty($expdate)) {
        $expires_date = substr($expdate, 0, 4) . substr($expdate, 5, 2) . substr($expdate, 8, 2);
      }

      if (!empty($schdate)) {
        $scheduled_date = substr($schdate, 0, 4) . substr($schdate, 5, 2) . substr($schdate, 8, 2);
      }

      $Qupdate = $CLICSHOPPING_Specials->db->prepare('update :table_specials
                                                      set specials_new_products_price = :specials_new_products_price,
                                                          specials_last_modified = now(),
                                                          expires_date = :expires_date,
                                                          scheduled_date = :scheduled_date,
                                                          flash_discount = :flash_discount
                                                      where specials_id = :specials_id
                                                    ');
      $Qupdate->bindDecimal(':specials_new_products_price', $specials_price);
      $Qupdate->bindValue(':expires_date', !empty($expires_date) ? $expires_date  : null);
      $Qupdate->bindValue(':scheduled_date', !empty($scheduled_date) ? $scheduled_date : null);
      $Qupdate->bindInt(':flash_discount', $flash_discount);
      $Qupdate->bindInt(':specials_id', $specials_id);

      $Qupdate->execute();

      $CLICSHOPPING_Hooks->call('Specials','Update');

      $CLICSHOPPING_Specials->redirect('Specials&page=', $page . '&sID=' . $specials_id);
    }
  }