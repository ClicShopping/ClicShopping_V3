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

  namespace ClicShopping\Apps\Payment\Stripe\Module\Hooks\ClicShoppingAdmin\Orders;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\Stripe\Stripe as StripeApp;

  class PageContentTab3 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Stripe')) {
        Registry::set('Stripe', new StripeApp());
      }

      $this->app = Registry::get('Stripe');
    }

    public function display()
    {

      if (!defined('CLICSHOPPING_APP_STRIPE_ST_STATUS')) {
        return false;
      }

      $this->app->loadDefinitions('Hooks/ClicShoppingAdmin/Orders/page_content_tab_3');

      $content = '<!-- stripe start -->';
      $content .= '<div class="separator"></div>';
      $content .= '<div class="row" id="stripeButton">';
      $content .= '<span class="col-md-2"><a href="https://dashboard.stripe.com" target="_blank" class="btn btn-primary" role="button">' . $this->app->getDef('text_stripe_dashboard') . '</a>';
      $content .= '</div>';
      $content .= '<!-- stripe end -->';

      $output = <<<EOD
<!-- ######################## -->
<!--  Start order Stripe     -->
<!-- ######################## -->
<script>
$('#ErpOrder').prepend(
    '{$content}'
);
</script>
<!-- ######################## -->
<!--  End order Stripe      -->
<!-- ######################## -->
EOD;

      return $output;
    }
  }
