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

  namespace ClicShopping\Sites\Shop\Pages\Checkout\Actions\Shipping;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\Sites\Shop\Shipping;

  class Process extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Hooks  = Registry::get('Hooks');

// if the customer is not logged on, redirect them to the login page
      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }

// process the selected shipping method
      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        if (!is_null($_POST['comments'])) {
          $_SESSION['comments'] = HTML::sanitize($_POST['comments']);
        }

// load the selected shipping module
        if (!Registry::exists('Shipping')) {
          Registry::set('Shipping', new Shipping());
        }

        $CLICSHOPPING_Shipping = Registry::get('Shipping');

        if (($CLICSHOPPING_Shipping->geCountShippingModules() > 0) || ($_SESSION['free_shipping'] === true)) {
          if ((isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_'))) {
            $_SESSION['shipping'] = $_POST['shipping'];

            $CLICSHOPPING_SM = null;

            if (strpos($_SESSION['shipping'], '\\') !== false) {

              list($vendor, $app, $module) = explode('\\', $_SESSION['shipping']);
              list($module, $method) = explode('_', $module);

              $module = $vendor . '\\' . $app . '\\' . $module;

              $code = 'Shipping_' . str_replace('\\', '_', $module);

              if (Registry::exists($code)) {
                $CLICSHOPPING_SM = Registry::get($code);
              }
            } else {
              list($module, $method) = explode('_', $_SESSION['shipping']);

              if (is_object($GLOBALS[$module])) {
                $CLICSHOPPING_SM = $GLOBALS[$module];
              }
            }

            if ( isset($CLICSHOPPING_SM) || ($_SESSION['shipping'] == 'free_free') ) {
              if ($_SESSION['shipping'] == 'free_free') {
                $quote[0]['methods'][0]['title'] = CLICSHOPPING::getDef('free_shipping_title');
                $quote[0]['methods'][0]['cost'] = '0';
              } else {
                $quote = $CLICSHOPPING_Shipping->getQuote($method, $module);
              }

              if (isset($quote['error'])) {
                unset($_SESSION['shipping']);
              } else {
                if ((isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost']))) {
                  $_SESSION['shipping'] = ['id' => $_SESSION['shipping'],
                                           'title' => (($_SESSION['free_shipping'] === true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . (isset($quote[0]['methods'][0]['title']) && !empty($quote[0]['methods'][0]['title']) ? ' (' . $quote[0]['methods'][0]['title'] . ')' : '')),
                                           'cost' => $quote[0]['methods'][0]['cost']
                                          ];

                  $CLICSHOPPING_Hooks->call('Shipping', 'Process');

                  CLICSHOPPING::redirect(null, 'Checkout&Billing');
                }
              }
            } else {
              unset($_SESSION['shipping']);
            }

          }
        } else {
          if (defined('SHIPPING_ALLOW_UNDEFINED_ZONES') && (SHIPPING_ALLOW_UNDEFINED_ZONES == 'False')) {
            unset($_SESSION['shipping']);
          } else {
            $_SESSION['shipping'] = false;

            $CLICSHOPPING_Hooks->call('Shipping', 'Process');

            CLICSHOPPING::redirect(null, 'Checkout&Billing');
          }
        }
      } else {
        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_during_process'), 'danger', 'checkout_shipping');

        CLICSHOPPING::redirect(null, 'Checkout&Shipping');
      }
    }
  }