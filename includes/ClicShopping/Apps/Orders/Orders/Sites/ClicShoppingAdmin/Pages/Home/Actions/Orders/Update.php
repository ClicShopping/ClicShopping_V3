<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    protected mixed $lang;
    protected mixed $db;
    protected int $oID;
    protected int $status;
    protected int $statusInvoice;
    protected string $comments;
    protected $notifyComments;
    protected $notify;
    protected $hooks;

    public function __construct()
    {
      $this->app = Registry::get('Orders');
      $this->lang = Registry::get('Language');
      $this->db = Registry::get('Db');

      if (isset($_GET['oID'])) $this->oID = HTML::sanitize($_GET['oID']);
      if (isset($_POST['status'])) $this->status = HTML::sanitize($_POST['status']);

      if (isset($_POST['status_invoice'])) $this->statusInvoice = HTML::sanitize($_POST['status_invoice']);
      if (isset($_POST['comments'])) $this->comments = HTML::sanitize($_POST['comments']);

      if (isset($_POST['notify_comments'])) $this->notifyComments = HTML::sanitize($_POST['notify_comments']);
      if (isset($_POST['notify'])) $this->notify = HTML::sanitize($_POST['notify']);

      $this->hooks = Registry::get('Hooks');
    }

    private function getCheckStatus()
    {
      $data_array = [
        'customers_name',
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

    private function getMail()
    {
      $CLICSHOPPING_Mail = Registry::get('Mail');

      if ($this->oID != 0) {
        $check = $this->getCheckStatus();
      }

      $notify_comments = '';

      if (isset($this->notifyComments)) {
        $notify_comments = $this->app->getDef('email_text_comments_update', ['comment' => nl2br($this->comments)]) . "\n\n";
        $notify_comments = html_entity_decode($notify_comments);
      }

      $template_email_intro_command = TemplateEmailAdmin::getTemplateEmailIntroCommand();
      $template_email_signature = TemplateEmailAdmin::getTemplateEmailSignature();
      $template_email_footer = TemplateEmailAdmin::getTemplateEmailTextFooter();
      $status_order = $this->app->getDef('email_text_new_order_status', ['status' => $this->status]);

      $email_subject = $this->app->getDef('email_text_subject', ['store_name' => STORE_NAME]);

      $email_text = $template_email_intro_command . '<br />' . $status_order . '<br />' . $this->app->getDef('email_separator') . '<br /><br />' . $this->app->getDef('email_text_order_number') . ' ' . $this->oID . '<br /><br />' . $this->app->getDef('email_text_invoice_url') . '<br />' . CLICSHOPPING::link('Shop/index.php', 'Account&HistoryInfo&order_id=' . $this->oID) . '<br /><br />' . $this->app->getDef('email_text_date_ordered') . ' ' . DateTime::toShort($check['date_purchased']) . '<br />' . $notify_comments . '<br /><br />' . $template_email_signature . '<br /><br />' . $template_email_footer;

// Envoie du mail avec gestion des images pour Fckeditor et Imanager.
      $message = html_entity_decode($email_text);
      $message = str_replace('src="/', 'src="' . CLICSHOPPING::getConfig('http_server', 'Shop') . '/', $message);
      $CLICSHOPPING_Mail->addHtmlCkeditor($message);

      $from = STORE_OWNER_EMAIL_ADDRESS;
      $CLICSHOPPING_Mail->send($check['customers_email_address'], $check['customers_name'], null, $from, $email_subject);

      $this->hooks->call('Orders', 'OrderEmail');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_GET['Update'])) {
        $order_updated = false;

        if ($this->oID != 0) {

          $check = $this->getCheckStatus();
// verify and update the status if changed
          if (($check['orders_status'] != $this->status) || ($check['orders_status_invoice'] != $this->statusInvoice) || !\is_null($this->comments)) {
            $data_array = [
              'orders_status' => (int)$this->status,
              'orders_status_invoice' => (int)$this->statusInvoice,
              'last_modified' => 'now()'
            ];

            $this->app->db->save('orders', $data_array, ['orders_id' => $this->oID]);

            $customer_notified = 0;

            if (isset($this->notify)) {
              $customer_notified = 1;
            }

            $data_array = [
              'orders_id' => (int)$this->oID,
              'orders_status_id' => (int)$this->status,
              'orders_status_invoice_id' => (int)$this->statusInvoice,
              'admin_user_name' => AdministratorAdmin::getUserAdmin(),
              'date_added' => 'now()',
              'customer_notified' => (int)$customer_notified,
              'comments' => $this->comments,
            ];

            $this->app->db->save('orders_status_history', $data_array);

            $order_updated = true;
          } else {
            $order_updated = true;
          }
        }

        if ($order_updated === true) {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('success_order_updated'), 'success');
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('warning_order_not_updated'), 'warning');
        }

        $this->hooks->call('Orders', 'Update');

        if (isset($this->notify)) {
          $this->getMail();
        }

        $this->app->redirect('Orders');
      }
    }
  }