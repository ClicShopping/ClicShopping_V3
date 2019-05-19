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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\Newsletters;

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general'])) {
        $newsletter_general = (int)$_POST['newsletter_general'];
      } else {
        $newsletter_general = 0;
      }

      $Qnewsletter = $CLICSHOPPING_Db->prepare('select customers_newsletter
                                               from :table_customers
                                               where customers_id = :customers_id
                                              ');
      $Qnewsletter->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qnewsletter->execute();


      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {

        $newsletter_general = (isset($_POST['newsletter_general']) && ($_POST['newsletter_general'] == 1)) ? 1 : 0;

        if ($newsletter_general !== $Qnewsletter->valueInt('customers_newsletter')) {
          $newsletter_general = ($Qnewsletter->valueInt('customers_newsletter') === 1) ? 0 : 1;

          $CLICSHOPPING_Db->save('customers', ['customers_newsletter' => $newsletter_general],
            ['customers_id' => (int)$CLICSHOPPING_Customer->getID()]
          );

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('success_newsletter_updated'), 'success', 'account_newsletter');
        }
      }

      $CLICSHOPPING_Hooks->call('Newsletters', 'Process');

      CLICSHOPPING::redirect(null, 'Account&Newsletters');
    }
  }