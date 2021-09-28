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


  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Sites\Shop\OrderTotal;
  use ClicShopping\Apps\Orders\Orders\Classes\Shop\Order as OrderClass;
  use ClicShopping\Sites\Shop\ShoppingCart;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class UpdateOrder extends \ClicShopping\OM\PagesActionsAbstract
  {
    /**
     * @var bool|\orders|null
     */
    protected mixed $app;
    /**
     * @var bool|\lang|null
     */
    protected mixed $lang;
    /**
     * @var bool|\db|null
     */
    protected mixed $db;
    /**
     * @var bool|\hooks|null
     */
    protected $hooks;
    /**
     * @var bool|\messageStack|null
     */
    private $messageStack;
    /**
     * @var string
     */
    private $orders_products_id;
    /**
     * @var string
     */
    private $orders_products_name;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $products_id;
    /**
     * @var int
     */
    private $old_quantity;


    public function __construct()
    {
      $this->app = Registry::get('Orders');
      $this->lang = Registry::get('Language');
      $this->db = Registry::get('Db');
      $this->messageStack = Registry::get('MessageStack');
      
      if (isset($_GET['oID'])) $this->id = HTML::sanitize($_GET['oID']);
      if (isset($_POST['products_id'])) $this->products_id = HTML::sanitize($_POST['products_id']);
      if (isset($_POST['quantity'])) $this->quantity = HTML::sanitize($_POST['quantity']);
      if (isset($_POST['orders_products_id'])) $this->orders_products_id = HTML::sanitize($_POST['orders_products_id']);
      if (isset($_POST['orders_products_name'])) $this->orders_products_name = HTML::sanitize($_POST['orders_products_name']);

      $this->hooks = Registry::get('Hooks');

      $this->checkQuantity();
    }

    /**
     * update history
     */
    private function updateHistory()
    {
      $date_format = 'Y-m-d H:i:s';

      $QstatusOrder = $this->app->db->prepare('select orders_status,
                                                      orders_status_invoice
                                              from :table_orders
                                              where orders_id = :orders_id
                                              ');

      $QstatusOrder->bindInt('orders_id', $this->id);
      $QstatusOrder->execute();

      $data_array = ['orders_id' => (int)$this->id,
        'orders_status_id' => (int)$this->status,
        'orders_status_invoice_id' => (int)$this->statusInvoice,
        'admin_user_name' => AdministratorAdmin::getUserAdmin(),
        'date_added' => 'now()',
        'customer_notified' => 1,
        'comments' => $this->app->getDef('text_info_new_quantity', ['new_quantity' => $this->quantity, 'products_name' => $this->orders_products_name]) .  "\n"  . 'Date : ' . DateTime::getNow($date_format) .  "\n\n" ,
      ];

      //$this->app->db->save('orders_status_history', $data_array);
    }

    /**
     * Chak the stock if it's correct
     * @return bool
     */
    public function checkQuantity(): bool
    {
      $Qstock = $this->db->prepare('select products_quantity
                                      from :table_products
                                      where products_id = :products_id
                                    ');

      $Qstock->bindInt(':products_id', $this->products_id);
      $Qstock->execute();

      $stock_left = $Qstock->valueInt('products_stock');

      $new_stock = $stock_left - $this->quantity;

      if (($new_stock <= 0) && STOCK_CHECK == 'true') {
        $this->messageStack->add($this->app->getDef('warning_order_stock_not_updated'), 'warning');
        return false;
      }

      return true;
    }

    /**
     * Calcul the new total order
     */
    public function NewOrderTotal()
    {
      Registry::set('Order', new OrderClass($this->id));
      $order = Registry::get('Order');

      $order_totals = $order->totals;

      for ($i = 0, $n = \count($order_totals); $i < $n; $i++) {
        $sql_data_array = [
          'title' => $order_totals[$i]['title'],
          'text' => $order_totals[$i]['text'],
          'value' => (float)$order_totals[$i]['value'],
          'class' => $order_totals[$i]['code'],
          'sort_order' => (int)$order_totals[$i]['sort_order']
        ];



        $update_array = [
          'orders_total_id' => (int)$this->orders_total_id,
          'orders_id' => (int)$this->id
        ];
exit;
//        $this->db->save('orders_total', $sql_data_array, $update_array);
      }
    }


/*
* Check min qty orders
*/


    public function execute()
    {
      if (isset($_GET['Orders'], $_GET['UpdateOrder'])) {
        $order_updated = false;

        if (empty($this->quantity)) {
          $order_updated = false;
        } else {
          $order_updated = true;

          $sql_array = [
            'products_quantity' => $this->quantity
          ];

          $update_array = [
            'orders_products_id' => $this->orders_products_id
          ];

          $this->db->save('orders_products', $sql_array, $update_array);

          $this->updateHistory();
          $this->NewOrderTotal();
        }

        if ($order_updated === true) {
          $this->messageStack->add($this->app->getDef('success_order_updated'), 'success');
        } else {
          $this->messageStack->add($this->app->getDef('warning_order_not_updated'), 'warning');
        }

        $this->hooks->call('Orders', 'UpdateOrder');

        $this->app->redirect('Orders&Edit&id='. $this->id);
      }
    }
  }