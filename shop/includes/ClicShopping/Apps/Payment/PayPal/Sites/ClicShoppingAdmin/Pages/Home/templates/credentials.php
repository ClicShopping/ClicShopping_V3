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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $current_module = $CLICSHOPPING_Page->data['current_module'];

  require_once(__DIR__ . '/template_top.php');
?>
  <div class="row col-md-12">
    <ul class="nav nav-tabs flex-column flex-sm-row" id="appPayPalToolbar">
      <li class="nav-item" data-module="PP">
        <a class="nav-link active"
           href="<?php echo $CLICSHOPPING_PayPal->link('Credentials&module=PP'); ?>"><?php echo $CLICSHOPPING_PayPal->getDef('section_paypal'); ?></a>
      </li>
      <li class="nav-item" data-module="PF">
        <a class="nav-link"
           href="<?php echo $CLICSHOPPING_PayPal->link('Credentials&module=PF'); ?>"><?php echo $CLICSHOPPING_PayPal->getDef('section_payflow'); ?></a>
      </li>
  </div>
  <script>
      $('#appPayPalToolbar li[data-module="<?php echo $current_module; ?>"]').addClass('active');
  </script>

  <form name="paypalCredentials"
        action="<?php echo $CLICSHOPPING_PayPal->link('Credentials&Process&module=' . $current_module); ?>"
        method="post">

    <?php
      require_once(__DIR__ . '/credentials_' . strtolower($current_module) . '.php');
    ?>
    <br/><br/>
    <div class="col-md-12 text-md-right">

      <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_save'), null, null, 'success'); ?>
    </div>
  </form>

<?php
  require_once(__DIR__ . '/template_bottom.php');