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


  namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home\Actions\Newsletter;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Newsletter = Registry::get('Newsletter');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_POST['newsletter_id'])) $newsletter_id = HTML::sanitize($_POST['newsletter_id']);

      $newsletter_module = basename(HTML::sanitize($_POST['module']));
      $title = HTML::sanitize($_POST['title']);
      $content = $_POST['message'];
      $customers_group_id = HTML::sanitize($_POST['customers_group_id']);
      $languages_newsletter_id = HTML::sanitize($_POST['languages_id']);
      $newsletters_accept_file = HTML::sanitize($_POST['newsletters_accept_file']);
      $newsletters_customer_no_account = HTML::sanitize($_POST['newsletters_customer_no_account']);

      $newsletter_error = false;

      if(empty($newsletter_module) &&  $newsletter_module != 'Newsletter') {
        $CLICSHOPPING_Newsletter->redirect('Newsletter&Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID']);
      }

      if(empty($newsletter_module) && $newsletter_module != 'ProductNotification') {
        $CLICSHOPPING_Newsletter->redirect('Newsletter&Newsletter&page=' . $_GET['page'] . '&nID=' . $_GET['nID']);
      }

      $allowed = array_map(function($v) {return basename($v, '.php');}, glob(CLICSHOPPING::BASE_DIR . '/Apps/Communication/Newsletter/Module/ClicShoppingAdmin/Newsletter/*.php'));

      if (!in_array($newsletter_module, $allowed)) {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Newsletter->getDef('error_newsletter_module_not_exists'), 'danger');
        $newsletter_error = true;
      }


      if ($newsletters_accept_file == 'on') {
        $newsletters_accept_file = 0;
      } else {
        $newsletters_accept_file = 1;
      }

      if ($newsletters_customer_no_account == 'on') {
        $newsletters_customer_no_account = 0;
      } else {
        $newsletters_customer_no_account = 1;
      }

      if (empty($title)) {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Newsletter->getDef('error_newsletter_title'), 'error');
        $newsletter_error = true;
      }

      if ((empty($newsletter_module)) || !is_file(CLICSHOPPING::BASE_DIR . '/Apps/Communication/Newsletter/Module/ClicShoppingAdmin/Newsletter/' . $newsletter_module . '.php')) {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Newsletter->getDef('error_newsletter_module'), 'error');
        $newsletter_error = true;
      }

      if ($newsletter_error === false) {
        $sql_data_array = ['title' => $title,
                           'content' => $content,
                           'module' => $newsletter_module,
                           'languages_id' => (int)$languages_newsletter_id,
                           'customers_group_id' => (int)$customers_group_id,
                           'newsletters_accept_file' => (int)$newsletters_accept_file,
                           'newsletters_customer_no_account' => (int)$newsletters_customer_no_account
                          ];

        $sql_data_array['date_added'] = 'now()';
        $sql_data_array['status'] = 0;
        $sql_data_array['locked'] = 0;

        $CLICSHOPPING_Newsletter->db->save('newsletters', $sql_data_array);

        $newsletter_id = $CLICSHOPPING_Newsletter->db->lastInsertId();

        $CLICSHOPPING_Hooks->call('Newsletter', 'Insert');

        $CLICSHOPPING_Newsletter->redirect('Newsletter&page=' . $_GET['page'] . '&nID=' . $newsletter_id);
      }
    }
  }