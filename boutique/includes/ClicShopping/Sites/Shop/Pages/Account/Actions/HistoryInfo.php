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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Shop\Pages\Account\Classes\HistoryInfo as Info;

  class HistoryInfo extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      global $QdonwloadProductsFiles;

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Breadcrumb= Registry::get('Breadcrumb');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $CLICSHOPPING_Hooks->call('HistoryInfo', 'PreAction');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect('index.php', 'Account&LogIn');
      }

      if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))) {
        CLICSHOPPING::redirect('index.php', 'Account&History');
      }

      $check_history_info = Info::getHistoryInfoCheck();

      if ($check_history_info === false || ($check_history_info['customers_id'] != $CLICSHOPPING_Customer->getID())) {
        CLICSHOPPING::redirect('index', 'Account&History');
      }

// download product
// Display the pdf type in function the status
      $QdonwloadProductsFiles = Info::getHistoryInfoDownloadFiles();

// templates
      $this->page->setFile('history_info.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('account_history_info');
//language
      $CLICSHOPPING_Language->loadDefinitions('account_history_info');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link('index.php', 'Account&Main'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'), CLICSHOPPING::link('index.php', 'Account&HistoryInfo'));
      $CLICSHOPPING_Breadcrumb->add(sprintf(CLICSHOPPING::getDef('navbar_title_3', ['order_id'=> HTML::sanitize($_GET['order_id'])]), $_GET['order_id']), CLICSHOPPING::link('index.php', 'Account&HistoryInfo&order_id=' . (int)$_GET['order_id']));
    }
  }
