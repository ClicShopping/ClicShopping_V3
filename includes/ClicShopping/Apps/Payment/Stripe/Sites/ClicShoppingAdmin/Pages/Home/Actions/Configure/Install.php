<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\Stripe\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

use ClicShopping\OM\Registry;
use ClicShopping\Apps\Payment\Stripe\Sql\MariaDb\MariaDb;

class Install extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
    $CLICSHOPPING_Stripe = Registry::get('Stripe');
    $CLICSHOPPING_Composer = Registry::get('Composer');

    $current_module = $this->page->data['current_module'];

    $CLICSHOPPING_Stripe->loadDefinitions('Sites/ClicShoppingAdmin/install');

    $m = Registry::get('StripeAdminConfig' . $current_module);
    $m->install();

    //add condition to select mariaDb ou postgres
    Registry::set('MariaDb', new MariaDb());
    $CLICSHOPPING_MariaDb = Registry::get('MariaDb');
    $CLICSHOPPING_MariaDb->execute();

    $CLICSHOPPING_Composer->install('stripe/stripe-php');

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Stripe->getDef('alert_module_install_success'), 'success', 'Stripe');

    $CLICSHOPPING_Stripe->redirect('Configure&module=' . $current_module);
  }
}
