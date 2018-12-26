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


  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $lang;
    protected $db;
    protected $oID;
    protected $status;
    protected $statusInvoice;
    protected $comments;
    protected $ordersStatusSupportId;
    protected $notifyComments;
    protected $notify;
    protected $ordersStatusSupport;

    public function __construct() {
      $this->app = Registry::get('Orders');
      $this->lang = Registry::get('Language');
      $this->db = Registry::get('Db');

      $this->oID = HTML::sanitize($_GET['oID']);
      $this->status = HTML::sanitize($_POST['status']);
      $this->statusInvoice = HTML::sanitize($_POST['status_invoice']);
      $this->comments = HTML::sanitize($_POST['comments']);
      $this->ordersTrackingId = HTML::sanitize($_POST['orders_tracking_id']);
      $this->ordersTrackingNumber = HTML::sanitize($_POST['orders_tracking_number']);
      $this->ordersStatusSupportId = HTML::sanitize($_POST['orders_status_support_id']);
      $this->notifyComments = HTML::sanitize($_POST['notify_comments']);
      $this->notify = HTML::sanitize($_POST['notify']);
      $this->ordersStatusSupport = HTML::sanitize($_POST['orders_status_support']);
    }

    private function getCheckStatus() {
      $data_array = ['customers_name',
                     'customers_email_address',
                     'orders_status',
                     'date_purchased',
                     'orders_status_invoice',
                     'erp_invoice'
                    ];

      $QcheckStatus = $this->app->db->get('orders', $data_array, ['orders_id' => (int)$this->oID]);

      $check = $QcheckStatus->fetch();

      return $check;
    }

    private function getMail() {
      global $tracking_id;

      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Mail  = Registry::get('Mail');

      if ($this->oID != 0) {
        $check = $this->getCheckStatus();
      }

      $notify_comments = '';

      if (isset($this->notifyComments) && ($this->notifyComments == 'on')) {
        $notify_comments = $this->app->getDef('email_text_comments_update',  ['comment' => nl2br($this->comments)]) . "\n\n";
        $notify_comments = html_entity_decode($notify_comments);
      }

      if ($this->ordersStatusSupportId > 1) {
        $QordersStatusSupport = $this->db->prepare('select orders_status_support_name
                                                     from :table_orders_status_support
                                                     where language_id = :language_id
                                                     and orders_status_support_id = :orders_status_support_id
                                                    ');
        $QordersStatusSupport->bindInt(':language_id', $this->lang->getId());
        $QordersStatusSupport->bindInt(':orders_status_support_id', $this->ordersStatusSupportId);
        $QordersStatusSupport->execute();

        $customer_support = $QordersStatusSupport->value('orders_status_support_name') .'<br />';
      }

      $CLICSHOPPING_Hooks->call('Orders','OrderEmail');

      $template_email_intro_command = TemplateEmailAdmin::getTemplateEmailIntroCommand();
      $template_email_signature = TemplateEmailAdmin::getTemplateEmailSignature();
      $template_email_footer = TemplateEmailAdmin::getTemplateEmailTextFooter();
      $status_order = $this->app->getDef('email_text_new_order_status', ['status' => $this->status]);

      $email_subject =  $this->app->getDef('email_text_subject', ['store_name' => STORE_NAME]);

      $email_text = $template_email_intro_command . '<br />'. $status_order . '<br />'.   $this->app->getDef('email_separator') . '<br /><br />'. $this->app->getDef('email_text_order_number') . ' '. $this->oID . '<br /><br />'. $this->app->getDef('email_text_invoice_url') . '<br />'. CLICSHOPPING::link('Shop/index.php', 'Account&HistoryInfo&order_id=' . $this->oID) . '<br /><br />' . $this->app->getDef('email_text_date_ordered') . ' ' . DateTime::toShort($check['date_purchased']) . '<br />' . $tracking_id . '<br />' . '<br />' . $customer_support . '<br />' . $notify_comments .'<br /><br />' .  $template_email_signature . '<br /><br />' . $template_email_footer;


// Envoie du mail avec gestion des images pour Fckeditor et Imanager.
      $message = html_entity_decode($email_text);
      $message = str_replace('src="/', 'src="' . CLICSHOPPING::getConfig('http_server', 'Shop') . '/', $message);
      $CLICSHOPPING_Mail->addHtmlCkeditor($message);
      $CLICSHOPPING_Mail->build_message();
      $from = STORE_OWNER_EMAIL_ADDRESS;
      $CLICSHOPPING_Mail->send($check['customers_name'], $check['customers_email_address'], '', $from, $email_subject);
    }

    public function execute() {
      $CLICSHOPPING_MessageStack =  Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $order_updated = false;

      $check = $this->getCheckStatus();

      if ($this->oID != 0) {

// verify and update the status if changed
        if ( ($check['orders_status'] != $this->status) || ($check['orders_status_invoice'] != $this->statusInvoice) || !is_null($this->comments)) {
           $data_array = ['orders_status' => (int)$this->status,
                         'orders_status_invoice' => (int)$this->statusInvoice,
                         'last_modified' => 'now()'
                         ];

          $this->app->db->save('orders', $data_array, ['orders_id' => $this->oID]);

          $customer_notified = 0;

          if (isset($this->notify) && ($this->notify == 'on')) {
            $this->getMail();
            $customer_notified = 1;
          }

          $data_array = [ 'orders_id' => (int)$this->oID,
                          'orders_status_id' => (int)$this->status,
                          'orders_status_invoice_id' => (int)$this->statusInvoice,
                          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
                          'date_added' => 'now()',
                          'customer_notified' => (int)$customer_notified,
                          'comments' => $this->comments,
                          'orders_status_support_id' => $this->ordersStatusSupportId
                        ];

          $this->app->db->save('orders_status_history', $data_array);

          $order_updated = true;
        } else {
          $order_updated = true;
        }
      }

      if ($order_updated === true) {
        $CLICSHOPPING_Hooks->call('Orders','Update');
        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_order_updated'), 'success');
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('warning_order_not_updated'), 'warning');
      }

      $this->app->redirect('Orders');
    }
  }