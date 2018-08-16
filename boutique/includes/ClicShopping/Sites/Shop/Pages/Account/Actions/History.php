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

  class History extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      global $ordersTotalRow, $Qorders;

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Breadcrumb= Registry::get('Breadcrumb');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect('index.php', 'Account&LogIn');
      }

      $Qorders = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS o.orders_id,
                                                             o.date_purchased,
                                                             o.delivery_name,
                                                             o.billing_name,
                                                             ot.text as order_total,
                                                             s.orders_status_name
                                     from :table_orders o,
                                          :table_orders_total ot,
                                          :table_orders_status s
                                     where o.customers_id = :customers_id
                                     and s.language_id = :language_id
                                     and (ot.class = :class or ot.class = :class1)
                                     and s.public_flag = 1
                                     and o.orders_id = ot.orders_id
                                     and o.orders_status = s.orders_status_id
                                     order by o.orders_id desc
                                     limit :page_set_offset,
                                           :page_set_max_results
                                    ');
      $Qorders->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qorders->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qorders->bindValue(':class', 'ot_total');
      $Qorders->bindValue(':class1', 'TO');
      $Qorders->setPageSet(MAX_DISPLAY_ORDER_HISTORY);
      $Qorders->execute();

      $ordersTotalRow = $Qorders->getPageSetTotalRows();

// templates
      $this->page->setFile('history.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('account_history');
//language
      $CLICSHOPPING_Language->loadDefinitions('account_history');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link('index.php', 'Account&Main'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'), CLICSHOPPING::link('index.php', 'Account&History'));
    }
  }
