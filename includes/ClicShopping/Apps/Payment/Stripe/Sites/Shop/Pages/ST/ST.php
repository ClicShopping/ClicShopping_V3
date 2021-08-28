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

  namespace ClicShopping\Apps\Payment\Stripe\Sites\Shop\Pages\ST;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\Stripe\Module\Payment\ST as PaymentStripeST;

  use Stripe\Event as StripeEvent;

  class ST extends \ClicShopping\OM\PagesAbstract
  {
    protected $file = null;
    protected bool $use_site_template = false;
    protected $pm;
    protected mixed $lang;

    protected function init()
    {

      $this->lang = Registry::get('Language');

      $this->pm = new PaymentStripeST();

      if (!\defined('CLICSHOPPING_APP_STRIPE_ST_STATUS') && CLICSHOPPING_APP_STRIPE_ST_STATUS == 'False') {
        return false;
      }

      $this->lang->loadDefinitions('Shop/checkout_process');

      $payload = @file_get_contents('php://input');
      $event = null;

      try {
        $event = StripeEvent::constructFrom(
            json_decode($payload, true)
        );
      } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit();
      }

//      $stripe_sca = StripeAPI();

// Handle the event
      switch ($event->type) {
        case 'payment_intent.succeeded':
          $event->data->object;
//          $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
//handlePaymentIntentSucceeded($paymentIntent);
          break;
        case 'payment_method.attached':
          $event->data->object;
//          $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
//handlePaymentMethodAttached($paymentMethod);
          break;
        // ... handle other event types
        default:
          // Unexpected event type
          http_response_code(400);
          exit();
      }

      http_response_code(200);

      Registry::get('Session')->kill();
    }
  }
