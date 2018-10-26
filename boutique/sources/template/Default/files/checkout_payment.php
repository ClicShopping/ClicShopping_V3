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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  $CLICSHOPPING_Customer = Registry::get('Customer');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');

  $CLICSHOPPING_Payment = Registry::get('Payment');

  echo $CLICSHOPPING_Payment->javascript_validation();

  require($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  echo HTML::form('checkout_payment', CLICSHOPPING::link('index.php', 'Checkout&Confirmation'), 'post', 'class="form-inline" role="form" id ="checkout_payment" onsubmit="return check_form();"',  ['tokenize' => true]);
?>
<section class="checkout_payment" id="checkout_payment">
  <div class="contentContainer">
    <div class="contentText">
<?php
    if (isset($_GET['payment_error'])) {
//pb here better with new payment registry : Registry('payment', new Payment($_GET['$_GET['payment_error']']) create a registry error
      $error = $CLICSHOPPING_Payment->get_error();
?>
      <div class="alert alert-danger">
        <div>
<?php
      if (!is_null($error)) {
        echo '<strong>' . HTML::outputProtected($error['title']) . '</strong> ';
        echo HTML::outputProtected($error['error']);
      } else {
        echo CLICSHOPPING::getDef('error_payment_obscur');
      }
?>
        </div>
        <div>
<?php
      if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
         echo CLICSHOPPING::getDef('text_conditions_description') . '<br />';
      }
?>
        </div>
      </div>
<?php
    }
?>
      <div class="page-header"><h1><?php echo CLICSHOPPING::getDef('heading_title_Payment'); ?></h1></div>
      <div class="form-group">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_checkout_payment'); ?>
      </div>
    </div>
  </div>
</section>
</form>
