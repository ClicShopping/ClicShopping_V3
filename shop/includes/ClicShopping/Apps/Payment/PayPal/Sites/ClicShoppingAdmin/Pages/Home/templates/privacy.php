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

  require_once(__DIR__ . '/template_top.php');
?>

  <h2><?php echo $CLICSHOPPING_PayPal->getDef('privacy_title'); ?></h2>

<?php echo $CLICSHOPPING_PayPal->getDef('privacy_body'); ?>

<?php
  require_once(__DIR__ . '/template_bottom.php');