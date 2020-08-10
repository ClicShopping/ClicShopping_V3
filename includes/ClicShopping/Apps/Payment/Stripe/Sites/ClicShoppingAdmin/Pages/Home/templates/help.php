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

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Stripe = Registry::get('Stripe');

  if ($CLICSHOPPING_MessageStack->exists('Stripe')) {
    echo $CLICSHOPPING_MessageStack->get('Stripe');
  }
?>
  <div class="contentBody">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/modules_modules_checkout_payment.gif', $CLICSHOPPING_Stripe->getDef('Stripe'), '40', '40'); ?></span>
            <span class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Stripe->getDef('heading_title'); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_Stripe->getDef('text_stripe') ; ?></strong></div>
    <div class="adminformTitle">
      <?php echo $CLICSHOPPING_Stripe->getDef('text_stripe_info') ; ?>
    </div>
  </div>
