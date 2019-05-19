<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home\Actions\Specials;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Specials = Registry::get('Specials');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;

      $products_id = HTML::sanitize($_POST['products_id']);
      $specials_price = HTML::sanitize($_POST['specials_price']);
      $expdate = HTML::sanitize($_POST['expdate']);
      $schdate = HTML::sanitize($_POST['schdate']);

      if (HTML::sanitize($_POST['flash_discount']) == 1) {
        $flash_discount = 1;
      } else {
        $flash_discount = 0;
      }

      if (substr($specials_price, -1) == '%') {
        $Qproduct = $CLICSHOPPING_Specials->db->get('products', 'products_price', ['products_id' => (int)$products_id]);

        $products_price = $Qproduct->valueDecimal('products_price');
        $discount = ($specials_price / 100) * $products_price;
        $specials_price = $products_price - $discount;
      }

      $expires_date = '';
      if (!empty($expdate)) {
        $expires_date = substr($expdate, 0, 4) . substr($expdate, 5, 2) . substr($expdate, 8, 2);
      }

      $scheduled_date = '';
      if (!empty($schdate)) {
        $schedule_date = substr($schdate, 0, 4) . substr($schdate, 5, 2) . substr($schdate, 8, 2);
      }

      $CLICSHOPPING_Specials->db->save('specials', ['products_id' => (int)$products_id,
          'specials_new_products_price' => (float)$specials_price,
          'specials_date_added' => 'now()',
          'scheduled_date' => !empty($schedule_date) ? $schedule_date : 'null',
          'expires_date' => !empty($expires_date) ? $expires_date : 'null',
          'status' => 1,
          'flash_discount' => (int)$flash_discount
        ]
      );

      $CLICSHOPPING_Hooks->call('Specials', 'Insert');

      $CLICSHOPPING_Specials->redirect('Specials', 'page=' . $page);
    }
  }